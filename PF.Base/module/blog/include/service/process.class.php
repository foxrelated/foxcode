<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Blog
 * @version 		$Id: process.class.php 6876 2013-11-12 10:48:57Z Miguel_Espinoza $
 */
class Blog_Service_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('blog');
	}

    /**
     * Add new blog item
     *
     * @param array $aVals
     *
     * @return int
     */
	public function add($aVals)
	{
		(($sPlugin = Phpfox_Plugin::get('blog.service_process__start')) ? eval($sPlugin) : false);
        
		$oFilter = Phpfox::getLib('parse.input');		
		
		if (!empty($aVals['module_id']) && !empty($aVals['item_id']))
		{
		    if (Phpfox::isModule($aVals['module_id']))
            {
                $aVals['privacy'] = 0;
                $aVals['privacy_comment'] = 0;
            }
            else
            {
                Phpfox_Error::set(_p('Cannot find the parent item.'));
                return false;
            }
		}

		// check if the user entered a forbidden word
        Ban_Service_Ban::instance()->checkAutomaticBan($aVals['text'] . ' ' . $aVals['title']);

		if (!Phpfox::getParam('blog.allow_links_in_blog_title'))
		{
			if (!Phpfox_Validator::instance()->check($aVals['title'], array('url')))
			{
				return Phpfox_Error::set(_p('we_do_not_allow_links_in_titles'));
			}
		}		
		if (!isset($aVals['privacy']))
		{
			$aVals['privacy'] = 0;
		}
		if (!isset($aVals['privacy_comment']))
		{
			$aVals['privacy_comment'] = 0;
		}

		$sTitle = $oFilter->clean($aVals['title'], 255);
		
		$bHasAttachments = (!empty($aVals['attachment']) && Phpfox::getUserParam('attachment.can_attach_on_blog'));
        if (!isset($aVals['post_status'])) {
            $aVals['post_status'] = 1;
        }
        $iPostStatus = (int) $aVals['post_status'];
        $aInsert = [
            'user_id'          => Phpfox::getUserId(),
            'title'            => $sTitle,
            'time_stamp'       => PHPFOX_TIME,
            'is_approved'      => 1,
            'privacy'          => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment'  => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'post_status'      => $iPostStatus,
            'total_attachment' => 0
        ];
        
        if (isset($aVals['item_id']) && isset($aVals['module_id'])) {
            $aInsert['item_id'] = (int)$aVals['item_id'];
            $aInsert['module_id'] = $oFilter->clean($aVals['module_id']);
        }
		
		$bIsSpam = false;
		if (Phpfox::getParam('blog.spam_check_blogs')) {
            if (Phpfox::getLib('spam')->check([
                    'action' => 'isSpam',
                    'params' => [
                        'module'  => 'blog',
                        'content' => $oFilter->prepare($aVals['text'])
                    ]
                ])
            ) {
                $aInsert['is_approved'] = '9';
                $bIsSpam = true;
			}
		}
        
        if (Phpfox::getUserParam('blog.approve_blogs') && $iPostStatus != 2) {
            $aInsert['is_approved'] = '0';
            $bIsSpam = true;
            //Remove total pending blog
            $this->cache()->remove('blog_pending_total', 'substr');
        }
		
		(($sPlugin = Phpfox_Plugin::get('blog.service_process_add_start')) ? eval($sPlugin) : false);

		$iId = $this->database()->insert(Phpfox::getT('blog'), $aInsert);		
		
		(($sPlugin = Phpfox_Plugin::get('blog.service_process_add_end')) ? eval($sPlugin) : false);
		
		$this->database()->insert(Phpfox::getT('blog_text'), array(
				'blog_id' => $iId,
				'text' => $oFilter->clean($aVals['text']),
				'text_parsed' => $oFilter->prepare($aVals['text'])
			)
		);
        
        if (!empty($aVals['selected_categories'])) {
            Blog_Service_Category_Process::instance()
                ->addCategoryForBlog($iId, explode(',', rtrim($aVals['selected_categories'], ',')), ($aVals['post_status'] == 1 ? true : false));
        }
        
        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && Phpfox::getUserParam('tag.can_add_tags_on_blogs')) {
            Tag_Service_Process::instance()->add('blog', $iId, Phpfox::getUserId(), $aVals['text'], true);
        } else {
            if (Phpfox::isModule('tag') && Phpfox::getUserParam('tag.can_add_tags_on_blogs') && isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list'])))) {
                Tag_Service_Process::instance()->add('blog', $iId, Phpfox::getUserId(), $aVals['tag_list']);
            }
        }

		// If we uploaded any attachments make sure we update the 'item_id'
        if ($bHasAttachments) {
            Attachment_Service_Process::instance()->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
        }
        
        if ($bIsSpam === true) {
            return $iId;
        }

		if ($aVals['post_status'] == 1)
		{
			if (isset($aVals['module_id']) && ($aVals['module_id'] != 'blog') && Phpfox::isModule($aVals['module_id']) && Phpfox::hasCallback($aVals['module_id'], 'getFeedDetails'))
			{
				(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->callback(Phpfox::callback($aVals['module_id'] . '.getFeedDetails', $aVals['item_id']))->add('blog', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0), $aVals['item_id']) : null);
			}
			else
			{
				(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->add('blog', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0)) : null);
			}

			//support add notification for parent module
			if (Phpfox::isModule('notification') && isset($aVals['module_id']) && Phpfox::isModule($aVals['module_id']) && Phpfox::hasCallback($aVals['module_id'], 'addItemNotification'))
			{
				Phpfox::callback($aVals['module_id'] . '.addItemNotification', ['page_id' => $aVals['item_id'], 'item_perm' => 'blog.view_browse_blogs', 'item_type' => 'blog', 'item_id' => $iId, 'onwer_id' => Phpfox::getUserId()]);
			}

			// Update user activity
			User_Service_Activity::instance()->update(Phpfox::getUserId(), 'blog', '+');
		}

		if ($aVals['privacy'] == '4')
		{
			Privacy_Service_Process::instance()->add('blog', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
		}

		(($sPlugin = Phpfox_Plugin::get('blog.service_process__end')) ? eval($sPlugin) : false);
        
        Blog_Service_Cache_Remove::instance()->my();
		return $iId;
	}
    
    /**
     * Update an exist blog
     *
     * @param int        $iId
     * @param int        $iUserId
     * @param array      $aVals
     * @param null|array $aRow
     *
     * @return int
     */
	public function update($iId, $iUserId, $aVals, &$aRow = null)
	{
		(($sPlugin = Phpfox_Plugin::get('blog.service_process_update__start')) ? eval($sPlugin) : false);
        
        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }
        
        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

		$oFilter = Phpfox::getLib('parse.input');

		$bHasAttachments = (!empty($aVals['attachment']) && Phpfox::getUserParam('attachment.can_attach_on_blog') && $iUserId == Phpfox::getUserId());
        Ban_Service_Ban::instance()->checkAutomaticBan($aVals['title'] . ' ' . $aVals['text']);
        if ($bHasAttachments) {
            Attachment_Service_Process::instance()->updateItemId($aVals['attachment'], $iUserId, $iId);
        }
        $aOldBlogData = Blog_Service_Blog::instance()->getBlogForEdit($iId);
        
		$iPostStatus = (isset($aVals['post_status']) ? $aVals['post_status'] : '1');
        
        //Publish a draft blog, but this user group's blog have to approve first.
        if ($aOldBlogData['post_status'] == 2 && $iPostStatus != 2 && Phpfox::getUserParam('blog.approve_blogs')) {
            $this->cache()->remove('blog_pending_total', 'substr');
            $aOldBlogData['is_approved'] = 0;
        }
		$sTitle = $oFilter->clean($aVals['title'], 255);
		$aUpdate = array(
			'title' => $sTitle,
			'time_update' => PHPFOX_TIME,
			'is_approved' => $aOldBlogData['is_approved'],
			'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
			'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
			'post_status' => $iPostStatus,
			'total_attachment' => (Phpfox::isModule('attachment') ? Attachment_Service_Attachment::instance()->getCountForItem($iId, 'blog') : '0')
		);

		if ($aRow !== null && isset($aVals['post_status']) && $aRow['post_status'] == '2' && $aVals['post_status'] == '1')
		{
			$aUpdate['time_stamp'] = PHPFOX_TIME;
		}
        
        if (Phpfox::getParam('blog.spam_check_blogs')) {
            if (Phpfox::getLib('spam')->check([
                'action' => 'isSpam',
                'params' => [
                    'module'  => 'blog',
                    'content' => $oFilter->prepare($aVals['text'])
                ]
            ])
            ) {
                $aInsert['is_approved'] = '9';
            }
        }
        
        (($sPlugin = Phpfox_Plugin::get('blog.service_process_update')) ? eval($sPlugin) : false);

		$this->database()->update(Phpfox::getT('blog'), $aUpdate, 'blog_id = ' . (int) $iId);
		$this->database()->update(Phpfox::getT('blog_text'), array(
			'text' => $oFilter->clean($aVals['text']),
			'text_parsed' => $oFilter->prepare($aVals["text"])
		), 'blog_id = ' . (int) $iId);
        
        Blog_Service_Category_Process::instance()->updateCategoryForBlog($iId, explode(',', rtrim($aVals['selected_categories'], ',')), ($aVals['post_status'] == 1 ? true : false), ((isset($aVals['draft_publish']) && $aVals['draft_publish']) ? false : true));


		if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && Phpfox::getUserParam('tag.can_add_tags_on_blogs'))
		{
            Tag_Service_Process::instance()->update('blog', $iId, Phpfox::getUserId(), $aVals['text'], true);
		}
		else
		{
			if (Phpfox::isModule('tag') && Phpfox::getUserParam('tag.can_add_tags_on_blogs'))
			{
                Tag_Service_Process::instance()->update('blog', $iId, $iUserId, (!Phpfox::getLib('parse.format')->isEmpty($aVals['tag_list']) ? $aVals['tag_list'] : null));
			}
		}

		if ($aRow !== null && $aRow['is_approved'] == 1 && $aRow['post_status'] == '2' && $aVals['post_status'] == '1')
		{
			if (isset($aRow['module_id']) && ($aRow['module_id'] != 'blog') && Phpfox::isModule($aRow['module_id']) && Phpfox::hasCallback($aRow['module_id'], 'getFeedDetails'))
			{
				(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->callback(Phpfox::callback($aRow['module_id'] . '.getFeedDetails', $aRow['item_id']))->add('blog', $iId, $aVals['privacy'], $aVals['privacy_comment'], $aRow['item_id'], $iUserId) : null);
			}
			else
			{
				(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->add('blog', $iId, $aVals['privacy'], $aVals['privacy_comment'], 0, $iUserId) : null);
			}

			//support add notification for parent module
			if (Phpfox::isModule('notification') && isset($aRow['module_id']) && Phpfox::isModule($aRow['module_id']) && Phpfox::hasCallback($aRow['module_id'], 'addItemNotification'))
			{
				Phpfox::callback($aRow['module_id'] . '.addItemNotification', ['page_id' => $aRow['item_id'], 'item_perm' => 'blog.view_browse_blogs', 'item_type' => 'blog', 'item_id' => $iId, 'owner_id' => $iUserId]);
			}

			// Update user activity
			User_Service_Activity::instance()->update($iUserId, 'blog');
		}
		else
		{
			(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->update('blog', $iId, $aVals['privacy'], $aVals['privacy_comment'], 0, $iUserId) : null);
		}
        
        if (Phpfox::isModule('privacy')) {
            if ($aVals['privacy'] == '4') {
                Privacy_Service_Process::instance()->update('blog', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : []));
            } else {
                Privacy_Service_Process::instance()->delete('blog', $iId);
            }
        }
        
        (($sPlugin = Phpfox_Plugin::get('blog.service_process_update__end')) ? eval($sPlugin) : false);
        $this->cache()->remove("blog_detail_view_" . (int) $iId);
        $this->cache()->remove("blog_detail_edit_" . (int) $iId);
        Blog_Service_Cache_Remove::instance()->my();
		return $iId;
	}
    
    /**
     * Update title of an exist blog
     * @param int $iId
     * @param string $sTitle
     *
     * @return bool
     */
	public function updateBlogTitle($iId, $sTitle)
	{
		(($sPlugin = Phpfox_Plugin::get('blog.service_process_updateblogtitle__start')) ? eval($sPlugin) : false);
		if (Blog_Service_Blog::instance()->hasAccess($iId, 'edit_own_blog', 'edit_user_blog'))
		{
            Ban_Service_Ban::instance()->checkAutomaticBan($sTitle);
			if (!Phpfox::getParam('blog.allow_links_in_blog_title'))
			{
				if (!Phpfox_Validator::instance()->check($sTitle, array('url')))
				{
					return Phpfox_Error::set(_p('we_do_not_allow_links_in_titles'));
				}
			}

			$oFilter = Phpfox::getLib('parse.input');
			(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->update('blog', $iId, $oFilter->clean($sTitle, 255)) : null);

			$this->database()->update(Phpfox::getT('blog'), array(
				'title' => Phpfox::getLib('parse.input')->clean($sTitle, 255),
				"time_update" => PHPFOX_TIME
			), "blog_id = " . (int) $iId);

			return true;
		}
		(($sPlugin = Phpfox_Plugin::get('blog.service_process_updateblogtitle__end')) ? eval($sPlugin) : false);
		return false;
	}
    
    /**
     * @param int $iId
     * @param string $sTitle
     *
     * @return bool
     */
	public function updatePermaLink($iId, $sTitle)
	{
		if (Blog_Service_Blog::instance()->hasAccess($iId, 'edit_own_blog', 'edit_user_blog'))
		{
			$this->database()->update(Phpfox::getT('blog'), array(
				"title_url" => Blog_Service_Blog::instance()->prepareTitle($sTitle),
			), "blog_id = " . (int)$iId);

			return true;
		}

		return false;
	}
    
    /**
     * @param int $iId
     * @param string $sText
     *
     * @return bool
     */
	public function updateBlogText($iId, $sText)
	{
        Ban_Service_Ban::instance()->checkAutomaticBan($sText);
		(($sPlugin = Phpfox_Plugin::get('blog.service_process_updateblogtext__start')) ? eval($sPlugin) : false);
		if (Blog_Service_Blog::instance()->hasAccess($iId, 'edit_own_blog', 'edit_user_blog'))
		{
			$oFilter = Phpfox::getLib('parse.input');

			if (Phpfox::getParam('blog.spam_check_blogs'))
			{
				if (Phpfox::getLib('spam')->check(array(
							'action' => 'isSpam',
							'params' => array(
								'module' => 'blog',
								'content' => Phpfox::getLib('parse.input')->prepare($sText)
							)
						)
					)
				)
				{
					$this->database()->update(Phpfox::getT('blog'), array('is_approved' => '9'), "blog_id = " . (int) $iId);

					Phpfox_Error::set(_p('your_blog_has_been_marked_as_spam'));
				}
			}

			$this->database()->update(Phpfox::getT('blog'), array(
				'time_update' => PHPFOX_TIME
			), "blog_id = " . (int) $iId);

			$this->database()->update(Phpfox::getT('blog_text'), array(
				'text' => $oFilter->clean($sText), "text_parsed" => $oFilter->prepare($sText)
			), "blog_id = " . (int) $iId);

			if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && Phpfox::getUserParam('tag.can_add_tags_on_blogs'))
			{
                Tag_Service_Process::instance()->update('blog', $iId, Phpfox::getUserId(), $sText, true);
			}
			
			return true;
		}
		(($sPlugin = Phpfox_Plugin::get('blog.service_process_updateblogtext__end')) ? eval($sPlugin) : false);
		return false;
	}
    
    /**
     * @param int $iId
     *
     * @return bool|array
     */
	public function deleteInline($iId)
	{
		(($sPlugin = Phpfox_Plugin::get('blog.service_process_deleteinline__start')) ? eval($sPlugin) : false);
		if (($iUserId = Blog_Service_Blog::instance()->hasAccess($iId, 'delete_own_blog', 'delete_user_blog')))
		{
			$aBlog = $this->database()->select('*')
				->from(Phpfox::getT('blog'))
				->where('blog_id = ' . (int) $iId)
				->execute('getSlaveRow');

			$this->delete($iId);
			
			(Phpfox::isModule('attachment') ? Attachment_Service_Process::instance()->deleteForItem($iUserId, $iId, 'blog') : null);
			(Phpfox::isModule('comment') ? Comment_Service_Process::instance()->deleteForItem($iUserId, $iId, 'blog') : null);
			(Phpfox::isModule('tag') ? Tag_Service_Process::instance()->deleteForItem($iUserId, $iId, 'blog') : null);
			
			// Update user activity
			User_Service_Activity::instance()->update($iUserId, 'blog', '-');
			
			if (Phpfox::isModule('tag'))
			{
                Tag_Service_Process::instance()->deleteForItem(Phpfox::getUserId(), $iId, 'blog');
			}

			return $aBlog;
		}
		(($sPlugin = Phpfox_Plugin::get('blog.service_process_deleteinline__end')) ? eval($sPlugin) : false);
		return false;
	}
    
    /**
     * @param int $iId
     */
	public function delete($iId)
	{
		(($sPlugin = Phpfox_Plugin::get('blog.service_process_delete__start')) ? eval($sPlugin) : false);
		$aBlog = Blog_Service_Blog::instance()->getBlogForEdit($iId);
		
		$this->database()->delete(Phpfox::getT('tag'), "category_id = 'blog' AND item_id = " . (int) $iId);
		
		$this->database()->delete(Phpfox::getT('blog'), "blog_id = " . (int) $iId);		
		$this->database()->delete(Phpfox::getT('blog_text'), "blog_id = " . (int) $iId);		
		$this->database()->delete(Phpfox::getT('track'), 'item_id = ' . (int)$iId . ' AND type_id="blog"');
		
		(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->delete('blog',(int) $iId) : null);
		(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->delete('comment_blog', $iId) : null);

		(Phpfox::isModule('like') ? Like_Service_Process::instance()->delete('blog',(int) $iId, 0, true) : null);
        (Phpfox::isModule('notification') ? Notification_Service_Process::instance()->deleteAllOfItem(['blog_like', 'comment_blog'],(int) $iId) : null);
		
		// Update user activity
		User_Service_Activity::instance()->update($aBlog['user_id'], 'blog', '-');
		
		$aRows = $this->database()->select('blog_id, category_id')
			->from(Phpfox::getT('blog_category_data'))
			->where('blog_id = ' . (int) $iId)
			->execute('getSlaveRows');
		
		if (count($aRows))
		{
			foreach ($aRows as $aRow)
			{
				$this->database()->delete(Phpfox::getT('blog_category_data'), "blog_id = " . (int) $aRow['blog_id'] . " AND category_id = " . (int) $aRow['category_id']);				
				$this->database()->updateCount('blog_category_data', 'category_id = ' . (int) $aRow['category_id'], 'used', 'blog_category', 'category_id = ' . (int) $aRow['category_id']);			
			}
		}	
		
		if (Phpfox::isModule('tag'))
		{
			$this->database()->delete(Phpfox::getT('tag'), 'item_id = ' . $aBlog['blog_id'] . ' AND category_id = "blog"', 1);		
			$this->cache()->remove('tag', 'substr');
		}
			
		(($sPlugin = Phpfox_Plugin::get('blog.service_process_delete')) ? eval($sPlugin) : false);

        Blog_Service_Cache_Remove::instance()->my();
        Blog_Service_Cache_Remove::instance()->blog($aBlog['blog_id']);
	}
    
    /**
     * @param int $iId
     *
     * @return bool
     */
	public function updateView($iId)
	{
        $this->database()->update($this->_sTable, ['total_view' => 'total_view + 1'], ['blog_id' => (int) $iId], false);
		
		return true;
	}
    
    /**
     * @param int $iId
     * @param bool $bMinus
     */
	public function updateCounter($iId, $bMinus = false)
	{
        $this->database()->update($this->_sTable, ['total_comment' => 'total_comment ' . ($bMinus ? "-" : "+") . ' 1'], ['blog_id' => (int) $iId], false);
	}
    
    /**
     * @param int $iId
     *
     * @return bool
     */
	public function approve($iId)
	{
		Phpfox::getUserParam('blog.can_approve_blogs', true);
		
		$aBlog = $this->database()->select('b.*, ' . Phpfox::getUserField())
			->from(Phpfox::getT('blog'), 'b')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
			->where('b.blog_id = ' . (int) $iId)
			->execute('getSlaveRow');
			
		if (!isset($aBlog['blog_id']))
		{
			return Phpfox_Error::set(_p('the_blog_you_are_trying_to_approve_is_not_valid'));
		}
		
		if ($aBlog['is_approved'] == '1')
		{
			return false;
		}
		
		$this->database()->update(Phpfox::getT('blog'), array('is_approved' => '1', 'time_stamp' => PHPFOX_TIME), 'blog_id = ' . $aBlog['blog_id']);

		if ($aBlog['post_status'] == 1)
		{
			if (isset($aBlog['module_id']) && ($aBlog['module_id'] != 'blog') && Phpfox::isModule($aBlog['module_id']) && Phpfox::hasCallback($aBlog['module_id'], 'getFeedDetails'))
			{
				(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->callback(Phpfox::callback($aBlog['module_id'] . '.getFeedDetails', $aBlog['item_id']))->add('blog', $iId, $aBlog['privacy'], $aBlog['privacy_comment'], $aBlog['item_id'], $aBlog['user_id']) : null);
			}
			else
			{
				(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->add('blog', $iId, $aBlog['privacy'], $aBlog['privacy_comment'], 0, $aBlog['user_id']) : null);
			}

			//support add notification for parent module
			if (Phpfox::isModule('notification') && isset($aBlog['module_id']) && Phpfox::isModule($aBlog['module_id']) && Phpfox::hasCallback($aBlog['module_id'], 'addItemNotification'))
			{
				Phpfox::callback($aBlog['module_id'] . '.addItemNotification', ['page_id' => $aBlog['item_id'], 'item_perm' => 'blog.view_browse_blogs', 'item_type' => 'blog', 'item_id' => $iId, 'owner_id' => $aBlog['user_id']]);
			}
		}		
		
		if (Phpfox::isModule('notification'))
		{
			Notification_Service_Process::instance()->add('blog_approved', $aBlog['blog_id'], $aBlog['user_id']);
		}
		
		if ($aBlog['is_approved'] == '9')
		{
			$this->database()->updateCounter('user', 'total_spam', 'user_id', $aBlog['user_id'], true);
		}
		
		User_Service_Activity::instance()->update($aBlog['user_id'], 'blog');
		
		(($sPlugin = Phpfox_Plugin::get('blog.service_process_approve__1')) ? eval($sPlugin) : false);
		
		// Send the user an email
		$sLink = Phpfox_Url::instance()->permalink('blog', $aBlog['blog_id'], $aBlog['title']);
		Phpfox::getLib('mail')->to($aBlog['user_id'])
			->subject(array('blog.your_blog_has_been_approved_on_site_title', array('site_title' => Phpfox::getParam('core.site_title'))))
			->message(array('blog.your_blog_has_been_approved_on_site_title_message', array('site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)))
			->notification('blog.blog_is_approved')
			->send();			
		//clear cache
        $this->cache()->remove('blog', 'substr');
		return true;
	}
    
    /**
     * @param string $sMethod
     * @param array $aArguments
     *
     * @return null
     */
	public function __call($sMethod, $aArguments)
	{
		if ($sPlugin = Phpfox_Plugin::get('blog.service_process__call'))
		{
			eval($sPlugin);
            return null;
		}
		
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}