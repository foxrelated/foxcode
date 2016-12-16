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
 * @package 		Phpfox_Service
 * @version 		$Id: callback.class.php 1496 2010-03-05 17:15:05Z Raymond_Benc $
 */
class Core_Service_Callback extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct() {}
    
    /**
     * @return array
     */
    public function getBlocksIndexMember()
    {
        return [
            'table' => 'user_dashboard',
            'field' => 'user_id'
        ];
    }
    
    /**
     * @return array
     */
    public function hideBlockNew()
    {
        return [
            'table' => 'user_dashboard'
        ];
    }
    
    /**
     * @return array
     */
    public function getBlockDetailsNew()
	{
        return [
            'title' => _p('what_s_new')
        ];
    }
    
    /**
     * @param string $sProduct
     * @param string $sModule
     * @param bool   $bCore
     *
     * @return bool
     */
    public function exportModule($sProduct, $sModule, $bCore)
	{
		$iCnt = 0;
		(Admincp_Service_Menu_Menu::instance()->export($sProduct, $sModule) ? $iCnt++ : null);
		(Admincp_Service_Setting_Setting::instance()->exportGroup($sProduct, $sModule) ? $iCnt++ : null);
		(Admincp_Service_Setting_Setting::instance()->export($sProduct, $sModule, $bCore) ? $iCnt++ : null);
		(Admincp_Service_Module_Block_Block::instance()->export($sProduct, $sModule) ? $iCnt++ : null);
		(Admincp_Service_Plugin_Plugin::instance()->exportHooks($sProduct, $sModule) ? $iCnt++ : null);
		(Admincp_Service_Plugin_Plugin::instance()->export($sProduct, $sModule) ? $iCnt++ : null);
		(Admincp_Service_Component_Component::instance()->export($sProduct, $sModule) ? $iCnt++ : null);
		(Admincp_Service_Cron_Cron::instance()->export($sProduct, $sModule) ? $iCnt++ : null);
		(Core_Service_Stat_Stat::instance()->export($sProduct, $sModule) ? $iCnt++ : null);
		
		return ($iCnt ? true : false);
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
		if ($sPlugin = Phpfox_Plugin::get('core.service_callback__call'))
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