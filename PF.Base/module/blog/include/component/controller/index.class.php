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
 * @version 		$Id: index.class.php 7290 2014-04-30 19:14:20Z Fern $
 */
class Blog_Component_Controller_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aParentModule = $this->getParam('aParentModule');

		if ($aParentModule === null && $this->request()->getInt('req2') > 0)
		{

			if (($this->request()->get('req1') == 'pages' && Phpfox::isModule('pages') == false) ||
				($aParentModule['module_id'] == 'pages' && Pages_Service_Pages::instance()->hasPerm($aParentModule['item_id'], 'blog.view_browse_blog') == false) )
			{
				return Phpfox_Error::display(_p('cannot_display_due_to_privacy'));
			}
			return Phpfox_Module::instance()->setController('blog.view');
		}

		if (defined('PHPFOX_IS_USER_PROFILE') && ($sLegacyTitle = $this->request()->get('req3')) && !empty($sLegacyTitle))
		{
            Core_Service_Core::instance()->getLegacyItem(array(
					'field' => array('blog_id', 'title'),
					'table' => 'blog',
					'redirect' => 'blog',
					'title' => $sLegacyTitle,
					'search' => 'title'
				)
			);
		}

		if ($this->request()->get('req2') == 'main')
		{
			return Phpfox_Module::instance()->setController('error.404');
		}

		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_index_process_start')) ? eval($sPlugin) : false);

		if (($iRedirectId = $this->request()->get('redirect')) && ($aRedirectBlog = Blog_Service_Blog::instance()->getBlogForEdit($iRedirectId)))
		{
			Phpfox::permalink('blog', $aRedirectBlog['blog_id'], $aRedirectBlog['title'], true);
		}

		Phpfox::getUserParam('blog.view_blogs', true);

		if (defined('PHPFOX_IS_AJAX_CONTROLLER'))
		{
			$bIsProfile = true;
			$aUser = User_Service_User::instance()->get($this->request()->get('profile_id'));
			$this->setParam('aUser', $aUser);
		}
		else
		{
			$bIsProfile = $this->getParam('bIsProfile');
            $aUser = $this->getParam('aUser');
			if ($bIsProfile === true)
			{
				$this->search()->setCondition('AND blog.user_id = ' . $aUser['user_id']);
			}
		}

		/**
		 * Check if we are going to view an actual blog instead of the blog index page.
		 * The 2nd URL param needs to be numeric.
		 */
		if (!Phpfox::isAdminPanel())
		{
			if ($this->request()->getInt('req2') > 0 && !isset($aParentModule['module_id']))
			{
				/**
				 * Since we are going to be viewing a blog lets reset the controller and get out of this one.
				 */
				return Phpfox_Module::instance()->setController('blog.view');
			}
		}

		/**
		 * This creates a global variable that can be used in other components. This is a good way to
		 * pass information to other components.
		 */
		$this->setParam('sTagType', 'blog');

		$this->template()->setTitle(($bIsProfile ? _p('full_name_s_blogs', array('full_name' => $aUser['full_name'])) : _p('blog_title')))->setBreadCrumb(($bIsProfile ? _p('blogs') : _p('blog_title')), ($bIsProfile ? $this->url()->makeUrl($aUser['user_name'], 'blog') : $this->url()->makeUrl('blog')));

		$sView = $this->request()->get('view');

		$this->search()->set(array(
				'type' => 'blog',
				'field' => 'blog.blog_id',
                'ignore_blocked' => true,
				'search_tool' => array(
					'table_alias' => 'blog',
					'search' => array(
                        'action' => ($aParentModule === null ? ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('blog', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('blog', array('view' => $this->request()->get('view')))) : $aParentModule['url'] . 'blog?view=' . $this->request()->get('view')),
						'default_value' => _p('search_blogs_dot'),
						'name' => 'search',
						'field' => array('blog.title')
					),
					'sort' => array(
						'latest' => array('blog.time_stamp', _p('latest')),
						'most-viewed' => array('blog.total_view', _p('most_viewed')),
						'most-liked' => array('blog.total_like', _p('most_liked')),
						'most-talked' => array('blog.total_comment', _p('most_discussed'))
					),
					'show' => array(10, 20, 30)
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

		$aFilterMenu = array();
		if (!defined('PHPFOX_IS_USER_PROFILE') && !isset($aParentModule['module_id']))
		{
			$aFilterMenu = array(
				_p('all_blogs') => '',
				_p('my_blogs') => 'my'
			);

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

		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_index_process_search')) ? eval($sPlugin) : false);

		$this->template()->buildSectionMenu('blog', $aFilterMenu);

		switch ($sView)
		{
			case 'spam':
				Phpfox::isUser(true);
				if (Phpfox::getUserParam('blog.can_approve_blogs'))
				{
					$this->search()->setCondition('AND blog.is_approved = 9');
				}
				break;
			case 'pending':
				Phpfox::isUser(true);
				if (Phpfox::getUserParam('blog.can_approve_blogs'))
				{
					$this->search()->setCondition('AND blog.is_approved = 0');
				}
				break;
			case 'my':
				Phpfox::isUser(true);
				$this->search()->setCondition('AND blog.user_id = ' . Phpfox::getUserId() . ' AND blog.post_status = 1');
				break;
			case 'draft':
				Phpfox::isUser(true);
				$this->search()->setCondition("AND blog.user_id = " . Phpfox::getUserId()  . " AND blog.post_status = 2");
				break;
			default:
				$aPage = $this->getParam('aPage');
				$sCondition = "AND blog.is_approved = 1 AND blog.post_status = 1" . (Phpfox::getUserParam('privacy.can_comment_on_all_items') ? "" : " AND blog.privacy IN(%PRIVACY%)");
				if (isset($aPage['privacy']) && $aPage['privacy'] == 1)
				{
					$sCondition = "AND blog.is_approved = 1 AND blog.privacy IN(%PRIVACY%, 1) AND blog.post_status = 1";
				}
				$this->search()->setCondition($sCondition);

				http_cache()->set();

				break;
		}

		if ($this->request()->get(($bIsProfile === true ? 'req3' : 'req2')) == 'category')
		{
			if ($aBlogCategory = Blog_Service_Category_Category::instance()->getCategory($this->request()->getInt(($bIsProfile === true ? 'req4' : 'req3'))))
			{
				$this->search()->setCondition('AND blog_category.category_id = ' . $this->request()->getInt(($bIsProfile === true ? 'req4' : 'req3')) . ' AND blog_category.user_id = ' . ($bIsProfile ? (int) $aUser['user_id'] : 0));

				$this->template()->setTitle(Phpfox::getSoftPhrase($aBlogCategory['name']));
				$this->template()->setBreadCrumb(Phpfox_Locale::instance()->convert($aBlogCategory['name']), $this->url()->makeUrl('current'), true);

				$this->search()->setFormUrl($this->url()->permalink(array('blog.category', 'view' => $this->request()->get('view')), $aBlogCategory['category_id'], $aBlogCategory['name']));
			}
		}
		elseif (Phpfox::isModule('tag') && !Phpfox::getParam('tag.enable_hashtag_support') && $this->request()->get((defined('PHPFOX_IS_PAGES_VIEW') ? 'req4' : ($bIsProfile === true ? 'req3' : 'req2'))) == 'tag')
		{
			if (!defined('PHPFOX_GET_FORCE_REQ')) define('PHPFOX_GET_FORCE_REQ', true);
			if (($aTag = Tag_Service_Tag::instance()->getTagInfo('blog', $this->request()->get((defined('PHPFOX_IS_PAGES_VIEW') ? 'req5' : ($bIsProfile === true ? 'req4' : 'req3'))))))
			{
				$this->template()->setBreadCrumb(_p('topic') . ': ' . $aTag['tag_text'] . '', $this->url()->makeUrl('current'), true);
				$this->search()->setCondition('AND tag.tag_text = \'' . urldecode(Phpfox_Database::instance()->escape($aTag['tag_text'])) . '\'');
			}
            else
            {
                $this->search()->setCondition('AND 0');
            }
		}

		if (isset($aParentModule) && isset($aParentModule['module_id']))
		{
			/* Only get items without a parent (not belonging to pages) */
			$this->search()->setCondition('AND blog.module_id = \''. $aParentModule['module_id'] .'\' AND blog.item_id = ' . (int) $aParentModule['item_id']);
		}
		else if ($aParentModule === null)
		{
			if (($sView == 'pending' && Phpfox::getUserParam('blog.can_approve_blogs')) || $sView == 'draft')
			{

			}
			else
			{
				$this->search()->setCondition('AND blog.module_id = \'blog\'');
			}
		}

		if (((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null, 'blog.view_browse_blogs'))
			|| (!defined('PHPFOX_IS_PAGES_VIEW') && $aParentModule['module_id'] == 'pages' && Pages_Service_Pages::instance()->hasPerm($aParentModule['item_id'], 'blog.view_browse_blogs')))
			)
		{
			$sService = defined('PHPFOX_PAGES_ITEM_TYPE') ? PHPFOX_PAGES_ITEM_TYPE : 'pages';
			if(Phpfox::getService($sService)->isAdmin($aParentModule['item_id']))
			{
				$this->request()->set('view', 'pages_admin');
			}
			elseif(Phpfox::getService($sService)->isMember($aParentModule['item_id']))
			{
				$this->request()->set('view', 'pages_member');
			}
		}
		
		$this->search()->setContinueSearch(true);
		$this->search()->browse()->params($aBrowseParams)->execute();

		$aItems = $this->search()->browse()->getRows();
		Phpfox_Pager::instance()->set(array('page' => $this->search()->getPage(), 'size' => $this->search()->getDisplay(), 'count' => $this->search()->browse()->getCount()));

		Blog_Service_Blog::instance()->getExtra($aItems, 'user_profile');

		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_index_process_middle')) ? eval($sPlugin) : false);

		$this->template()->setMeta('keywords', Phpfox::getParam('blog.blog_meta_keywords'));
		$this->template()->setMeta('description', Phpfox::getParam('blog.blog_meta_description'));
		if ($bIsProfile)
		{
			$this->template()->setMeta('description', '' . $aUser['full_name'] . ' has ' . $this->search()->browse()->getCount() . ' blogs.');
		}

		foreach ($aItems as $aItem)
		{
			$this->template()->setMeta('keywords', $this->template()->getKeywords($aItem['title']));
			if (!empty($aItem['tag_list']))
			{
				$this->template()->setMeta('keywords', Tag_Service_Tag::instance()->getKeywords($aItem['tag_list']));
			}
		}

		/**
		 * Here we assign the needed variables we plan on using in the template. This is used to pass
		 * on any information that needs to be used with the specific template for this component.
		 */
		$cnt = $this->search()->browse()->getCount();
		$this->template()->assign(array(
					'iCnt' => $cnt,
					'aBlogs' => $aItems,
					'sSearchBlock' => _p('search_blogs_'),
					'bIsProfile' => $bIsProfile,
					'sTagType' => ($bIsProfile === true ? 'blog_profile' : 'blog'),
					'sBlogStatus' => $this->request()->get('status'),
					'sView' => $sView
				)
			)
			->setHeader('cache', array(
				'quick_submit.js' => 'module_blog',
				'jquery/plugin/jquery.highlightFade.js' => 'static_script',
			)
		);

		$aModerationMenu = array (
			array(
				'phrase' => _p('delete'),
				'action' => 'delete'
			)
		);
		if ($sView == 'pending') {
			$aModerationMenu[] = array(
				'phrase' => _p('approve'),
				'action' => 'approve'
			);
		}
		$this->setParam('global_moderation', array(
				'name' => 'blog',
				'ajax' => 'blog.moderation',
				'menu' => $aModerationMenu
			)
		);

		//Special breadcrumb for pages
		if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')){
			if (Phpfox::hasCallback(PHPFOX_PAGES_ITEM_TYPE, 'checkPermission') && !Phpfox::callback(PHPFOX_PAGES_ITEM_TYPE . '.checkPermission', $aParentModule['item_id'], 'blog.view_browse_blogs')) {
				$this->template()->assign(['aSearchTool' => []]);
				return Phpfox_Error::display(_p('cannot_display_due_to_privacy'));
			}
			$this->template()
				->clearBreadCrumb();
			$this->template()
				->setBreadCrumb(Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']), $aParentModule['url'])
				->setBreadCrumb(_p('blogs'), $aParentModule['url'] . 'blog/');
		}

		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_index_process_end')) ? eval($sPlugin) : false);
        return null;
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		$this->template()->clean(array(
				'iCnt',
				'aItems',
				'sSearchBlock'
			)
		);

		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}