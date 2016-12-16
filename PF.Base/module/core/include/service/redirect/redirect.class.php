<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package 		Phpfox_Service
 * @version 		$Id: core.class.php 2209 2010-11-26 12:24:28Z Miguel_Espinoza $
 */
class Core_Service_Redirect_Redirect extends Phpfox_Service
{
    /**
     * If a url is requested and it does not exist this function checks if it ever existed
     * if so then it provides the old title for the item
     *
     * @example http://site.com/index.php?do=/user1/blog/blog-1fds/ vs http://site.com/index.php?do=/user1/blog/blog-1/
     *
     * @param string $sModule
     * @param string $sOldTitle
     *
     * @return string|false
     */
	public function getRedirection($sModule, $sOldTitle)
	{
		
		if (Phpfox::isModule($sModule) && Phpfox::hasCallback($sModule, 'getRedirectionTable'))
		{
			$sNewTitle = $this->database()->select('new_title')
					->from(Phpfox::callback($sModule.'.getRedirectionTable'))
					->where('old_title = "' . $sOldTitle . '"')
					->execute('getSlaveField');

			if (!empty($sNewTitle))
			{
				return $sNewTitle;
			}
		}
		return false;
	}

    /**
     * This function checks if a user is allowed to update the URL of a specific blog
     *
     * @param int $sModule
     * @param int $iUser
     * @param int $iItemId
     *
     * @return bool
     */
	public function canUpdateURL($sModule, $iUser, $iItemId)
	{
		// first the general permission
		if (!Phpfox::isModule($sModule) 
				|| (Phpfox::getUserParam($sModule.'.can_update_url') == false)
				|| !Phpfox::hasCallback($sModule, 'getRedirectionTable'))
		{
			return false;
		}
		$iCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::callback($sModule. '.getRedirectionTable'))
				->where('item_id = ' . (int)$iItemId)
				->execute('getSlaveField');
		if ($iCnt >= Phpfox::getUserParam($sModule.'.how_many_url_updates') && $iCnt > 0 &&
				Phpfox::getUserParam($sModule.'.how_many_url_updates') > 0)
		{
			return false;
		}

		return true;
	}
    
    /**
     * @param array $aParams
     *
     * @return bool
     */
	public function check404($aParams)
	{
		if (!Phpfox::getParam('core.force_404_check'))
		{
			return false;
		}
		
		if ( (defined('PHPFOX_IS_AJAX') && (PHPFOX_IS_AJAX == true) ) || (strpos($_SERVER['REQUEST_URI'], 'core[call]=core.page') !== false && strpos($_SERVER['REQUEST_URI'], '/_ajax/?') !== false) )
		{
			return true;
		}
		
		
		if (isset($_SERVER['REQUEST_URI']))
		{
			if (strpos($_SERVER['REQUEST_URI'], '&') !== false)
			{
				define('PHPFOX_IS_FORCED_404', true); 
				return false;
			}
			// Should not allow question marks in the url
			if (Phpfox::getParam('core.url_rewrite') == 1 && strpos($_SERVER['REQUEST_URI'], '?') !== false)
			{
				define('PHPFOX_IS_FORCED_404', true);
				return false;
			}
			
			
			// There should not be two (or more) forward slashes at the end
			if (strpos($_SERVER['REQUEST_URI'],'//') !== false)
			{
				define('PHPFOX_IS_FORCED_404', true);
				return false;
			}			
		}		
		
		$aReserved = array('page', 'view', 'sort', 'show', 'when', 'search-id', 'location');
		if (isset($aParams['reserved']) && !empty($aParams['reserved']))
		{
			$aReserved = array_merge($aReserved, $aParams['reserved']);
		}
		$aUrls = Phpfox_Url::instance()->getParams();
		$iCnt = 0;
		
		foreach ($aUrls as $sKey => $sValue)
		{
			if (preg_match('/^req([0-9]+)$/i', $sKey, $aMatches))
			{
				$iCnt++;				
				
				if (isset($aParams['start']) && $iCnt < $aParams['start'])
				{
					continue;
				}
				
				if ((int) $aMatches[1] === 1)
				{
					continue;
				}
				
				if (isset($aParams['reqs']) && isset($aParams['reqs'][$aMatches[1]]) && in_array($sValue, $aParams['reqs'][$aMatches[1]]))
				{
					continue;
				}
				
				// Check for allow ids (int)
				if (defined('PHPFOX_ALLOW_ID_404_CHECK') && isset($aParams['reqs'][$aMatches[1]]) && in_array(PHPFOX_ALLOW_ID_404_CHECK, $aParams['reqs'][$aMatches[1]]))
				{
					continue;
				}
				return false;
				
			}
			else
			{
				if (!in_array($sKey, $aReserved))
				{
					define('PHPFOX_IS_FORCED_404', true);
					return false;
				}
			}
		}
		
		return true;
	}
    
    /**
     * This function gets the ReWrites from cache or database, this is not related to a specific section, but to the
     * entire site. This function is used in the AdminCP -> Tools -> SEO -> Rewrite URL, but not in the URL Library.
     * There is very little benefit in caching this query because it is only used in the AdminCP, and the cache object
     * created in the URL library includes the reverse rewrites which makes it too complex for just displaying in the
     * AdminCP.
     *
     * @return array
     */
	 public function getRewrites()
	 {
				
		$aRows = Phpfox_Database::instance()->select('r.url, r.replacement, r.rewrite_id')
			->from(Phpfox::getT('rewrite'), 'r')
			->order('rewrite_id DESC')
			->execute('getSlaveRows');
		
		return $aRows;
	 }
}