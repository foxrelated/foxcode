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
 * @version 		$Id: view.class.php 7019 2014-01-06 17:06:31Z Fern $
 */
class Blog_Component_Controller_View extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{
		if ($this->request()->getInt('id'))
		{
			return Phpfox_Module::instance()->setController('error.404');
		}

		if (Phpfox::isUser() && Phpfox::isModule('notification'))
		{
			Notification_Service_Process::instance()->delete('comment_blog', $this->request()->getInt('req2'), Phpfox::getUserId());
			Notification_Service_Process::instance()->delete('blog_like', $this->request()->getInt('req2'), Phpfox::getUserId());
		}
		
		Phpfox::getUserParam('blog.view_blogs', true);

		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_view_process_start')) ? eval($sPlugin) : false);

		$bIsProfile = $this->getParam('bIsProfile');		
		if ($bIsProfile === true)
		{
			$this->setParam(array(
					'bViewProfileBlog' => true,
					'sTagType' => 'blog'
				)
			);
		}
	
		$aItem = Blog_Service_Blog::instance()->getBlog($this->request()->getInt('req2'));

        if (empty($aItem['blog_id']))
        {
            return Phpfox_Error::display(_p('blog_not_found'));
        }

        if (isset($aItem['module_id']) && !empty($aItem['item_id']) && !Phpfox::isModule($aItem['module_id']))
        {
            return Phpfox_Error::display(_p('Cannot find the parent item.'));
        }


		if (Phpfox::isUser() && User_Service_Block_Block::instance()->isBlocked(null, $aItem['user_id']))
        {
            return Phpfox_Module::instance()->setController('error.invalid');
        }
		
		if (Phpfox::getUserId() == $aItem['user_id'] && Phpfox::isModule('notification'))
		{
			Notification_Service_Process::instance()->delete('blog_approved', $this->request()->getInt('req2'), Phpfox::getUserId());
		}
        
		if (Phpfox::isModule('privacy'))
		{
			Privacy_Service_Privacy::instance()->check('blog', $aItem['blog_id'], $aItem['user_id'], $aItem['privacy'], $aItem['is_friend']);
		}

		if(isset($aItem['module_id']) && Phpfox::isModule($aItem['module_id']) && Phpfox::hasCallback($aItem['module_id'], 'checkPermission'))
		{
			if(!Phpfox::callback($aItem['module_id'] . '.checkPermission', $aItem['item_id'], 'blog.view_browse_blogs'))
			{
				return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
			}
		}

		if (!Phpfox::getUserParam('blog.can_approve_blogs'))
		{
			if ($aItem['is_approved'] != '1' && $aItem['user_id'] != Phpfox::getUserId())
			{
				return Phpfox_Error::display(_p('blog_not_found'), 404);
			}
		}
		
		if ($aItem['post_status'] == 2 && Phpfox::getUserId() != $aItem['user_id'] && !Phpfox::getUserParam('blog.edit_user_blog'))
		{
			return Phpfox_Error::display(_p('blog_not_found'));
		}		
		
		if (Phpfox::isModule('track') && Phpfox::isUser() && Phpfox::getUserId() != $aItem['user_id'] && !$aItem['is_viewed'])
		{
		    Track_Service_Process::instance()->add('blog', $aItem['blog_id']);
            Blog_Service_Process::instance()->updateView($aItem['blog_id']);
		}
		
		if (Phpfox::isUser() && Phpfox::isModule('track') && Phpfox::getUserId() != $aItem['user_id'] && $aItem['is_viewed'] && !Phpfox::getUserBy('is_invisible')) {
		    if (Phpfox::getParam('track.unique_viewers_counter')){
                Track_Service_Process::instance()->update('blog', $aItem['blog_id']);
            } else {
                Track_Service_Process::instance()->add('blog', $aItem['blog_id']);
            }
		}
		
		// Define params for "review views" block
		$this->setParam(array(
				'sTrackType' => 'blog',
				'iTrackId' => $aItem['blog_id'],
				'iTrackUserId' => $aItem['user_id']
			)
		);
		
