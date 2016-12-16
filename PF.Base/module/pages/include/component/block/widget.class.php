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
 * @version 		$Id: block.class.php 103 2009-01-27 11:32:36Z Raymond_Benc $
 */
class Pages_Component_Block_Widget extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aWidgetBlocks = Pages_Service_Pages::instance()->getWidgetBlocks();
		
		if (!count($aWidgetBlocks))
		{
			return false;
		}
		
		$this->template()->assign(array(
				'aWidgetBlocks' => $aWidgetBlocks
			)
		);
		
		if (defined("PHPFOX_IN_DESIGN_MODE"))
		{
			return 'block';
		}
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('pages.component_block_widget_clean')) ? eval($sPlugin) : false);
	}
}