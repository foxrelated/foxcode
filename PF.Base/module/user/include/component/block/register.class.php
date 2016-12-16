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
 * @package  		Module_User
 * @version 		$Id: register.class.php 3382 2011-10-31 11:53:10Z Raymond_Benc $
 */
class User_Component_Block_Register extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (!Phpfox::getParam('user.allow_user_registration'))
		{
			return false;
		}
		
		if (Phpfox::isUser())
		{
			return false;
		}
		
		$this->template()->assign(array(
				'sHeader' => _p('sign_up'),
				'sSiteUrl' => Phpfox::getParam('core.path'),
				'aTimeZones' => Core_Service_Core::instance()->getTimeZones(),
				'aPackages' => (Phpfox::isModule('subscribe') ? Subscribe_Service_Subscribe::instance()->getPackages(true) : null),
				'sDobStart' => Phpfox::getParam('user.date_of_birth_start'),
				'sDobEnd' => Phpfox::getParam('user.date_of_birth_end'),
				'sSiteTitle' => Phpfox::getParam('core.site_title')
			)
		);
		
		return 'block';
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}