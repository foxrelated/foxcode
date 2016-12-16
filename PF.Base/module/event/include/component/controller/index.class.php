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
 * @package  		Module_Event
 * @version 		$Id: index.class.php 7268 2014-04-11 18:04:29Z Fern $
 */
class Event_Component_Controller_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::getUserParam('event.can_access_event', true);

		$aParentModule = $this->getParam('aParentModule');

		if ($aParentModule === null && $this->request()->getInt('req2') > 0)
		{
			return Phpfox_Module::instance()->setController('event.view');
		}

		if (($sLegacyTitle = $this->request()->get('req2')) && !empty($sLegacyTitle))
		{
			if ($this->request()->get('req3') != '')
			{
				$sLegacyTitle = $this->request()->get('req3');
			}

			$aLegacyItem = Core_Service_Core::instance()->getLegacyItem(array(
					'field' => array('category_id', 'name'),
					'table' => 'event_category',
					'redirect' => 'event.category',
					'title' => $sLegacyTitle,
					'search' => 'name_url'
				)
			);
		}

		if (($iRedirectId = $this->request()->getInt('redirect'))
			&& ($aEvent = Event_Service_Event::instance()->getEvent($iRedirectId, true))
			&& $aEvent['module_id'] != 'event'
			&& Phpfox::hasCallback($aEvent['module_id'], 'getEventRedirect')
		)
		{
			if (($sForward = Phpfox::callback($aEvent['module_id'] . '.getEventRedirect', $aEvent['event_id'])))
			{
				Notification_Service_Process::instance()->delete('event_invite', $aEvent['event_id'], Phpfox::getUserId());

				$this->url()->forward($sForward);
			}
		}

		if (($iDeleteId = $this->request()->getInt('delete')))
		{
			if (($mDeleteReturn = Event_Service_Process::instance()->delete($iDeleteId)))
			{
				if (is_bool($mDeleteReturn))
				{
					$this->url()->send('event', null, _p('event_successfully_deleted'));
				}
				else
				{
					$this->url()->forward($mDeleteReturn, _p('event_successfully_deleted'));
				}
			}
		}

		if (($iRedirectId = $this->request()->getInt('redirect')) && ($aEvent = Event_Service_Event::instance()->getEvent($iRedirectId, true)))
		{
			Notification_Service_Process::instance()->delete('event_invite', $aEvent['event_id'], Phpfox::getUserId());

			$this->url()->permalink('event', $aEvent['event_id'], $aEvent['title']);
		}

		$bIsUserProfile = false;
		if (defined('PHPFOX_IS_AJAX_CONTROLLER'))
		{
			$bIsUserProfile = true;
			$aUser = User_Service_User::instance()->get($this->request()->get('profile_id'));
			$this->setParam('aUser', $aUser);
		}

		if (defined('PHPFOX_IS_USER_PROFILE'))
		{
			$bIsUserProfile = true;
			$aUser = $this->getParam('aUser');
		}

		$oServiceEventBrowse = Event_Service_Browse::instance();
		$sCategory = null;
		$sView = $this->request()->get('view', false);
		$aCallback = $this->getParam('aCallback', false);

		$this->search()->set(array(
				'type' => 'event',
				'field' => 'm.event_id',
                'ignore_blocked' => true,
				'search_tool' => array(
					'default_when' => 'ongoing',
					'when_field' => 'start_time',
                    'when_end_field' => 'end_time',
					'when_upcoming' => true,
                    'when_ongoing' => true,
					'table_alias' => 'm',
					'search' => array(
						'action' => ($aParentModule === null ? ($bIsUserProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('event', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('event', array('view' => $this->request()->get('view')))) : $aParentModule['url'] . 'event/view_' . $this->request()->get('view') . '/'),
						'default_value' => _p('search_events'),
						'name' => 'search',
						'field' => 'm.title'
					),
					'sort' => array(
						'latest' => array('m.start_time', _p('latest'), 'ASC'),
						'most-liked' => array('m.total_like', _p('most_liked')),
						'most-talked' => array('m.total_comment', _p('most_discussed'))
					),
					'show' => array(12, 15, 18, 21)
				)
			)
		);

		$aBrowseParams = array(
			'module_id' => 'event',
			'alias' => 'm',
			'field' => 'event_id',
			'table' => Phpfox::getT('event'),
			'hide_view' => array('pending', 'my')
		);

		switch ($sView)
		{
			case 'pending':
				if (Phpfox::getUserParam('event.can_approve_events'))
				{
					$this->search()->setCondition('AND m.view_id = 1');
				}
				break;
			case 'my':
				Phpfox::isUser(true);
				$this->search()->setCondition('AND m.user_id = ' . Phpfox::getUserId());
				break;
			default:
				if ($bIsUserProfile)
				{
					$this->search()->setCondition('AND m.view_id ' . ($aUser['user_id'] == Phpfox::getUserId() ? 'IN(0,2)' : '= 0') . ' AND m.module_id = "event" AND m.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Core_Service_Core::instance()->getForBrowse($aUser)) . ') AND m.user_id = ' . (int) $aUser['user_id']);
				}
				elseif ($aParentModule !== null)
				{
					$this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) AND m.module_id = \'' . Phpfox_Database::instance()->escape($aParentModule['module_id']) . '\' AND m.item_id = ' . (int) $aParentModule['item_id'] . '');
				}
				else
				{
					switch ($sView)
					{
						case 'attending':
							$oServiceEventBrowse->attending(1);
							break;
						case 'may-attend':
							$oServiceEventBrowse->attending(2);
							break;
						case 'not-attending':
							$oServiceEventBrowse->attending(3);
							break;
						case 'invites':
							$oServiceEventBrowse->attending(0);
							break;
					}

					if ($sView == 'attending')
					{
						$this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%)');
					}
					else
					{
						$this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) AND m.item_id = ' . ($aCallback !== false ? (int) $aCallback['item'] : 0) . '');
					}

					if ($this->request()->getInt('user') && ($aUserSearch = User_Service_User::instance()->getUser($this->request()->getInt('user'))))
					{
						$this->search()->setCondition('AND m.user_id = ' . (int) $aUserSearch['user_id']);
						$this->template()->setBreadCrumb($aUserSearch['full_name'] . '\'s Events', $this->url()->makeUrl('event', array('user' => $aUserSearch['user_id'])), true);
					}
				}
				break;
		}

		if ($this->request()->getInt('sponsor') == 1)
		{
		    $this->search()->setCondition('AND m.is_sponsor != 1');
		    Phpfox::addMessage(_p('sponsor_help'));
		}

		if ($this->request()->get('req2') == 'category')
		{
			$sCategory = $this->request()->getInt('req3');
			$this->search()->setCondition('AND mcd.category_id = ' . (int) $sCategory);
		}

		if ($sView == 'featured')
		{
			$this->search()->setCondition('AND m.is_featured = 1');
		}

		$this->setParam('sCategory', $sCategory);

		$oServiceEventBrowse->callback($aCallback)->category($sCategory);

		$this->search()->setContinueSearch(true);
		$this->search()->browse()->params($aBrowseParams)->execute();

		$bSetFilterMenu = (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW') );
		if ($sPlugin = Phpfox_Plugin::get('event.component_controller_index_set_filter_menu_1'))
		{
			eval($sPlugin);
			if (isset($mReturnFromPlugin))
			{
				return $mReturnFromPlugin;
			}
		}

		if ($bSetFilterMenu)
		{
			$aFilterMenu = array(
				_p('all_events') => '',
				_p('my_events') => 'my'
			);

			if (Phpfox::isModule('friend') && !Phpfox::getParam('core.friends_only_community'))
			{
				$aFilterMenu[_p('friends_events')] = 'friend';
			}

			list($iTotalFeatured, $aFeatured) = Event_Service_Event::instance()->getFeatured();
			if ($iTotalFeatured)
			{
				$aFilterMenu[_p('featured_events') . '<span class="pending">' . $iTotalFeatured . '</span>'] = 'featured';
			}

			if (Phpfox::getUserParam('event.can_approve_events'))
			{
				$iPendingTotal = Event_Service_Event::instance()->getPendingTotal();

				if ($iPendingTotal)
				{
					$aFilterMenu[_p('pending_events') . '<span class="pending">' . $iPendingTotal . '</span>'] = 'pending';
				}
			}

			$aFilterMenu[] = true;

			$aFilterMenu[_p('events_i_m_attending')] = 'attending';
			$aFilterMenu[_p('events_i_may_attend')] = 'may-attend';
			$aFilterMenu[_p('events_i_m_not_attending')] = 'not-attending';
			$aFilterMenu[_p('event_invites')] = 'invites';

			$this->template()->buildSectionMenu('event', $aFilterMenu);
		}

		$this->template()->setTitle(($bIsUserProfile ? _p('full_name_s_events', array('full_name' => $aUser['full_name'])) : _p('events')))->setBreadCrumb(_p('events'), ($aCallback !== false ? $this->url()->makeUrl($aCallback['url_home'][0], array_merge($aCallback['url_home'][1], array('event'))) : ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'], 'event') : $this->url()->makeUrl('event'))))
			->setHeader('cache', array(
					'country.js' => 'module_core',
					'browse.css' => 'module_event',
				)
			)
			->assign(array(
					'aEvents' => $this->search()->browse()->getRows(),
					'sView' => $sView,
					'aCallback' => $aCallback,
					'sParentLink' => ($aCallback !== false ? $aCallback['url_home'][0] . '.' . implode('.', $aCallback['url_home'][1]) . '.event' : 'event'),
					'sApproveLink' => $this->url()->makeUrl('event', array('view' => 'pending'))
				)
			);

		if ($sCategory !== null)
		{
			$aCategories = Event_Service_Category_Category::instance()->getParentBreadcrumb($sCategory);
			$iCnt = 0;
			foreach ($aCategories as $aCategory)
			{
				$iCnt++;

				$this->template()->setTitle(Phpfox::getSoftPhrase($aCategory[0]));

				if ($aCallback !== false)
				{
					$sHomeUrl = '/' . Phpfox_Url::instance()->doRewrite($aCallback['url_home'][0]) . '/' . implode('/', $aCallback['url_home'][1]) . '/' . Phpfox_Url::instance()->doRewrite('event') . '/';
					$aCategory[1] = preg_replace('/^http:\/\/(.*?)\/' . Phpfox_Url::instance()->doRewrite('event') . '\/(.*?)$/i', 'http://\\1' . $sHomeUrl . '\\2', $aCategory[1]);
				}

				$this->template()->setBreadCrumb($aCategory[0], $aCategory[1], (empty($sView) ? true : false));
			}
		}

		if ($aCallback !== false)
		{
			$this->template()->rebuildMenu('event.index', $aCallback['url_home']);
		}

		Phpfox_Pager::instance()->set(array('page' => $this->search()->getPage(), 'size' => $this->search()->getDisplay(), 'count' => $this->search()->browse()->getCount()));

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
				'name' => 'event',
				'ajax' => 'event.moderation',
				'menu' => $aModerationMenu
			)
		);
        //Special breadcrumb for pages
        if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')){
			if (Phpfox::hasCallback(PHPFOX_PAGES_ITEM_TYPE, 'checkPermission') && !Phpfox::callback(PHPFOX_PAGES_ITEM_TYPE . '.checkPermission', $aParentModule['item_id'], 'event.view_browse_events')) {
				$this->template()->assign(['aSearchTool' => []]);
				return Phpfox_Error::display(_p('Cannot display this section due to privacy.'));
			}
			$this->template()
				->clearBreadCrumb();
			$this->template()
				->setBreadCrumb(Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']), $aParentModule['url'])
				->setBreadCrumb(_p('events'), $aParentModule['url'] . 'event/');
        }
        return null;
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('event.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}