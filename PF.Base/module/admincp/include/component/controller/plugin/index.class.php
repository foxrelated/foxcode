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
 * @version 		$Id: index.class.php 1931 2010-10-25 11:58:06Z Raymond_Benc $
 */
class Admincp_Component_Controller_Plugin_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (Phpfox::getParam('core.phpfox_is_hosted'))
		{
			$this->url()->send('admincp');
		}		
		
		if ($aVals = $this->request()->getArray('val'))
		{
			if (Admincp_Service_Plugin_Process::instance()->updateActive($aVals))
			{
				$this->url()->send('admincp.plugin', null, _p('plugin_s_updated'));
			}			
		}				
		
		if ($sDeletePlugin = $this->request()->get('delete'))
		{
			if (Admincp_Service_Plugin_Process::instance()->delete($sDeletePlugin))
			{
				$this->url()->send('admincp.plugin', null, _p('plugin_successfully_deleted'));
			}
		}		
		
		$this->template()->setTitle(_p('manage_plugins'))
			->setBreadCrumb(_p('manage_plugins'))
			->assign(array(
				'aPlugins' => Admincp_Service_Plugin_Plugin::instance()->get()
			)
		);
	}
}