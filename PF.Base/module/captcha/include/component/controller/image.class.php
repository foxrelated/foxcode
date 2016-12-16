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
 * @package  		Module_Captcha
 * @version 		$Id: image.class.php 225 2009-02-13 13:24:59Z Raymond_Benc $
 */
class Captcha_Component_Controller_Image extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{		
		$iLimit = Phpfox::getParam('captcha.captcha_limit');
		$oServiceCaptcha = Captcha_Service_Captcha::instance();
		$sCode = $oServiceCaptcha->generateCode($iLimit);
		$oServiceCaptcha->setHash($sCode);
		echo $oServiceCaptcha->displayCaptcha($sCode);
		exit;
	}
}