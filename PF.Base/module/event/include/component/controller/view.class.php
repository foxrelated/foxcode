<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

define('PHPFOX_IS_EVENT_VIEW', true);

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Component
 * @version 		$Id: view.class.php 7230 2014-03-26 21:14:12Z Fern $
 */
class Event_Component_Controller_View extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if ($this->request()->get('req2') == 'view' && ($sLegacyTitle = $this->request()->get('req3')) && !empty($sLegacyTitle))
		{
            Core_Service_Core::instance()->getLegacyItem(array(
					'field' => array('event_id', 'title'),
					'table' => 'event',		
					'redirect' => 'event',
					'title' => $sLegacyTitle
				)
			);
		}		
		
		Phpfox::getUserParam('event.can_access_event', true);		
		
		$sEvent = $this->request()->get('req2');
		
		if (Phpfox::isUser() && Phpfox::isModule('notification'))
		{
			if ($this->request()->getInt('comment-id'))
			{
				Notification_Service_Process::instance()->delete('event_comment', $this->request()->getInt('comment-id'), Phpfox::getUserId());
				Notification_Service_Process::instance()->delete('event_comment_feed', $this->request()->getInt('comment-id'), Phpfox::getUserId());
				Notification_Service_Process::instance()->delete('event_comment_like', $this->request()->getInt('comment-id'), Phpfox::getUserId());
			}
			Notification_Service_Process::instance()->delete('event_like', $this->request()->getInt('req2'), Phpfox::getUserId());
			Notification_Service_Process::instance()->delete('event_invite', $this->request()->getInt('req2'), Phpfox::getUserId());
		}		
		
		if (!($aEvent = Event_Service_Event::instance()->getEvent($sEvent)))
		{
			return Phpfox_Error::display(_p('the_event_you_are_looking_for_does_not_exist_or_has_been_removed'), 404);
		}

        if (Phpfox::isUser() && User_Service_Block_Block::instance()->isBlocked(null, $aEvent['user_id']))
        {
            return Phpfox_Module::instance()->setController('error.invalid');
        }

		if (Phpfox::isModule('privacy'))
		{
			Privacy_Service_Privacy::instance()->check('event', $aEvent['event_id'], $aEvent['user_id'], $aEvent['privacy'], $aEvent['is_friend']);
		}
		
		$this->setParam('aEvent', $aEvent);
		
		$bCanPostComment = true;
		if (isset($aEvent['privacy_comment']) && $aEvent['user_id'] != Phpfox::getUserId() && !Phpfox::getUserParam('privacy.can_comment_on_all_items'))
		{
			switch ($aEvent['privacy_comment'])
			{
			    // Everyone is case 0. Skipped.
			    // Friends only
			    case 1:
			        if(Phpfox::isModule('friend') && !Friend_Service_Friend::instance()->isFriend(Phpfox::getUserId(), $aEvent['user_id']))
			        {
			            $bCanPostComment = false;
			        }
			        break;
			    // Friend of friends
			    case 2:
			        if (Phpfox::isModule('friend') && !Friend_Service_Friend::instance()->isFriendOfFriend($aEvent['user_id']))
			        {
			            $bCanPostComment = false;    
			        }
			        break;
			    // Only me
			    case 3:
			        $bCanPostComment = false;
			        break;
			}
		}
		
		$aCallback = false;
		if ($aEvent['item_id'] && Phpfox::hasCallback($aEvent['module_id'], 'viewEvent'))
		{
			$aCallback = Phpfox::callback($aEvent['module_id'] . '.viewEvent', $aEvent['item_id']);	
			$this->template()->setBreadCrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
			$this->template()->setBreadCrumb($aCallback['title'], $aCallback['url_home']);
			if(isset($aEvent['module_id']) && Phpfox::isModule($aEvent['module_id']) && Phpfox::hasCallback($aEvent['module_id'], 'checkPermission'))
			{
                if(!Phpfox::callback($aEvent['module_id'] . '.checkPermission', $aEvent['item_id'], 'event.view_browse_events'))
				{
					return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
				}
			}
		}		
		
		if (Phpfox::getUserId())
		{
			$bIsBlocked = User_Service_Block_Block::instance()->isBlocked($aEvent['user_id'], Phpfox::getUserId());
			if ($bIsBlocked)
			{
				$bCanPostComment = false;
			}
		}
		
		$this->setParam('aFeedCallback', array(
				'module' => 'event',
				'table_prefix' => 'event_',
				'ajax_request' => 'event.addFeedComment',
				'item_id' => $aEvent['event_id'],
				'disable_share' => ($bCanPostComment ? false : true),
                'disable_sort' => true
			)
		);
		
		if ($aEvent['view_id'] == '1')
		{
			$this->template()->setHeader('<script type="text/javascript">$Behavior.eventIsPending = function(){ $(\'#js_block_border_feed_display\').addClass(\'js_moderation_on\').hide(); }</script>');
		}
		
		if (Phpfox::getUserId() == $aEvent['user_id'])
		{
			if (Phpfox::isModule('notification'))
			{
				Notification_Service_Process::instance()->delete('event_approved', $this->request()->getInt('req2'), Phpfox::getUserId());
			}
			
			define('PHPFOX_FEED_CAN_DELETE', true);
		}					
			
		$this->template()->setTitle($aEvent['title'])
            ->setMeta('description', $aEvent['title'])
			->setMeta('description', $aEvent['description'])
			->setMeta('keywords', $this->template()->getKeywords($aEvent['title']))
			->setBreadCrumb(_p('events'), ($aCallback === false ? $this->url()->makeUrl('event') : $this->url()->makeUrl($aCallback['url_home_pages'])))
			->setBreadCrumb($aEvent['title'], $this->url()->permalink('event', $aEvent['event_id'], $aEvent['title']), true)
			->setEditor(array(
					'load' => 'simple'
				)
			)
			->setHeader('cache', array(
					'jquery/plugin/jquery.highlightFade.js' => 'static_script',	
					'jquery/plugin/jquery.scrollTo.js' => 'static_script',
					'feed.js' => 'module_feed',
					'event.js' => 'module_event'
				)
			)
			->assign(array(
					'aEvent' => $aEvent,
					'aCallback' => $aCallback,
					'sMicroPropType' => 'Event'
				)
			);

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

		(($sPlugin = Phpfox_Plugin::get('event.component_controller_view_process_end')) ? eval($sPlugin) : false);
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('event.component_controller_view_clean')) ? eval($sPlugin) : false);
	}
}
