<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Display the image details when viewing an image.
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_Friend
 * @version 		$Id: detail.class.php 254 2009-02-23 12:36:20Z Miguel_Espinoza $
 */
class Friend_Component_Controller_Mybirthday extends Phpfox_Component
{
	public function process()
	{
		Phpfox::isUser(true);
		// get the request for just one message
		$iId = (int)$this->request()->get('id');		
		$aMessages = Friend_Service_Friend::instance()->getBirthdayMessages(Phpfox::getUserId(), $iId);
		
		$this->template()->assign(array(
			'aMessages' => $aMessages
		))
		->setBreadCrumb(_p('my_friends'), $this->url()->makeUrl('friend'))
		->setBreadCrumb(_p('birthday_e_cards'), $this->url()->makeUrl('friend.mybirthday'), true)
		->setTitle(_p('birthday_e_cards'));

		Phpfox::isModule('notification') ? Notification_Service_Process::instance()->delete('friend_birthday', $iId, Phpfox::getUserId()) : null;
	}
}