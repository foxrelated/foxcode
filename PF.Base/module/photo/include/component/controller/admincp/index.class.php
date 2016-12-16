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
 * @version 		$Id: index.class.php 1522 2010-03-11 17:56:49Z Miguel_Espinoza $
 */
class Photo_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process() {
        
		if (($aOrder = $this->request()->getArray('order')) && Phpfox::getUserParam('photo.can_edit_photo_categories', true) && Photo_Service_Category_Process::instance()->updateOrder($aOrder)) {
			$this->url()->send('admincp.photo', null, _p('photo_category_order_successfully_updated'));
		}		
		
        $iParentCategoryId = $this->request()->getInt('parent');
		if (!Phpfox::getUserParam('photo.can_add_public_categories') && !Phpfox::getUserParam('photo.can_edit_photo_categories')) {
			return Phpfox_Error::display(_p('invalid_section'));
		}
        $aCategories = Photo_Service_Category_Category::instance()->getForManage($iParentCategoryId);
		$this->template()->setTitle(_p('manage_photo_categories'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
			->setBreadCrumb(_p('manage_photo_categories'), $this->url()->makeUrl('admincp.photo'))
			->setHeader('cache', array(
                'drag.js' => 'static_script',
                '<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'photo.categoryOrdering\'}); }</script>'
            ))->assign([
                'aCategories' => $aCategories
            ]);
        if ($iParentCategoryId){
            $aParentCategory = Photo_Service_Category_Category::instance()->getCategory($iParentCategoryId);
            $this->template()->setBreadCrumb(Phpfox::getSoftPhrase($aParentCategory['name']), $this->url()->makeUrl('admincp.photo',['parent' => $iParentCategoryId]));
        }
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('photo.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}