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
class Music_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
        //Get all category belong to this category
        $aGenres = Music_Service_Genre_Genre::instance()->getForManage();
        
        $this->template()->setTitle(_p('manage_genres'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('manage_genres'), $this->url()->makeUrl('admincp.music'))
            ->setHeader([
                'drag.js' => 'static_script',
                '<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'music.genreOrdering\'}); }</script>'
            ])
            ->assign([
                'aGenres' => $aGenres
            ]);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('music.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}