<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Event
 * @version 		$Id: event.class.php 6139 2013-06-24 15:02:48Z Raymond_Benc $
 */
class Event_Service_Event extends Phpfox_Service 
{
    /**
     * @var bool|array
     */
	private $_aCallback = false;
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('event');
	}
    
    /**
     * @param array $aCallback
     *
     * @return $this
     */
	public function callback($aCallback)
	{
		$this->_aCallback = $aCallback;
		return $this;
	}
    
    /**
     * @param string $sEvent
     * @param bool   $bUseId
     * @param bool   $bNoCache
     *
     * @return array|int|null|string
     */
	public function getEvent($sEvent, $bUseId = false, $bNoCache = false)
	{		
		static $aEvent = null;
		
		if ($aEvent !== null && $bNoCache === false)
		{
			return $aEvent;
		}
		
		
		if (Phpfox::isUser())
		{
			$this->database()->select('ei.invite_id, ei.rsvp_id, ')->leftJoin(Phpfox::getT('event_invite'), 'ei', 'ei.event_id = e.event_id AND ei.invited_user_id = ' . Phpfox::getUserId());
		}
		
		if (Phpfox::isModule('friend'))
		{
			$this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = e.user_id AND f.friend_user_id = " . Phpfox::getUserId());					
		}				
		else
		{
			$this->database()->select('0 as is_friend, ');
		}

		$aEvent = $this->database()->select('e.*, e.country_iso, ' . (Phpfox::getParam('core.allow_html') ? 'et.description_parsed' : 'et.description') . ' AS description, ' . (Phpfox::getUserField() ? Phpfox::getUserField().', ':'') . 'e.country_iso')
			->from($this->_sTable, 'e')		
			->innerJoin(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
			->join(Phpfox::getT('event_text'), 'et', 'et.event_id = e.event_id')				
			->where('e.event_id = ' . (int) $sEvent)
			->execute('getSlaveRow');

		if (!isset($aEvent['event_id']))
		{
			return false;
		}
		
		if (!Phpfox::isUser())
		{
			$aEvent['invite_id'] = 0;	
			$aEvent['rsvp_id'] = 0;
		}
		
		if ($aEvent['view_id'] == '1')
		{
			if ($aEvent['user_id'] == Phpfox::getUserId() || Phpfox::getUserParam('event.can_approve_events') || Phpfox::getUserParam('event.can_view_pirvate_events'))
			{
				
			}
			else 
			{
				return false;
			}
		}
		
		$aEvent['event_date'] = Phpfox::getTime(Phpfox::getParam('event.event_basic_information_time'), $aEvent['start_time']);
		if ($aEvent['start_time'] < $aEvent['end_time'])
		{
			$aEvent['event_date'] .= ' - ';
			if (date('dmy', $aEvent['start_time']) === date('dmy', $aEvent['end_time']))
			{
				$aEvent['event_date'] .= Phpfox::getTime(Phpfox::getParam('event.event_basic_information_time_short'), $aEvent['end_time']);
			}
			else 
			{
				$aEvent['event_date'] .= Phpfox::getTime(Phpfox::getParam('event.event_basic_information_time'), $aEvent['end_time']);
			}		
		}
		
		if (isset($aEvent['gmap']) && !empty($aEvent['gmap']))
		{
			$aEvent['gmap'] = unserialize($aEvent['gmap']);
		}		
		
		$aEvent['categories'] = Event_Service_Category_Category::instance()->getCategoriesById($aEvent['event_id']);
		
		if (!empty($aEvent['address']))
		{
			$aEvent['map_location'] = $aEvent['address'];
			if (!empty($aEvent['city']))
			{
				$aEvent['map_location'] .= ',' . $aEvent['city'];
			}
			if (!empty($aEvent['postal_code']))
			{
				$aEvent['map_location'] .= ',' . $aEvent['postal_code'];
			}	
			if (!empty($aEvent['country_child_id']))
			{
				$aEvent['map_location'] .= ',' . Core_Service_Country_Country::instance()->getChild($aEvent['country_child_id']);
			}			
			if (!empty($aEvent['event_country_iso']))
			{
				$aEvent['map_location'] .= ',' . Core_Service_Country_Country::instance()->getCountry($aEvent['event_country_iso']);
			}			
			
			$aEvent['map_location'] = urlencode($aEvent['map_location']);
		}
		
		$aEvent['start_time_micro'] = Phpfox::getTime('Y-m-d', $aEvent['start_time']);
				
		return $aEvent;
	}
    
    /**
     * @param int $iId
     *
     * @return string
     */
	public function getTimeLeft($iId)
	{
		$aEvent = $this->getEvent($iId, true);
		
		return ($aEvent['mass_email'] + (Phpfox::getUserParam('event.total_mass_emails_per_hour') * 60));		
	}
    
    /**
     * @param int  $iId
     * @param bool $bNoCache
     *
     * @return bool
     */
	public function canSendEmails($iId, $bNoCache = false)
	{
        if (Phpfox::getUserParam('event.total_mass_emails_per_hour') === 0) {
            return true;
        }
		$aEvent = $this->getEvent($iId, true, $bNoCache);
		return (($aEvent['mass_email'] + (Phpfox::getUserParam('event.total_mass_emails_per_hour') * 60) > PHPFOX_TIME) ? false : true);
	}
    
    /**
     * @param int  $iId
     * @param bool $bForce
     *
     * @return array|bool
     */
	public function getForEdit($iId, $bForce = false)
	{
		$aEvent = $this->database()->select('e.*, et.description')
			->from($this->_sTable, 'e')		
			->join(Phpfox::getT('event_text'), 'et', 'et.event_id = e.event_id')	
			->where('e.event_id = ' . (int) $iId)
			->execute('getSlaveRow');
			
                if (empty($aEvent))
                {
                    return false;
                }
		if ((($aEvent['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('event.can_edit_own_event')) || Phpfox::getUserParam('event.can_edit_other_event')) || $bForce === true)
		{			
			$aEvent['start_time'] = Phpfox::getLib('date')->convertFromGmt($aEvent['start_time'], Phpfox::getTimeZone());
			$aEvent['end_time'] = Phpfox::getLib('date')->convertFromGmt($aEvent['end_time'], Phpfox::getTimeZone());
			
			$aEvent['start_month'] = date('n', $aEvent['start_time']);
			$aEvent['start_day'] = date('j', $aEvent['start_time']);
			$aEvent['start_year'] = date('Y', $aEvent['start_time']);
			$aEvent['start_hour'] = date('H', $aEvent['start_time']);
			$aEvent['start_minute'] = date('i', $aEvent['start_time']);
			
			$aEvent['end_month'] = date('n', $aEvent['end_time']);
			$aEvent['end_day'] = date('j', $aEvent['end_time']);
			$aEvent['end_year'] = date('Y', $aEvent['end_time']);
			$aEvent['end_hour'] = date('H', $aEvent['end_time']);
			$aEvent['end_minute'] = date('i', $aEvent['end_time']);
			
			$aEvent['categories'] = Event_Service_Category_Category::instance()->getCategoryIds($aEvent['event_id']);
				
			return $aEvent;
		}

		Phpfox_Error::set(_p('You don\'t have permission to {{ action }} this {{ item }}.', ['action' => _p('edit__l'), 'item' => _p('event__l')]));
		return false;
	}
    
    /**
     * @param int $iEvent
     * @param int $iRsvp
     * @param int $iPage
     * @param int $iPageSize
     *
     * @return array
     */
	public function getInvites($iEvent, $iRsvp, $iPage = 0, $iPageSize = 8)
	{
        $aInvites = [];
        $iCnt = $this->database()
            ->select('COUNT(*)')
            ->from(Phpfox::getT('event_invite'))
            ->where('event_id = ' . (int)$iEvent . ' AND rsvp_id = ' . (int)$iRsvp)
            ->execute('getSlaveField');
        
        if ($iCnt) {
            $aInvites = $this->database()
                ->select('ei.*, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('event_invite'), 'ei')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ei.invited_user_id')
                ->where('ei.event_id = ' . (int)$iEvent . ' AND ei.rsvp_id = ' . (int)$iRsvp)
                ->limit($iPage, $iPageSize, $iCnt)
                ->order('ei.invite_id DESC')
                ->execute('getSlaveRows');
        }
        
        return [$iCnt, $aInvites];
    }
    
    /**
     * @param int $iLimit
     *
     * @return array
     */
    public function getInviteForUser($iLimit = 6)
	{
        $aRows = $this->database()
            ->select('e.*,et.description, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('event_invite'), 'ei')
            ->join(Phpfox::getT('event'), 'e', 'e.event_id = ei.event_id')
            ->join(Phpfox::getT('event_text'), 'et', 'e.event_id = et.event_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('ei.rsvp_id = 0 AND ei.invited_user_id = ' . Phpfox::getUserId())
            ->limit($iLimit)
            ->execute('getSlaveRows');
        
        foreach ($aRows as $iKey => $aRow) {
            if ($iKey === 4) {
                break;
            }
            
            $aFeatured[] = $aRow;
        }
			
		foreach ($aRows as $iKey => $aRow)
		{
			$aRows[$iKey]['start_time_phrase'] = Phpfox::getTime(Phpfox::getParam('event.event_browse_time_stamp'), $aRow['start_time']);
			$aRows[$iKey]['start_time_phrase_stamp'] = Phpfox::getTime('g:sa', $aRow['start_time']);
			$aRows[$iKey]['start_time_short_month'] = Phpfox::getTime('M', $aRow['start_time']);
            $aRows[$iKey]['start_time_month'] = Phpfox::getTime('F', $aRow['start_time']);
			$aRows[$iKey]['start_time_short_day'] = Phpfox::getTime('j', $aRow['start_time']);
		}
			
		return $aRows;
	}
    
    /**
     * @param int $iUserId
     * @param int $iLimit
     *
     * @return array|int|string
     */
	public function getForProfileBlock($iUserId, $iLimit = 5)
	{
		$iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
		
		$aEvents = $this->database()->select('m.*')
			->from($this->_sTable, 'm')
			->join(Phpfox::getT('event_invite'), 'ei', 'ei.event_id = m.event_id AND ei.rsvp_id = 1 AND ei.invited_user_id = ' . (int) $iUserId)
			->where('m.view_id = 0 AND m.start_time >= \'' . $iTimeDisplay . '\'')
			->limit($iLimit)
			->order('m.start_time ASC')
			->executeRows();
		
		foreach ($aEvents as $iKey => $aEvent)
		{			
			$aEvents[$iKey]['url'] = Phpfox_Url::instance()->permalink('event', $aEvent['event_id'], $aEvent['title']);
			$aEvents[$iKey]['start_time_stamp'] = Phpfox::getTime(Phpfox::getParam('event.event_view_time_stamp_profile'), $aEvent['start_time']);
			$aEvents[$iKey]['location_clean'] = Phpfox::getLib('parse.output')->split(Phpfox::getLib('parse.output')->clean($aEvent['location']), 10);
			$aEvents[$iKey]['start_time_short_day'] = Phpfox::getTime('j', $aEvent['start_time']);
			$aEvents[$iKey]['start_time_short_month'] = Phpfox::getTime('M', $aEvent['start_time']);
            $aEvents[$iKey]['start_time_month'] = Phpfox::getTime('F', $aEvent['start_time']);
			$aEvents[$iKey]['start_time_phrase'] = Phpfox::getTime(Phpfox::getParam('event.event_browse_time_stamp'), $aEvent['start_time']);
			$aEvents[$iKey]['start_time_phrase_stamp'] = Phpfox::getTime(Phpfox::getParam('event.event_basic_information_time_short'), $aEvent['start_time']);
			$aEvents[$iKey]['start_time_micro'] = Phpfox::getTime('Y-m-d', $aEvent['start_time']);
		}
			
		return $aEvents;
	}
    
    /**
     * @param string $sModule
     * @param int    $iItemId
     * @param int    $iLimit
     *
     * @return array|int|string
     */
	public function getForParentBlock($sModule, $iItemId, $iLimit = 5)
	{
		$iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
		
		$aEvents = $this->database()->select('m.event_id, m.title, m.image_path, m.server_id, m.start_time, m.location, m.country_iso, m.city, m.module_id, m.item_id, m.user_id, ' . Phpfox::getUserField())
			->from($this->_sTable, 'm')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
			->where('m.view_id = 0 AND m.module_id = \'' . $this->database()->escape($sModule) . '\' AND m.item_id = ' . (int) $iItemId . ' AND m.start_time >= \'' . $iTimeDisplay . '\'')
			->limit($iLimit)
			->order('m.start_time ASC')
			->execute('getSlaveRows');
			
		foreach ($aEvents as $iKey => $aEvent)
		{
			$aEvents[$iKey]['url'] = Phpfox_Url::instance()->makeUrl('event', array('redirect' => $aEvent['event_id']));
			$aEvents[$iKey]['start_time_stamp'] = Phpfox::getTime(Phpfox::getParam('event.event_view_time_stamp_profile'), $aEvent['start_time']);
			$aEvents[$iKey]['location_clean'] = Phpfox::getLib('parse.output')->split(Phpfox::getLib('parse.output')->clean($aEvent['location']), 10);
			$aEvents[$iKey]['start_time_short_day'] = Phpfox::getTime('j', $aEvent['start_time']);
			$aEvents[$iKey]['start_time_short_month'] = Phpfox::getTime('M', $aEvent['start_time']);
            $aEvents[$iKey]['start_time_month'] = Phpfox::getTime('F', $aEvent['start_time']);
			$aEvents[$iKey]['start_time_phrase'] = Phpfox::getTime(Phpfox::getParam('event.event_browse_time_stamp'), $aEvent['start_time']);
			$aEvents[$iKey]['start_time_phrase_stamp'] = Phpfox::getTime(Phpfox::getParam('event.event_basic_information_time_short'), $aEvent['start_time']);
			$aEvents[$iKey]['start_time_micro'] = Phpfox::getTime('Y-m-d', $aEvent['start_time']);
		}
			
		return $aEvents;
	}
    
    /**
     * @return array|int|string
     */
	public function getPendingTotal()
	{
		$iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
		
		return $this->database()->select('COUNT(*)')
			->from($this->_sTable)
			->where('view_id = 1 AND start_time >= \'' . $iTimeDisplay . '\'')
			->execute('getSlaveField');
	}
    
    /**
     * @return bool|array
     */
	public function getRandomSponsored()
	{
		$iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		
		$sCacheId = $this->cache()->set('event_sponsored_' . $iToday);
		if (!($aEvents = $this->cache()->get($sCacheId)))
		{
			$aEvents = $this->database()->select('s.*, s.country_iso AS sponsor_country_iso, e.*')
				->from($this->_sTable, 'e')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
				->join(Phpfox::getT('ad_sponsor'),'s','s.item_id = e.event_id')
				->where('e.view_id = 0 AND e.privacy = 0 AND e.is_sponsor = 1 AND s.module_id = \'event\' AND e.start_time >= \'' . $iToday . '\'')
				->execute('getSlaveRows');

			foreach ($aEvents as $iKey => $aEvent)
			{
				$aEvents[$iKey]['categories'] = Event_Service_Category_Category::instance()->getCategoriesById($aEvent['event_id']);
				$aEvents[$iKey]['event_date'] = Phpfox::getTime(Phpfox::getParam('event.event_basic_information_time'), $aEvent['start_time']) . ' - ';
				if (date('dmy', $aEvent['start_time']) === date('dmy', $aEvent['end_time']))
				{
					$aEvents[$iKey]['event_date'] .= Phpfox::getTime(Phpfox::getParam('event.event_basic_information_time_short'), $aEvent['end_time']);
				}
				else
				{
					$aEvents[$iKey]['event_date'] .= Phpfox::getTime(Phpfox::getParam('event.event_basic_information_time'), $aEvent['end_time']);
				}
                $aEvents[$iKey]['start_time_short_month'] = Phpfox::getTime('M', $aEvent['start_time']);
                $aEvents[$iKey]['start_time_month'] = Phpfox::getTime('F', $aEvent['start_time']);
                $aEvents[$iKey]['start_time_short_day'] = Phpfox::getTime('j', $aEvent['start_time']);
			}

			$this->cache()->save($sCacheId, $aEvents);
		}

		if ($aEvents === true || (is_array($aEvents) && !count($aEvents)))
		{
			return false;
		}

		// Randomize to get a event
		return $aEvents[rand(0, (count($aEvents) - 1))];
	}
    
    /**
     * @param int   $iItemId
     * @param array $aFriends
     *
     * @return array|bool
     */
	public function isAlreadyInvited($iItemId, $aFriends)
	{
		if ((int) $iItemId === 0)
		{
			return false;
		}
		
		if (is_array($aFriends))
		{
			if (!count($aFriends))
			{
				return false;
			}
			
			$sIds = '';
			foreach ($aFriends as $aFriend)
			{
				if (!isset($aFriend['user_id']))
				{
					continue;
				}
				
				$sIds[] = $aFriend['user_id'];
			}			
			
			$aInvites = $this->database()->select('invite_id, rsvp_id, invited_user_id')
				->from(Phpfox::getT('event_invite'))
				->where('event_id = ' . (int) $iItemId . ' AND invited_user_id IN(' . implode(', ', $sIds) . ')')
				->execute('getSlaveRows');
			
			$aCache = array();
			foreach ($aInvites as $aInvite)
			{
				$aCache[$aInvite['invited_user_id']] = ($aInvite['rsvp_id'] > 0 ? _p('responded') : _p('invited'));
			}
			
			if (count($aCache))
			{
				return $aCache;
			}
		}
		
		return false;
	}
    
    /**
     * @return array
     */
	public function getSiteStatsForAdmins()
	{
		$iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        
        return [
            'phrase' => _p('events'),
            'value'  => $this->database()
                ->select('COUNT(*)')
                ->from(Phpfox::getT('event'))
                ->where('view_id = 0 AND time_stamp >= ' . $iToday)
                ->execute('getSlaveField')
        ];
    }
    
    /**
     * @return array
     */
    public function getFeatured()
	{
		static $aFeatured = null;
		static $iTotal = null;
		
		if ($aFeatured !== null)
		{
			return array($iTotal, $aFeatured);
		}
		
		$iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		
		$aFeatured = array();
		$sCacheId = $this->cache()->set('event_featured_' . $iToday);		
		if (!($aRows = $this->cache()->get($sCacheId)))
		{
			$aRows = $this->database()->select('v.*, ' . Phpfox::getUserField())
				->from(Phpfox::getT('event'), 'v')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
				->where('v.is_featured = 1 AND v.start_time >= \'' . $iToday . '\'')			
				->execute('getSlaveRows');
			
			$this->cache()->save($sCacheId, $aRows);
		}
		
		$iTotal = 0;
		if (is_array($aRows) && count($aRows))
		{
			$iTotal = count($aRows);
			shuffle($aRows);
			foreach ($aRows as $iKey => $aRow)
			{
				if ($iKey === 4)
				{
					break;
				}
                $aRow['start_time_short_month'] = Phpfox::getTime('M', $aRow['start_time']);
                $aRow['start_time_phrase_stamp'] = Phpfox::getTime(Phpfox::getParam('event.event_basic_information_time_short'), $aRow['start_time']);
                $aRow['start_time_month'] = Phpfox::getTime('F', $aRow['start_time']);
                $aRow['start_time_short_day'] = Phpfox::getTime('j', $aRow['start_time']);
				$aFeatured[] = $aRow;
			}
		}
		return array($iTotal, $aFeatured);
	}
    
    /**
     * @return array
     */
	public function getForRssFeed()
	{
		$iTimeDisplay = Phpfox::getLib('phpfox.date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
		$aConditions = array();
		$aConditions[] = "e.view_id = 0 AND e.module_id = 'event' AND e.item_id = 0";
		$aConditions[] = "AND e.start_time >= '" . $iTimeDisplay . "'";		
		
		$aRows = $this->database()->select('e.*, et.description_parsed AS description, ' . Phpfox::getUserField())
			->from(Phpfox::getT('event'), 'e')
			->join(Phpfox::getT('event_text'), 'et', 'et.event_id = e.event_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
			->where($aConditions)
			->order('e.time_stamp DESC')
			->executeRows();
		
		foreach ($aRows as $iKey => $aRow)
		{
			$aRows[$iKey]['link'] = Phpfox::permalink('event', $aRow['event_id'], $aRow['title']);
			$aRows[$iKey]['creator'] = $aRow['full_name'];
		}		
		
		return $aRows;
	}
    
    /**
     * @param array $aItem
     *
     * @return array
     */
	public function getInfoForAction($aItem)
	{
		if (is_numeric($aItem))
		{
			$aItem = array('item_id' => $aItem);
		}
		$aRow = $this->database()->select('e.event_id, e.title, e.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('event'), 'e')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
			->where('e.event_id = ' . (int) $aItem['item_id'])
			->executeRow();
			
		$aRow['link'] = Phpfox_Url::instance()->permalink('event', $aRow['event_id'], $aRow['title']);
		return $aRow;
	}
    
    /**
     * @description: check permission to view an event
     *
     * @param int  $iId
     * @param bool $bReturnItem
     *
     * @return array|bool|int|null|string
     */
    public function canViewItem($iId, $bReturnItem = false)
    {

        if (!Phpfox::getUserParam('event.can_access_event'))
        {
            return false;
        }

        if (!($aEvent = Event_Service_Event::instance()->getEvent($iId, false, $bReturnItem)))
        {
            Phpfox_Error::set(_p('This {{ item }} cannot be found.', ['item' => _p('event__l')]));
            return false;
        }

        if (Phpfox::isUser() && User_Service_Block_Block::instance()->isBlocked(null, $aEvent['user_id']))
        {
            Phpfox_Error::set(_p('Sorry, this content isn\'t available right now'));
            return false;
        }

        if (Phpfox::isModule('privacy'))
        {
            if (!Privacy_Service_Privacy::instance()->check('event', $aEvent['event_id'], $aEvent['user_id'], $aEvent['privacy'], $aEvent['is_friend'], true))
            {
                return false;
            }
        }

        if(isset($aEvent['module_id']) && Phpfox::isModule($aEvent['module_id']) && Phpfox::hasCallback($aEvent['module_id'], 'checkPermission'))
        {
            if(!Phpfox::callback($aEvent['module_id'] . '.checkPermission', $aEvent['item_id'], 'event.view_browse_events'))
            {
                Phpfox_Error::set(_p('unable_to_view_this_item_due_to_privacy_settings'));
                return false;
            }
        }

        return $bReturnItem ? $aEvent : true;
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
        if ($sPlugin = Phpfox_Plugin::get('event.service_event__call')) {
            eval($sPlugin);
            return null;
        }
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}