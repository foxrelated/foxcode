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
 * @version 		$Id: add-category-list.class.php 328 2009-03-29 12:26:31Z Raymond_Benc $
 */
class Blog_Component_Block_Add_Category_List extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{		
        $sCond = 'AND c.is_active = 1';
        $sOrder = 'ordering ASC';

		$aItems = Blog_Service_Category_Category::instance()->getCategories(array($sCond), $sOrder);
		$selected = $this->getParam('aSelectedCategories');
		if ($selected) {
			$check = [];
			foreach ($selected as $select) {
				$check[] = $select['category_id'];
			}
			foreach ($aItems as $key => $item) {
				if (in_array($item['category_id'], $check)) {
					$aItems[$key]['is_active'] = true;
				}
			}
		}

		$this->template()->assign(array(
			'aItems' => $aItems
		));	
		
		(($sPlugin = Phpfox_Plugin::get('blog.component_block_add_category_list_process')) ? eval($sPlugin) : false);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('blog.component_block_add_category_list_clean')) ? eval($sPlugin) : false);
	}
}