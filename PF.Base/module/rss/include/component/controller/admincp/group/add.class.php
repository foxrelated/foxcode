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
 * @version 		$Id: add.class.php 1179 2009-10-12 13:56:40Z Raymond_Benc $
 */
class Rss_Component_Controller_Admincp_Group_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$bIsEdit = false;		
		if (($iId = $this->request()->getInt('id')))
		{
			if (($aGroup = Rss_Service_Group_Group::instance()->getForEdit($iId)))
			{
				$bIsEdit = true;
				$this->template()->assign('aForms', $aGroup);
			}
		}
		
		if (($aVals = $this->request()->getArray('val')))
		{
			if ($bIsEdit && isset($aGroup))
			{
				if (Rss_Service_Group_Process::instance()->update($aGroup['group_id'], $aVals))
				{
					$this->url()->send('admincp.rss.group.add', array('id' => $aGroup['group_id']), _p('group_successfully_updated'));
				}				
			}
			else 
			{
				if (Rss_Service_Group_Process::instance()->add($aVals))
				{
					$this->url()->send('admincp.rss.group', null, _p('group_successfully_added'));
				}
			}
		}
		
		$this->template()->setTitle(_p('add_new_group'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
			->setBreadCrumb(_p('manage_groups'), $this->url()->makeUrl('admincp.rss.group'))
			->setBreadCrumb(_p('add_new_group'), null, true)
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
		(($sPlugin = Phpfox_Plugin::get('rss.component_controller_admincp_group_add_clean')) ? eval($sPlugin) : false);
	}
}