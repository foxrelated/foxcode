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
 * @version 		$Id: add.class.php 982 2009-09-16 08:11:36Z Raymond_Benc $
 */
class Core_Component_Controller_Admincp_Country_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$bIsEdit = false;
		if (($sIso = $this->request()->get('id')) && ($aCountry = Core_Service_Country_Country::instance()->getForEdit($sIso)))
		{
			$bIsEdit = true;
			$this->template()->assign(array(
					'aForms' => $aCountry
				)
			);
		}
		
		if (($aVals = $this->request()->getArray('val')))
		{
			if ($bIsEdit)
			{
				if (Core_Service_Country_Process::instance()->update($aCountry['country_iso'], $aVals))
				{
					$this->url()->send('admincp.core.country.add', array('id' => $aCountry['country_iso']), _p('country_successfully_updated'));
				}
			}
			else 
			{
				if (Core_Service_Country_Process::instance()->add($aVals))
				{
					$this->url()->send('admincp.core.country.add', null, _p('country_successfully_added'));
				}				
			}
		}
		
		$this->template()->setTitle(($bIsEdit ? _p('editing_country') . ': ' : _p('add_a_country')))
			->setBreadCrumb(_p('country_manager'), $this->url()->makeUrl('admincp.core.country'))
			->setBreadCrumb(($bIsEdit ? _p('editing_country') . ': ' : _p('add_a_country')), $this->url()->current(), true)
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
		(($sPlugin = Phpfox_Plugin::get('core.component_controller_admincp_country_add_clean')) ? eval($sPlugin) : false);
	}
}