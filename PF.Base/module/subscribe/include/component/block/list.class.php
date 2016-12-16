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
 * @version 		$Id: list.class.php 5485 2013-03-11 09:44:15Z Miguel_Espinoza $
 */
class Subscribe_Component_Block_List extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (Phpfox::isUser())
		{
			$aGroup = User_Service_Group_Group::instance()->getGroup(Phpfox::getUserBy('user_group_id'));
		}
		
		$this->template()->assign(array(
				'aPurchases' => (Phpfox::isUser() ? Subscribe_Service_Purchase_Purchase::instance()->get(Phpfox::getUserId(), 5) : array()),
				'aPackages' => Subscribe_Service_Subscribe::instance()->getPackages((Phpfox::isUser() ? false : true), (Phpfox::isUser() ? true: false)),
				'aGroup' => ((Phpfox::isUser() && isset($aGroup)) ? $aGroup : array()),
				'bIsOnSignup' => ($this->getParam('on_signup') ? true : false)
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('subscribe.component_block_list_clean')) ? eval($sPlugin) : false);
	}
}