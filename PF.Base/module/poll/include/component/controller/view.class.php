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
 * @package  		Module_Poll
 * @version 		$Id: view.class.php 3587 2011-11-28 07:14:49Z Raymond_Benc $
 */
class Poll_Component_Controller_View extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::getUserParam('poll.can_access_polls', true);	
	
		(($sPlugin = Phpfox_Plugin::get('poll.component_controller_view_process_start')) ? eval($sPlugin) : false);

		// there are times when this controller is actually called
		// in the Poll_Component_Controller_Profile like when the poll
		// is in the profile

		$iPage = $this->request()->getInt('page', 0);
		$iPageSize = 10;

		// we need to make sure we're getting the
		if (!($iPoll = $this->request()->getInt('req2')))
		{
			$this->url()->send('poll');
		}
		
		if (Phpfox::isModule('notification') && Phpfox::isUser())
		{
			Notification_Service_Process::instance()->delete('comment_poll', $this->request()->getInt('req2'), Phpfox::getUserId());
			Notification_Service_Process::instance()->delete('poll_like', $this->request()->getInt('req2'), Phpfox::getUserId());
		}				

		// we need to load one poll
		$aPoll = Poll_Service_Poll::instance()->getPollByUrl($iPoll, $iPage, $iPageSize, true);
		
		if ($aPoll === false)
		{			
			return Phpfox_Error::display('Not a valid poll.');
		}

        if (Phpfox::isUser() && User_Service_Block_Block::instance()->isBlocked(null, $aPoll['user_id']))
        {
            return Phpfox_Module::instance()->setController('error.invalid');
        }

		if (Phpfox::getUserId() == $aPoll['user_id'] && Phpfox::isModule('notification'))
		{
			Notification_Service_Process::instance()->delete('poll_approved', $this->request()->getInt('req2'), Phpfox::getUserId());
		}		
		
		if (!isset($aPoll['is_friend']))
		{
			$aPoll['is_friend'] = 0;
		}
		
		if (Phpfox::isModule('privacy'))
		{
			Privacy_Service_Privacy::instance()->check('poll', $aPoll['poll_id'], $aPoll['user_id'], $aPoll['privacy'], $aPoll['is_friend']);
		}
		
		// set if we can show the poll results
		// is guest the owner of the poll
		$bIsOwner = $aPoll['user_id'] == Phpfox::getUserId();
		$bShowResults = false;
		if ($bIsOwner && Phpfox::getUserParam('poll.can_view_user_poll_results_own_poll') ||
			(!$bIsOwner && Phpfox::getUserParam('poll.can_view_user_poll_results_other_poll'))
		)
		{
			$bShowResults = true;
		}
		$this->template()->assign(array('bShowResults' => $bShowResults));

		if ($aPoll['view_id'] == 1)
		{
			if ((!Phpfox::getUserParam('poll.poll_can_moderate_polls') && $aPoll['user_id'] != Phpfox::getUserId()))
			{
				return Phpfox_Error::display(_p('unable_to_view_this_poll'));
			}

			if ($sModerate = $this->request()->get('moderate'))
			{
				Phpfox::getUserParam('poll.poll_can_moderate_polls', true);
				switch ($sModerate)
				{
					case 'approve':
						if (Poll_Service_Process::instance()->moderatePoll($aPoll['poll_id'], 0))
						{
							$this->url()->send('current', array('poll', $aPoll['question_url']), _p('poll_successfully_approved'));
						}
						break;
					default:
						break;
				}
			}
		}

		// Track users
		if (Phpfox::isModule('track') && Phpfox::isUser() && (Phpfox::getUserId() != $aPoll['user_id']) && !$aPoll['poll_is_viewed'] && !Phpfox::getUserBy('is_invisible'))
		{
            Track_Service_Process::instance()->add('poll', $aPoll['poll_id']);
            Poll_Service_Process::instance()->updateView($aPoll['poll_id']);
		}
	
		if (Phpfox::isUser() && Phpfox::isModule('track') && Phpfox::getUserId() != $aPoll['user_id'] && $aPoll['poll_is_viewed'] && !Phpfox::getUserBy('is_invisible'))
		{
            Track_Service_Process::instance()->update('poll', $aPoll['poll_id']);
		}	
		
		// check editing permissions		
		$aPoll['bCanEdit'] = Poll_Service_Poll::instance()->bCanEdit($aPoll['user_id']);
		$aPoll['bCanDelete'] = Poll_Service_Poll::instance()->bCanDelete($aPoll['user_id']);
		
		// Define params for "review views" block tracker
		$this->setParam(array(
				'sTrackType' => 'poll',
				'iTrackId' => $aPoll['poll_id'],
				'iTrackUserId' => $aPoll['user_id'],
				'aPoll' => $aPoll
			)
		);		
		
		$this->setParam('aFeed', array(				
				'comment_type_id' => 'poll',
				'privacy' => $aPoll['privacy'],
				'comment_privacy' => $aPoll['privacy_comment'],
				'like_type_id' => 'poll',
				'feed_is_liked' => $aPoll['is_liked'],
				'feed_is_friend' => $aPoll['is_friend'],
				'item_id' => $aPoll['poll_id'],
				'user_id' => $aPoll['user_id'],
				'total_comment' => $aPoll['total_comment'],
				'total_like' => $aPoll['total_like'],
				'feed_link' => $this->url()->permalink('poll', $aPoll['poll_id'], $aPoll['question']),
				'feed_title' => $aPoll['question'],
				'feed_display' => 'view',
				'feed_total_like' => $aPoll['total_like'],
				'report_module' => 'poll',
				'report_phrase' => _p('report_this_poll_lowercase')
			)
		);				
		
		$this->template()->setTitle($aPoll['question'])			
			->setBreadCrumb(_p('polls'), $this->url()->makeUrl('poll'))
			->setBreadCrumb($aPoll['question'], $this->url()->permalink('poll', $aPoll['poll_id'], $aPoll['question']), true)
			->setMeta('keywords', $this->template()->getKeywords($aPoll['question']))
			->setMeta('description',  _p('full_name_s_poll_from_time_stamp_question', array(
						'full_name' => $aPoll['full_name'],
						'time_stamp' => Phpfox::getTime(Phpfox::getParam('core.description_time_stamp'), $aPoll['time_stamp']),
						'question' => $aPoll['question']
					)
				)
			)
			->setMeta('description', Phpfox::getParam('poll.poll_meta_description'))
			->setMeta('keywords', Phpfox::getParam('poll.poll_meta_keywords'))		
			->setHeader('cache', array(
					'jquery/plugin/jquery.highlightFade.js' => 'static_script',
					'jquery/plugin/jquery.scrollTo.js' => 'static_script',
					'poll.js' => 'module_poll',
					'poll.css' => 'module_poll',
			)
		)->setEditor(array(
				'load' => 'simple'
			)
		)->assign(array(
					'bIsViewingPoll' => true,
					'aPoll' => $aPoll
			)
		);
		
		if (isset($aPoll['answer']) && is_array($aPoll['answer']))
		{
			foreach ($aPoll['answer'] as $aAnswer)
			{
				$this->template()->setMeta('keywords', $this->template()->getKeywords($aAnswer['answer']));
			}
		}

		$aFilterMenu = array();
		if (!defined('PHPFOX_IS_USER_PROFILE'))
		{
			$aFilterMenu = array(
					_p('all_polls') => '',
					_p('my_polls') => 'my'
			);

			if (Phpfox::isModule('friend') && !Phpfox::getParam('core.friends_only_community'))
			{
				$aFilterMenu[_p('friends_polls')] = 'friend';
			}

			if (Phpfox::getUserParam('poll.poll_can_moderate_polls'))
			{
				$iPendingTotal = Poll_Service_Poll::instance()->getPendingTotal();

				if ($iPendingTotal)
				{
					$aFilterMenu[_p('pending_polls') . ' <span class="pending">' . $iPendingTotal . '</span>'] = 'pending';
				}
			}
		}

		$this->template()->buildSectionMenu('poll', $aFilterMenu);

		(($sPlugin = Phpfox_Plugin::get('poll.component_controller_view_process_end')) ? eval($sPlugin) : false);
        return null;
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('poll.component_controller_view_clean')) ? eval($sPlugin) : false);
	}
}