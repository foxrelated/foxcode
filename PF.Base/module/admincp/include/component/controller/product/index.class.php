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
 * @version 		$Id: index.class.php 5180 2013-01-22 14:58:13Z Miguel_Espinoza $
 */
class Admincp_Component_Controller_Product_Index extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{	
		if ($aVals = $this->request()->getArray('val'))
		{
			if (Admincp_Service_Product_Process::instance()->updateActive($aVals))
			{
				$this->url()->send('admincp.product', null, _p('product_s_updated'));
			}			
		}				
		
		if ($sPlugin = Phpfox_Plugin::get('admincp.component_controller_product_index_3')){eval($sPlugin);if (isset($mReturnFromPlugin)){return $mReturnFromPlugin;}}
		if ($sDeleteProduct = $this->request()->get('delete'))
		{
			if ($sDeleteProduct == 'phpfox' || $sDeleteProduct == 'phpfox_installer') {
				if ( $this->request()->get('app')) {
					$this->url()->send('admincp.apps', null, _p('cannot_delete_this_app_dot'));
				}
				else {
					$this->url()->send('admincp.product', null, _p('cannot_delete_this_module_dot'));
				}
			}
			if (Admincp_Service_Product_Process::instance()->delete($sDeleteProduct))
			{
				if ( $this->request()->get('app')) {
					$this->url()->send('admincp.apps', null, _p('app_successfully_uninstalled_dot'));
				}
				else {
					$this->url()->send('admincp.product', null, _p('product_successfully_deleted'));
				}
			}
		}

		if (($sUpgrade = $this->request()->get('upgrade')))
		{
			if ($sPlugin = Phpfox_Plugin::get('admincp.component_controller_product_index_1')){eval($sPlugin);if (isset($mReturnFromPlugin)){return $mReturnFromPlugin;}}
			if (Admincp_Service_Product_Process::instance()->upgrade($sUpgrade))
			{
				Phpfox_Plugin::set();
				if ($sPlugin = Phpfox_Plugin::get('admincp.component_controller_product_index_2')){eval($sPlugin);if (isset($mReturnFromPlugin)){return $mReturnFromPlugin;}}
				$this->url()->send('admincp.product', null, _p('product_successfully_upgraded'));
			}
		}

		$aProducts = Admincp_Service_Product_Product::instance()->get(false);
		foreach ($aProducts as $iKey => $aProduct)
		{
			if ($aProduct['product_id'] == 'phpfox' || $aProduct['product_id'] == 'phpfox_installer')
			{
				unset($aProducts[$iKey]);
			}
		}

		$this->template()->setTitle(_p('modules'))
			->setSectionTitle(_p('modules'))
			->assign(array(
					'aProducts' => $aProducts
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('admincp.component_controller_product_index_clean')) ? eval($sPlugin) : false);
	}
}