		$aCategories = Blog_Service_Category_Category::instance()->getCategoriesById($aItem['blog_id']);
		
		if (Phpfox::isModule('tag'))
		{
			$aTags = Tag_Service_Tag::instance()->getTagsById('blog', $aItem['blog_id']);
			if (isset($aTags[$aItem['blog_id']]))
			{
				$aItem['tag_list'] = $aTags[$aItem['blog_id']];
			}
		}

		$sCategories = '';
		if (isset($aCategories[$aItem['blog_id']]))
		{
			$sCategories = '';
			foreach ($aCategories[$aItem['blog_id']] as $iKey => $aCategory)
			{
				$sCategories .= ($iKey != 0 ? ',' : '') . ' <a href="' . ($aCategory['user_id'] ? $this->url()->permalink($aItem['user_name'] . '.blog.category', $aCategory['category_id'], Phpfox::getSoftPhrase($aCategory['category_name'])) : $this->url()->permalink('blog.category', $aCategory['category_id'], Phpfox::getSoftPhrase($aCategory['category_name']))) . '">' . Phpfox::getSoftPhrase($aCategory['category_name']) . '</a>';
				
				$this->template()->setMeta('keywords', Phpfox::getSoftPhrase($aCategory['category_name']));
			}
		}

		if (isset($sCategories))
		{
			$aItem['info'] = _p('posted_x_by_x_in_x', array('date' => Phpfox::getTime(Phpfox::getParam('blog.blog_time_stamp'), $aItem['time_stamp']), 'link' => Phpfox_Url::instance()->makeUrl('profile', array($aItem['user_name'])), 'user' => $aItem, 'categories' => $sCategories));
		}
		else 
		{
			$aItem['info'] = _p('posted_x_by_x', array('date' => Phpfox::getTime(Phpfox::getParam('blog.blog_time_stamp'), $aItem['time_stamp']), 'link' => Phpfox_Url::instance()->makeUrl('profile', array($aItem['user_name'])), 'user' => $aItem));
		}		
		
		$aItem['bookmark_url'] = Phpfox::permalink('blog', $aItem['blog_id'], $aItem['title']);

		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_view_process_middle')) ? eval($sPlugin) : false);
		
		// Add tags to meta keywords
		if (!empty($aItem['tag_list']) && $aItem['tag_list'] && Phpfox::isModule('tag'))
		{
			$this->template()->setMeta('keywords', Tag_Service_Tag::instance()->getKeywords($aItem['tag_list']));
		}	
		
		if (isset($aItem['module_id']) && Phpfox::hasCallback($aItem['module_id'], 'getBlogDetails'))
		{
		    if ($aCallback = Phpfox::callback($aItem['module_id'] . '.getBlogDetails', $aItem))
			{
				$this->template()->setBreadCrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
				$this->template()->setBreadCrumb($aCallback['title'], $aCallback['url_home']);
			}
		}
		$this->setParam('aFeed', array(				
				'comment_type_id' => 'blog',
				'privacy' => $aItem['privacy'],
				'comment_privacy' => $aItem['privacy_comment'],
				'like_type_id' => 'blog',
				'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
				'feed_is_friend' => $aItem['is_friend'],
				'item_id' => $aItem['blog_id'],
				'user_id' => $aItem['user_id'],
				'total_comment' => $aItem['total_comment'],
				'total_like' => $aItem['total_like'],
				'feed_link' => $aItem['bookmark_url'],
				'feed_title' => $aItem['title'],
				'feed_display' => 'view',
				'feed_total_like' => $aItem['total_like'],
				'report_module' => 'blog',
				'report_phrase' => _p('report_this_blog'),
				'time_stamp' => $aItem['time_stamp']
			)
		);		
		$sBreadcrumb = $this->url()->makeUrl('blog');
		if (isset($aCallback) && isset($aCallback['item_id']))
		{
		    $sBreadcrumb = $this->url()->makeUrl('pages.' . $aCallback['item_id'] .'.blog');
		}
		
