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
 * @package  		Module_Quiz
 * @version 		$Id: index.class.php 3551 2011-11-22 14:49:19Z Raymond_Benc $
 */
class Quiz_Component_Controller_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (defined('PHPFOX_IS_USER_PROFILE') && ($sLegacyTitle = $this->request()->get('req3')) && !empty($sLegacyTitle))
		{
            Core_Service_Core::instance()->getLegacyItem(array(
					'field' => array('quiz_id', 'title'),
					'table' => 'quiz',		
					'redirect' => 'quiz',
					'title' => $sLegacyTitle
				)
			);
		}		
		
		Phpfox::getUserParam('quiz.can_access_quiz', true);
		
		if (($iRedirect = $this->request()->getInt('redirect')) && ($sUrl = Quiz_Service_Callback::instance()->getFeedRedirect($iRedirect)))
		{
			$this->url()->forward($sUrl);
		}
		
		if (defined('PHPFOX_IS_AJAX_CONTROLLER'))
		{
			$aUser = User_Service_User::instance()->get($this->request()->get('profile_id'));
			$this->setParam('aUser', $aUser);
		}
		else 
		{		
			$bIsProfile = $this->getParam('bIsProfile');	
			if ($bIsProfile === true)
			{
				$aUser = $this->getParam('aUser');
			} else {
                //TODO $aUser use in many place. Check it.
                $aUser = [];
            }

		}		
		
		if ($this->request()->getInt('req2') > 0)
		{
			return Phpfox_Module::instance()->setController('quiz.view');
		}			

		$sView = $this->request()->get('view');	
		
		$this->search()->set(array(
				'type' => 'quiz',
				'field' => 'q.quiz_id',
                'ignore_blocked' => true,
				'search_tool' => array(
					'table_alias' => 'q',
					'search' => array(
						'action' => (defined('PHPFOX_IS_USER_PROFILE') ? $this->url()->makeUrl($aUser['user_name'], array('quiz', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('quiz', array('view' => $this->request()->get('view')))),
						'default_value' => _p('search_quizzes'),
						'name' => 'search',
						'field' => 'q.title'
					),
					'sort' => array(
						'latest' => array('q.time_stamp', _p('latest')),
						'most-viewed' => array('q.total_view', _p('most_viewed')),
						'most-liked' => array('q.total_like', _p('most_liked')),
						'most-talked' => array('q.total_comment', _p('most_discussed'))
					),
					'show' => array(Phpfox::getParam('quiz.quizzes_to_show'), Phpfox::getParam('quiz.quizzes_to_show') * 2, Phpfox::getParam('quiz.quizzes_to_show') * 3)
				)
			)
		);			
		
		switch ($sView)
		{
			case 'my':
				Phpfox::isUser(true);
				$this->search()->setCondition('AND q.user_id = ' . (int) Phpfox::getUserId());
				break;
			case 'pending':
				Phpfox::isUser(true);
				Phpfox::getUserParam('quiz.can_approve_quizzes', true);
				$this->search()->setCondition('AND q.view_id = 1');
				break;
			default:
				if ($this->getParam('bIsProfile') === true)
				{
					$this->search()->setCondition('AND q.view_id IN(' . ($aUser['user_id'] == Phpfox::getUserId() ? '0,1' : '0') . ') AND q.user_id = ' . (int) $aUser['user_id'] . ' AND  q.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Core_Service_Core::instance()->getForBrowse($aUser)) . ')');
				}
				else 
				{
					$this->search()->setCondition('AND q.view_id = 0 AND q.privacy IN(%PRIVACY%)');
				}
				break;
		}		
		
		$aBrowseParams = array(
			'module_id' => 'quiz',
			'alias' => 'q',
			'field' => 'quiz_id',
			'table' => Phpfox::getT('quiz'),
			'hide_view' => array('pending', 'my')				
		);			

		$this->search()->setContinueSearch(true);
		$this->search()->browse()->params($aBrowseParams)->execute();
		
		$iCnt = $this->search()->browse()->getCount();
		$aQuizzes = $this->search()->browse()->getRows();				
		
		foreach ($aQuizzes as $aQuiz)
		{
			$this->template()->setMeta('keywords', $this->template()->getKeywords($aQuiz['title']));
		}
		
		Phpfox_Pager::instance()->set(array('page' => $this->search()->getPage(), 'size' => $this->search()->getDisplay(), 'count' => $iCnt));
		
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

		$this->template()->setTitle((defined('PHPFOX_IS_USER_PROFILE') ? _p('full_name_s_quizzes', array('full_name' => $aUser['full_name'])) : _p('quizzes')))
			->setBreadCrumb(_p('quizzes'), (defined('PHPFOX_IS_USER_PROFILE') ? $this->url()->makeUrl($aUser['user_name'], 'quiz') : $this->url()->makeUrl('quiz')))
			->setMeta('keywords', Phpfox::getParam('quiz.quiz_meta_keywords'))
			->setMeta('description', Phpfox::getParam('quiz.quiz_meta_description'))
			->setHeader('cache', array(
					'quiz.js' => 'module_quiz',
					'jquery/plugin/jquery.highlightFade.js' => 'static_script',
				)
			)
			->setPhrase(array(
					'are_you_sure_you_want_to_delete_this_quiz'
				)
			)			
			->assign(array(
				'aQuizzes' => $aQuizzes,
				'bIsProfile' => (defined('PHPFOX_IS_USER_PROFILE') && PHPFOX_IS_USER_PROFILE) ? true : false
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
				'name' => 'quiz',
				'ajax' => 'quiz.moderation',
				'menu' => $aModerationMenu
			)
		);
        return null;
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('quiz.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}