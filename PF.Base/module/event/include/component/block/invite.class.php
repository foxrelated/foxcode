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
 * @version 		$Id: invite.class.php 3533 2011-11-21 14:07:21Z Raymond_Benc $
 */
class Event_Component_Block_Invite extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (!Phpfox::isUser())
		{
			return false;
		}
		
		$aEventInvites = Event_Service_Event::instance()->getInviteForUser();
		
		if (!count($aEventInvites))
		{
			return false;
		}
		
		$this->template()->assign(array(
				'sHeader' => _p('invites'),
				'aEventInvites' => $aEventInvites
			)
		);
		
		return 'block';	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('event.component_block_invite_clean')) ? eval($sPlugin) : false);
	}
}