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
 * @package 		Phpfox_Component
 * @version 		$Id: add.class.php 4074 2012-03-28 14:02:40Z Raymond_Benc $
 */
class Quiz_Component_Controller_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('quiz.can_create_quiz', true);
		
		(($sPlugin = Phpfox_Plugin::get('quiz.component_controller_add_process_start')) ? eval($sPlugin) : false);		
		$iMaxAnswers = Phpfox::getUserParam('quiz.max_answers');
		$iMinAnswers = Phpfox::getUserParam('quiz.min_answers');
		$iMaxQuestions = Phpfox::getUserParam('quiz.max_questions');
		$iMinQuestions = Phpfox::getUserParam('quiz.min_questions');
		$sFormSubmit = $this->url()->makeUrl('quiz.add');
		// bErrors is used to tell JS when there has been errors so it knows when to add more
		// questions or not
		$bErrors = 'false';
		$bIsAdd = 'true';
		// determine if we should show the questions and the title sections
		$bShowQuestions = true;
		$bShowTitle = true;
		
		// is user editing?
		if($iQuizId = $this->request()->getInt('id')) // Editing
		{			
			$bIsAdd = 'false';
			$sFormSubmit = $this->url()->makeUrl('current');
			$aQuiz = Quiz_Service_Quiz::instance()->getQuizToEdit($iQuizId);
			if (empty($aQuiz))
			{
				$this->url()->send('quiz', null, _p('that_quiz_does_not_exist_or_its_awaiting_moderation'));
			}

			if ($aVals = $this->request()->getArray('val'))
			{
				$aQuestions = isset($aVals['q']) ? $aVals['q'] : false;
				$mValid = true;
				if ($aQuestions !== false)
				{
					list($mValid, $bNull) = Quiz_Service_Quiz::instance()->checkStructure($aQuestions);
				}
				
				if ($mValid === true)
				{
					list($mEdit, $sUrl) = Quiz_Service_Process::instance()->update($aVals, Phpfox::getUserId());
					if ($mEdit === true)
					{
						$this->url()->permalink('quiz', $aQuiz['quiz_id'], Phpfox::getLib('parse.input')->clean($aVals['title']), true, _p('your_quiz_has_been_edited'));
					}
				}
				else
				{
                    if (is_array($mValid)){
                        foreach ($mValid as $sError) {
                            Phpfox_Error::set($sError);
                        }
                    }
					$aQuiz['questions'] = isset($bNull) ? $bNull : '';
				}
			}
			
			$this->template()->setTitle(_p('edit_quiz'))
				->setBreadCrumb(_p('quizzes'), $this->url()->makeUrl('quiz'))
				->assign(array('aQuiz' => $aQuiz, 'aForms' => $aQuiz))
				->setBreadCrumb(_p('editing_quiz') . ': ' . Phpfox::getLib('parse.output')->shorten($aQuiz['title'], Core_Service_Core::instance()->getEditTitleSize(), '...'), $this->url()->makeUrl('quiz.add', array('id' => $aQuiz['quiz_id'])), true);
		}
		else
		{
			$this->template()->setTitle(_p('add_new_quiz'))
				->setBreadCrumb(_p('quizzes'), $this->url()->makeUrl('quiz'))
				->setBreadCrumb(_p('add_new_quiz'), $this->url()->makeUrl('quiz.add'), true);
		}

		// Using it:
		$aValidation = array(
			'title' => array(
				'def' => 'required',
				'title' => _p('you_need_to_write_a_title')
			),
			'description' => array(
				'def' => 'required',
				'title' => _p('you_need_to_write_a_description')
			)
		);
		$oValid = Phpfox_Validator::instance()->set(array(
				'sFormName' => 'js_form',	
				'aParams' => $aValidation
			)
		);

		// are we getting a new quiz
		if (($aVals = $this->request()->getArray('val')) && empty($iQuizId))
		{
			// check that there is at least one question and one answer:
			$aQuestions = $aVals['q'];
			$iCntQuestions = count($aQuestions);

			// moved the contents of the whole check to be called as well when editing
			list($mValid, $aQuestions) = Quiz_Service_Quiz::instance()->checkStructure($aQuestions);
			if ($mValid !== true && is_array($mValid))
			{
				foreach($mValid as $sError)
				{
					Phpfox_Error::set($sError);
				}
			}
			
			if (($iFlood = Phpfox::getUserParam('quiz.flood_control_quiz')) !== 0)
			{
				$aFlood = array(
					'action' => 'last_post', // The SPAM action
					'params' => array(
						'field' => 'time_stamp', // The time stamp field
						'table' => Phpfox::getT('quiz'), // Database table we plan to check
						'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
						'time_stamp' => $iFlood * 60 // Seconds);	
					)
				);
							 			
				// actually check if flooding
				if (Phpfox::getLib('spam')->check($aFlood))
				{
					Phpfox_Error::set(_p('you_are_creating_a_quiz_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());	
				}
			}
            $aImage = $_FILES['image'];
            if (Phpfox::getUserParam('quiz.is_picture_upload_required') && (!isset($aImage['size']) || $aImage['size'] == 0)){
                Phpfox_Error::set(_p('please_select_quiz_banner'));
            } elseif ($oValid->isValid($aVals))
			{
				if (($iId = Quiz_Service_Process::instance()->add($aVals, Phpfox::getUserId())))
				{					
					$this->url()->permalink('quiz', $iId, Phpfox::getLib('parse.input')->clean($aVals['title']), true, (Phpfox::getUserParam('quiz.new_quizzes_need_moderation') ? _p('your_quiz_has_been_added_it_needs_to_be_approved_by_our_staff_before_it_can_be_shown') : _p('your_quiz_has_been_added')));
				}
				else
				{
					Phpfox_Error::set(_p('there_was_an_error_with_your_quiz_please_try_again'));
				}
			}
			else
			{
				$aQQuestions['questions'] = $aVals['q'];
				$bErrors = true;
				$this->template()->assign(array(
						'aQuiz' => $aQQuestions,
						'bErrors' => $bErrors
					)
				);
			}
		}
		if ($this->request()->getInt('id'))
		{
			$iQuizOwner = (isset($aQuiz) ? (int) $aQuiz['user_id']: '');
			if ( $iQuizOwner == Phpfox::getUserId())
			{
				$bShowQuestions = Phpfox::getUserParam('quiz.can_edit_own_questions');
				$bShowTitle = Phpfox::getUserParam('quiz.can_edit_own_title');
				
			}
			else
			{
				$bShowQuestions = Phpfox::getUserParam('quiz.can_edit_others_questions');
				$bShowTitle = Phpfox::getUserParam('quiz.can_edit_others_title');
			}

			// redirect
			if ($bShowQuestions == false && $bShowTitle == false)
			{
				$this->url()->send('quiz', null, _p('you_are_not_allowed_to_edit_this_quiz'));
			}
		}
		
		$this->template()
			->setFullSite()			
			->setPhrase(array(
					'you_have_reached_the_maximum_questions_allowed_per_quiz',
					'you_are_required_a_minimum_of_total_questions',
					'you_have_reached_the_maximum_answers_allowed_per_question',
					'you_are_required_a_minimum_of_total_answers_per_question',
					'are_you_sure',
					'answer',
					'delete',
					'you_cannot_write_more_then_limit_characters',
					'you_have_limit_character_s_left',
					'question_count',
					'answer_count'
				)
			)
			->setHeader(array(
					'jquery/plugin/jquery.limitTextarea.js' => 'static_script',
					'add.js' => 'module_quiz',
					'add.css' => 'module_quiz',
					'<script type="text/javascript">$Behavior.quizAddQuestion = function() { $Core.quiz.init({sRequired:"'.Phpfox::getParam('core.required_symbol').'", isAdd: '.$bIsAdd.', bErrors: '.$bErrors.', iMaxAnswers: '.$iMaxAnswers.', iMinAnswers: '.$iMinAnswers.', iMaxQuestions: '.$iMaxQuestions.', iMinQuestions: '.$iMinQuestions.'}); }</script>'
				)
			)
			->assign(array(
					'sCreateJs' => $oValid->createJS(),
					'sGetJsForm' => $oValid->getJsForm(),
					'sFormAction' => $sFormSubmit,
					'bIsAdd' => $bIsAdd,
					'bShowQuestions' => $bShowQuestions,
					'bShowTitle' => $bShowTitle
				)
			);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('quiz.component_controller_add_clean')) ? eval($sPlugin) : false);
	}
}