		if (isset($aCallback) && isset($aCallback['module_id']) && $aCallback['module_id'] == 'pages')
		{
			$this->setParam('sTagListParentModule', $aItem['module_id']);
			$this->setParam('iTagListParentId', (int) $aItem['item_id']);
		}
		$this->template()->setTitle($aItem['title'])
		 	->setBreadCrumb(_p('blogs_title'), $sBreadcrumb)
		 	->setBreadCrumb($aItem['title'], $this->url()->permalink('blog', $aItem['blog_id'], $aItem['title']), true)
			->setMeta('description', $aItem['title'] . '.')
			->setMeta('description', $aItem['text'] . '.')
			->setMeta('description', $aItem['info'] . '.')
			->setMeta('keywords', $this->template()->getKeywords($aItem['title']))	
			->assign(array(
					'aItem' => $aItem,
					'bBlogView' => true,
					'bIsProfile' => $bIsProfile,
					'sTagType' => ($bIsProfile === true ? 'blog_profile' : 'blog'),
					'sMicroPropType' => 'BlogPosting',
					'sCategories' => $sCategories
				)
			)->setHeader('cache', array(
				'jquery/plugin/jquery.highlightFade.js' => 'static_script',
				'jquery/plugin/jquery.scrollTo.js' => 'static_script',
			)
		);
		
		if ($this->request()->get('req4') == 'comment')
		{
			$this->template()->setHeader('<script type="text/javascript">var $bScrollToBlogComment = false; $Behavior.scrollToBlogComment = function () { if ($bScrollToBlogComment) { return; } $bScrollToBlogComment = true; if ($(\'#js_feed_comment_pager_' . $aItem['blog_id'] . '\').length > 0) { $.scrollTo(\'#js_feed_comment_pager_' . $aItem['blog_id'] . '\', 800); } }</script>');
		}
		
		if ($this->request()->get('req4') == 'add-comment')
		{
			$this->template()->setHeader('<script type="text/javascript">var $bScrollToBlogComment = false; $Behavior.scrollToBlogComment = function () { if ($bScrollToBlogComment) { return; } $bScrollToBlogComment = true; if ($(\'#js_feed_comment_form_' . $aItem['blog_id'] . '\').length > 0) { $.scrollTo(\'#js_feed_comment_form_' . $aItem['blog_id'] . '\', 800); $Core.commentFeedTextareaClick($(\'.js_comment_feed_textarea\')); } }</script>');
		}

		$aFilterMenu = array();
		if (!defined('PHPFOX_IS_USER_PROFILE') && !isset($aParentModule['module_id']))
		{
            $aFilterMenu = [
                _p('all_blogs') => '',
                _p('my_blogs')  => 'my'
            ];
            
            if (Phpfox::isUser() && ($iDraftTotal = Blog_Service_Blog::instance()->getTotalDrafts())) {
				$sDraftTotal = ($iDraftTotal >= 100) ? '99+' : $iDraftTotal;
				$aFilterMenu[_p('My Draft Blogs') . '<span class="pending">' . $sDraftTotal . '</span>'] = 'draft';
			}
			
			if (!Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend'))
			{
				$aFilterMenu[_p('friends_blogs')] = 'friend';
			}

			if (Phpfox::getUserParam('blog.can_approve_blogs'))
			{
				$iPendingTotal = Blog_Service_Blog::instance()->getPendingTotal();

				if ($iPendingTotal)
				{
					$aFilterMenu[_p('pending_blogs') . (Phpfox::getUserParam('blog.can_approve_blogs') ? '<span class="pending">' . $iPendingTotal . '</span>' : 0)] = 'pending';
				}
			}
		}

		$this->template()->buildSectionMenu('blog', $aFilterMenu);

		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_view_process_end')) ? eval($sPlugin) : false);
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_view_clean')) ? eval($sPlugin) : false);
	}
}