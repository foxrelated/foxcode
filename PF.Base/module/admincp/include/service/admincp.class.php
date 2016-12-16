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
 * @package  		Module_Admincp
 * @version 		$Id: admincp.class.php 6343 2013-07-19 19:42:10Z Raymond_Benc $
 */
class Admincp_Service_Admincp extends Phpfox_Service 
{
    /**
     * Class constructor
     */
    public function __construct() { }
    
    /**
     * @deprecated from 4.6.0
     *
     * @param       $sCall
     * @param array $aPost
     */
    public function getHostingInfo($sCall, $aPost = []) { }
    
    /**
     * @return array
     */
    public function getHostingStats()
	{
		$sCacheId = $this->cache()->set('admincp_site_cache');
		if (!($aReturn = $this->cache()->get($sCacheId, 1 * 60 * 60))) // cache is in hours
		{
			$aParts = explode('|', Phpfox::getParam('core.phpfox_max_users_online'));
			$iTotalMemberCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('user'))
				->where('view_id = 0')
				->execute('getSlaveField');
			
			$iOneGigIntoBytes = 1073741824;
			$iTotalSpace = ($aParts[0] * $iOneGigIntoBytes);
			$iTotalUsed = Phpfox::getLib('cdn')->getUsage();
			
			$iCount1 = $iTotalUsed / $iTotalSpace;
			$iCount2 = $iCount1 * 100;
			$iTotalUsage = number_format($iCount2, 0);	
			if (!$iTotalUsage)
			{
				$iTotalUsage = '< 1';
			}	
			
			if ($aParts[1] == '0')
			{
				$aParts[1] = 'Unlimited';
			}		
			else
			{	
				$iCount1 = $iTotalMemberCnt / $aParts[1];
				$iCount2 = $iCount1 * 100;
				$iTotalMemberUsage = number_format($iCount2, 0);
				if (!$iTotalMemberUsage)
				{
					$iTotalMemberUsage = '< 1';
				}		
			}

			$sPastHistory = Phpfox::getParam('core.phpfox_total_users_online_history');
			$aParts = explode('|', $sPastHistory);
			$iVideosUploaded = 0;
			$iTotalVideoUsage = 0;
			if (isset($aParts[1]))
			{
				$iVideosUploaded = (int) $aParts[0];
				$iCount1 = $iVideosUploaded / Phpfox::getParam('core.phpfox_total_users_online_mark');
				$iCount2 = $iCount1 * 100;
				$iTotalVideoUsage = number_format($iCount2, 0);
			}

			$aReturn = array(
				'sTotalSpaceUsage' => $iTotalUsage . '% (' . Phpfox_File::instance()->filesize($iTotalUsed) . ' out of ' . $aParts[0] . ' GB)',
				'sTotalMemberUsage' => (isset($iTotalMemberUsage) ? $iTotalMemberUsage . '% (' . $iTotalMemberCnt . ' out of ' . number_format($aParts[1]) . ')' : 'Unlimited'),
				'sTotalVideoUsage' => $iTotalVideoUsage . '% (' . $iVideosUploaded . ' out of ' . Phpfox::getParam('core.phpfox_total_users_online_mark') . ')'
			);

			$this->cache()->save($sCacheId, $aReturn);
		}
		
