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
 * @version 		$Id: add.class.php 3402 2011-11-01 09:07:31Z Miguel_Espinoza $
 */
class Pages_Component_Controller_Admincp_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$bIsEdit = false;
		$bIsSub = false;
        $aLanguages = Language_Service_Language::instance()->getAll();
		if (($iEditId = $this->request()->getInt('id')))
		{
			$aRow = Pages_Service_Type_Type::instance()->getForEdit($iEditId);
			$bIsEdit = true;
			$this->template()->assign(array(			
					'aForms' => $aRow,
					'iEditId' => $iEditId
				)
			);
		}
		
		if (($iSubtEditId = $this->request()->getInt('sub')))
		{
			$aRow = Pages_Service_Category_Category::instance()->getForEdit($iSubtEditId);
			$iEditId = $iSubtEditId;
			$bIsEdit = true;
			$bIsSub = true;
			$this->template()->assign(array(			
					'aForms' => $aRow,
					'iEditId' => $iEditId
				)
			);
		}		
		
		if (($aVals = $this->request()->getArray('val')))
		{
			if ($bIsEdit)
			{
				if (Pages_Service_Process::instance()->updateCategory($iEditId, $aVals))
				{
					if ($bIsSub)
					{
						$this->url()->send('admincp.pages', array('sub' => $aVals['type_id']), _p('successfully_updated_the_category'));
					}
					else
					{
						$this->url()->send('admincp.pages', null, _p('successfully_updated_the_category'));
					}					
				}				
			}
			else
			{
				if (Pages_Service_Process::instance()->addCategory($aVals))
				{
					$this->url()->send('admincp.pages', null, _p('successfully_created_a_new_category'));
				}
			}
		}
		
		$this->template()->setTitle(_p('add_category'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("Pages"), $this->url()->makeUrl('admincp.pages'))
			->setBreadCrumb(_p('add_category'))
			->assign(array(
				'bIsEdit' => $bIsEdit,
				'aTypes' => Pages_Service_Type_Type::instance()->get(),
                'aLanguages' => $aLanguages
			)
		)		
			->setHeader(array(
				'add.js' => 'module_pages'
			));
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('pages.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
	}
}