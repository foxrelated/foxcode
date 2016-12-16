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
 * @version 		$Id: add.class.php 1522 2010-03-11 17:56:49Z Miguel_Espinoza $
 */
class Report_Component_Controller_Admincp_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$bIsEdit = false;
		if (($iId = $this->request()->getInt('id')))
		{
			if ($aCategory = Report_Service_Report::instance()->getForEdit($iId))
			{
				$bIsEdit = true;
				
				$this->template()->assign('aForms', $aCategory);
			}
		}
		
		if (($aVals = $this->request()->getArray('val')))
		{
			if ($bIsEdit && isset($aCategory))
			{
				if (Report_Service_Process::instance()->update($aCategory['report_id'], $aVals))
				{
					$this->url()->send('admincp.report.add', array('id' => $aCategory['report_id']), _p('category_successfully_updated'));
				}				
			}
			else 
			{
				if (Report_Service_Process::instance()->add($aVals))
				{
					$this->url()->send('admincp.report.add', null, _p('category_successfully_added'));
				}
			}
		}
		
		$this->template()->setTitle(($bIsEdit === true ? _p('edit_a_category') : _p('add_a_category')))
			->setBreadCrumb(($bIsEdit === true ? _p('edit_a_category') : _p('add_a_category')), $this->url()->makeUrl('admincp.report'))
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
		(($sPlugin = Phpfox_Plugin::get('report.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
	}
}