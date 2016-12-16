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
 * @version 		$Id: categories.class.php 2323 2011-03-03 18:24:00Z Raymond_Benc $
 */
class Blog_Component_Block_Categories extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{
        $bIsProfile = false;
        $aUser = [];
        if ($this->getParam('bIsProfile') === true && ($aUser = $this->getParam('aUser'))) {
            $bIsProfile = true;
        }
        
        $aCategories = Blog_Service_Category_Category::instance()->getCategories('c.is_active = 1');
        if (!is_array($aCategories)) {
            return false;
        }
        
        if (!$aCategories) {
            return false;
        }

		$sView = $this->request()->get('view');

		foreach ($aCategories as $iKey => $aCategory) {
			$aCategories[$iKey]['url'] = ($bIsProfile ? $this->url()->permalink(array($aUser['user_name'] . '.blog.category', 'view' => $sView), $aCategory['category_id'], $aCategory['name']) : $this->url()->permalink(array('blog.category', 'view' => $sView), $aCategory['category_id'], $aCategory['name']));
		}
        
        $this->template()->assign([
                'sHeader'           => _p('categories'),
                'aCategories'       => $aCategories,
                'iCategoryBlogView' => $this->request()->getInt('req3')
            ]);
        
        (($sPlugin = Phpfox_Plugin::get('blog.component_block_categories_process')) ? eval($sPlugin) : false);
		
		return 'block';
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		$this->template()->clean(array(
				'aCategories'
			)
		);
	
		(($sPlugin = Phpfox_Plugin::get('blog.component_block_categories_clean')) ? eval($sPlugin) : false);
	}	
}