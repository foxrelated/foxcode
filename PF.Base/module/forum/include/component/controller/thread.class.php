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
 * @package  		Module_Forum
 * @version 		$Id: controller.class.php 103 2009-01-27 11:32:36Z Raymond_Benc $
 */
class Forum_Component_Controller_Thread extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::getUserParam('forum.can_view_forum', true);

		$iPage = $this->request()->getInt('page');
		$iPageSize = Phpfox::getParam('forum.total_posts_per_thread');
		$aThreadCondition = array();
		$aCallback = $this->getParam('aCallback', null);

		if (($iPostRedirect = $this->request()->getInt('permalink')) && ($sUrl = Forum_Service_Callback::instance()->getFeedRedirectPost($iPostRedirect)))
		{
			$this->url()->forward(preg_replace('/\/post_(.*)\//i', '/view_\\1/', $sUrl));
		}

		if (Phpfox::isUser() && ($iView = $this->request()->getInt('view')) && Phpfox::isModule('notification'))
		{
			Notification_Service_Process::instance()->delete('forum_subscribed_post', $iView, Phpfox::getUserId());
			Notification_Service_Process::instance()->delete('forum_post_like', $iView, Phpfox::getUserId());
		}

		if (($iRedirect = $this->request()->getInt('redirect')) && ($aThread = Forum_Service_Thread_Thread::instance()->getForRedirect($iRedirect)))
		{
			if ($aThread['group_id'] > 0)
			{
				$aCallback = Phpfox::callback('group.addForum', $aThread['group_id']);
				if (isset($aCallback['module']))
				{
					$this->url()->send($aCallback['url_home'], array('forum', $aThread['title_url']));
				}
			}
			$this->url()->send('forum', array($aThread['forum_url'] . '-' . $aThread['forum_id'], $aThread['title_url']));
		}

		$threadId = $this->request()->getInt('req3');
		if ($this->request()->segment(3) == 'replies' && $this->request()->getInt('id')) {
			$threadId = $this->request()->getInt('id');
			$iPage = 1;
			$iPageSize = 200;
			$this->template()->setBreadCrumb(_p('latest_replies'), $this->url()->current(), true);
			$this->template()->assign([
				'isReplies' => true
			]);
		}

		$aThreadCondition[] = 'ft.thread_id = ' . $threadId . '';

		$sPermaView = $this->request()->get('view', null);
		if ((int) $sPermaView <= 0)
		{
			$sPermaView = null;
		}

		list($iCnt, $aThread) = Forum_Service_Thread_Thread::instance()->getThread($aThreadCondition, array(), 'fp.time_stamp ASC', $iPage, $iPageSize, $sPermaView);

		if (!isset($aThread['thread_id']))
		{
			return Phpfox_Error::display(_p('not_a_valid_thread'));
		}

		if ($aThread['group_id'] > 0 && ($sParentId = Phpfox::getLib('pages.facade')->getPageItemType($aThread['group_id'])) && Phpfox::isModule($sParentId))
		{
			$aCallback = Phpfox::callback($sParentId . '.addForum', $aThread['group_id']);
			if (!Phpfox::getService($sParentId)->hasPerm($aThread['group_id'], 'forum.view_browse_forum'))
			{
				return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
			}
		}

		if ($aThread['view_id'] != '0' && $aThread['user_id'] != Phpfox::getUserId())
		{
			if (!Phpfox::getUserParam('forum.can_approve_forum_thread') && !Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'approve_thread'))
			{
				return Phpfox_Error::display(_p('not_a_valid_thread'));
			}
		}

		if ($aCallback === null && !Forum_Service_Forum::instance()->hasAccess($aThread['forum_id'], 'can_view_forum'))
		{
            if (Phpfox::isUser())
            {
                return Phpfox_Error::display(_p('you_do_not_have_the_proper_permission_to_view_this_thread'));
            }
            else
            {
                return Phpfox_Error::display(_p('log_in_to_view_thread'));
            }

		}

		if ($aCallback === null && !Forum_Service_Forum::instance()->hasAccess($aThread['forum_id'], 'can_view_thread_content'))
		{
			$this->url()->send('forum', null, _p('you_do_not_have_the_proper_permission_to_view_this_thread'));
		}

		Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));

		$aForum = Forum_Service_Forum::instance()
			->id($aThread['forum_id'])
			->getForum();

		if ($this->request()->get('approve') && (Phpfox::getUserParam('forum.can_approve_forum_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'approve_thread')) && $aThread['view_id'])
		{
			$sCurrentUrl = $this->url()->permalink('forum.thread', $aThread['thread_id'], $aThread['title']);

			if (Forum_Service_Thread_Process::instance()->approve($aThread['thread_id'], $sCurrentUrl))
			{
				$this->url()->forward($sCurrentUrl);
			}
		}

		if ($iPostId = $this->request()->getInt('post'))
		{
			$iCurrentPage = Forum_Service_Post_Post::instance()->getPostPage($aThread['thread_id'], $iPostId, $iPageSize);

			$sFinalLink = $this->url()->permalink('forum.thread', $aThread['thread_id'], $aThread['title'], false, null, array('page' => $iCurrentPage));

			$this->url()->forward($sFinalLink . '#post' . $iPostId);
		}
        
        Forum_Service_Thread_Process::instance()->updateTrack($aThread['thread_id']);

		if (Phpfox::isModule('tag') && $aCallback === null)
		{
			$aTags = Tag_Service_Tag::instance()->getTagsById(($aCallback === null ? 'forum' : 'forum_group'), $aThread['thread_id']);
			if (isset($aTags[$aThread['thread_id']]))
			{
				$aThread['tag_list'] = $aTags[$aThread['thread_id']];
			}
		}

		// Add tags to meta keywords
		if (!empty($aThread['tag_list']) && $aThread['tag_list'] && Phpfox::isModule('tag'))
		{
			$this->template()->setMeta('keywords', Tag_Service_Tag::instance()->getKeywords($aThread['tag_list']));
		}
		

		$this->setParam('iActiveForumId', $aForum['forum_id']);

		if (Phpfox::getParam('forum.rss_feed_on_each_forum'))
		{
			if ($aCallback === null)
			{
				$this->template()->setHeader('<link rel="alternate" type="application/rss+xml" title="' . _p('forum') . ': ' . $aForum['name'] . '" href="' . $this->url()->makeUrl('forum', array('rss', 'forum' => $aForum['forum_id'])) . '" />');
			}
			else
			{
				$this->template()->setHeader('<link rel="alternate" type="application/rss+xml" title="' . _p('group_forum') . ': ' . $aCallback['title'] . '" href="' . $this->url()->makeUrl('forum', array('rss', 'group' => $aCallback['group_id'])) . '" />');
			}
		}

		if (Phpfox::getParam('forum.enable_rss_on_threads'))
		{
			$this->template()->setHeader('<link rel="alternate" type="application/rss+xml" title="' . _p('thread') . ': ' . $aThread['title'] . '" href="' . $this->url()->makeUrl('forum', array('rss', 'thread' => $aThread['thread_id'])) . '" />');
		}

		if ($aCallback === null)
		{
			$this->template()->setBreadCrumb(_p('forum'), $this->url()->makeUrl('forum'))
				->setBreadCrumb($aForum['breadcrumb'])->setBreadCrumb(Phpfox_Locale::instance()->convert(Phpfox::getSoftPhrase($aForum['name'])), $this->url()->permalink('forum', $aForum['forum_id'], $aForum['name']));
		}
		else
		{
			$this->template()->setBreadCrumb((isset($aCallback['module_title']) ? $aCallback['module_title'] : _p('pages')), $this->url()->makeUrl($aCallback['module']));
			$this->template()->setBreadCrumb($aCallback['title'], $aCallback['url_home']);
			$this->template()->setBreadCrumb(_p('discussions'), $aCallback['url_home'] . 'forum/');
		}

		$bCanManageThread = false;
		$bCanEditThread = false;
		$bCanDeleteThread = false;
		$bCanStickThread = false;
		$bCanCloseThread = false;
		$bCanMergeThread = false;
		if ($aCallback === null)
		{
			if (((Phpfox::getUserParam('forum.can_edit_own_post') && $aThread['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('forum.can_edit_other_posts') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'edit_post')))
			{
				$bCanEditThread = true;
			}

			if ((Phpfox::getUserParam('forum.can_delete_own_post') && $aThread['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('forum.can_delete_other_posts') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'delete_post'))
			{
				$bCanDeleteThread = true;
			}

			if ((Phpfox::getUserParam('forum.can_stick_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'post_sticky')))
			{
				$bCanStickThread = true;
			}

			if ((Phpfox::getUserParam('forum.can_close_a_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'close_thread')))
			{
				$bCanCloseThread = true;
			}

			if ((Phpfox::getUserParam('forum.can_merge_forum_threads') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'merge_thread')))
			{
				$bCanMergeThread = true;
			}

			if (
				((Phpfox::getUserParam('forum.can_edit_own_post') && $aThread['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('forum.can_edit_other_posts') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'edit_post'))
				|| (Phpfox::getUserParam('forum.can_move_forum_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'move_thread'))
				|| (Phpfox::getUserParam('forum.can_copy_forum_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'copy_thread'))
				|| (Phpfox::getUserParam('forum.can_delete_own_post') && $aThread['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('forum.can_delete_other_posts') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'delete_post')
				|| (Phpfox::getUserParam('forum.can_stick_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'post_sticky'))
				|| (Phpfox::getUserParam('forum.can_close_a_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'close_thread'))
				|| (Phpfox::getUserParam('forum.can_merge_forum_threads') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'merge_thread'))
			)
			{
				$bCanManageThread = true;
			}
		}
		else
		{
			if (Phpfox::isAdmin() || Phpfox::getService($aCallback['module'])->isAdmin($aCallback['item']))
			{
				$bCanEditThread = true;
				$bCanDeleteThread = true;
				$bCanStickThread = true;
				$bCanCloseThread = true;
				$bCanMergeThread = true;
				$bCanManageThread = true;
			}
		}

		$bCanPurchaseSponsor = false;
		if (
		    ((Phpfox::getUserParam('forum.can_purchase_sponsor') && $aThread['user_id'] == Phpfox::getUserId())
		  || ($bCanCloseThread || $bCanStickThread)
		  || Phpfox::getUserParam('forum.can_sponsor_thread')
			) && !defined('PHPFOX_IS_GROUP_VIEW')) // sponsor is disabled in gorups
		{
		    $bCanPurchaseSponsor = true;
		}

        $sCurrentThreadLink = ($aCallback === null ? $this->url()->makeUrl('forum', array($aForum['name_url'] . '-' . $aForum['forum_id'], $aThread['title_url'])) : $this->url()->makeUrl($aCallback['url_home'], $aThread['title_url']));

		if ($this->request()->get('view')) {
			Phpfox_Module::instance()->appendPageClass('single_mode');
		}
		if (Phpfox::isUser() && Forum_Service_Thread_Thread::instance()->canReplyOnThread($aThread['thread_id'])) {
				$this->template()->menu(_p('reply'), '#', 'onclick="$Core.box(\'forum.reply\', 800, \'id=' . $aThread['thread_id'] . '\'); return false;"');
		}

		$aJsLoad = array(
			'jquery/plugin/jquery.scrollTo.js' => 'static_script',
			'forum.js' => 'module_forum',
			'jquery/plugin/jquery.highlightFade.js' => 'static_script',
			'switch_legend.js' => 'static_script',
			'switch_menu.js' => 'static_script',
		);

		if (!empty($aThread['poll'])) {
			$aJsLoad = array_merge($aJsLoad, ['poll.js' => 'module_poll', 'poll.css' => 'module_poll',]);
		}
		$this->template()->setTitle($aThread['title'])
			->setBreadCrumb($aThread['title'], $this->url()->permalink('forum.thread', $aThread['thread_id'], $aThread['title']), true)
			->setMeta('description', $aThread['title'] . ' - ' . $aForum['name'])
			->setMeta('keywords', $this->template()->getKeywords($aThread['title']))
			->setPhrase(array(
					'provide_a_reply',
					'adding_your_reply',
					'are_you_sure',
					'post_successfully_deleted'
				)
			)
			->setEditor()
			->setHeader('cache', $aJsLoad
			)
			->assign(array(
					'aThread' => $aThread,
					'aPost' => (isset($aThread['post_starter']) ? $aThread['post_starter'] : ''),
					'iTotalPosts' => $iCnt,
					'sCurrentThreadLink' => $sCurrentThreadLink,
					'aCallback' => $aCallback,
					'bCanManageThread' => $bCanManageThread,
					'bCanEditThread' => $bCanEditThread,
					'bCanDeleteThread' => $bCanDeleteThread,
					'bCanStickThread' => $bCanStickThread,
					'bCanCloseThread' => $bCanCloseThread,
					'bCanMergeThread' => $bCanMergeThread,
					'bCanPurchaseSponsor' => $bCanPurchaseSponsor,
					'sPermaView' => $sPermaView,
					'aPoll' => (empty($aThread['poll']) ? false : $aThread['poll']),
					'bIsViewingPoll' => true,
					'bIsCustomPoll' => true,
					'sMicroPropType' => 'CreativeWork'
				)
			);

        if (!empty($aThread['post_starter'])) {
            $this->template()->setMeta('description', ' - ' . $aThread['post_starter']['text']);
        }

        $this->setParam('global_moderation', array(
                'name' => 'forumpost',
                'ajax' => 'forum.postModeration',
                'menu' => array(
                    array(
                        'phrase' => _p('delete'),
                        'action' => 'delete'
                    )
                )
            )
        );

		Phpfox::getLib('parse.output')->setEmbedParser(array(
				'width' => 640,
				'height' => 360
			)
		);

		if ($this->request()->get('is_ajax_get')) {
			$this->template()->assign('isReplies', true);
			Phpfox_Module::instance()->getControllerTemplate();
			$content = ob_get_contents();
			ob_clean();

			return [
				'run' => "$('.thread_replies .tr_view_all').remove();",
				'html' => [
					'to' => '.tr_content',
					'with' => $content
				]
			];
		}
        return null;
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('forum.component_controller_thread_clean')) ? eval($sPlugin) : false);
	}
}