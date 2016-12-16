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
 * @version 		$Id: view.class.php 7230 2014-03-26 21:14:12Z Fern $
 */
class Quiz_Component_Controller_View extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::getUserParam('quiz.can_access_quiz', true);
		
		if (Phpfox::isModule('notification') && Phpfox::isUser())
		{
			Notification_Service_Process::instance()->delete('comment_quiz', $this->request()->getInt('req2'), Phpfox::getUserId());
			Notification_Service_Process::instance()->delete('quiz_like', $this->request()->getInt('req2'), Phpfox::getUserId());
		}			

		if ($this->request()->get('req4') && ($this->request()->get('req4') == 'answer'))
		{
			// check that this user has not taken the quiz yet
			$aVals = $this->request()->getArray('val');
			if (Quiz_Service_Quiz::instance()->hasTakenQuiz(Phpfox::getUserId(), $this->request()->get('req2')))
			{
				Phpfox_Error::set(_p('you_have_already_answered_this_quiz'));
			}
			elseif (!isset($aVals['answer']))// check to see all questions have been answered
			{
				Phpfox_Error::set(_p('you_have_to_answer_the_questions_if_you_want_to_do_that')); 
			}
			else
			{
				Phpfox::isUser(true);
				// check if user is allowed to answer their own quiz
				$aQuizC = Quiz_Service_Quiz::instance()->getQuizById($this->request()->get('req2'));
				if (!isset($aQuizC['user_id']) || empty($aQuizC['user_id']))
				{
					$this->url()->send('quiz', null, _p('that_quiz_does_not_exist_or_its_awaiting_moderation'));
				}
				$iScore = Quiz_Service_Process::instance()->answerQuiz($this->request()->get('req2'), $aVals['answer']);
				if ( is_numeric($iScore))
				{ // Answers submitted correctly
					$aUser = $this->getParam('aUser');
					$this->url()->permalink('quiz', $this->request()->get('req2'), $this->request()->get('req3'), true, _p('your_answers_have_been_submitted_and_your_score_is_score', array('score' => $iScore)), array('results', 'id' => Phpfox::getUserId())); 
				}
				else
				{					
					Phpfox_Error::set($iScore);
				}
			}
		}
		
		$this->setParam('bViewingQuiz', true); 
		$aQuiz = array();
		$bShowResults = false;
		$bShowUsers = false;
		$bCanTakeQuiz = true;
		// $bShowResults == true -> only when viewing results for one user only
		// $bShowUsers == true -> when viewing all results from a quiz
		
		$sQuizUrl = $this->request()->get('req2');
		$sQuizUrl = Phpfox::getLib('parse.input')->clean($sQuizUrl);
		
		if ($this->request()->get('req4') == 'results')
		{
			$bHasTaken = Quiz_Service_Quiz::instance()->hasTakenQuiz(Phpfox::getUserId(), $sQuizUrl);
			if ($bHasTaken)
			{
				$bCanTakeQuiz = false;
			}
			
			if ($iUser = $this->request()->getInt('id'))
			{
				// show the results of just one user				
				$aQuiz = Quiz_Service_Quiz::instance()->getQuizByUrl($sQuizUrl, $iUser);
				$bShowResults = true;				
			}
			else 
			{
				$bShowUsers = true;				
				$aQuiz = Quiz_Service_Quiz::instance()->getQuizByUrl($sQuizUrl, false);
				$this->template()
					->assign([
						'bPopup'=>true,
						'sAjax'=>'quiz.browseUsers',
						'hasMore'=> count($aQuiz['aTakenBy']) == 10,
						'aPager'=>[
							'nextAjaxUrl'=> 2,
							'sParamsAjax'=>'&quiz_id='. $aQuiz['quiz_id'],
						],
					]);
			}
			
			// need it here to have the quiz' info
			if (!Phpfox::getUserParam('quiz.can_view_results_before_answering') && !$bHasTaken && ($aQuiz['user_id'] != Phpfox::getUserId()))
			{
				$this->url()->send($this->request()->get('req1') . '/' . $this->request()->get('req2') . '/' . $sQuizUrl, null, _p('you_need_to_answer_the_quiz_before_looking_at_the_results'));
			}
			if (Phpfox::getUserParam('quiz.can_post_comment_on_quiz'))
			{
				$this->template()->assign(array('bShowInputComment' => true))
					->setHeader(array(
						'jquery/plugin/jquery.scrollTo.js' => 'static_script',
					)
				);
			}
		}
		elseif ($this->request()->get('req4') == 'take')
		{
			$bShowResults = false;
			$bShowUsers = false;
			$bCanTakeQuiz = false;
			$aQuiz = Quiz_Service_Quiz::instance()->getQuizByUrl($sQuizUrl, true, true);
		}
		else
		{
			if (Quiz_Service_Quiz::instance()->hasTakenQuiz(Phpfox::getUserId(), $sQuizUrl))
			{
				$bCanTakeQuiz = false;
				$bShowResults = false;
				$bShowUsers = true;
				$aQuiz = Quiz_Service_Quiz::instance()->getQuizByUrl($sQuizUrl, false);
			}
			else
			{
				$bCanTakeQuiz = true;
				$aQuiz = Quiz_Service_Quiz::instance()->getQuizByUrl($sQuizUrl, false, true);
			}
			if (Phpfox::getUserParam('quiz.can_post_comment_on_quiz'))
			{
				$this->template()->assign(array('bShowInputComment' => true))
					->setHeader(array(
						'jquery/plugin/jquery.scrollTo.js' => 'static_script',
					)
				);
			}
		}
		
		// crash control, in a perfect world this shouldn't happen
		if (empty($aQuiz))
		{
			$this->url()->send('quiz', null, _p('that_quiz_does_not_exist_or_its_awaiting_moderation'));
		}

        if (Phpfox::isUser() && User_Service_Block_Block::instance()->isBlocked(null, $aQuiz['user_id']))
        {
            return Phpfox_Module::instance()->setController('error.invalid');
        }

		if (Phpfox::getUserId() == $aQuiz['user_id'] && Phpfox::isModule('notification'))
		{
			Notification_Service_Process::instance()->delete('quiz_approved', $this->request()->getInt('req2'), Phpfox::getUserId());
		}			
		
		if (Phpfox::isModule('privacy'))
		{
			if (!isset($aQuiz['is_friend']))
			{
				$aQuiz['is_friend'] = 0;
			}
			Privacy_Service_Privacy::instance()->check('quiz', $aQuiz['quiz_id'], $aQuiz['user_id'], $aQuiz['privacy'], $aQuiz['is_friend']);
		}
		
		// extra info: used for displaying results for one user
		if (isset($aQuiz['results'][0]))
		{
			$aQuiz['takerInfo']['userinfo'] = array(
				'user_name' => $aQuiz['results'][0]['user_name'],
				'user_id' => $aQuiz['results'][0]['user_id'],
				'server_id' => $aQuiz['results'][0]['server_id'],
				'full_name' => $aQuiz['results'][0]['full_name'],
				'gender' => $aQuiz['results'][0]['gender'],
				'user_image' => $aQuiz['results'][0]['user_image']
			);
			$aQuiz['takerInfo']['time_stamp'] = $aQuiz['results'][0]['time_stamp'];
		}
		
		if (!isset($aQuiz['is_viewed']))
		{
			$aQuiz['is_viewed'] = 0;
		}
		
		if (Phpfox::isUser() && (Phpfox::getUserId() != $aQuiz['user_id']) && !$aQuiz['is_viewed'] && !Phpfox::getUserBy('is_invisible'))
		{
			// the updateView should only happen when the user has submitted a
			Quiz_Service_Process::instance()->updateView($aQuiz, Phpfox::getUserId());
			if (Phpfox::isModule('track'))
			{
                Track_Service_Process::instance()->add('quiz', $aQuiz['quiz_id']);
			}
		}
		
		if (Phpfox::isUser() && Phpfox::isModule('track') && Phpfox::getUserId() != $aQuiz['quiz_id'] && $aQuiz['is_viewed'] && !Phpfox::getUserBy('is_invisible')) {
            Track_Service_Process::instance()->update('quiz', $aQuiz['quiz_id']);
		}			
		
		if (isset($aQuiz['aTakenBy'])) {
			$this->setParam('aTakers', $aQuiz['aTakenBy']);
		}
		
		if (Phpfox::isModule('notification') && $aQuiz['user_id'] == Phpfox::getUserId())
		{
			Notification_Service_Process::instance()->delete('quiz_notifyLike', $aQuiz['quiz_id'], Phpfox::getUserId());
		}		
		
		/*
		 * the track table is used to track who has viewed the quiz
		 * the quiz_result to track who has taken the quiz.
		 */
		$this->setParam(array(
				'sTrackType' => 'quiz',
				'iTrackId' => $aQuiz['quiz_id'],
				'iTrackUserId' => $aQuiz['user_id']
			)
		);
		
		$this->setParam('aFeed', array(				
				'comment_type_id' => 'quiz',
				'privacy' => $aQuiz['privacy'],
				'comment_privacy' => $aQuiz['privacy_comment'],
				'like_type_id' => 'quiz',
				'feed_is_liked' => $aQuiz['is_liked'],
				'feed_is_friend' => $aQuiz['is_friend'],
				'item_id' => $aQuiz['quiz_id'],
				'user_id' => $aQuiz['user_id'],
				'total_comment' => $aQuiz['total_comment'],
				'total_like' => $aQuiz['total_like'],
				'feed_link' => $this->url()->permalink('quiz', $aQuiz['quiz_id'], $aQuiz['title']),
				'feed_title' => $aQuiz['title'],
				'feed_display' => 'view',
				'feed_total_like' => $aQuiz['total_like'],
				'report_module' => 'quiz',
				'report_phrase' => _p('report_this_quiz')
			)
		);			
		
		$this->template()->setTitle($aQuiz['title'])
			->setTitle(_p('quizzes'))
			->setBreadCrumb(_p('quizzes'), $this->url()->makeUrl('quiz'))
			->setBreadCrumb($aQuiz['title'], $this->url()->permalink('quiz', $aQuiz['quiz_id'], $aQuiz['title']), true)
			->setMeta('description',  _p('full_name_s_quiz_from_time_stamp_title', array(
						'full_name' => $aQuiz['full_name'],
						'time_stamp' => Phpfox::getTime(Phpfox::getParam('core.description_time_stamp'), $aQuiz['time_stamp']),
						'title' => $aQuiz['title']
					)
				)
			)
			->setMeta('keywords', $this->template()->getKeywords($aQuiz['title']))
			->setMeta('keywords', Phpfox::getParam('quiz.quiz_meta_keywords'))
			->setMeta('description', Phpfox::getParam('quiz.quiz_meta_description'))
			->assign(array(
				'bIsViewingQuiz' => true,
				'bShowResults' => $bShowResults,
				'bShowUsers' => $bShowUsers,
				'bCanTakeQuiz' => $bCanTakeQuiz,
				'aQuiz' => $aQuiz
			)
		)
		->setPhrase(array(
				'are_you_sure_you_want_to_delete_this_quiz'
			)
		)
		->setHeader('cache', array(
				'quiz.js' => 'module_quiz',
				'jquery/plugin/jquery.highlightFade.js' => 'static_script',
				'jquery/plugin/jquery.scrollTo.js' => 'static_script',
			)
		);

		$aFilterMenu = array();
		if (!defined('PHPFOX_IS_USER_PROFILE'))
		{
			$aFilterMenu = array(
					_p('all_quizzes') => '',
					_p('my_quizzes') => 'my'
			);

			if (Phpfox::isModule('friend') && !Phpfox::getParam('core.friends_only_community'))
			{
				$aFilterMenu[_p('friends_quizzes')] = 'friend';
			}

			if (Phpfox::getUserParam('quiz.can_approve_quizzes'))
			{
				$iPendingTotal = Quiz_Service_Quiz::instance()->getPendingTotal();

				if ($iPendingTotal)
				{
					$aFilterMenu[_p('pending_quizzes') . ' <span class="pending">' . $iPendingTotal . '</span>'] = 'pending';
				}
			}
		}

		$this->template()->buildSectionMenu('quiz', $aFilterMenu);

		(($sPlugin = Phpfox_Plugin::get('quiz.component_controller_view_process_end')) ? eval($sPlugin) : false);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('quiz.component_controller_view_clean')) ? eval($sPlugin) : false);
	}
}