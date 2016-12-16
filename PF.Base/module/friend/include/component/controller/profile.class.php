<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Profile
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Friend
 * @version 		$Id: profile.class.php 5224 2013-01-28 13:05:21Z Raymond_Benc $
 */
class Friend_Component_Controller_Profile extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{	
		if (defined('PHPFOX_IS_AJAX_CONTROLLER'))
		{
			$aUser = User_Service_User::instance()->get($this->request()->get('profile_id'));
			$this->setParam('aUser', $aUser);
		}		
		
		$iPageSize = 12;
		$iPage = $this->request()->getInt('page');
		$aUser = $this->getParam('aUser');
		$bMutual = (($this->request()->get('req3') == 'mutual') ? true : false);
		
		if (!User_Service_Privacy_Privacy::instance()->hasAccess($aUser['user_id'], 'friend.view_friend'))
		{
			return Phpfox_Error::display('<div class="extra_info">' . _p('full_name_has_closed_gender_friends_section',[
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
		
		if ($bMutual === true)
		{
			$oFilter->setCondition('friend.is_page = 0 AND friend.user_id = ' . Phpfox::getUserId());
			$this->template()->setBreadCrumb(_p('mutual_friends'), null, true);
		}
		else 
		{
			$oFilter->setCondition('friend.is_page = 0 AND friend.user_id = ' . (int) $aUser['user_id']);
			if ($this->request()->get('view'))
			{
				$this->template()->setBreadCrumb(_p('friends_online'), null, true);
			}
		}
		
		if (($iListId = $this->request()->getInt('list')) && ($aList = Friend_Service_List_List::instance()->getList($iListId, Phpfox::getUserId())) && isset($aList['list_id']))
		{
			$this->search()->setCondition('AND fld.list_id = ' . (int) $aList['list_id'] . ' AND friend.user_id = ' . $aUser['user_id']);
			$this->template()->setTitle($aList['name'])->setBreadCrumb($aList['name'], $this->url()->makeUrl($aUser['user_name'].'.friend', array('list' => $iListId)), true);
		}		
		
		list($iCnt, $aFriends) = Friend_Service_Friend::instance()->get($oFilter->getConditions(), $oFilter->getSort(), $oFilter->getPage(), $iPageSize, true, true, ($this->request()->get('view') ? true : false), ($bMutual === true ? $aUser['user_id'] : null));

		$iCnt = $oFilter->getSearchTotal($iCnt);
		
		Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));
		
		$this->setParam('aTotalFriends', $iCnt);		
		
		$this->template()->setMeta('keywords', _p('full_name_s_friends', array('full_name' => $aUser['full_name'])));
		$this->template()->setMeta('keywords', Phpfox::getParam('friend.friend_meta_keywords'));
		$this->template()->setMeta('description', _p('full_name_is_on_site_title_and_has_total_friends', array('full_name' => $aUser['full_name'], 'site_title' => Phpfox::getParam('core.site_title'), 'total' => $iCnt)));
		
		if ($iCnt)
		{
			$sCustomFriends = '';
			foreach ($aFriends as $aFriend)
			{
				$sCustomFriends .= $aFriend['full_name'] . ', ';
			}		
			$sCustomFriends = rtrim($sCustomFriends, ', ');
			
			$this->template()->setMeta('description', _p('full_name_is_connected_with_friends', array('full_name' => $aUser['full_name'], 'friends' => $sCustomFriends)));
		}
		
		$this->template()->setMeta('description', _p('sign_up_on_site_title_and_connect_with_full_name_message_full_name_or_add_full_name_as_you', array('site_title' => Phpfox::getParam('core.site_title'), 'full_name' => $aUser['full_name'])));

		if (Phpfox::getUserId() == $aUser['user_id']) {
			$this->template()->menu(_p('manage_friends'), $this->url()->makeUrl('friend'));
		}
		$this->template()->setTitle(_p('full_name_s_friends', array('full_name' => $aUser['full_name'])))
			->setBreadCrumb(_p('friends'), $this->url()->makeUrl($aUser['user_name'].'.friend'), false)
            ->setHeader('cache', array(
                'friend.js' => 'module_friend',
            ))
			->assign(array(
					'aFriends' => $aFriends,
					'sFriendView' => $this->request()->get('view'),
					'activeList' => $this->request()->get('list')
				)
			);
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		$this->template()->clean(array(
				'aFriends'
			)
		);
	
		(($sPlugin = Phpfox_Plugin::get('friend.component_controller_profile_clean')) ? eval($sPlugin) : false);
	}
}