		return $aReturn;
	}
    
    /**
     * @return array
     */
	public function getAdmincpRules()
	{
		$aRows = $this->database()->select('*')
			->from(Phpfox::getT('admincp_privacy'))
			->order('time_stamp DESC')
			->execute('getSlaveRows');
		
		$aUserGroupCache = array();
		$aUserGroups = $this->database()->select('*')
			->from(Phpfox::getT('user_group'))
			->execute('getSlaveRows');
		foreach ($aUserGroups as $aUserGroup)
		{
			$aUserGroupCache[$aUserGroup['user_group_id']] = $aUserGroup['title'];
		}
		
		foreach ($aRows as $iKey => $aRow)
		{
			$aRows[$iKey]['user_groups'] = '';
			foreach ((array) json_decode($aRow['user_group'], true) as $iGroup)
			{
				if (!isset($aUserGroups[$iGroup]))
				{
					continue;
				}

				$aRows[$iKey]['user_groups'] .= $aUserGroupCache[$iGroup] . ', ';
			}
			
			$aRows[$iKey]['user_groups'] = rtrim($aRows[$iKey]['user_groups'] , ', ');
		}
				
		return $aRows;
	}
    
    /**
     * @param array $aMenus
     *
     * @return array
     */
	public function checkAdmincpPrivacy($aMenus)
	{
		$sCacheId = $this->cache()->set('admincp_url_' . Phpfox::getUserId());
		
			$aPrivacyCache = array();
			$aRows = $this->database()->select('*')
				->from(Phpfox::getT('admincp_privacy'))
				->order('time_stamp DESC')
				->execute('getSlaveRows');
			foreach ($aRows as $aRow)
			{
				foreach ((array) json_decode($aRow['user_group'], true) as $iGroup)
				{
					$aPrivacyCache[$iGroup][$aRow['url']] = ($aRow['wildcard'] ? true : false);
				}
			}		
			
			$aCache = array();
			if (isset($aPrivacyCache[Phpfox::getUserBy('user_group_id')]))
			{
				$aCache = $aPrivacyCache[Phpfox::getUserBy('user_group_id')];
				$sUrl = Phpfox_Url::instance()->getFullUrl(true);
				$sUrl = str_replace('/', '.', $sUrl);
				$sUrl = trim($sUrl, '.');
				$sNewParts = '';
				$aParts = explode('.', $sUrl);
				foreach ($aParts as $sPart)
				{
					if (strpos($sPart, '_'))
					{
						continue;
					}
					$sNewParts .= $sPart . '.';
				}
				$sNewParts = rtrim($sNewParts, '.');			
				
				$bFailed = false;
				foreach ($aCache as $sUrlValue => $bWildcard)
				{
					if ($sUrlValue == $sNewParts)
					{
						$bFailed = true;					
					}
					
					if ($bWildcard && preg_match('/' . $sUrlValue . '(.*)/i', $sNewParts))
					{
						$bFailed = true;
					}
				}
				
				if ($bFailed)
				{
					Phpfox_Url::instance()->send('admincp');
				}
			}
			
			foreach ($aMenus as $sPhrase1 => $mValue1)
			{
				if (is_array($mValue1))
				{
					foreach ($mValue1 as $sPhrase2 => $mValue2)
					{
						if (is_array($mValue2))
						{
							foreach ($mValue2 as $sPhrase3 => $mValue3)
							{
								if (isset($aCache[$mValue3]))
								{
									unset($aMenus[$sPhrase1][$sPhrase2][$sPhrase3]);
								}
								
								foreach ($aCache as $sUrlValue => $bWildcard)
								{							
									if ($bWildcard && preg_match('/' . $sUrlValue . '(.*)/i', $mValue3))
									{
										if (isset($aMenus[$sPhrase1][$sPhrase2][$sPhrase3]))
										{
											unset($aMenus[$sPhrase1][$sPhrase2][$sPhrase3]);
										}
									}
								}							
							}
						}
						else
						{
							if (isset($aCache[$mValue2]))
							{
								unset($aMenus[$sPhrase1][$sPhrase2]);
							}							
						}
					}
				}
			}
			
			$aMenuCache = $aMenus;
			
			foreach ($aMenuCache as $sP1 => $mV1)
			{
				if (is_array($mV1))
				{
					foreach ($mV1 as $sP2 => $mV2)
					{
						if (is_array($mV2) && empty($mV2))
						{
							unset($aMenuCache[$sP1][$sP2]);
						}
					}
				}
			}
			
			$this->cache()->save($sCacheId, $aMenuCache);

		return $aMenuCache;
	}
    
    /**
     * @deprecated from 4.6.0
     * @return int
     */
	public function check()
	{
		return 0;
	}

	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('admincp.service_admincp__call'))
		{
			eval($sPlugin);
            return null;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
}