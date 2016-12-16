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
 * @package  		Module_Core
 * @version 		$Id: ajax.class.php 1289 2009-12-02 16:13:11Z Raymond_Benc $
 */
class Core_Component_Ajax_Admincp_Ajax extends Phpfox_Ajax
{
	public function updateNote()
	{
		Core_Service_Admincp_Process::instance()->updateNote($this->get('admincp_note'));
		
		$this->hide('#js_save_note');
	}
	
	public function viewAdminLogin()
	{
		Phpfox::getBlock('core.view-admincp-login');
	}
	
	public function countryChildTranslate()
	{
		Phpfox::getBlock('core.translate-child-country');	
	}
	
	public function translateCountryChildProcess()
	{
		if (Core_Service_Country_Child_Process::instance()->translate($this->get('val')))
		{
			
		}
	}	
	
	public function countryTranslate()
	{
		Phpfox::getBlock('core.translate-country');
	}
	
	public function translateCountryProcess()
	{
		if (Core_Service_Country_Process::instance()->translate($this->get('val')))
		{
			
		}
	}
}