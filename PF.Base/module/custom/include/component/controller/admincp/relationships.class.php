<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 *  
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Admincp
 * @version 		$Id: index.class.php 2536 2011-04-14 19:37:29Z Raymond_Benc $
 * 
 */
class Custom_Component_Controller_Admincp_Relationships extends Phpfox_Component
{

	public function process()
	{
		if (($aVals = $this->request()->getArray('val')))
		{
			if (Custom_Service_Relation_Process::instance()->add($aVals))
			{
				$this->url()->send('admincp.custom.relationships',array(), _p('status_added'));
			}
		}
		
		if ( ($iId = $this->request()->getInt('delete')))
		{
			if (Custom_Service_Relation_Process::instance()->delete($iId))
			{
				$this->url()->send('admincp.custom.relationships',array(), _p('status_deleted'));
			}
		}
		
		
		$aStatuses = Custom_Service_Relation_Relation::instance()->getAll();
		/* If we're editing lets make it easier and just find the one we're looking for here */
		if ($iEdit = $this->request()->getInt('edit'))
		{
			$aEdit = array();
			foreach ($aStatuses as $aStatus)
			{
				if ($aStatus['relation_id'] == $iEdit)
				{
					$aEdit = $aStatus;
					break;
				}
			}
			if (empty($aEdit))
			{
				Phpfox_Error::display(_p('not_found'));
			}
			else
			{
				$this->template()->assign(array('aEdit' => $aEdit));
			}
		}

		$this->template()->setTitle(_p('admin_menu_manage_relationships'))
				->setBreadCrumb(_p('admin_menu_manage_relationships'))
				->setSectionTitle(_p('custom_relationship_statuses'))
				->assign(array(
					'aStatuses' => $aStatuses
				));
	}

}