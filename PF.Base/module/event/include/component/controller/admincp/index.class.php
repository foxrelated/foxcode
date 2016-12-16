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
 * @version 		$Id: index.class.php 6219 2013-07-09 06:43:36Z Raymond_Benc $
 */
class Event_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process() {
        if ($iParentId = $this->request()->getInt('parent')){
            $aParentCategory = Event_Service_Category_Category::instance()->getForEdit($iParentId);
        }
        
        //Get all category belong to this category
        $aCategories = Event_Service_Category_Category::instance()->getForManage($iParentId);

		$this->template()->setTitle(_p('manage_categories'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
			->setBreadCrumb(_p('manage_categories'), $this->url()->makeUrl('admincp.event'))
			->setHeader([
                'drag.js' => 'static_script',
                '<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'event.categoryOrdering\'}); }</script>',
            ])->assign([
                'aCategories' => $aCategories
            ]);
        if ($iParentId > 0 && isset($aParentCategory['category_id'])){
            $this->template()->setTitle(Phpfox::getSoftPhrase($aParentCategory['name']))
                ->setBreadCrumb(Phpfox::getSoftPhrase($aParentCategory['name']), $this->url()->makeUrl('admincp.event', ['parent' => $iParentId]));
        }
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('event.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}