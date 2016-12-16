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
 * @version 		$Id: index.class.php 6186 2013-06-28 14:19:43Z Miguel_Espinoza $
 */
class Marketplace_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
        if ($iParentId = $this->request()->getInt('parent')){
            $aParentCategory = Marketplace_Service_Category_Category::instance()->getForEdit($iParentId);
        }

        //Get all category belong to this category
        $aCategories = Marketplace_Service_Category_Category::instance()->getForManage($iParentId);

        $this->template()->setTitle(_p('manage_categories'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('manage_categories'), $this->url()->makeUrl('admincp.marketplace'))
            ->setHeader([
                'drag.js' => 'static_script',
                '<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'marketplace.categoryOrdering\'}); }</script>',
            ])->assign([
                'aCategories' => $aCategories
            ]);
        if ($iParentId > 0 && isset($aParentCategory['category_id'])){
            $this->template()->setBreadCrumb(Phpfox::getSoftPhrase($aParentCategory['name']), $this->url()->makeUrl('admincp.marketplace', ['parent' => $iParentId]));
        }
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('marketplace.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}