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
 * @version 		$Id: index.class.php 5074 2012-12-06 10:37:26Z Raymond_Benc $
 */
class Poll_Component_Controller_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (defined('PHPFOX_IS_USER_PROFILE') && ($sLegacyTitle = $this->request()->get('req3')) && !empty($sLegacyTitle))
		{
            Core_Service_Core::instance()->getLegacyItem(array(
					'field' => array('poll_id', 'question'),
					'table' => 'poll',		
					'redirect' => 'poll',
					'search' => 'question_url',
					'title' => $sLegacyTitle
				)
			);
		}		
		
		Phpfox::getUserParam('poll.can_access_polls', true);
		
		if (($iRedirect = $this->request()->getInt('redirect')) && ($sUrl = Poll_Service_Callback::instance()->getFeedRedirect($iRedirect)))
		{
			$this->url()->forward($sUrl);
		}		
		
		(($sPlugin = Phpfox_Plugin::get('poll.component_controller_index_process_start')) ? eval($sPlugin) : false);

		$sView = $this->request()->get('view');		
		
		if ($iDeleteId = $this->request()->getInt('delete'))
		{
			if (User_Service_Auth::instance()->hasAccess('poll', 'poll_id', $iDeleteId, 'poll.poll_can_delete_own_polls', 'poll.poll_can_delete_others_polls') && Poll_Service_Process::instance()->moderatePoll($iDeleteId, 2))
			{
				$this->url()->send('poll', null, _p('poll_successfully_deleted'));
			}
		}		

		if (defined('PHPFOX_IS_AJAX_CONTROLLER'))
		{
			$bIsProfile = true;
			$aUser = User_Service_User::instance()->get($this->request()->get('profile_id'));
			$this->setParam('aUser', $aUser);
		}
		else 
		{		
			$bIsProfile = $this->getParam('bIsProfile');	
			if ($bIsProfile === true)
			{
				$aUser = $this->getParam('aUser');
			}
		}
		
		if ($this->request()->getInt('req2') > 0)
		{
			return Phpfox_Module::instance()->setController('poll.view');
		}			
		
		$this->search()->set(array(
				'type' => 'poll',
				'field' => 'poll.poll_id',
                'ignore_blocked' => true,
				'search_tool' => array(
					'table_alias' => 'poll',
					'search' => array(
						'action' => (defined('PHPFOX_IS_USER_PROFILE') ? $this->url()->makeUrl($aUser['user_name'], array('poll', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('poll', array('view' => $this->request()->get('view')))),
						'default_value' => _p('search_polls'),
						'name' => 'search',
						'field' => 'poll.question'
					),
					'sort' => array(
						'latest' => array('poll.time_stamp', _p('latest')),
						'most-viewed' => array('poll.total_view', _p('most_viewed')),
						'most-liked' => array('poll.total_like', _p('most_liked')),
						'most-talked' => array('poll.total_comment', _p('most_discussed'))
					),
					'show' => array(5, 10, 15)
				)
			)
		);	
		
		$aBrowseParams = array(
			'module_id' => 'poll',
			'alias' => 'poll',
			'field' => 'poll_id',
			'table' => Phpfox::getT('poll'),
			'hide_view' => array('pending', 'my')				
		);			
		
		switch ($sView)
		{
			case 'my':
				Phpfox::isUser(true);
				$this->search()->setCondition('AND poll.user_id = ' . (int) Phpfox::getUserId());
				break;
			case 'pending':
				Phpfox::isUser(true);
				Phpfox::getUserParam('poll.poll_can_moderate_polls', true);
				$this->search()->setCondition('AND poll.view_id = 1');
				break;
			default:
				if ($bIsProfile === true)
				{
					$this->search()->setCondition('AND poll.item_id = 0 AND poll.user_id = ' . (int) $aUser['user_id'] . ' AND poll.view_id IN(' . ($aUser['user_id'] == Phpfox::getUserId() ? '0,1' : '0') . ') AND poll.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Core_Service_Core::instance()->getForBrowse($aUser)) . ')');
				}
				else 
				{
					$this->search()->setCondition('AND poll.item_id = 0 AND poll.view_id = 0 AND poll.privacy IN(%PRIVACY%)');
				}
				break;
		}

		$this->search()->setContinueSearch(true);
		$this->search()->browse()->params($aBrowseParams)->execute();
		
		$iCnt = $this->search()->browse()->getCount();
		$aPolls = $this->search()->browse()->getRows();

		if (defined('PHPFOX_IS_USER_PROFILE'))
		{
			$this->template()->setMeta('keywords', _p('full_name_s_polls', array('full_name' => $aUser['full_name'])));
			$this->template()->setMeta('description', _p('full_name_s_polls_on_site_title_full_name_has_total_poll_s', array(
						'full_name' => $aUser['full_name'],
						'site_title' => Phpfox::getParam('core.site_title'),
						'total' => $iCnt
					)
				)
			);
		}		
		
		Phpfox_Pager::instance()->set(array('page' => $this->search()->getPage(), 'size' => $this->search()->getDisplay(), 'count' => $iCnt));
		
		// check if user has voted here already
		$aVotedPollsByUser = Poll_Service_Poll::instance()->getVotedAnswersByUser(Phpfox::getUserId());
		// check editing permissions
		foreach ($aPolls as $iKey => &$aPoll)
		{
			// is guest the owner?
			$aPoll['bCanEdit'] = Poll_Service_Poll::instance()->bCanEdit($aPoll['user_id']);
			$aPoll['bCanDelete'] = Poll_Service_Poll::instance()->bCanDelete($aPoll['user_id']);

			$this->template()->setMeta('keywords', $this->template()->getKeywords($aPoll['question']));
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
		
		$this->template()->setTitle((defined('PHPFOX_IS_USER_PROFILE') ? _p('full_name_s_polls_upper', array('full_name' => $aUser['full_name'])) : _p('polls')))
			->setHeader('cache', array(
					'moderate.js' => 'module_poll',

				)
			)
			->setBreadCrumb(_p('polls'), (defined('PHPFOX_IS_USER_PROFILE') ? $this->url()->makeUrl($aUser['user_name'], 'poll') : $this->url()->makeUrl('poll')))
			->setMeta('description', Phpfox::getParam('poll.poll_meta_description'))
			->setMeta('keywords', Phpfox::getParam('poll.poll_meta_keywords'))
			->setHeader('cache', array(
					'poll.css' => 'module_poll',
				)
			)
			->assign(array(
					'aPolls' => $aPolls
				)
			);
			
		$this->setParam('global_moderation', array(
				'name' => 'poll',
				'ajax' => 'poll.moderation',
				'menu' => array(
					array(
						'phrase' => _p('delete'),
						'action' => 'delete'
					),
					array(
						'phrase' => _p('approve'),
						'action' => 'approve'
					)					
				)
			)
		);			
			
		(($sPlugin = Phpfox_Plugin::get('poll.component_controller_index_process_end')) ? eval($sPlugin) : false);
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('poll.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}