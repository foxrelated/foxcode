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
 * @version 		$Id: ajax.class.php 6864 2013-11-07 12:48:15Z Miguel_Espinoza $
 */
class Forum_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function addReply()
	{
		Phpfox::isUser(true);

		$aVals = $this->get('val');
        if (!Forum_Service_Thread_Thread::instance()->canReplyOnThread($aVals['thread_id'])){
            return false;
        }
        Ban_Service_Ban::instance()->checkAutomaticBan($aVals['text']);
		if (Phpfox::getLib('parse.format')->isEmpty($aVals['text']))
		{
			$this->alert(_p('provide_a_reply'));
			$this->call('setTimeout(\'tb_remove();$Core.processForm("#js_forum_form", true);\', 1000);');

			return false;
		}

		$aCallback = false;
		if (isset($aVals['module'])
			&& Phpfox::isModule($aVals['module'])
			&& isset($aVals['item'])
			&& Phpfox::hasCallback($aVals['module'], 'addForum')
		)
		{
			$aCallback = Phpfox::callback($aVals['module'] . '.addForum', $aVals['item']);

			if ($aCallback === false)
			{
				$this->alert(_p('only_members_can_add_a_reply_to_threads'));
				$this->call('setTimeout(\'tb_remove();$Core.processForm("#js_forum_form", true);\', 2000);');

				return false;
			}
		}

		$bPassCaptcha = true;

		if (Phpfox::isModule('captcha') && Phpfox::getUserParam('forum.enable_captcha_on_posting') && !Captcha_Service_Captcha::instance()->checkHash($aVals['image_verification']))
		{
			$bPassCaptcha = false;

			$this->call("$('#js_captcha_image').ajaxCall('captcha.reload', 'sId=js_captcha_image&sInput=image_verification'); $('#js_post_entry').message('" . _p('captcha_failed_please_try_again', array('phpfox_squote' => true)) . "', 'error').slideDown('slow'); $('#js_quick_reply_form .button').attr('disabled', false).removeClass('disabled'); $('#js_quick_reply_form #text').attr('disabled', false).removeClass('disabled'); $('#js_reply_process').html('');");
		}

		if (!$bPassCaptcha)
		{
			$this->call('setTimeout(\'tb_remove();$Core.processForm("#js_forum_form", true);\', 2000);');
			return false;
		}

		$aThread = Forum_Service_Thread_Thread::instance()->getActualThread($aVals['thread_id'], $aCallback);

		if ($aThread['is_closed'])
		{
			$this->alert(_p('thread_is_closed_for_posting'));
			$this->call('setTimeout(\'tb_remove();$Core.processForm("#js_forum_form", true);\', 2000);');

			return false;
		}

		if ($aCallback === false && $aThread['is_announcement'])
		{
			$this->alert(_p('thread_is_closed_for_posting'));
			$this->call('setTimeout(\'tb_remove();$Core.processForm("#js_forum_form", true);\', 2000);');

			return false;
		}

		if (!isset($aThread['thread_id']))
		{
			return false;
		}

		$bPass = false;
		if ((Phpfox::getUserParam('forum.can_reply_to_own_thread') && $aThread['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('forum.can_reply_on_other_threads') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'can_reply'))
		{
			$bPass = true;
		}

		if ($bPass === false)
		{
			$this->alert(_p('insufficient_permission_to_reply_to_this_thread'));
			$this->call('setTimeout(\'tb_remove();$Core.processForm("#js_forum_form", true);\', 2000);');

			return false;
		}

		if (($iFlood = Phpfox::getUserParam('forum.forum_post_flood_control')) !== 0)
		{
			$aFlood = array(
				'action' => 'last_post', // The SPAM action
				'params' => array(
					'field' => 'time_stamp', // The time stamp field
					'table' => Phpfox::getT('forum_post'), // Database table we plan to check
					'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
					'time_stamp' => $iFlood * 60 // Seconds);
				)
			);

			// actually check if flooding
			if (Phpfox::getLib('spam')->check($aFlood))
			{
				$this->alert(_p('posting_a_new_thread_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
				$this->call('setTimeout(\'tb_remove();$Core.processForm("#js_forum_form", true);\', 2000);');

				return false;
			}
		}

		$aVals['forum_id'] = $aThread['forum_id'];

		Phpfox::getLib('parse.output')->setEmbedParser(array(
				'width' => 640,
				'height' => 360
			)
		);

		if ($iId = Forum_Service_Post_Process::instance()->add($aVals, $aCallback))
		{
			$aPost = Forum_Service_Post_Post::instance()->getPost($iId);

			Forum_Service_Thread_Process::instance()->updateTrack($aThread['thread_id']);

			$aThread = Forum_Service_Thread_Thread::instance()->getActualThread($aPost['thread_id']);
			$aPost['count'] = $aThread['total_post'];
			$this->template()->assign(array(
					'aPost' => $aPost,
					'aThread' => $aThread,
					'aCallback' => $aCallback
				)
			)->getTemplate('forum.block.post');

			$this->append('#js_post_new_thread', $this->getContent(false))->call('$Core.forum.processReply(' . $aPost['post_id'] . ');');
		}
		else
		{
			if (Phpfox::getUserParam('forum.approve_forum_post') && $aCallback === false)
			{
				$this->call('js_box_remove($(\'#js_forum_form\'));');
				$this->alert(_p('your_post_has_successfully_been_added_however_it_is_pending_an_admins_approval_before_it_can_be_displayed_publicly'));
				$this->call('$("#js_reply_process").hide();');
			}
		}

		$this->call('$Core.loadInit();');
        return null;
	}

	public function deletePost()
	{
		Phpfox::isUser(true);

		$aPost = Forum_Service_Post_Post::instance()->getPost($this->get('id'));

		$bHasAccess = false;
		if ((int) $aPost['group_id'] > 0)
		{
			if (Pages_Service_Pages::instance()->isAdmin($aPost['group_id']))
			{
				$bHasAccess = true;
			}
		}
		else
		{
			if ((Forum_Service_Moderate_Moderate::instance()->hasAccess($aPost['forum_id'], 'delete_post') || User_Service_Auth::instance()->hasAccess('forum_post', 'post_id', $this->get('id'), 'forum.can_delete_own_post', 'forum.can_delete_other_posts')))
			{
				$bHasAccess = true;
			}
		}

		if ($bHasAccess && Forum_Service_Post_Process::instance()->delete($this->get('id')))
		{

		}
	}

	public function getModerators()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);
		Phpfox::getUserParam('forum.can_manage_forum_moderators', true);

		Phpfox::getBlock('forum.admincp.moderator', array('id' => $this->get('id')));

		$this->html('#js_forum_edit_content', $this->getContent(false));
	}

	public function getModerator()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);
		Phpfox::getUserParam('forum.can_manage_forum_moderators', true);

		$mUserData = Forum_Service_Moderate_Moderate::instance()->getUserPerm($this->get('forum_id'), $this->get('user_id'));

		$this->call('$Core.forum.build(' . $mUserData . ');');
	}

	public function removeModerator()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);
		Phpfox::getUserParam('forum.can_manage_forum_moderators', true);

		Forum_Service_Moderate_Process::instance()->delete($this->get('id'));
	}

	public function updateModerator()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);
		Phpfox::getUserParam('forum.can_manage_forum_moderators', true);

		$aVals = $this->get('val');
		if (empty($aVals['user_id']) && ((!isset($aVals['users'])) || (isset($aVals['users']) && !count($aVals['users']))))
		{
			$this->html('#js_update_mod', '')->alert(_p('select_moderators'));

			return false;
		}

		if (Forum_Service_Moderate_Process::instance()->add($this->get('val')))
		{
			$this->html('#js_update_mod', _p('done'), '.fadeOut(5000)');
		}
        return null;
	}

	public function getText()
	{
		Phpfox::isUser(true);

		$aPost = Forum_Service_Post_Post::instance()->getForEdit($this->get('post_id'));

		$bHasAccess = false;
		if ((int) $aPost['group_id'] > 0)
		{
			if ((Phpfox::getUserParam('forum.can_edit_own_post') && $aPost['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('forum.can_edit_other_posts'))
			{
				$bHasAccess = true;
			}
		}
		else
		{
			if ((Forum_Service_Moderate_Moderate::instance()->hasAccess($aPost['forum_id'], 'edit_post') || User_Service_Auth::instance()->hasAccess('forum_post', 'post_id', $this->get('post_id'), 'forum.can_edit_own_post', 'forum.can_edit_other_posts')))
			{
				$bHasAccess = true;
			}
		}

		(($sPlugin = Phpfox_Plugin::get('forum.component_ajax_get_text')) ? eval($sPlugin) : false);

		if (!isset($bHasPluginCall))
		{
			if ($bHasAccess)
			{
				$this->call("$('#js_quick_edit_id" . $this->get('id') . "').html('<div><div id=\"sJsEditorMenu\" class=\"editor_menu\" style=\"display:block;\">' + Editor.setId('js_quick_edit" . $this->get('id') . "').getEditor(true) + '</div><textarea style=\"width:98%;\" name=\"quick_edit_input\" cols=\"90\" rows=\"10\" id=\"js_quick_edit" . $this->get('id') . "\">" . Phpfox::getLib('parse.output')->ajax($aPost['text']) . "</textarea></div>');");
			}
		}
	}

	public function updateText()
	{
		Phpfox::isUser(true);

		$aVals = (array) $this->get('val');
		$sTxt = $aVals['text'];

		if (Phpfox::getLib('parse.format')->isEmpty($sTxt))
		{
			$this->alert(_p('add_some_text'));

			return false;
		}

		$aPost = Forum_Service_Post_Post::instance()->getPost($this->get('edit'));

		$bHasAccess = false;
		if ((int) $aPost['group_id'] > 0)
		{
			if ((Phpfox::getUserParam('forum.can_edit_own_post') && $aPost['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('forum.can_edit_other_posts'))
			{
				$bHasAccess = true;
			}
		}
		else
		{
			if ((Forum_Service_Moderate_Moderate::instance()->hasAccess($aPost['forum_id'], 'edit_post') || User_Service_Auth::instance()->hasAccess('forum_post', 'post_id', $this->get('edit'), 'forum.can_edit_own_post', 'forum.can_edit_other_posts')))
			{
				$bHasAccess = true;
			}
		}

		if ($bHasAccess)
		{
			if (Forum_Service_Post_Process::instance()->updateText($this->get('edit'), $sTxt, $aVals))
			{
				$aPost = Forum_Service_Post_Post::instance()->getPost($this->get('edit'));

				$this->html('#js_post_edit_text_' . $aPost['post_id'], Phpfox::getLib('parse.output')->split(Phpfox::getLib('parse.output')->parse($aPost['text']), 55));
				$this->call('$("#js_post_edit_text_' . $aPost['post_id'] . '").removeClass("twa_built");');
				$this->call('tb_remove();');

                if(isset($aPost['attachments']) && count($aPost['attachments']) > 0)
				{
					Phpfox::getBlock('attachment.list', array('sType' => 'forum', 'attachments' => $aPost['attachments']));
					$this->call("$('#post" . $aPost['post_id'] . "').find('.attachment_holder_view').remove();");
					$this->call("$('" . $this->getContent() . "').insertAfter('#js_post_edit_text_" . $aPost['post_id'] . "');");
				}
				$this->call('$Core.loadInit();');
			}
		}
        return null;
	}

	public function move()
	{
		Phpfox::isUser(true);

		if (!Phpfox::getUserParam('forum.can_move_forum_thread'))
		{
			$aThread = Forum_Service_Thread_Thread::instance()->getActualThread($this->get('thread_id'));

			if (!Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'move_thread'))
			{
				$this->alert(_p('not_permitted_to_move_threads'));

				return false;
			}
		}

		Phpfox::getBlock('forum.move');
	}

	public function processMove()
	{
		Phpfox::isUser(true);

		if ((Phpfox::getUserParam('forum.can_move_forum_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess($this->get('forum_id'), 'move_thread')) && Forum_Service_Thread_Process::instance()->move($this->get('thread_id'), $this->get('forum_id')))
		{
			$aForum = Forum_Service_Forum::instance()
				->id($this->get('forum_id'))
				->getForum();

			$aThread = Forum_Service_Thread_Thread::instance()->getActualThread($this->get('thread_id'));

			$sUrl = Phpfox_Url::instance()->makeUrl('forum', array($aForum['name_url'] . '-' . $aForum['forum_id'], $aThread['title_url']));

			Phpfox::addMessage(_p('thread_successfully_moved'));

			$this->call('window.location.href = \'' . $sUrl . '\';');

		}
		else
		{
			$this->alert(_p('you_are_not_permitted_to_move_this_thread_to_this_specific_forum'));
		}
	}

	public function copy()
	{
		Phpfox::isUser(true);

		Phpfox::getBlock('forum.copy');
	}

	public function processCopy()
	{
		Phpfox::isUser(true);

		if ((Phpfox::getUserParam('forum.can_copy_forum_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess($this->get('forum_id'), 'copy_thread')) && Forum_Service_Thread_Process::instance()->copy($this->get('thread_id'), $this->get('forum_id'), $this->get('title')))
		{
			$aForum = Forum_Service_Forum::instance()
				->id($this->get('forum_id'))
				->getForum();

			$sUrl = Phpfox_Url::instance()->makeUrl('forum', array($aForum['name_url'] . '-' . $aForum['forum_id'], Phpfox::getLib('parse.input')->prepareTitle('forum', $this->get('title'), 'title_url', null, Phpfox::getT('forum_thread'), true)));

			Phpfox::addMessage(_p('successfully_copied_the_thread'));

			$this->call('window.location.href= \'' . $sUrl . '\';');
		}
		else
		{
			$this->alert(_p('you_are_not_permitted_to_copy_this_thread_to_this_specific_forum'));
		}
	}

	public function deleteThread()
	{
		Phpfox::isUser(true);

		$aThread = Forum_Service_Thread_Thread::instance()->getActualThread($this->get('thread_id'));

		$bHasAccess = false;
		if ((int) $aThread['group_id'] > 0)
		{
			if ((Phpfox::getUserParam('forum.can_delete_own_post') && $aThread['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('forum.can_delete_other_posts'))
			{
				$bHasAccess = true;
			}
		}
		else
		{
			if ((Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'delete_post') || User_Service_Auth::instance()->hasAccess('forum_thread', 'thread_id', $this->get('thread_id'), 'forum.can_delete_own_post', 'forum.can_delete_other_posts')))
			{
				$bHasAccess = true;
			}
		}

		if ($bHasAccess)
		{
            Forum_Service_Thread_Process::instance()->delete($this->get('thread_id'));

			Phpfox::addMessage(_p('thread_successfully_deleted'));

			if ((int) $aThread['group_id'] > 0)
			{
				$aPage = Pages_Service_Callback::instance()->addForum($aThread['group_id']);

				if (isset($aPage['url_home']))
				{
					$this->call('window.location.href = \'' . $aPage['url_home'] . 'forum/\';');
				}
			}
			else
			{
				$this->call('window.location.href = \'' . Phpfox_Url::instance()->makeUrl('forum') . '\';');
			}
		}
	}

	public function stickThread()
	{
		Phpfox::isUser(true);

		$aThread = Forum_Service_Thread_Thread::instance()->getActualThread($this->get('thread_id'));

		$bHasAccess = false;
		if ((int) $aThread['group_id'] > 0)
		{
		    $sType = Phpfox::getPagesType($aThread['group_id']);
            if ($sType == 'pages') {
                if (Phpfox::isModule('pages') && Pages_Service_Pages::instance()->isAdmin($aThread['group_id'])) {
                    $bHasAccess = true;
                }
            } elseif ($sType == 'groups') {
                if (Phpfox::isModule('groups') && Phpfox::getService('groups')->isAdmin($aThread['group_id'])) {
                    $bHasAccess = true;
                }
            }
		}
		else
		{
			if ((Phpfox::getUserParam('forum.can_stick_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'post_sticky')))
			{
				$bHasAccess = true;
			}
		}

		if ($bHasAccess)
		{
			if (Forum_Service_Thread_Process::instance()->stick($this->get('thread_id'), $this->get('type_id')))
			{
				if ($this->get('type_id') == 1)
				{
					$this->html('#js_stick_thread', '<li id="js_stick_thread"><a href="#" onclick="return $Core.forum.stickThread(\'' . $this->get('thread_id') . '\', 0);">' . _p('unstick_thread') . '</a></li>')->alert(_p('thread_successfully_stuck'));
				}
				else
				{
					$this->html('#js_stick_thread', '<li id="js_stick_thread"><a href="#" onclick="return $Core.forum.stickThread(\'' . $this->get('thread_id') . '\', 1);">' . _p('stick_thread') . '</a></li>')->alert(_p('thread_successfully_unstuck'));
				}
			}
		}
	}

	public function closeThread()
	{
		Phpfox::isUser(true);

		$aThread = Forum_Service_Thread_Thread::instance()->getActualThread($this->get('thread_id'));

		$bHasAccess = false;
		if ((int) $aThread['group_id'] > 0)
		{

		}
		else
		{
			if ((Phpfox::getUserParam('forum.can_close_a_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'close_thread')))
			{
				$bHasAccess = true;
			}
		}

		if ($bHasAccess)
		{
			if (Forum_Service_Thread_Process::instance()->close($this->get('thread_id'), $this->get('type_id')))
			{
				if ($this->get('type_id') == 1)
				{
					$this->html('#js_close_thread', '<li id="js_close_thread"><a href="#" onclick="return $Core.forum.closeThread(\'' . $this->get('thread_id') . '\', 0);">' . _p('open_thread') . '</a></li>')->hide('#js_quick_reply')->alert(_p('thread_successfully_closed'));
				}
				else
				{
					$this->html('#js_close_thread', '<li id="js_close_thread"><a href="#" onclick="return $Core.forum.closeThread(\'' . $this->get('thread_id') . '\', 1);">' . _p('close_thread') . '</a></li>')->show('#js_quick_reply')->alert(_p('thread_successfully_opened'));
				}
			}
		}
	}

	public function merge()
	{
		Phpfox::isUser(true);

		Phpfox::getBlock('forum.merge');
	}

	public function processMerge()
	{
		Phpfox::isUser(true);
		$this->error(false);

		$aThread = Forum_Service_Thread_Thread::instance()->getActualThread($this->get('thread_id'));

		$bHasAccess = false;
		$mReturn = false;
		if ((int) $aThread['group_id'] > 0)
		{
			$aPage = Pages_Service_Pages::instance()->getForView($aThread['group_id']);
			if (isset($aPage['is_admin']) && $aPage['is_admin'])
			{
				$bHasAccess = true;
			}
		}
		else
		{
			if ((Phpfox::getUserParam('forum.can_merge_forum_threads') || Forum_Service_Moderate_Moderate::instance()->hasAccess($this->get('forum_id'), 'merge_thread')))
			{
				$bHasAccess = true;
			}
		}

		if ($bHasAccess)
		{
			$mReturn = Forum_Service_Thread_Process::instance()->merge($this->get('thread_id'), $this->get('forum_id'), $this->get('url'));
		}
		else
		{
			Phpfox_Error::set(_p('not_allowed_to_merge_threads_from_this_specific_forum'));
		}

		if ($mReturn !== false)
		{
			Phpfox::addMessage(_p('threads_successfully_merged'));

			$this->call('window.location.href = \'' . $mReturn . '\';');
		}
		else
		{
			$aErrors = Phpfox_Error::get();
			$sErrors = '';
			foreach ($aErrors as $sError)
			{
				$sErrors .= '<div class="error_message">' . $sError . '</div>';
			}

			$this->html('#js_error_message', '' . $sErrors . '');
		}
	}

	public function subscribe()
	{
		if ($this->get('subscribe'))
		{
			Forum_Service_Subscribe_Process::instance()->add($this->get('thread_id'), Phpfox::getUserId());
		}
		else
		{
            Forum_Service_Subscribe_Process::instance()->delete($this->get('thread_id'), Phpfox::getUserId());
		}
	}

	/**
	 * Only meant ofr the ajax call available to admins and moderators, regular users should use the
	 * link to the ad.sponsor
	 * type 1 = sponsor; 0|else = unsponsor
	 */
	public function sponsor()
	{
	    $iThreadId = (int)$this->get('thread_id');
	    $iType = (int)$this->get('type');

	    if (Forum_Service_Thread_Process::instance()->sponsor($iThreadId, $iType))
	    {
			// ajax call to change the hidden status for the spans
			if ($iType == '2')
			{
			    Phpfox::getService('ad.process')->addSponsor(array('module' => 'forum', 'section'=>'thread', 'item_id' => $iThreadId));
			    // making sponsored means hide sponsor and show unsponsor
			    $this->call('$("#js_sponsor_thread_'.$iThreadId.'").hide();');
			    $this->call('$("#js_unsponsor_thread_'.$iThreadId.'").show();');
			    $this->alert(_p('thread_successfully_sponsored'));
			}
			else
			{
			    Phpfox::getService('ad.process')->deleteAdminSponsor('forum-thread', $iThreadId);
			    $this->call('$("#js_sponsor_thread_'.$iThreadId.'").show();');
			    $this->call('$("#js_unsponsor_thread_'.$iThreadId.'").hide();');
			    $this->alert(_p('thread_successfully_unsponsored'));
			}
	    }
	}

	public function approvePost()
	{
		Phpfox::getUserParam('forum.can_approve_forum_post', true);
		if (Forum_Service_Post_Process::instance()->approve($this->get('post_id')))
		{
			$this->call('$(\'#post' . $this->get('post_id') . '\').find(\'.forum_content:first\').removeClass(\'row_moderate\');');
		}
	}

	public function thanks()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('forum.can_thank_on_forum_posts', true);
		if ($iThankId = Forum_Service_Post_Process::instance()->thank($this->get('post_id'))) {

			$this->call('$("#forum_thanks_btn_' . $this->get('post_id') . '").addClass("thanked");');
			$this->call('$("#forum_thanks_btn_' . $this->get('post_id') . '").attr("onclick","$.ajaxCall(\'forum.removeThanks\', \'thank_id=' . $iThankId . '\');return false;");');

			$iThanksCount = Forum_Service_Post_Post::instance()->getThanksCount($this->get('post_id'));
			$sCountPhrase = _p('thanks_count', array('count' => $iThanksCount));
			$this->show('#js_thank_' . $this->get('post_id'));
			$this->call('$("#js_thank_' . $this->get('post_id') . ' a").html("' . $sCountPhrase . '");');
		}
	}

	public function removeThanks()
	{
		Phpfox::isUser(true);
		if ($iPostId = Forum_Service_Post_Process::instance()->deleteThanks($this->get('thank_id'))) {
			if(!$this->get('popup', false) || ($this->get('user_id', 0) == Phpfox::getUserId())) {
				$this->call('$("a#forum_thanks_btn_' . $iPostId . '").removeClass("thanked");');
				$this->call('$("a#forum_thanks_btn_' . $iPostId . '").attr("onclick","$.ajaxCall(\'forum.thanks\', \'post_id=' . $iPostId . '\');return false;");');
			}
			if($this->get('popup', false)) {
				$this->slideUp('#js_post_' . $iPostId . '_thank_' .  $this->get('user_id', 0));
			}

			$iThanksCount = Forum_Service_Post_Post::instance()->getThanksCount($iPostId);
			if ($iThanksCount == 0) {
				$this->hide('div#js_thank_' . $iPostId);
			}
			else {
				$sCountPhrase = _p('thanks_count', array('count' => $iThanksCount));
				$this->call('$("div#js_thank_' . $iPostId . ' a").html("' . $sCountPhrase . '");');
			}
		}
	}

	public function thanksBrowse()
	{
		$this->error(false);
		Phpfox::getBlock('forum.thanks');
		$sTitle = _p('people_who_thanked_this');

		$this->setTitle($sTitle);
		$this->call('<script>$Core.loadInit();</script>');
	}

	public function loadPermissions()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('forum.can_manage_forum_permissions', true);
		if ($this->get('user_group_id'))
		{
			$this->template()->assign('aPerms', Forum_Service_Forum::instance()->getUserGroupAccess($this->get('forum_id'), $this->get('user_group_id')))->getTemplate('forum.block.admincp.permission');
			$aUserGroup = User_Service_Group_Group::instance()->getGroup($this->get('user_group_id'));

			$this->slideDown('#js_display_perms')
				->show('#js_save_perms')
				->html('#js_form_perm_group', $aUserGroup['title'])
				->html('#js_display_list_perms', $this->getContent(false));
			$this->call('$Core.loadInit();');
		}
	}

	public function savePerms()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('forum.can_manage_forum_permissions', true);
		if (Forum_Service_Process::instance()->savePerms($this->get('val')))
		{
			$this->softNotice('permissions_saved_successfully');
		}
	}

	public function permReset()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('forum.can_manage_forum_permissions', true);
        Forum_Service_Process::instance()->resetPerms($this->get('forum_id'), $this->get('user_group_id'));
		$this->template()->assign('aPerms', Forum_Service_Forum::instance()->getUserGroupAccess($this->get('forum_id'), $this->get('user_group_id')))->getTemplate('forum.block.admincp.permission');
		$this->html('#js_display_list_perms', $this->getContent(false));
	}

	public function deletePoll()
	{
		if (User_Service_Auth::instance()->hasAccess('poll', 'poll_id', $this->get('poll_id'), 'poll.poll_can_delete_own_polls', 'poll.poll_can_delete_others_polls'))
		{
			Poll_Service_Process::instance()->moderatePoll($this->get('poll_id'), 2);
			Phpfox_Database::instance()->update(Phpfox::getT('forum_thread'), array('poll_id' => '0'), 'thread_id = ' . (int) $this->get('thread_id'));
			$this->show('#js_attach_poll')->html('#js_attach_poll_question', '');
		}
	}

	public function reply()
	{
    define('PHPFOX_FORUM_REPLY_THREAD', true);
		if (!$this->get('edit') && !$this->get('quote'))
		{
			$this->setTitle(_p('post_a_reply'));
		}
		Phpfox::getComponent('forum.post', array(), 'controller');

		(($sPlugin = Phpfox_Plugin::get('forum.component_ajax_reply')) ? eval($sPlugin) : false);

        $this->call('<script>$Core.loadInit();</script>');
	}

	public function moderation()
	{
		Phpfox::isUser(true);

		switch ($this->get('action'))
		{
			case 'approve':
				Phpfox::getUserParam('forum.can_approve_forum_thread', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
                    Forum_Service_Thread_Process::instance()->approve($iId);
					$this->call('$(".js_selector_class_' . $iId . '").parent().prev().remove();');
					$this->call('$(".js_selector_class_' . $iId . '").parent().remove();');
				}
				$this->updateCount();
				$sMessage = _p('thread_s_successfully_approved');
				break;
			case 'delete':
				Phpfox::getUserParam('forum.can_delete_other_posts', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
                    Forum_Service_Thread_Process::instance()->delete($iId);
					$this->call('$(".js_selector_class_' . $iId . '").parent().prev().remove();');
					$this->call('$(".js_selector_class_' . $iId . '").parent().remove();');
				}
				$sMessage = _p('thread_s_successfully_deleted');
				break;
            default:
                $sMessage = '';
                break;
		}

		$this->alert($sMessage, _p('moderation'), 300, 150, true);
		$this->hide('.moderation_process');
	}

	public function postModeration()
	{
		Phpfox::isUser(true);

		switch ($this->get('action'))
		{
			case 'approve':
				Phpfox::getUserParam('forum.can_approve_forum_thread', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
                    Forum_Service_Post_Process::instance()->approve($iId);
					$this->call('$(\'#post' . $iId . '\').parent().remove();');
				}
				$this->updateCount();
				$sMessage = _p('post_s_successfully_approved');
				break;
			case 'delete':
				Phpfox::getUserParam('forum.can_delete_other_posts', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
                    Forum_Service_Post_Process::instance()->delete($iId);
					$this->call('$(\'#post' . $iId . '\').parent().remove();');
				}				
				$sMessage = _p('post_s_successfully_deleted');
				break;
            default:
                $sMessage = '';
                break;
		}
		
		$this->alert($sMessage, 'Moderation', 300, 150, true);
		$this->hide('.moderation_process');			
	}	
}