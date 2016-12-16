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
 * @package  		Module_Subscribe
 * @version 		$Id: index.class.php 1321 2009-12-15 18:19:30Z Raymond_Benc $
 */
class Subscribe_Component_Controller_Index extends Phpfox_Component
{	
	public function process()
	{
		if (Phpfox::getParam('subscribe.enable_subscription_packages'))
		{		
			$this->template()->setTitle(_p('membership_packages'))
				->setBreadCrumb(_p('membership_packages'))
				->assign(array(
					'aPackages' => Subscribe_Service_Subscribe::instance()->getPackages()
				)
			);		
		}
		else 
		{
			$this->template()->setTitle(_p('membership_notice'))->setBreadCrumb(_p('membership_notice'));
		}
	}
}