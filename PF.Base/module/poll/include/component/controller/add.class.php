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
 * @version 		$Id: add.class.php 6513 2013-08-28 06:30:48Z Miguel_Espinoza $
 */
class Poll_Component_Controller_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{		
		Phpfox::isUser(true);
		Phpfox::getUserParam('poll.can_create_poll', true);
		
		$bIsCustom = (($this->request()->get('item_id') && $this->request()->get('module_id')) ? true : false);		
		
		// minimum answers
		$iMinAnswers = 2;
		$iMaxAnswers = (int)Phpfox::getUserParam('poll.maximum_answers_count');
		$iTotalDefaultAnswers = 4;
		$bIsEdit = false;
		
		(($sPlugin = Phpfox_Plugin::get('poll.component_controller_add_process_start')) ? eval($sPlugin) : false);
		
		// ajax validation
		// check input fields
		$aValidation = array(
			'question' => array(
				'def' => 'required',
				'title' => _p('provide_a_question_for_your_poll')
			)
		);
		
		// do they need to complete a captcha challenge?
		if (Phpfox::isModule('captcha') && Phpfox::getUserParam('poll.poll_require_captcha_challenge'))
		{
			$aValidation['image_verification'] = _p('complete_captcha_challenge');
		}

		$oValid = Phpfox_Validator::instance()->set(array(
				'sFormName' => 'js_poll_form',	
				'aParams' => $aValidation
			)
		);		
		$this->template()->assign(array('aForms' => array('randomize' => 0)));
		if ($iReq = $this->request()->getInt('id'))
		{
			$aPoll = Poll_Service_Poll::instance()->getPollById((int) $iReq);
			// did we get a result
			if (!empty($aPoll))
			{
				$bIsOwnPoll = ($aPoll['user_id'] == Phpfox::getUserId());
				
				$bCanEditTitle = (Phpfox::getUserParam('poll.can_edit_title') && ( ($bIsOwnPoll && Phpfox::getUserParam('poll.poll_can_edit_own_polls')) || (!$bIsOwnPoll && Phpfox::getUserParam('poll.poll_can_edit_others_polls'))));
				$bCanEditQuestion = (Phpfox::getUserParam('poll.can_edit_question') && ( ($bIsOwnPoll && Phpfox::getUserParam('poll.poll_can_edit_own_polls')) || (!$bIsOwnPoll && Phpfox::getUserParam('poll.poll_can_edit_others_polls'))));
				$bCanEditAnything = $bCanEditTitle || $bCanEditQuestion;
				
				if ($bCanEditAnything &&
					($bIsOwnPoll && Phpfox::getUserParam('poll.poll_can_edit_own_polls') ||
					(!$bIsOwnPoll && Phpfox::getUserParam('poll.poll_can_edit_others_polls'))))
				{
					$bIsEdit = true;
					$aAnswers = Poll_Service_Poll::instance()->getAnswers($iReq);
					$this->template()->assign(array(
							'aForms' => $aPoll, 
							'aAnswers' => $aAnswers,
							'bCanEditTitle' => $bCanEditTitle,
							'bCanEditQuestion' => $bCanEditQuestion
						)
					);					
				}
				else
				{
					$this->url()->send('poll', null, _p('your_user_group_lacks_permissions_to_edit_that_poll'));
				}
			}
			else
			{
				$this->url()->send('poll', null, _p('that_poll_does_not_exist'));
			}
		}
		
