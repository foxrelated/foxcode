<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Component
 * @version 		$Id: add.class.php 1558 2010-05-04 12:51:22Z Raymond_Benc $
 */
class Core_Component_Controller_Admincp_Currency_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$bIsEdit = false;
		if (($sId = $this->request()->get('id')) && ($aCurrency = Core_Service_Currency_Currency::instance()->getForEdit($sId)))
		{
			$bIsEdit = true;
			$this->template()->assign('aForms', $aCurrency);	
		}
		
		if (($aVals = $this->request()->getArray('val')))
		{
			if ($bIsEdit)
			{
				if (Core_Service_Currency_Process::instance()->update($aCurrency['currency_id'], $aVals))
				{
					$this->url()->send('admincp.core.currency.add', array('id' => $aVals['currency_id']), _p('currency_successfully_updated'));
				}				
			}
			else 
			{
				if (Core_Service_Currency_Process::instance()->add($aVals))
				{
					$this->url()->send('admincp.core.currency', null, _p('currency_successfully_added'));
				}
			}
		}
		
		$this->template()->setTitle(_p('currency_manager'))
			->setBreadCrumb(_p('currency_manager'), $this->url()->makeUrl('admincp.core.currency'))		
			->setBreadCrumb(_p('add_currency'), $this->url()->current(), true)
			->assign(array(
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
		(($sPlugin = Phpfox_Plugin::get('core.component_controller_admincp_currency_add_clean')) ? eval($sPlugin) : false);
	}
}