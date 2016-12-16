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
 * @package  		Module_Mail
 * @version 		$Id: compose.class.php 4607 2012-08-27 07:23:45Z Miguel_Espinoza $
 */
class Pages_Component_Controller_Admincp_Claim extends Phpfox_Component
{

	public function process()
	{
		$aClaims = Pages_Service_Pages::instance()->getClaims();
		
		$this->template()->setTitle(_p('Claims'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("Pages"), $this->url()->makeUrl('admincp.pages'))
			->setBreadCrumb(_p('Claims'))
			->setHeader(array(
				'claim.js' => 'module_pages'
			))
			->assign(array(
				'aClaims' => $aClaims			
			))
			->setPhrase(array(
					'are_you_sure_you_want_to_transfer_ownership',
					'are_you_sure_you_want_to_deny_this_claim_request'
				)
			);
	}
}