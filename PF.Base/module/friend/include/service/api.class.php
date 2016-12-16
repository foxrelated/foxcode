<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');


class Friend_Service_Api extends \Core\Api\ApiServiceBase
{
    public function __construct()
    {
        //set public fields
        $this->setPublicFields([
            'user_id',
            'user_name',
            'full_name',
            'friend_id',
            'friend_user_id',
            'is_top_friend'
        ]);
    }

    /**
     * @description: get friends of user
     *
     * @return array|bool
     */
    public function get()
    {
        //check exists user
        $iUserId = $this->request()->get('user_id' , 0);
        if (!$iUserId)
        {
            $iUserId = Phpfox::getUserId();
        }
        if (!$iUserId)
        {
            return $this->error(_p('Please provide id of user you want to get friends list.'));
        }

        $aUser = Phpfox::getService('user')->get($iUserId, true);
        if (empty($aUser) || empty($aUser['user_id']))
        {
            return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('user__l')]));
        }

        //check permission
        if ($iUserId != Phpfox::getUserId() && Phpfox::getService('user.block')->isBlocked(null, $iUserId))
        {
            return $this->error(_p('Sorry, information of this user isn\'t available for you.'));
        }

        if (!User_Service_Privacy_Privacy::instance()->hasAccess($iUserId, 'friend.view_friend'))
        {
            return $this->error(_p('full_name_has_closed_gender_friends_section',[
                    'full_name' => User_Service_User::instance()->getFirstName($aUser['full_name']),
                    'gender' => User_Service_User::instance()->gender($aUser['gender'], true)
                ]));
        }

        $aFilters = array(
            'sort' => array(
                'type' => 'select',
                'options' => array(),
                'default' => 'full_name',
                'alias' => 'u'
            ),
            'sort_by' => array(
                'type' => 'select',
                'options' => array(
                    'DESC' => _p('descending'),
                    'ASC' => _p('ascending')
                ),
                'default' => 'ASC'
            ),
            'search' => array(
                'type' => 'input:text',
                'search' => '(u.full_name LIKE \'%[VALUE]%\' OR u.email LIKE \'%[VALUE]%\') AND',
                'size' => '15',
                'onclick' => _p('Search friend...')
            )
        );

        $oFilter = Phpfox_Search::instance()->set(array(
                'type' => 'friend',
                'filters' => $aFilters,
                'search' => 'search'
            )
        );

        $sView = $this->request()->get('view', '');
        if (Phpfox::getUserId() && $sView == 'mutual')
        {
            $oFilter->setCondition('friend.is_page = 0 AND friend.user_id = ' . Phpfox::getUserId());
        }
        else
        {
            $oFilter->setCondition('friend.is_page = 0 AND friend.user_id = ' . (int) $aUser['user_id']);
        }

        $this->initSearchParams();
        list(, $aFriends) = Friend_Service_Friend::instance()->get($oFilter->getConditions(), $oFilter->getSort(), $oFilter->getPage(), $this->getSearchParam('limit'), true, true, ($sView == 'online' ? true : false), ($sView === 'mutual' ? $aUser['user_id'] : null));

        $results = [];
        foreach ($aFriends as $aFriend)
        {
            $results[] = $this->getItem($aFriend);
        }

        return $this->success($results);
    }

    /**
     * @description: delete a friend
     *
     * @return array|bool
     */
    public function delete()
    {
        $this->isUser();
        $this->requireParams(['friend_user_id']);
        $iUserId = $this->request()->get('friend_user_id', 0);
        $aUser = Phpfox::getService('user')->get($iUserId, true);
        if (!$iUserId || empty($aUser) || empty($aUser['user_id']))
        {
            return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('friend__l')]));
        }

        if (!Friend_Service_Friend::instance()->isFriend(Phpfox::getUserId(), $iUserId))
        {
            return $this->error(_p('You and this user aren\'t friends.'));
        }

        Friend_Service_Process::instance()->delete($iUserId, false);

        return $this->success([], [_p('{{ item }} successfully deleted.', ['item' => _p('friend')])]);
    }

    /**
     * @description: send a friend request
     * @return array|bool
     */
    public function addRequest()
    {
        $this->isUser();
        if (!Phpfox::getUserParam('friend.can_add_friends'))
        {
            return $this->error(_p('You don\'t have permission to add new {{ item }}.', ['item' => _p('friend__l')]));
        }
        $this->requireParams(['user_id']);
        $iUserId = $this->request()->get('user_id', 0);
        $aUser = Phpfox::getService('user')->get($iUserId, true);
        if (!$iUserId || empty($aUser) || empty($aUser['user_id']))
        {
            return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('user__l')]));
        }

        if ($iUserId == Phpfox::getUserId())
        {
            return $this->error(_p('Request is invalid.'));
        }

        if (Friend_Service_Request_Request::instance()->isRequested(Phpfox::getUserId(), $aUser['user_id']))
        {
            return $this->error(_p('you_were_already_requested_to_be_friends'));
        }
        if (Friend_Service_Request_Request::instance()->isRequested($aUser['user_id'], Phpfox::getUserId()))
        {
            return $this->error(_p('you_already_requested_to_be_friends'));
        }
        if (Friend_Service_Friend::instance()->isFriend($aUser['user_id'], Phpfox::getUserId()))
        {
            return $this->error(_p('you_are_already_friends_with_this_user'));
        }
        if (User_Service_Block_Block::instance()->isBlocked($aUser['user_id'], Phpfox::getUserId()))
        {
            return $this->error(_p('unable_to_send_a_friend_request_to_this_user_at_this_moment'));
        }

        Friend_Service_Request_Process::instance()->add(Phpfox::getUserId(), $iUserId);

        return $this->success([], [_p('friend_request_successfully_sent')]);
    }

    /**
     * @description: cancel a friend request
     * @return array|bool
     */
    public function cancelRequest()
    {
        $this->isUser();
        $this->requireParams(['user_id']);
        $iUserId = $this->request()->get('user_id', 0);
        $aUser = Phpfox::getService('user')->get($iUserId, true);
        if (!$iUserId || empty($aUser) || empty($aUser['user_id']))
        {
            return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('user__l')]));
        }

        if ($iUserId == Phpfox::getUserId())
        {
            return $this->error(_p('Request is invalid.'));
        }

        if (Friend_Service_Friend::instance()->isFriend($aUser['user_id'], Phpfox::getUserId()))
        {
            return $this->error(_p('you_are_already_friends_with_this_user'));
        }

        if (!($iRequestId = Friend_Service_Request_Request::instance()->isRequested(Phpfox::getUserId(), $aUser['user_id'], true)))
        {
            return $this->error(_p('You haven\'t sent a friend request to this user yet.'));
        }

        Friend_Service_Request_Process::instance()->delete($iRequestId, Phpfox::getUserId());

        return $this->success([], [_p('{{ item }} successfully deleted.', ['item' => _p('Friend request')])]);
    }

    /**
     * @description: accept/deny an incomming friend request
     * @return array|bool
     */
    public function processRequest()
    {
        $this->isUser();
        $this->requireParams(['user_id', 'action']);

        $iUserId = $this->request()->get('user_id', 0);
        $aUser = Phpfox::getService('user')->get($iUserId, true);
        if (!$iUserId || empty($aUser) || empty($aUser['user_id']))
        {
            return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('user__l')]));
        }

        if ($iUserId == Phpfox::getUserId())
        {
            return $this->error(_p('Request is invalid.'));
        }

        if (Friend_Service_Friend::instance()->isFriend($aUser['user_id'], Phpfox::getUserId()))
        {
            return $this->error(_p('you_are_already_friends_with_this_user'));
        }

        if (!($iRequestId = Friend_Service_Request_Request::instance()->isRequested($aUser['user_id'], Phpfox::getUserId(), true)))
        {
            return $this->error(_p('Request is invalid.'));
        }

        $action = $this->request()->get('action', 'accept');
        if (!in_array($action, ['accept', 'deny']))
        {
            return $this->error(_p('Request is invalid.'));
        }

        if ($action == 'accept')
        {
            Friend_Service_Process::instance()->add(Phpfox::getUserId(), $iUserId, 0);
            return $this->success([], [_p('Friend request successfully accepted.')]);
        }

        $aRequest = Friend_Service_Request_Request::instance()->getRequest($iRequestId);
        if ($aRequest['is_ignore'])
        {
            return $this->error(_p('You have denied this friend request already.'));
        }
        Friend_Service_Process::instance()->deny(Phpfox::getUserId(), $iUserId);
        return $this->success([], [_p('Friend request successfully denied.')]);
    }
}