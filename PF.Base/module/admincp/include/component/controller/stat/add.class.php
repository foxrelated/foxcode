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
 * @version 		$Id: add.class.php 977 2009-09-12 15:29:04Z Raymond_Benc $
 */
class Admincp_Component_Controller_Stat_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$bIsEdit = false;		
		if (($iId = $this->request()->getInt('id')))
		{
			if (($aStat = Core_Service_Stat_Stat::instance()->getForEdit($iId)))
			{
				$bIsEdit = true;
				$this->template()->assign('aForms', $aStat);
			}
		}
		
		if (($aVals = $this->request()->getArray('val')))
		{
			if ($bIsEdit)
			{
				if (Core_Service_Stat_Process::instance()->update($aStat['stat_id'], $aVals))
				{
					$this->url()->send('admincp.stat.add', array('id' => $aStat['stat_id']), _p('stat_successfully_updated'));
				}				
			}
			else 
			{
				if (Core_Service_Stat_Process::instance()->add($aVals))
				{
					$this->url()->send('admincp.stat', null, _p('stat_successfully_added'));
				}
			}
		}
		
		$this->template()->setTitle(_p('add_new_stat'))
			->setBreadCrumb(_p('manage_stats'), $this->url()->makeUrl('admincp.stat'))
			->setBreadCrumb(_p('add_new_stat'), null, true)
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
		(($sPlugin = Phpfox_Plugin::get('admincp.component_controller_stat_add_clean')) ? eval($sPlugin) : false);
	}
}