		if ($aVals = $this->request()->getArray('val'))
		{
			if (!$bIsEdit)
			{
				// avoid a flood
				$iFlood = Phpfox::getUserParam('poll.poll_flood_control');
				if ($iFlood != '0')
				{
					$aFlood = array(
						'action' => 'last_post', // The SPAM action
			 			'params' => array(
			 				'field' => 'time_stamp', // The time stamp field
			 				'table' => Phpfox::getT('poll'), // Database table we plan to check
			 				'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
			 				'time_stamp' => $iFlood * 60 // Seconds);	
			 			)
			 		);
			 		// actually check if flooding
			 		if (Phpfox::getLib('spam')->check($aFlood))
			 		{
						// Set an error
			 			Phpfox_Error::set(_p('poll_flood_control', array('x' => $iFlood)));
			 		}
				}
			}

			$mErrors = Poll_Service_Poll::instance()->checkStructure($aVals);
			if (is_array($mErrors))
			{
				foreach ($mErrors as $sError)
				{
					Phpfox_Error::set($sError);
				}
				$this->template()->assign('aForms', $aVals);				
			}
			
			// check theres an image
			if (Phpfox::getParam('poll.is_image_required') && empty($_FILES['image']['name']))
			{
				Phpfox_Error::set(_p('each_poll_requires_an_image'));
			}			
			
			if ($oValid->isValid($aVals))
			{
				// check if question has a question mark
				if (strpos($aVals['question'], '?') === false)
				{
					$aVals['question'] = $aVals['question'] . '?';
				}				
				
				// we do the insert
				// check if its updating:
				if ($bIsEdit)
				{
					$aVals['poll_id'] = $aPoll['poll_id'];
		
					if (Poll_Service_Process::instance()->add(Phpfox::getUserId(), $aVals, true))
					{
						if ($this->request()->get('submit_poll'))
						{
							$this->url()->permalink('poll', $aPoll['poll_id'], $aPoll['question'], true, _p('your_poll_has_been_updated'));
						}
						else 
						{
							$this->url()->send('poll.design', array('id' => $aPoll['poll_id']), _p('your_poll_has_been_updated'));
						}
					}
					else 
					{
						$this->template()->assign('aForms', $aVals);
					}
				}
				else
				{
					if (list($iId, $aPoll) = Poll_Service_Process::instance()->add(Phpfox::getUserId(), $aVals))
					{
						
						if ($this->request()->get('submit_poll'))
						{
							$this->url()->permalink('poll', $iId, $aPoll['question'], true, _p('your_poll_has_been_added') . ((Phpfox::getUserParam('poll.poll_requires_admin_moderation') == true) ? ' ' . _p('your_poll_needs_to_be_approved_before_being_shown_on_the_site') : ''));
						}
						else
						{
							$this->url()->send('poll.design', array('id' => $iId), _p('your_poll_has_been_added_feel_free_to_custom_design_it_the_way_you_want_here') . ((Phpfox::getUserParam('poll.poll_requires_admin_moderation') == true) ? ' ' . _p('your_poll_needs_to_be_approved_before_being_shown_on_the_site') : ''));
						}
					}	
					else 
					{
						$this->template()->assign('aForms', $aVals);
					}
				}				
			}
			else 
			{
				$this->template()->assign('aForms', $aVals);
			}
		}
		
		// final assigns
		$this->template()->setTitle(_p('polls'))
			->setTitle(($bIsEdit ? _p('editing_a_new_poll') : _p('adding_a_new_poll')))
			->setBreadCrumb(_p('polls'), $this->url()->makeUrl('poll'))
			->setBreadCrumb(($bIsEdit ? _p('editing_poll') . ': ' . Phpfox::getLib('parse.output')->shorten($aPoll['question'], Core_Service_Core::instance()->getEditTitleSize(), '...') : _p('adding_poll')), $this->url()->makeUrl('poll.add', array('id' => $this->request()->getInt('id'))), true)
			->setFullSite()			
			->setHeader(array(
					'<script type="text/javascript">$Behavior.setSortableAnswers = function() {iMaxAnswers = '.$iMaxAnswers.'; iMinAnswers = '.$iMinAnswers.';}</script>',
					'poll.js' => 'module_poll',
					'jquery/ui.js' => 'static_script',
					'poll.css' => 'module_poll',					
					'<script type="text/javascript">$Behavior.loadSortableAnswers = function() {$(".sortable").sortable({placeholder: "placeholder", axis: "y"});}</script>'
				)
			)
			->setPhrase(array(
					'you_have_reached_your_limit',
					'answer',
					'you_must_have_a_minimum_of_total_answers',
					'are_you_sure'
				)
			)			
			->assign(array(
				'iTotalAnswers' => $iTotalDefaultAnswers,
				'iMaxAnswers' => $iMaxAnswers,
				'iMin' => $iMinAnswers,
				'bIsEdit' => $bIsEdit,
				'sCreateJs' => $oValid->createJS(),
				'sGetJsForm' => $oValid->getJsForm(($bIsCustom ? false : true)),
				'bIsCustom' => $bIsCustom,
				'iItemId' => (int) $this->request()->get('item_id'),
				'sModuleId' => $this->request()->get('module_id', null)
			)
		);
		
		(($sPlugin = Phpfox_Plugin::get('poll.component_controller_add_process_end')) ? eval($sPlugin) : false);		
	}	
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('poll.component_controller_add_clean')) ? eval($sPlugin) : false);
	}	
}