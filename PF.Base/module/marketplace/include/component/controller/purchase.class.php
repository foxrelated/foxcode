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
 * @version 		$Id: purchase.class.php 7178 2014-03-10 19:07:21Z Fern $
 */
class Marketplace_Component_Controller_Purchase extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		$bInvoice = ($this->request()->get('invoice') ? true : false);		
		$iId = $this->request()->get('id');
		if ($bInvoice)
		{
			if (($aInvoice = Marketplace_Service_Marketplace::instance()->getInvoice($this->request()->get('invoice'))))
			{
				if ($aInvoice['user_id'] != Phpfox::getUserId())
				{
					return Phpfox_Error::display(_p('unable_to_purchase_this_item'));
				}
				
				$iId = $aInvoice['listing_id'];
				$aUserGateways = Api_Service_Gateway_Gateway::instance()->getUserGateways($aInvoice['marketplace_user_id']);
				$aActiveGateways = Api_Service_Gateway_Gateway::instance()->getActive();
				$aPurchaseDetails = array(
					'item_number' => 'marketplace|' . $aInvoice['invoice_id'],
					'currency_code' => $aInvoice['currency_id'],
					'amount' => $aInvoice['price'],
					'item_name' => $aInvoice['title'],
					'return' => $this->url()->makeUrl('marketplace.invoice', array('payment' => 'done')),
					'recurring' => '',
					'recurring_cost' => '',
					'alternative_cost' => '',
					'alternative_recurring_cost' => ''						
				);				
				
				if (is_array($aUserGateways) && count($aUserGateways))
				{
					foreach ($aUserGateways as $sGateway => $aData)
					{						
						if (is_array($aData['gateway']))
						{
							foreach ($aData['gateway'] as $sKey => $mValue)
							{
								$aPurchaseDetails['setting'][$sKey] = $mValue;
							}
						}
						else 
						{
							$aPurchaseDetails['fail_' . $sGateway] = true;
						}
						
						// Payment gateways added after user configured their payment gateway settings
						if(empty($aActiveGateways))
						{
							continue;
						}
						$bActive = false;
						foreach ($aActiveGateways as $aActiveGateway)
						{
							if($sGateway == $aActiveGateway['gateway_id'])
							{
								$bActive = true;
							}
						}
						if(!$bActive)
						{	
							$aPurchaseDetails['fail_' . $aActiveGateway['gateway_id']] = true;
						}
					}
				}
				
				$this->setParam('gateway_data', $aPurchaseDetails);
			}
		}
		
		if (!($aListing = Marketplace_Service_Marketplace::instance()->getForEdit($iId, true)))
		{
			return Phpfox_Error::display(_p('unable_to_find_the_listing_you_are_looking_for'));
		}
		
		if ($this->request()->get('process'))
		{
			if (($iInvoice = Marketplace_Service_Process::instance()->addInvoice($aListing['listing_id'], $aListing['currency_id'], $aListing['price'])))
			{
				$this->url()->send('marketplace.purchase', array('invoice' => $iInvoice));
			}
		}
		
		$this->template()->setTitle(_p('review_and_confirm_purchase'))
			->setBreadCrumb(_p('marketplace'), $this->url()->makeUrl('marketplace'))
			->setBreadCrumb(_p('review_and_confirm_purchase'), null, true)
			->assign(array(
					'aListing' => $aListing,
					'bInvoice' => $bInvoice
				)			
			);
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('marketplace.component_controller_purchase_clean')) ? eval($sPlugin) : false);
	}
}