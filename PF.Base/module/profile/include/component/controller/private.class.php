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
 * @version 		$Id: private.class.php 4755 2012-09-25 08:00:44Z Miguel_Espinoza $
 */
class Profile_Component_Controller_Private extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		define('PHPFOX_PROFILE_PRIVACY', true);
		
		$aUser = $this->getParam('aUser');
		$bCanFrRequest = true;
		if (User_Service_Block_Block::instance()->isBlocked($aUser['user_id'], Phpfox::getUserId()))
		{
			$bCanFrRequest = false;
		}
		$this->template()->setTitle($aUser['full_name'])
			->assign(array(
				'aUser' => $aUser,
				'bIsFriend' => (Phpfox::getUserId() && Phpfox::isModule('friend') ? Friend_Service_Friend::instance()->isFriend(Phpfox::getUserId(), $aUser['user_id']) : false),
				'bIsBlocked' => (Phpfox::isUser() ? User_Service_Block_Block::instance()->isBlocked(Phpfox::getUserId(), $aUser['user_id']) : false),
				'bCanFrRequest' => $bCanFrRequest
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('profile.component_controller_private_clean')) ? eval($sPlugin) : false);
	}
}