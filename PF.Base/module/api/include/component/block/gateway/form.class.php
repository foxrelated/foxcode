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
 * @version 		$Id: form.class.php 7107 2014-02-11 19:46:17Z Fern $
 */
class Api_Component_Block_Gateway_Form extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aGatewayData = $this->getParam('gateway_data');
		
		$bIsThickBox = $this->getParam('bIsThickBox');

		$this->template()->assign(array(
				'aGateways' => Api_Service_Gateway_Gateway::instance()->get($aGatewayData),
				'aGatewayData' => $aGatewayData,
				'bIsThickBox' => $bIsThickBox
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('api.component_block_list_clean')) ? eval($sPlugin) : false);
	}
}