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
 * @version 		$Id: add.class.php 5481 2013-03-11 08:02:19Z Raymond_Benc $
 */
class Event_Component_Controller_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		
		$bIsEdit = false;
		$bIsSetup = ($this->request()->get('req4') == 'setup' ? true : false);
		$sAction = $this->request()->get('req3');
		$aCallback = false;		
		$sModule = $this->request()->get('module', false);
		$iItem =  $this->request()->getInt('item', false);
		$aEvent = false;
		
		if ($iEditId = $this->request()->get('id'))
		{
			if (($aEvent = Event_Service_Event::instance()->getForEdit($iEditId)))
			{
				$bIsEdit = true;
				$this->setParam('aEvent', $aEvent);
				$this->setParam(array(
						'country_child_value' => $aEvent['country_iso'],
						'country_child_id' => $aEvent['country_child_id']
					)
				);				
				$this->template()->setHeader(array(
							'<script type="text/javascript">$Behavior.eventEditCategory = function(){  var aCategories = explode(\',\', \'' . $aEvent['categories'] . '\'); for (i in aCategories) { $(\'#js_mp_holder_\' + aCategories[i]).show(); $(\'#js_mp_category_item_\' + aCategories[i]).prop(\'selected\', true); } }</script>'
						)
					)
					->assign(array(
						'aForms' => $aEvent,
						'aEvent' => $aEvent
					)
				);
				
				if ($aEvent['module_id'] != 'event')
				{
					$sModule = $aEvent['module_id'];
					$iItem = $aEvent['item_id'];	
				}
			}
		}

		if (!$bIsEdit)
        {
            Phpfox::getUserParam('event.can_create_event', true);
        }
		
		if ($sModule && $iItem && Phpfox::hasCallback($sModule, 'viewEvent'))
		{
			$aCallback = Phpfox::callback($sModule . '.viewEvent', $iItem);
            if ($aCallback === false)
            {
                return Phpfox_Error::display(_p('Cannot find the parent item.'));
            }
			$this->template()->setBreadCrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
			$this->template()->setBreadCrumb($aCallback['title'], $aCallback['url_home']);
			$bCheckParentPrivacy = true;
			if (!$bIsEdit && Phpfox::hasCallback($sModule, 'checkPermission')) {
				$bCheckParentPrivacy = Phpfox::callback($sModule . '.checkPermission' , $iItem, 'event.share_events');
			}

			if (!$bCheckParentPrivacy)
			{
				return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
			}
		}
		else if ($sModule && $iItem && $aCallback === false) {
            return Phpfox_Error::display(_p('Cannot find the parent item.'));
        }

        $aValidation = [
            'title' => _p('provide_a_name_for_this_event'),
            'location' => _p('provide_a_location_for_this_event')
        ];

        $oValidator = Phpfox_Validator::instance()->set([
                'sFormName' => 'js_event_form',
                'aParams' => $aValidation
            ]
        );
		
		if ($aVals = $this->request()->get('val'))
		{
			if ($oValidator->isValid($aVals))
			{				
				if ($bIsEdit)
				{
					if (Event_Service_Process::instance()->update($aEvent['event_id'], $aVals, $aEvent))
					{
						switch ($sAction)
						{
							case 'customize':
								$this->url()->send('event.add.invite.setup', array('id' => $aEvent['event_id']), _p('successfully_added_a_photo_to_your_event'));
								break;
							default:
								$this->url()->permalink('event', $aEvent['event_id'], $aEvent['title'], true, _p('successfully_invited_guests_to_this_event'));
								break;
						}	
					}
					else
					{
						$aVals['event_id'] = $aEvent['event_id'];
						$this->template()->assign(array('aForms' => $aVals, 'aEvent' => $aVals));
					}
				}
				else 
				{
					if (($iFlood = Phpfox::getUserParam('event.flood_control_events')) !== 0)
					{
						$aFlood = array(
							'action' => 'last_post', // The SPAM action
							'params' => array(
								'field' => 'time_stamp', // The time stamp field
								'table' => Phpfox::getT('event'), // Database table we plan to check
								'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
								'time_stamp' => $iFlood * 60 // Seconds);	
							)
						);
							 			
						// actually check if flooding
						if (Phpfox::getLib('spam')->check($aFlood))
						{
							Phpfox_Error::set(_p('you_are_creating_an_event_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
						}
					}					
					
					if (Phpfox_Error::isPassed())
					{	
						if ($iId = Event_Service_Process::instance()->add($aVals, ($aCallback !== false ? $sModule : 'event'), ($aCallback !== false ? $iItem : 0)))
						{
							$aEvent = Event_Service_Event::instance()->getForEdit($iId);
							$this->url()->permalink('event', $aEvent['event_id'], $aEvent['title'], true, _p('event_successfully_added'));
						}
					}
				}
			}

			$this->template()->assign('aForms', $aVals);		
		}

		if ($bIsEdit)
		{
			$aMenus = array(
				'detail' => _p('event_details'),
				'customize' => 'Banner',
				'invite' => _p('invite_guests')
			);

			if (!$bIsSetup)
			{
				$aMenus['manage'] = _p('manage_guest_list');
			}

			$this->template()->buildPageMenu('js_event_block', 
				$aMenus,
				array(
					'link' => $this->url()->permalink('event', $aEvent['event_id'], $aEvent['title']),
					'phrase' => _p('view_this_event')
				)				
			);		
		}
		
		$this->template()->setTitle(($bIsEdit ? _p('managing_event') . ': ' . $aEvent['title'] : _p('create_an_event')))
			->setBreadCrumb(_p('events'), ($aCallback === false ? $this->url()->makeUrl('event') : $this->url()->makeUrl($aCallback['url_home_pages'])))
			->setBreadCrumb(($bIsEdit ? _p('managing_event') . ': ' . $aEvent['title'] : _p('create_new_event')), ($bIsEdit ? $this->url()->makeUrl('event.add', array('id' => $aEvent['event_id'])) : $this->url()->makeUrl('event.add')), true)
			->setEditor()
			->setPhrase(array(
					'select_a_file_to_upload'
				)
			)				
			->setHeader('cache', array(	
					'add.js' => 'module_event',
					'progress.js' => 'static_script',					
					'country.js' => 'module_core'					
				)
			)			
			->setHeader(array(
					'<script type="text/javascript">$Behavior.eventProgressBarSettings = function(){ if ($Core.exists(\'#js_event_block_customize_holder\')) { oProgressBar = {holder: \'#js_event_block_customize_holder\', progress_id: \'#js_progress_bar\', uploader: \'#js_progress_uploader\', add_more: false, max_upload: 1, total: 1, frame_id: \'js_upload_frame\', file_id: \'image\'}; $Core.progressBarInit(); } }</script>'
				)
			)
			->assign(array(
					'sCreateJs' => $oValidator->createJS(),
					'sGetJsForm' => $oValidator->getJsForm(false),
					'bIsEdit' => $bIsEdit,
					'bIsSetup' => $bIsSetup,
					'sCategories' => Event_Service_Category_Category::instance()->get(),
					'sModule' => ($aCallback !== false ? $sModule : ''),
					'iItem' => ($aCallback !== false ? $iItem : ''),
					'aCallback' => $aCallback,
					'iMaxFileSize' => (Phpfox::getUserParam('event.max_upload_size_event') === 0 ? null : Phpfox::getLib('phpfox.file')->filesize((Phpfox::getUserParam('event.max_upload_size_event') / 1024) * 1048576)),
					'bCanSendEmails' => ($bIsEdit ? Event_Service_Event::instance()->canSendEmails($aEvent['event_id']) : false),
					'iCanSendEmailsTime' => ($bIsEdit ? Event_Service_Event::instance()->getTimeLeft($aEvent['event_id']) : false),
					'sJsEventAddCommand' => (isset($aEvent['event_id']) ? "\$Core.jsConfirm({message: '" . _p('are_you_sure', array('phpfox_squote' => true)) . "'}, function(){ $('#js_submit_upload_image').show(); $('#js_event_upload_image').show(); $('#js_event_current_image').remove(); $.ajaxCall('event.deleteImage', 'id={$aEvent['event_id']}'); },function(){}); return false;" : ''),
					'sTimeSeparator' => _p('time_separator')
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
		(($sPlugin = Phpfox_Plugin::get('event.component_controller_add_clean')) ? eval($sPlugin) : false);
	}
}