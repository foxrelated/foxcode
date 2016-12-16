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
 * @version 		$Id: add.class.php 1931 2010-10-25 11:58:06Z Raymond_Benc $
 */
class Admincp_Component_Controller_Product_Add extends Phpfox_Component 
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
		
		$bIsEdit = false;
		if (($iEditId = $this->request()->get('id')))
		{			
			$aProduct = Admincp_Service_Product_Product::instance()->getForEdit($iEditId);
			if (isset($aProduct['product_id']))
			{
				$bIsEdit = true;				
				$this->template()->assign(array(
						'aForms' => $aProduct,
						'aDependencies' => Admincp_Service_Product_Product::instance()->getDependencies($aProduct['product_id']),
						'aInstalls' => Admincp_Service_Product_Product::instance()->getInstalls($aProduct['product_id'])
					)
				);
			}
		}
		
		$aValidation = array(
			'product_id' => _p('add_a_product_id'),
			'title' => _p('add_a_product_title')
		);		
		
		$oValid = Phpfox_Validator::instance()->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));
		
		if ($aVals = $this->request()->getArray('val'))
		{			
			$oAdmincpProductProcess = Admincp_Service_Product_Process::instance();
			
			if (isset($aVals['dependency']))
			{
				if (isset($aVals['dependency']['delete']))
				{
					foreach ($aVals['dependency']['delete'] as $iDeleteId)
					{
						$bDeleted = $oAdmincpProductProcess->deleteDependency($iDeleteId);
					}
				}
				
				if (isset($aVals['dependency']['update']))
				{
					foreach ($aVals['dependency']['update'] as $iDependencyId => $aDependency)
					{
						$oAdmincpProductProcess->updateDependency($iDependencyId, $aDependency);	
					}
				}				
				
				$bAdded = $oAdmincpProductProcess->addDependency($aVals['dependency']);
				
				$this->url()->send('admincp', array('product', 'add', 'id' => $aVals['dependency']['product_id']), _p('product_dependency_updated'));
			}
			elseif (isset($aVals['install']))
			{
				if (isset($aVals['install']['delete']))
				{
					foreach ($aVals['install']['delete'] as $iDeleteId)
					{
						$bDeleted = $oAdmincpProductProcess->deleteInstall($iDeleteId);
					}
				}
				
				if (isset($aVals['install']['update']))
				{
					foreach ($aVals['install']['update'] as $iInstallId => $aInstallUpdate)
					{
						$oAdmincpProductProcess->updateInstall($iInstallId, $aInstallUpdate);	
					}
				}
				
				$bAdded = $oAdmincpProductProcess->addInstall($aVals['install']);
				
				$this->url()->send('admincp', array('product', 'add', 'id' => $aVals['install']['product_id']), _p('product_install_uninstall_updated'));
			}
			else 
			{
				if ($oValid->isValid($aVals))
				{
					if ($bIsEdit)
					{
						if (Admincp_Service_Product_Process::instance()->update($aProduct['product_id'], $aVals))
						{
							$this->url()->send('admincp', array('product', 'add', 'id' => $aProduct['product_id']), _p('product_successfully_updated'));
						}					
					}
					else 
					{
						if (($sName = Admincp_Service_Product_Process::instance()->add($aVals)))
						{							
							$this->url()->send('admincp', array('product'), _p('product_successfully_created'));
						}
					}
				}
			}
		}		
		
		$this->template()->setTitle(($bIsEdit ? _p('editing_product') . ': ' . $aProduct['title'] : _p('create_new_product')))
			->setBreadCrumb(_p('products'), $this->url()->makeUrl('admincp.product'))
			->setBreadCrumb(($bIsEdit ? _p('editing_product') . ': ' . $aProduct['title'] : _p('create_new_product')), $this->url()->current(), true)
			->assign(array(
				'sCreateJs' => $oValid->createJS(),
				'sGetJsForm' => $oValid->getJsForm(),
				'bIsEdit' => $bIsEdit
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('admincp.component_controller_module_product_clean')) ? eval($sPlugin) : false);
	}
}