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
 * @package 		Phpfox_Ajax
 * @version 		$Id: ajax.class.php 4620 2012-09-09 12:55:15Z Raymond_Benc $
 */
class Api_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function processActivityPayment()
	{
		$aParts = explode('|', $this->get('item_number'));

		if (User_Service_Process::instance()->purchaseWithPoints($aParts[0], $aParts[1], $this->get('amount'), $this->get('currency_code')))
		{
			Phpfox::addMessage(_p('purchase_successfully_completed_dot'));
			
			$this->call('window.location.href = \'' . Phpfox_Url::instance()->makeUrl('') . '\'');
		}
		else {
			$this->alert(_p('error_purchase_can_not_complete'));
		}
	}
	
	public function updateGatewayActivity()
	{
		Api_Service_Gateway_Process::instance()->updateActivity($this->get('gateway_id'), $this->get('active'));
	}
	
	public function updateGatewayTest()
	{
		if (Api_Service_Gateway_Process::instance()->updateTest($this->get('gateway_id'), $this->get('active')))
		{
			
		}			
	}
}