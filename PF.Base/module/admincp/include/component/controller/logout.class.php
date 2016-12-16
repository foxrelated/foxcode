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
 * @version 		$Id: logout.class.php 1629 2010-06-06 07:28:54Z Raymond_Benc $
 */
class Admincp_Component_Controller_Logout extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (!Phpfox::getParam('core.admincp_do_timeout'))
		{
			User_Service_Auth::instance()->logout();
			
			$this->url()->send('');	
		}
		
		User_Service_Auth::instance()->logoutAdmin();
		
		$this->url()->send('admincp', null, _p('successfully_logged_out'));
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('admincp.component_controller_logout_clean')) ? eval($sPlugin) : false);
	}
}