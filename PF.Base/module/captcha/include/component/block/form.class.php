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
 * @version 		$Id: form.class.php 4348 2012-06-26 10:13:10Z Raymond_Benc $
 */
class Captcha_Component_Block_Form extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{
		$bHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? true : false);
		$sCaptchaType = Phpfox::getParam('captcha.captcha_type');
		
		$this->template()->assign(array(
				'sCaptchaType'=>$sCaptchaType,
                'sRecaptchaPublicKey'=> Phpfox::getParam('captcha.recaptcha_public_key'),
				'sImage' => $this->url()->makeUrl('captcha.image', array('id' => md5(rand(100, 1000)))),
				'sCaptchaData' => null,
				'sCatpchaType' => $this->getParam('captcha_type', null),
				'bCaptchaPopup' => $this->getParam('captcha_popup', false)
			)
		);
		
		(($sPlugin = Phpfox_Plugin::get('captcha.component_block_form_process')) ? eval($sPlugin) : false);		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('captcha.component_block_form_clean')) ? eval($sPlugin) : false);
	}
}