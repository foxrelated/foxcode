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
 * @version 		$Id: block.class.php 3325 2011-10-20 08:33:09Z Miguel_Espinoza $
 */
class Custom_Component_Block_Static extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		
		$iId = $this->getParam('field-id');
		//TODO might this block not use anymore
		$aField = Custom_Service_Custom::instance()->getStaticCustomField($iId);
		
		$this->template()->assign(array(
			'aField' => $aField
		));
				
				
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('custom.component_block_block_clean')) ? eval($sPlugin) : false);
	}
}