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
 * @version 		$Id: sentbox.class.php 2696 2011-06-30 19:30:33Z Raymond_Benc $
 */
class Mail_Component_Controller_Sentbox extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{		
		$this->url()->send('mail', array('view' => 'sent'), null, 301);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('mail.component_controller_sentbox_clean')) ? eval($sPlugin) : false);
	}
}