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
 * @package  		Module_Blog
 * @version 		$Id: index.class.php 1522 2010-03-11 17:56:49Z Miguel_Espinoza $
 */
class Blog_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{		
		if ($aDeleteIds = $this->request()->getArray('id')) {
			if (Blog_Service_Category_Process::instance()->deleteMultiple($aDeleteIds)) {
				$this->url()->send('admincp.blog', null, _p('categories_successfully_deleted'));
			}
		}
		
		list(, $aCategories) = Blog_Service_Category_Category::instance()->get();
		$this->template()->setTitle(_p('blog'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
			->setBreadCrumb(_p('blog'), $this->url()->makeUrl('admincp.blog'))
            ->setHeader([
                    'drag.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'blog.categorySubOrdering\'}); }</script>'
                ])
            ->assign([
                    'aCategories' => $aCategories
                ]);
    }
    
    /**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}