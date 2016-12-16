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
 * @version 		$Id: add.class.php 2000 2010-10-29 11:24:24Z Raymond_Benc $
 */
class Admincp_Component_Controller_Privacy_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (($iDeleteId = $this->request()->getInt('delete')) && Admincp_Service_Process::instance()->deletePrivacyRule($iDeleteId))
		{
			$this->url()->send('admincp.privacy', array(), 'Successfully deleted this rule.');
		}
		
		if (($aVals = $this->request()->getArray('val')))
		{
			if (Admincp_Service_Process::instance()->addNewPrivacyRule($aVals))
			{
				$this->url()->send('admincp.privacy', array(), 'Successfully added a new rule.');	
			}
		}
		
		$this->template()->setTitle(_p('admincp_priacy_control'))
			->setBreadCrumb(_p('admincp_priacy_control'))
			->assign(array(
					'aUserGroups' => User_Service_Group_Group::instance()->get(),
					'aRules' => Admincp_Service_Admincp::instance()->getAdmincpRules()
				)
			);
	}
}