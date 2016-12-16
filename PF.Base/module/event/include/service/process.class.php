<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Event
 */
class Event_Service_Process extends Phpfox_Service 
{
    /**
     * @var bool
     */
	private $_bHasImage = false;
    
    /**
     * @var array
     */
	private $_aInvited = array();
    
    /**
     * @var array
     */
	private $_aCategories = array();
    
    /**
     * @var bool
     */
	private $_bIsEndingInThePast = false;
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('event');
	}
    
    /**
     * @param array  $aVals
     * @param string $sModule
     * @param int    $iItem
     *
     * @return int
     */
	public function add($aVals, $sModule = 'event', $iItem = 0)
	{
        if (!$this->_verify($aVals)) {
            return false;
        }
        
        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }
		
		$oParseInput = Phpfox::getLib('parse.input');
        Ban_Service_Ban::instance()->checkAutomaticBan($aVals);
					
		$iStartTime = Phpfox::getLib('date')->mktime($aVals['start_hour'], $aVals['start_minute'], 0, $aVals['start_month'], $aVals['start_day'], $aVals['start_year']);		
		if ($this->_bIsEndingInThePast === true)
		{
			$aVals['end_hour'] = ($aVals['start_hour'] + 1);
			$aVals['end_minute'] = $aVals['start_minute'];
			$aVals['end_day'] = $aVals['start_day'];
			$aVals['end_year'] = $aVals['start_year'];			
		}
		
		$iEndTime = Phpfox::getLib('date')->mktime($aVals['end_hour'], $aVals['end_minute'], 0, $aVals['end_month'], $aVals['end_day'], $aVals['end_year']);				
		
		if ($iStartTime > $iEndTime)
		{
			$iEndTime = $iStartTime;
		}
				
		$aSql = array(
			'view_id' => (($sModule == 'event' && Phpfox::getUserParam('event.event_must_be_approved')) ? '1' : '0'),
			'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
			'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
			'module_id' => $sModule,
			'item_id' => $iItem,
			'user_id' => Phpfox::getUserId(),
			'title' => $oParseInput->clean($aVals['title'], 255),
			'location' => $oParseInput->clean($aVals['location'], 255),
			'country_iso' => (empty($aVals['country_iso']) ? Phpfox::getUserBy('country_iso') : $aVals['country_iso']),
			'country_child_id' => (isset($aVals['country_child_id']) ? (int) $aVals['country_child_id'] : 0),
			'postal_code' => (empty($aVals['postal_code']) ? null : Phpfox::getLib('parse.input')->clean($aVals['postal_code'], 20)),
			'city' => (empty($aVals['city']) ? null : $oParseInput->clean($aVals['city'], 255)),
			'time_stamp' => PHPFOX_TIME,
			'start_time' => Phpfox::getLib('date')->convertToGmt($iStartTime),
			'end_time' => Phpfox::getLib('date')->convertToGmt($iEndTime),
			'start_gmt_offset' => Phpfox::getLib('date')->getGmtOffset($iStartTime),
			'end_gmt_offset' => Phpfox::getLib('date')->getGmtOffset($iEndTime),
			'address' => (empty($aVals['address']) ? null : Phpfox::getLib('parse.input')->clean($aVals['address']))
		);
		
		if ($sPlugin = Phpfox_Plugin::get('event.service_process_add__start')){return eval($sPlugin);}
        
        if (!Phpfox_Error::isPassed()) {
            return false;
        }
        
        $iId = $this->database()->insert($this->_sTable, $aSql);
        
        if (!$iId) {
            return false;
        }
		
		$this->database()->insert(Phpfox::getT('event_text'), array(
				'event_id' => $iId,
				'description' => (empty($aVals['description']) ? null : $oParseInput->clean($aVals['description'])),
				'description_parsed' => (empty($aVals['description']) ? null : $oParseInput->prepare($aVals['description']))
			)
		);		
		
		foreach ($this->_aCategories as $iCategoryId)
		{
			$this->database()->insert(Phpfox::getT('event_category_data'), array('event_id' => $iId, 'category_id' => $iCategoryId));
		}		
		
		$bAddFeed = ($sModule == 'event' ? (Phpfox::getUserParam('event.event_must_be_approved') ? false : true) : true);
		
		if ($bAddFeed === true)
		{
			if ($sModule == 'event' && Phpfox::isModule('feed'))
			{
				Feed_Service_Process::instance()->add('event', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0));
			}
			else {
				if (Phpfox::isModule('feed'))
				{
					Feed_Service_Process::instance()
						->callback(Phpfox::callback($sModule . '.getFeedDetails', $iItem))
						->add('event', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0), $iItem);

				}

				//support add notification for parent module
				if (Phpfox::isModule('notification') && $sModule != 'event' && Phpfox::isModule($sModule) && Phpfox::hasCallback($sModule, 'addItemNotification'))
				{
					Phpfox::callback($sModule . '.addItemNotification', ['page_id' => $iItem, 'item_perm' => 'event.who_can_view_browse_events', 'item_type' => 'event', 'item_id' => $iId, 'owner_id' => Phpfox::getUserId()]);
				}
			}
			
			User_Service_Activity::instance()->update(Phpfox::getUserId(), 'event');
		}
		
		$this->addRsvp($iId, 1, Phpfox::getUserId());

		$sCacheId = $this->cache()->set(array('events', Phpfox::getUserId()));
		$this->cache()->remove($sCacheId);
		if (Phpfox::getParam('event.cache_events_per_user'))
		{
			$sCacheId = $this->cache()->set(array('events_by_user', Phpfox::getUserId()));
			$this->cache()->remove($sCacheId);
		}

		if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support'))
		{
            Tag_Service_Process::instance()->add('event', $iId, Phpfox::getUserId(), $aVals['description'], true);
		}

        // Plugin call
		if ($sPlugin = Phpfox_Plugin::get('event.service_process_add__end')){eval($sPlugin);}

		return $iId;
	}
    
    /**
     * @param int        $iId
     * @param array      $aVals
     * @param null|array $aEventPost
     *
     * @return bool
     */
	public function update($iId, $aVals, $aEventPost = null)
	{
        if (!$this->_verify($aVals, true)) {
            return false;
        }
        
        if (isset($aEventPost) && isset($aEventPost['is_featured']) && $aEventPost['is_featured']) {
            $this->cache()->remove('event_featured', 'substr');
        }
        
        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }
        
        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

		$oParseInput = Phpfox::getLib('parse.input');
        
        Ban_Service_Ban::instance()->checkAutomaticBan($aVals['title'] . ' ' . $aVals['description']);

		$iStartTime = Phpfox::getLib('date')->mktime($aVals['start_hour'], $aVals['start_minute'], 0, $aVals['start_month'], $aVals['start_day'], $aVals['start_year']);
		$iEndTime = Phpfox::getLib('date')->mktime($aVals['end_hour'], $aVals['end_minute'], 0, $aVals['end_month'], $aVals['end_day'], $aVals['end_year']);
        
        if ($iStartTime > $iEndTime) {
            $iEndTime = $iStartTime;
        }
        
		$aSql = array(
			'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
			'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
			'title' => $oParseInput->clean($aVals['title'], 255),
			'location' => $oParseInput->clean($aVals['location'], 255),
			'country_iso' => $aVals['country_iso'],
			'country_child_id' => (isset($aVals['country_child_id']) ? Core_Service_Country_Country::instance()->getValidChildId($aVals['country_iso'], (int) $aVals['country_child_id']) : 0),
			'city' => (empty($aVals['city']) ? null : $oParseInput->clean($aVals['city'], 255)),
			'postal_code' => (empty($aVals['postal_code']) ? null : Phpfox::getLib('parse.input')->clean($aVals['postal_code'], 20)),
			'start_time' => Phpfox::getLib('date')->convertToGmt($iStartTime),
			'end_time' => Phpfox::getLib('date')->convertToGmt($iEndTime),
			'start_gmt_offset' => Phpfox::getLib('date')->getGmtOffset($iStartTime),
			'end_gmt_offset' => Phpfox::getLib('date')->getGmtOffset($iEndTime),
			'address' => (empty($aVals['address']) ? null : Phpfox::getLib('parse.input')->clean($aVals['address']))
		);

		if ($this->_bHasImage)
		{
			$oImage = Phpfox_Image::instance();

			$sFileName = Phpfox_File::instance()->upload('image', Phpfox::getParam('event.dir_image'), $iId);
			$iFileSizes = filesize(Phpfox::getParam('event.dir_image') . sprintf($sFileName, ''));

			$aSql['image_path'] = $sFileName;
			$aSql['server_id'] = Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID');

			$oImage->createThumbnail(Phpfox::getParam('event.dir_image') . sprintf($sFileName, ''), Phpfox::getParam('event.dir_image') . sprintf($sFileName, ''), 600, 400);

			// Update user space usage
			User_Service_Space::instance()->update(Phpfox::getUserId(), 'event', $iFileSizes);
		}
		if ($sPlugin = Phpfox_Plugin::get('event.service_process_update__start')){return eval($sPlugin);}
		$this->database()->update($this->_sTable, $aSql, 'event_id = ' . (int) $iId);

		$this->database()->update(Phpfox::getT('event_text'), array(
				'description' => (empty($aVals['description']) ? null : $oParseInput->clean($aVals['description'])),
				'description_parsed' => (empty($aVals['description']) ? null : $oParseInput->prepare($aVals['description']))
			), 'event_id = ' . (int) $iId
		);

		$aEvent = $this->database()->select('event_id, user_id, title, module_id')
			->from($this->_sTable)
			->where('event_id = ' . (int) $iId)
			->execute('getSlaveRow');

		if (isset($aVals['emails']) || isset($aVals['invite']))
		{
			$aInvites = $this->database()->select('invited_user_id, invited_email')
				->from(Phpfox::getT('event_invite'))
				->where('event_id = ' . (int) $iId)
				->execute('getSlaveRows');
			$aInvited = array();
			foreach ($aInvites as $aInvite)
			{
				$aInvited[(empty($aInvite['invited_email']) ? 'user' : 'email')][(empty($aInvite['invited_email']) ? $aInvite['invited_user_id'] : $aInvite['invited_email'])] = true;
			}
		}

		if (isset($aVals['emails']))
		{
			// if (strpos($aVals['emails'], ','))
			{
				$aEmails = explode(',', $aVals['emails']);
				$aCachedEmails = array();
				foreach ($aEmails as $sEmail)
				{
					$sEmail = trim($sEmail);
					if (!Phpfox::getLib('mail')->checkEmail($sEmail))
					{
						continue;
					}

					if (isset($aInvited['email'][$sEmail]))
					{
						continue;
					}

					$sLink = Phpfox_Url::instance()->permalink('event', $aEvent['event_id'], $aEvent['title']);

					$sMessage = _p('full_name_invited_you_to_the_title', array(
							'full_name' => Phpfox::getUserBy('full_name'),
							'title' => $oParseInput->clean($aVals['title'], 255),
							'link' => $sLink
						)
					);
					if (!empty($aVals['personal_message']))
					{
						$sMessage .= _p('full_name_added_the_following_personal_message', array(
								'full_name' => Phpfox::getUserBy('full_name')
							)
						) . "\n";
						$sMessage .= $aVals['personal_message'];
					}
					$oMail = Phpfox::getLib('mail');
					if (isset($aVals['invite_from']) && $aVals['invite_from'] == 1)
					{
						$oMail->fromEmail(Phpfox::getUserBy('email'))
								->fromName(Phpfox::getUserBy('full_name'));
					}
					$bSent = $oMail->to($sEmail)
						->subject(array('event.full_name_invited_you_to_the_event_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $oParseInput->clean($aVals['title'], 255))))
						->message($sMessage)
						->send();

					if ($bSent)
					{
						$this->_aInvited[] = array('email' => $sEmail);

						$aCachedEmails[$sEmail] = true;

						$this->database()->insert(Phpfox::getT('event_invite'), array(
								'event_id' => $iId,
								'type_id' => 1,
								'user_id' => Phpfox::getUserId(),
								'invited_email' => $sEmail,
								'time_stamp' => PHPFOX_TIME
							)
						);
					}
				}
			}
		}

		if (isset($aVals['invite']) && is_array($aVals['invite']))
		{
			$sUserIds = '';
			foreach ($aVals['invite'] as $iUserId)
			{
				if (!is_numeric($iUserId))
				{
					continue;
				}
				$sUserIds .= $iUserId . ',';
			}
			$sUserIds = rtrim($sUserIds, ',');

			$aUsers = $this->database()->select('user_id, email, language_id, full_name')
				->from(Phpfox::getT('user'))
				->where('user_id IN(' . $sUserIds . ')')
				->execute('getSlaveRows');

			foreach ($aUsers as $aUser)
			{
				if (isset($aCachedEmails[$aUser['email']]))
				{
					continue;
				}

				if (isset($aInvited['user'][$aUser['user_id']]))
				{
					continue;
				}

                if (Phpfox::isModule('friend') && !Friend_Service_Friend::instance()->isFriend(Phpfox::getUserId(), $aUser['user_id']))
                {
                    continue;
                }

				$sLink = Phpfox_Url::instance()->permalink('event', $aEvent['event_id'], $aEvent['title']);

				$sMessage = _p('full_name_invited_you_to_the_title', array(
						'full_name' => Phpfox::getUserBy('full_name'),
						'title' => $oParseInput->clean($aVals['title'], 255),
						'link' => $sLink
					), false,null, $aUser['language_id']);
				if (!empty($aVals['personal_message']))
				{
					$sMessage .= _p('full_name_added_the_following_personal_message', array(
							'full_name' => Phpfox::getUserBy('full_name')
						), false, null, $aUser['language_id']
					) .":\n". $aVals['personal_message'];
				}
				$bSent = Phpfox::getLib('mail')->to($aUser['user_id'])
					->subject(array('event.full_name_invited_you_to_the_event_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $oParseInput->clean($aVals['title'], 255))))
					->message($sMessage)
					->notification('event.invite_to_event')
					->send();

				if ($bSent)
				{
					$this->_aInvited[] = array('user' => $aUser['full_name']);

					$iInviteId = $this->database()->insert(Phpfox::getT('event_invite'), array(
							'event_id' => $iId,
							'user_id' => Phpfox::getUserId(),
							'invited_user_id' => $aUser['user_id'],
							'time_stamp' => PHPFOX_TIME
						)
					);

					(Phpfox::isModule('request') ? Request_Service_Process::instance()->add('event_invite', $iId, $aUser['user_id']) : null);
				}
			}
		}

		$this->database()->delete(Phpfox::getT('event_category_data'), 'event_id = ' . (int) $iId);
		foreach ($this->_aCategories as $iCategoryId)
		{
			$this->database()->insert(Phpfox::getT('event_category_data'), array('event_id' => $iId, 'category_id' => $iCategoryId));
		}

		if (empty($aEvent['module_id']) || $aEvent['module_id'] == 'event')
		{
			(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->update('event', $iId, $aVals['privacy'], $aVals['privacy_comment'], 0, $aEvent['user_id']) : null);
		}

		Feed_Service_Process::instance()->clearCache('event', $iId);

		(($sPlugin = Phpfox_Plugin::get('event.service_process_update__end')) ? eval($sPlugin) : false);

		if (Phpfox::getParam('event.cache_events_per_user'))
		{
			$sCacheId = $this->cache()->set(array('events_by_user', $aEvent['user_id']));
			$this->cache()->remove($sCacheId);
		}

		if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support'))
		{
            Tag_Service_Process::instance()->update('event', $aEvent['event_id'], $aEvent['user_id'], $aVals['description'], true);
		}
		
		return true;
	}
    
    /**
     * @param int $iId
     *
     * @return bool
     */
	public function deleteImage($iId)
	{
		$aEvent = $this->database()->select('user_id, image_path, server_id')
			->from($this->_sTable)
			->where('event_id = ' . (int) $iId)
			->execute('getSlaveRow');
        
        if (!isset($aEvent['user_id'])) {
            return Phpfox_Error::set(_p('unable_to_find_the_event'));
        }
        
        if (!User_Service_Auth::instance()
            ->hasAccess('event', 'event_id', $iId, 'event.can_edit_own_event', 'event.can_edit_other_event', $aEvent['user_id'])
        ) {
            return Phpfox_Error::set(_p('you_do_not_have_sufficient_permission_to_modify_this_event'));
        }
		
		if (!empty($aEvent['image_path']))
		{
			$aImages = array(
				Phpfox::getParam('event.dir_image') . sprintf($aEvent['image_path'], ''),
				Phpfox::getParam('event.dir_image') . sprintf($aEvent['image_path'], '_50'),
				Phpfox::getParam('event.dir_image') . sprintf($aEvent['image_path'], '_120'),
				Phpfox::getParam('event.dir_image') . sprintf($aEvent['image_path'], '_200')
			);			
			
			$iFileSizes = 0;
			foreach ($aImages as $sImage)
			{
				if (file_exists($sImage))
				{
					$iFileSizes += filesize($sImage);
					
					Phpfox_File::instance()->unlink($sImage);
				}
				
				if(Phpfox::getParam('core.allow_cdn') && $aEvent['server_id'] > 0)
				{
					// Get the file size stored when the photo was uploaded
					$sTempUrl = Phpfox::getLib('cdn')->getUrl(str_replace(Phpfox::getParam('event.dir_image'), Phpfox::getParam('event.url_image'), $sImage));
					
					$aHeaders = get_headers($sTempUrl, true);
					if(preg_match('/200 OK/i', $aHeaders[0]))
					{
						$iFileSizes += (int) $aHeaders["Content-Length"];
					}
					
					Phpfox::getLib('cdn')->remove($sImage);
				}
			}
			
			if ($iFileSizes > 0)
			{
				User_Service_Space::instance()->update($aEvent['user_id'], 'event', $iFileSizes, '-');
			}
		}

		$this->database()->update($this->_sTable, array('image_path' => null), 'event_id = ' . (int) $iId);	
		
		(($sPlugin = Phpfox_Plugin::get('event.service_process_deleteimage__end')) ? eval($sPlugin) : false);
		return true;
	}
    
    /**
     * @param int $iEvent
     * @param int $iRsvp
     * @param int $iUserId
     *
     * @return bool
     */
	public function addRsvp($iEvent, $iRsvp, $iUserId)
	{
        if (!Phpfox::isUser()) {
            return false;
        }
		
		if (($iInviteId = $this->database()->select('invite_id')
			->from(Phpfox::getT('event_invite'))
			->where('event_id = ' . (int) $iEvent . ' AND invited_user_id = ' . (int) $iUserId)
			->execute('getSlaveField')))
		{
			$this->database()->update(Phpfox::getT('event_invite'), array(
					'rsvp_id' => $iRsvp,					
					'invited_user_id' => $iUserId,
					'time_stamp' => PHPFOX_TIME
				), 'invite_id = ' . $iInviteId
			);
			
			(Phpfox::isModule('request') ? Request_Service_Process::instance()->delete('event_invite', $iEvent, $iUserId) : false);
		}
		else 
		{
			$this->database()->insert(Phpfox::getT('event_invite'), array(
					'event_id' => $iEvent,			
					'rsvp_id' => $iRsvp,					
					'user_id' => $iUserId,
					'invited_user_id' => $iUserId,
					'time_stamp' => PHPFOX_TIME
				)
			);
		}
		
		return true;
	}
    
    /**
     * @param int $iInviteId
     *
     * @return bool
     */
	public function deleteGuest($iInviteId)
	{
		$aEvent = $this->database()->select('e.event_id, e.user_id')
			->from(Phpfox::getT('event_invite'), 'ei')
			->join($this->_sTable, 'e', 'e.event_id = ei.event_id')
			->where('ei.invite_id = ' . (int) $iInviteId)
			->execute('getSlaveRow');
			
		if (!isset($aEvent['user_id']))
		{
			return Phpfox_Error::set(_p('unable_to_find_the_event'));
		}
			
		if (!User_Service_Auth::instance()->hasAccess('event', 'event_id', $aEvent['event_id'], 'event.can_edit_own_event', 'event.can_edit_other_event', $aEvent['user_id']))
		{
			return Phpfox_Error::set(_p('you_do_not_have_sufficient_permission_to_modify_this_event'));
		}	

		$this->database()->delete(Phpfox::getT('event_invite'), 'invite_id = ' . (int) $iInviteId);	
			
		return true;
	}
    
    /**
     * @param int        $iId
     * @param null|array $aEvent
     *
     * @return bool|mixed|string
     */
	public function delete($iId, &$aEvent = null)
	{
		if ($sPlugin = Phpfox_Plugin::get('event.service_process_delete__start')){return eval($sPlugin);}
	
		$mReturn = true;
		if ($aEvent === null)
		{
			$aEvent = $this->database()->select('user_id, module_id, item_id, image_path, is_sponsor, is_featured, server_id')
				->from($this->_sTable)
				->where('event_id = ' . (int) $iId)
				->execute('getSlaveRow');

            if (empty($aEvent))
            {
                return Phpfox_Error::set(_p('unable_to_find_the_event_you_want_to_delete'));
            }
			
			if ($aEvent['module_id'] == 'pages' && Pages_Service_Pages::instance()->isAdmin($aEvent['item_id']))
			{
				$mReturn = Pages_Service_Pages::instance()->getUrl($aEvent['item_id']) . 'event/';
			}
			else
			{
				if (!isset($aEvent['user_id']))
				{
					return Phpfox_Error::set(_p('unable_to_find_the_event_you_want_to_delete'));
				}

				if (!User_Service_Auth::instance()->hasAccess('event', 'event_id', $iId, 'event.can_delete_own_event', 'event.can_delete_other_event', $aEvent['user_id']))
				{
					return Phpfox_Error::set(_p('You don\'t have permission to {{ action }} this {{ item }}.', ['action' => _p('delete__l'), 'item' => _p('event__l')]));
				}
			}
		}
		
		if (!empty($aEvent['image_path']))
		{
			$aImages = array(
				Phpfox::getParam('event.dir_image') . sprintf($aEvent['image_path'], ''),
				Phpfox::getParam('event.dir_image') . sprintf($aEvent['image_path'], '_50'),
				Phpfox::getParam('event.dir_image') . sprintf($aEvent['image_path'], '_120'),
				Phpfox::getParam('event.dir_image') . sprintf($aEvent['image_path'], '_200')
			);			
			
			$iFileSizes = 0;
			foreach ($aImages as $sImage)
			{
				if (file_exists($sImage))
				{
					$iFileSizes += filesize($sImage);
					if ($sPlugin = Phpfox_Plugin::get('event.service_process_delete__pre_unlink')){return eval($sPlugin);}
					Phpfox_File::instance()->unlink($sImage);
				}
				
				if(Phpfox::getParam('core.allow_cdn') && $aEvent['server_id'] > 0)
				{
					// Get the file size stored when the photo was uploaded
					$sTempUrl = Phpfox::getLib('cdn')->getUrl(str_replace(Phpfox::getParam('event.dir_image'), Phpfox::getParam('event.url_image'), $sImage));
					
					$aHeaders = get_headers($sTempUrl, true);
					if(preg_match('/200 OK/i', $aHeaders[0]))
					{
						$iFileSizes += (int) $aHeaders["Content-Length"];
					}
					if ($sPlugin = Phpfox_Plugin::get('event.service_process_delete__pre_unlink')){return eval($sPlugin);}
					Phpfox::getLib('cdn')->remove($sImage);
				}
			}
			
			if ($iFileSizes > 0)
			{
				if ($sPlugin = Phpfox_Plugin::get('event.service_process_delete__pre_space_update')){return eval($sPlugin);}
				User_Service_Space::instance()->update($aEvent['user_id'], 'event', $iFileSizes, '-');
			}
		}
		
		if ($sPlugin = Phpfox_Plugin::get('event.service_process_delete__pre_deletes')){return eval($sPlugin);}
		
		(Phpfox::isModule('comment') ? Comment_Service_Process::instance()->deleteForItem(null, $iId, 'event') : null);
		(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->delete('event', $iId) : null);
		(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->delete('comment_event', $iId) : null);
		(Phpfox::isModule('like') ? Like_Service_Process::instance()->delete('event',(int) $iId, 0, true) : null);
        (Phpfox::isModule('notification') ? Notification_Service_Process::instance()->deleteAllOfItem(['event_like', 'event_comment', 'event_invite'],(int) $iId) : null);
		
		$aInvites = $this->database()->select('invite_id, invited_user_id')
			->from(Phpfox::getT('event_invite'))
			->where('event_id = ' . (int) $iId)
			->execute('getSlaveRows');
		foreach ($aInvites as $aInvite)
		{
			(Phpfox::isModule('request') ? Request_Service_Process::instance()->delete('event_invite', $aInvite['invite_id'], $aInvite['invited_user_id']) : false);
		}		
		
		$this->database()->delete($this->_sTable, 'event_id = ' . (int) $iId);
		$this->database()->delete(Phpfox::getT('event_text'), 'event_id = ' . (int) $iId);
		$this->database()->delete(Phpfox::getT('event_category_data'), 'event_id = ' . (int) $iId);
		$this->database()->delete(Phpfox::getT('event_invite'), 'event_id = ' . (int) $iId);
		$iTotalEvent = $this->database()
                        ->select('total_event')
                        ->from(Phpfox::getT('user_field'))
                        ->where('user_id =' . (int)$aEvent['user_id'])->execute('getSlaveField');
        $iTotalEvent = $iTotalEvent -1;
        
		if ($iTotalEvent > 0)
		{
			$this->database()->update(Phpfox::getT('user_field'),
                        array('total_event' => $iTotalEvent),
                        'user_id = ' . (int)$aEvent['user_id']);
		}
        
		if (isset($aEvent['is_sponsor']) && $aEvent['is_sponsor'] == 1)
		{
			$this->cache()->remove('event_sponsored');
		}
		if (isset($aEvent['is_featured']) && $aEvent['is_featured'])
		{
			$this->cache()->remove('event_featured', 'substr');
		}
		if ($sPlugin = Phpfox_Plugin::get('event.service_process_delete__end')){return eval($sPlugin);}
		
		
		$sCacheId = $this->cache()->set(array('events', Phpfox::getUserId()));
		$this->cache()->remove($sCacheId);
		
		if (Phpfox::getParam('event.cache_events_per_user'))
		{
			$sCacheId = $this->cache()->set(array('events_by_user', $aEvent['user_id']));
			$this->cache()->remove($sCacheId);
		}
		
		return $mReturn;
	}
    
    /**
     * @param int $iId
     * @param int $iType
     *
     * @return bool
     */
	public function feature($iId, $iType)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('event.can_feature_events', true);
		
		$this->database()->update($this->_sTable, array('is_featured' => ($iType ? '1' : '0')), 'event_id = ' . (int) $iId);
		
		$this->cache()->remove('event_featured', 'substr');
		
		return true;
	}
    
    /**
     * @param int $iId
     * @param int $iType
     *
     * @return bool|mixed
     */
	public function sponsor($iId, $iType)
	{
	    if (!Phpfox::getUserParam('event.can_sponsor_event') && !Phpfox::getUserParam('event.can_purchase_sponsor') && !defined('PHPFOX_API_CALLBACK'))
	    {
			return Phpfox_Error::set(_p('hack_attempt'));
	    }
	    
	    $iType = (int)$iType;
	    if ($iType != 1 && $iType != 0)
	    {
			return false;
	    }
	    
	    $this->database()->update($this->_sTable, array('is_sponsor' => $iType), 'event_id = ' . (int)$iId);

	    $this->cache()->remove('event_sponsored', 'substr');
	    
	    if ($sPlugin = Phpfox_Plugin::get('event.service_process_sponsor__end')){return eval($sPlugin);}
	    
	    return true;
	}
    
    /**
     * @param int $iId
     *
     * @return bool
     */
	public function approve($iId)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('event.can_approve_events', true);
		
		$aEvent = $this->database()->select('v.*, ' . Phpfox::getUserField())
			->from($this->_sTable, 'v')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->where('v.event_id = ' . (int) $iId)
			->executeRow();
			
		if (!isset($aEvent['event_id']))
		{
			return Phpfox_Error::set(_p('unable_to_find_the_event_you_want_to_approve'));
		}
		
		$this->database()->update($this->_sTable, array('view_id' => '0'), 'event_id = ' . $aEvent['event_id']);
		
		if (Phpfox::isModule('notification'))
		{
			Notification_Service_Process::instance()->add('event_approved', $aEvent['event_id'], $aEvent['user_id']);
		}
		
		// Send the user an email
		$sLink = Phpfox_Url::instance()->permalink('event' , $aEvent['event_id'], $aEvent['title']);
		
		Phpfox::getLib('mail')->to($aEvent['user_id'])
			->subject(array('event.your_event_has_been_approved_on_site_title', array('site_title' => Phpfox::getParam('core.site_title'))))
			->message(array('event.your_event_has_been_approved_on_site_title_link', array('site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)))
			->notification('event.event_is_approved')
			->send();				

		User_Service_Activity::instance()->update($aEvent['user_id'], 'event');
		
		$this->addRsvp($aEvent['event_id'], 1, $aEvent['user_id']);
		
		$bAddFeed = true;
		(($sPlugin = Phpfox_Plugin::get('event.service_process_approve__1')) ? eval($sPlugin) : false);
		
		if ($bAddFeed)
		{
			(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->add('event', $aEvent['event_id'], $aEvent['privacy'], $aEvent['privacy_comment'], 0, $aEvent['user_id']) : null);
		}
		
		return true;	
	}
    
    /**
     * @param int    $iId
     * @param int    $iPage
     * @param string $sSubject
     * @param string $sText
     *
     * @return bool|mixed
     */
	public function massEmail($iId, $iPage, $sSubject, $sText)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('event.can_mass_mail_own_members', true);
		
		$aEvent = Event_Service_Event::instance()->getEvent($iId, true);
		
		if (!isset($aEvent['event_id']))
		{
			return false;
		}
		
		if ($aEvent['user_id'] != Phpfox::getUserId())
		{
			return false;
		}
		if ($sPlugin = Phpfox_Plugin::get('event.service_process_massemail__start')){return eval($sPlugin);}
        Ban_Service_Ban::instance()->checkAutomaticBan($sText);
		list($iCnt, $aGuests) = Event_Service_Event::instance()->getInvites($iId, 1, $iPage, 20);
		
		$sLink = Phpfox_Url::instance()->permalink('event' , $aEvent['event_id'], $aEvent['title']);
		
		$sText = '<br />
		' . _p('notice_this_is_a_newsletter_sent_from_the_event') . ': ' . $aEvent['title'] . '<br />
		<a href="' . $sLink . '">' . $sLink . '</a>
		<br /><br />
		' . $sText;
		
		foreach ($aGuests as $aGuest)
		{
			if ($aGuest['user_id'] == Phpfox::getUserId())
			{
				continue;
			}
			
			Phpfox::getLib('mail')->to($aGuest['user_id'])
				->subject($sSubject)
				->message($sText)
				->notification('event.mass_emails')
				->send();			
		}
		if ($sPlugin = Phpfox_Plugin::get('event.service_process_massemail__end')){return eval($sPlugin);}
		$this->database()->update($this->_sTable, array('mass_email' => PHPFOX_TIME), 'event_id = ' . $aEvent['event_id']);
		
		return $iCnt;
	}
    
    /**
     * @param int $iId
     *
     * @return bool
     */
	public function removeInvite($iId)
	{
		$this->database()->delete(Phpfox::getT('event_invite'), 'event_id = ' . (int) $iId . ' AND invited_user_id = ' . Phpfox::getUserId());
		
		(Phpfox::isModule('request') ? Request_Service_Process::instance()->delete('event_invite', $iId, Phpfox::getUserId()) : false);
		
		return true;
	}
    
    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod    is the name of the method
     * @param array  $aArguments is the array of arguments of being passed
     *
     * @return null
     */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('event.service_process__call'))
		{
			eval($sPlugin);
            return null;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
    
    /**
     * @param array $aVals
     * @param bool  $bIsUpdate
     *
     * @return bool
     */
	private function _verify(&$aVals, $bIsUpdate = false)
	{				
		if (isset($aVals['category'])) {
            if (!is_array($aVals['category']))
            {
                $aVals['category'] = [$aVals['category']];
            }
		    $aCategories = array_filter($aVals['category']);
            $iCategoryId = end($aCategories);
            if ($iCategoryId)
            {
                $sCategories = Event_Service_Category_Category::instance()->getParentCategories($iCategoryId);
                if (empty($sCategories))
                {
                    return Phpfox_Error::set(_p('The {{ item }} cannot be found.', ['item' => _p('category__l')]));
                }
                $this->_aCategories = explode(',', trim($sCategories, ','));
            }
		}
		
		if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != ''))
		{
			$aImage = Phpfox_File::instance()->load('image', array(
					'jpg',
					'gif',
					'png'
				), (Phpfox::getUserParam('event.max_upload_size_event') === 0 ? null : (Phpfox::getUserParam('event.max_upload_size_event') / 1024))
			);
			
			if ($aImage === false)
			{
				return false;
			}
			
			$this->_bHasImage = true;
		}

		$iStartTime = Phpfox::getLib('date')->mktime($aVals['start_hour'], $aVals['start_minute'], 0, $aVals['start_month'], $aVals['start_day'], $aVals['start_year']);
		$iEndTime = Phpfox::getLib('date')->mktime($aVals['end_hour'], $aVals['end_minute'], 0, $aVals['end_month'], $aVals['end_day'], $aVals['end_year']);

		if ($iEndTime < $iStartTime)
		{
			$this->_bIsEndingInThePast = true;
		}
		return true;
	}
}