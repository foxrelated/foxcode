<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');


class Blog_Service_Api extends \Core\Api\ApiServiceBase
{
    public function __construct()
    {
        $this->setPublicFields([
            'blog_id',
            'user_id',
            'title',
            'time_stamp',
            'text',
            'module_id',
            'item_id',
            'total_comment',
            'total_attachment',
            'total_view',
            'total_like',
            'time_update',
            'is_approved',
            'privacy',
            'post_status',
            'categories',
            'tag_list'
        ]);
    }

    /**
     * @description: get info of a blog
     * @param array $params
     * @param array $messages
     *
     * @return array|bool
     */
    public function get($params, $messages = [])
    {
        if (!($aBlog = Blog_Service_Blog::instance()->canViewItem($params['id'], true)))
        {
            return $this->error(_p('You don\'t have permission to {{ action }} this {{ item }}.', ['action' => _p('view__l'), 'item' => _p('blog__l')]), true);
        }

        $aItem = $this->getItem($aBlog, 'public');
        return $this->success($aItem, $messages);
    }

    /**
     * @description: update an event
     * @param $params
     *
     * @return array|bool
     */
    public function put($params)
    {
        $this->isUser();
        $oBlog = Blog_Service_Blog::instance();

        //check blog is exists
        $aRow = $oBlog->getBlogForEdit($params['id']);

        if (empty($aRow) || empty($aRow['blog_id']))
        {
            return $this->error(_p('This {{ item }} cannot be found.', ['item' => _p('blog__l')]), true);
        }

        //check permission to edit the event
        if ($aRow['is_approved'] != '1' &&
            ($aRow['user_id'] != Phpfox::getUserId() && !Phpfox::getUserParam('blog.edit_user_blog')))
        {
            return $this->error(_p('You don\'t have permission to {{ action }} this {{ item }}.', ['action' => _p('edit__l'), 'item' => _p('blog__l')]));
        }

        $canEdit = (Phpfox::getUserId() == $aRow['user_id'] ? Phpfox::getUserParam('blog.edit_own_blog') : Phpfox::getUserParam('blog.edit_user_blog'));
        if (!$canEdit)
        {
            return $this->error(_p('You don\'t have permission to {{ action }} this {{ item }}.', ['action' => _p('edit__l'), 'item' => _p('blog__l')]));
        }

        //validate data
        $aValidation = [];
        $aVals = $this->request()->getArray('val');

        if (isset($aVals['title']))
        {
            $aValidation['title'] = ['def' => 'required',
                                     'title' => _p('Field "{{ field }}" is required.', ['field' => 'title'])];
        }

        if (isset($aVals['text']))
        {
            $aValidation['text'] = ['def' => 'required',
                                     'title' => _p('Field "{{ field }}" is required.', ['field' => 'title'])];
        }

        $oValid = Phpfox_Validator::instance()->set(array(
                'sFormName' => 'core_js_blog_form',
                'aParams' => $aValidation
            )
        );


        if (isset($aVals['publish']))
        {
            $aVals['post_status'] = 1;
            $aVals['draft_publish'] = $aVals['publish'];
        }

        if ($oValid->isValid($aVals))
        {
            $aVals = array_merge($aRow, $aVals);
            if (isset($aVals['categories']))
            {
                $aVals['selected_categories'] = $aVals['categories'];
            }
            else
            {
                $aCategories = Blog_Service_Category_Category::instance()->getCategoriesById($aRow['blog_id']);
                $sCategories = '';
                if (isset($aCategories[$aRow['blog_id']]))
                {
                    foreach ($aCategories[$aRow['blog_id']] as $aCategory)
                    {
                        $sCategories .= $aCategory['category_id'] . ',';
                    }
                }
                $aVals['selected_categories'] = $sCategories;
            }

            if (Phpfox::isModule('tag') && empty($aVals['tag_list']))
            {
                $aTags = Tag_Service_Tag::instance()->getTagsById('blog', $aVals['blog_id']);
                $aVals['tag_list'] = '';
                if (isset($aTags[$aVals['blog_id']]))
                {
                    $aVals['tag_list'] = '';
                    foreach ($aTags[$aVals['blog_id']] as $aTag)
                    {
                        $aVals['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
                    }
                    $aVals['tag_list'] = trim(trim($aVals['tag_list'], ','));
                }
            }
            if ($iId = Blog_Service_Process::instance()->update($aRow['blog_id'], $aRow['user_id'], $aVals, $aRow))
            {
                return $this->get(['id' => $aRow['blog_id']], [_p('{{ item }} successfully updated.', ['item' => _p('blog')])]);
            }
        }

        return $this->error(_p('Cannot {{ action }} this {{ item }}.', ['action' => _p('edit__l'), 'item' => _p('blog__l')]), true);
    }

    /**
     * @description: delete a blog
     * @param $params
     *
     * @return array|bool
     */
    public function delete($params)
    {
        $this->isUser();
        $oBlog = Blog_Service_Blog::instance();

        //check blog is exists
        $aRow = $oBlog->getBlogForEdit($params['id']);

        if (empty($aRow) || empty($aRow['blog_id']))
        {
            return $this->error(_p('This {{ item }} cannot be found.', ['item' => _p('blog__l')]), true);
        }

        if (Blog_Service_Process::instance()->deleteInline($params['id']) === false)
        {
            return $this->error(_p('Cannot {{ action }} this {{ item }}.', ['action' => _p('delete__l'), 'item' => _p('blog__l')]), true);
        }

        return $this->success([], [_p('{{ item }} successfully deleted.', ['item' => _p('blog')])]);
    }

    /**
     * @description: add new blog item
     * @return array|bool
     */
    public function post()
    {
        //check permission
        $this->isUser();
        if (!Phpfox::getUserParam('blog.add_new_blog'))
        {
            return $this->error(_p('You don\'t have permission to add new {{ item }}.', ['item' => _p('blog__l')]));
        }

        $aVals = $this->request()->getArray('val');
        if (!empty($aVals['module_id']) && !empty($aVals['item_id'])) {
            if (Phpfox::hasCallback($aVals['module_id'], 'getItem') && Phpfox::callback($aVals['module_id'] . '.getItem' , $aVals['item_id']) === false)
            {
                return $this->error(_p('Cannot find the parent item.'));
            }

            if (Phpfox::hasCallback($aVals['module_id'], 'checkPermission') && !Phpfox::callback($aVals['module_id'] . '.checkPermission' , $aVals['item_id'], 'blog.share_blogs'))
            {
                return $this->error(_p('You don\'t have permission to add new {{ item }} on this item.', ['item' => _p('blog__l')]));
            }
        }

        //validate data
        $aValidation = array(
            'title' => array(
                'def' => 'required',
                'title' => _p('Field "{{ field }}" is required.', ['field' => 'val[title]'])
            ),
            'text' => array(
                'def' => 'required',
                'title' => _p('Field "{{ field }}" is required.', ['field' => 'val[text]'])
            )
        );

        $oValid = Phpfox_Validator::instance()->set(array(
                'sFormName' => 'core_js_blog_form',
                'aParams' => $aValidation
            )
        );

        if ($oValid->isValid($aVals))
        {
            if (!empty($aVals['draft']))
            {
                $aVals['post_status'] = 2;
            }

            if (isset($aVals['categories']))
            {
                $aVals['selected_categories'] = $aVals['categories'];
            }
            if (($iFlood = Phpfox::getUserParam('blog.flood_control_blog')) !== 0)
            {
                $aFlood = [
                    'action' => 'last_post', // The SPAM action
                    'params' => [
                        'field'      => 'time_stamp', // The time stamp field
                        'table'      => Phpfox::getT('blog'), // Database table we plan to check
                        'condition'  => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                        'time_stamp' => $iFlood * 60 // Seconds);
                    ]
                ];

                // actually check if flooding
                if (Phpfox::getLib('spam')->check($aFlood)) {
                    return $this->error(_p('your_are_posting_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
                }
            }
            if (Phpfox_Error::isPassed() && $iId = Blog_Service_Process::instance()->add($aVals))
            {
                return $this->get(['id' => $iId], [_p('{{ item }} successfully added.', ['item' => _p('blog')])]);
            }
        }
        return $this->error();
    }

    /**
     * @description: get blogs
     * @return array|bool
     */
    public function gets()
    {
        if (!Phpfox::getUserParam('blog.view_blogs'))
        {
            return $this->error(_p('You don\'t have permission to browse {{ items }}.', ['items' => _p('blogs__l')]));
        }

        $userId = $this->request()->get('user_id', null);
        if ($userId)
        {
            $aUser = User_Service_User::instance()->get($userId);
            if (!$aUser)
            {
                return $this->error('The {{ item }} cannot be found.', ['item' => _p('user__l')]);
            }

            if (Phpfox::isUser() && User_Service_Block_Block::instance()->isBlocked(null, $userId))
            {
                return $this->error('Sorry, this content isn\'t available right now');
            }

            $this->search()->setCondition('AND blog.user_id = ' . $aUser['user_id']);
        }

        $this->initSearchParams();
        $sView = $this->request()->get('view');

        $this->search()->set(array(
                'type' => 'blog',
                'field' => 'blog.blog_id',
                'ignore_blocked' => true,
                'search_tool' => array(
                    'table_alias' => 'blog',
                    'search' => array(
                        'name' => 'search',
                        'field' => array('blog.title'),
                        'default_value' => _p('search_blogs_dot'),
                    ),
                    'sort' => array(
                        'latest' => array('blog.time_stamp', _p('latest')),
                        'most-viewed' => array('blog.total_view', _p('most_viewed')),
                        'most-liked' => array('blog.total_like', _p('most_liked')),
                        'most-talked' => array('blog.total_comment', _p('most_discussed'))
                    ),
                    'show' => [$this->getSearchParam('limit')]
                )
            )
        );

        $aBrowseParams = array(
            'module_id' => 'blog',
            'alias' => 'blog',
            'field' => 'blog_id',
            'table' => Phpfox::getT('blog'),
            'hide_view' => array('pending', 'my')
        );

        $bCanBrowse = false;
        switch ($sView)
        {
            case 'spam':
                Phpfox::isUser();
                if (Phpfox::isUser() && Phpfox::getUserParam('blog.can_approve_blogs'))
                {
                    $bCanBrowse = true;
                    $this->search()->setCondition('AND blog.is_approved = 9');
                }
                break;
            case 'pending':
                if (Phpfox::isUser() && Phpfox::getUserParam('blog.can_approve_blogs'))
                {
                    $bCanBrowse = true;
                    $this->search()->setCondition('AND blog.is_approved = 0');
                }
                break;
            case 'my':
                if (Phpfox::isUser())
                {
                    $bCanBrowse = true;
                    $this->search()->setCondition('AND blog.user_id = ' . Phpfox::getUserId() . ' AND blog.post_status = 1');
                }
                break;
            case 'draft':
                if (Phpfox::isUser())
                {
                    $bCanBrowse = true;
                    $this->search()->setCondition("AND blog.user_id = " . Phpfox::getUserId()  . " AND blog.post_status = 2");
                }
                break;
            default:
                $bCanBrowse = true;
                $sCondition = "AND blog.is_approved = 1 AND blog.post_status = 1 AND blog.privacy IN(%PRIVACY%)";
                $this->search()->setCondition($sCondition);

                break;
        }

        if (!$bCanBrowse)
        {
            return $this->error('You don\'t have permission to browse those {{ items }}.', ['items' => _p('blogs__l')]);
        }

        $moduleId = $this->request()->get('module_id', null);
        $itemId = $this->request()->get('item_id', null);

        $category = $this->request()->get('category', null);
        if ($category)
        {
            $this->search()->setCondition('AND blog_category.category_id = ' . $category);
        }

        if (Phpfox::isModule('tag') && !Phpfox::getParam('tag.enable_hashtag_support') && ($tag = $this->request()->get('tag', null)))
        {
            if (!defined('PHPFOX_GET_FORCE_REQ')) define('PHPFOX_GET_FORCE_REQ', true);
            if ($aTag = Tag_Service_Tag::instance()->getTagInfo('blog', $tag))
            {
                $this->search()->setCondition('AND tag.tag_text = \'' . Phpfox_Database::instance()->escape($aTag['tag_text']) . '\'');
            }
            else
            {
                $this->search()->setCondition('AND 0');
            }
        }

        if ($moduleId && $itemId)
        {
            if (Phpfox::hasCallback($moduleId, 'getItem') && Phpfox::callback($moduleId . '.getItem' , $itemId) === false)
            {
                return $this->error(_p('Cannot find the parent item.'));
            }

            if (Phpfox::hasCallback($moduleId, 'checkPermission') && !Phpfox::callback($moduleId . '.checkPermission' , $itemId, 'blog.view_browse_blogs'))
            {
                return $this->error(_p('You don\'t have permission to browse {{ items }} on this item.', ['items' => _p('blogs__l')]));
            }
            $this->search()->setCondition('AND blog.module_id = \''. $moduleId .'\' AND blog.item_id = ' . (int) $itemId);
        }
        else{
            $this->search()->setCondition('AND blog.module_id = \'blog\'');
        }
        $this->search()->browse()->params($aBrowseParams)->execute();

        $aItems = $this->search()->browse()->getRows();

        if (Phpfox_Error::isPassed())
        {
            $result = [];
            foreach ($aItems as $aItem)
            {
                $aCategories = Blog_Service_Category_Category::instance()->getCategoriesById($aItem['blog_id']);
                $aItem['categories'] = isset($aCategories[$aItem['blog_id']]) ? $aCategories[$aItem['blog_id']] : [];

                if (Phpfox::isModule('tag'))
                {
                    $aTags = Tag_Service_Tag::instance()->getTagsById('blog', $aItem['blog_id']);
                    $aItem['tag_list'] = '';
                    if (isset($aTags[$aItem['blog_id']]))
                    {
                        $aItem['tag_list'] = '';
                        foreach ($aTags[$aItem['blog_id']] as $aTag)
                        {
                            $aItem['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
                        }
                        $aItem['tag_list'] = trim(trim($aItem['tag_list'], ','));
                    }
                }
                $result[] = $this->getItem($aItem);
            }
            return $this->success($result);
        }
        return $this->error();
    }
}