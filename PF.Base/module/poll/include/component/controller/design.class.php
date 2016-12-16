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
 * @version 		$Id: design.class.php 5978 2013-05-29 08:36:19Z Miguel_Espinoza $
 */
class Poll_Component_Controller_Design extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		
		(($sPlugin = Phpfox_Plugin::get('poll.component_controller_design_process_start')) ? eval($sPlugin) : false);
		// security measures:
		Phpfox::isUser(true);
	
		
		$iReq = $this->request()->getInt('id');
		/**
		 * @fix When calling a service index file we only need to add the name once and it will find the index file for us
		 */
		$aPoll = Poll_Service_Poll::instance()->getPollById($iReq, Phpfox::getUserId());
	
		// did we get a result
		if (empty($aPoll))
		{
			/**
			 * @fix We use the method display() and use it with a return so we exit the script, otherwise we continue to execute the code below.
			 */
			return Phpfox_Error::display(_p('that_poll_doesn_t_exist'));
		}
		
		$bIsOwner = $aPoll['user_id'] == Phpfox::getUserId();

		if (!Phpfox::getUserParam('poll.can_edit_title') || ($bIsOwner && !Phpfox::getUserParam('poll.poll_can_edit_own_polls')) ||
			(!$bIsOwner && !Phpfox::getUserParam('poll.poll_can_edit_others_polls')))
		{
			return Phpfox_Error::display(_p('you_do_not_have_permission_to_change_the_design_of_this_poll'));
		}
	
		
		/**
		 * @fix Moved after the empty($aPoll) as it would add to the $aPoll array and causing it not to be empty()
		 */
		$aPoll['voted'] = 1;

		// did we get a design submission?
		/**
		 * @fix Moved after getPollById() so we can add a check to see if another user can edit this poll
		 */
		if ($aVals = $this->request()->getArray('val'))
		{
			$mSuccess = Poll_Service_Process::instance()->updateDesign(Phpfox::getUserId(),$aVals);
			if ($mSuccess !== false)
			{
				$this->url()->permalink('poll', $aPoll['poll_id'], $aPoll['question'], true, _p('your_design_has_been_updated'));
			}
			else
			{
				$this->url()->send('current', null, _p('error'));
			}			
		}		
		
		// get the answers
		/**
		 * @fix We seem to be getting the answers twice.
		 */
		$aAnswers = Poll_Service_Poll::instance()->getAnswers($iReq);
		if (empty($aAnswers))
		{
			// show this message only if there is a poll, in which case a hack happened because
			// this beats the logic of having at least 2 answers
			if(!empty($aPoll))Phpfox_Error::set(_p('there_are_no_answers_for_this_poll'));
		}


		// set permission for editing
		// is guest the owner?
			if ($aPoll['user_id'] == Phpfox::getUserId())
			{
				$bCanEdit = Phpfox::getUserParam('poll.poll_can_edit_own_polls') && (Phpfox::getUserParam('poll.can_edit_question') || Phpfox::getUserParam('poll.can_edit_title'));
			}
			else
			{
				$bCanEdit = Phpfox::getUserParam('poll.poll_can_edit_others_polls') && (Phpfox::getUserParam('poll.can_edit_question') || Phpfox::getUserParam('poll.can_edit_title'));
			}
			$aPoll['bCanEdit'] = $bCanEdit;

		$this->template()->setTitle(_p('polls'))
			->setBreadCrumb(_p('polls'), $this->url()->makeUrl('poll'));
		
		$this->template()->setHeader(array(
						'<script type="text/javascript">var iMaxAnswers = 10; var iMinAnswers = 2;</script>',
						'colorpicker/js/colpick.js' => 'static_script',
						'design.js' => 'module_poll',
						'poll.css' => 'module_poll',
						'poll.js' => 'module_poll',
					)
				)
				->setPhrase(array(
						'you_have_reached_your_limit',
						'answer',
						'you_must_have_a_minimum_of_total_answers',
						'are_you_sure'
					)
				)
				->setBreadCrumb(_p('design_your_poll'), $this->url()->makeUrl('poll.design', array('id' => $aPoll['poll_id'])), true)
				->setTitle(_p('design_your_poll'))
				->assign(array(						
						'aPoll' => $aPoll,			
						'aAnswers' =>$aAnswers,			
						'bProcessPoll' => true,
						'bIsSample' => true,
						'bDesign' => true,
						'bCanEdit' => $bCanEdit
					)
				);
		(($sPlugin = Phpfox_Plugin::get('poll.component_controller_design_process_end')) ? eval($sPlugin) : false);
        return null;
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