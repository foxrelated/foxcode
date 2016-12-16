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
class Core_Component_Controller_Admincp_Country_Child_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$bIsEdit = false;
		$mCountry = '';
		if (($sIso = $this->request()->get('iso')))
		{
			$mCountry = Core_Service_Country_Country::instance()->getCountry($sIso);
			
			if ($mCountry === false)
			{
				return Phpfox_Error::display(_p('not_a_valid_country'));
			}
		}
		elseif (($iChild = $this->request()->getInt('id')))
		{
			if (($aChild = Core_Service_Country_Country::instance()->getChildEdit($iChild)))
			{
				$bIsEdit = true;
				$this->template()->assign(array(
						'aForms' => $aChild
					)
				);
			}
		}
		
		if (($aVals = $this->request()->getArray('val')))
		{
			if ($bIsEdit)
			{
				if (Core_Service_Country_Child_Process::instance()->update($aChild['child_id'], $aVals))
				{					
					$this->url()->send('admincp.core.country.child', array('id' => $aChild['country_iso']), _p('state_province_successfully_updated'));
				}				
			}
			else 
			{
				if (Core_Service_Country_Child_Process::instance()->add($aVals))
				{					
					$this->url()->send('admincp.core.country.child', array('id' => $aVals['country_iso']), _p('state_province_successfully_added'));
				}
			}
		}
		
		$this->template()->setTitle(_p('country_manager'))
			->setBreadCrumb(_p('country_manager'), $this->url()->makeUrl('admincp.core.country'))
			->setBreadCrumb(($bIsEdit ? _p('editing_state_province') . ': ' : _p('adding_state_province') . ': ' . $mCountry), null, true)
			->assign(array(
					'bIsEdit' => $bIsEdit,
					'sIso' => $sIso
				)
			);
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('core.component_controller_admincp_country_child_add_clean')) ? eval($sPlugin) : false);
	}
}