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
 * @version 		$Id: add.class.php 5538 2013-03-25 13:20:22Z Miguel_Espinoza $
 */
class Marketplace_Component_Controller_Admincp_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
        if ($iDelete = $this->request()->getInt('delete')) {
            if (Marketplace_Service_Category_Process::instance()->delete($iDelete)) {
                $this->url()->send('admincp.marketplace', null, _p('category_successfully_deleted'));
            }
        }

        $bIsEdit = false;
        $aLanguages = Language_Service_Language::instance()->getAll();
        if ($iEditId = $this->request()->getInt('id'))  {
            $bIsEdit = true;
            $aCategory = Marketplace_Service_Category_Category::instance()->getForEdit($iEditId);
            if (!isset($aCategory['category_id'])){
                $this->url()->send('admincp.marketplace', null, _p('not_found'));
            }
            $this->template()->assign([
                'aForms' => $aCategory,
                'iEditId' => $iEditId
            ]);
        }

        if ($aVals = $this->request()->getArray('val')) {
            $aVals['parent_id'] = (int) $aVals['parent_id'];
            if ($aVals['parent_id'] > 0){
                $aRedirectParam = ['parent' => $aVals['parent_id']];
            } else {
                $aRedirectParam = [];
            }
            if ($bIsEdit)  {
                if (Marketplace_Service_Category_Process::instance()->update($aVals))  {
                    $this->url()->send('admincp.marketplace', $aRedirectParam, _p('category_successfully_updated'));
                }
            } else {
                if (Marketplace_Service_Category_Process::instance()->add($aVals)) {
                    $this->url()->send('admincp.marketplace', $aRedirectParam, _p('category_successfully_added'));
                }
            }
        }

        $aParentCategories = Marketplace_Service_Category_Category::instance()->getAllParentCategories();

        $this->template()->setTitle(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("Marketplace"), $this->url()->makeUrl('admincp.marketplace'))
            ->setBreadCrumb(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')), $this->url()->makeUrl('admincp.marketplace.add'))
            ->assign([
                'bIsEdit' => $bIsEdit,
                'aLanguages' => $aLanguages,
                'aParentCategories' => $aParentCategories
            ]);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('marketplace.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
	}
}