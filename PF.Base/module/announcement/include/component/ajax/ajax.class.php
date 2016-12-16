<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package 		Phpfox_Ajax
 * @version 		$Id: ajax.class.php 1371 2009-12-28 19:39:30Z Miguel_Espinoza $
 */
class Announcement_Component_Ajax_Ajax extends Phpfox_Ajax
{
	/**
	 * Sets the new state of the announcements (active / inactive)
	 */
	public function setActive()
	{
		Phpfox::isAdmin(true);
		(($sPlugin = Phpfox_Plugin::get('announcement.component_ajax_setactive__start')) ? eval($sPlugin) : false);
		$iId = (int)$this->get('id');
		$iState = (int)$this->get('active'); // we don't parse because its a potential risk since 0 is a valid value
		
		if ($iId < 1 || ($iState > 1 || $iState < 0))
			return false;
		$mUpdate = Announcement_Service_Process::instance()->setStatus($iId, $iState);
		if ($mUpdate !== true)
		{
			$this->alert($mUpdate);
		}
		(($sPlugin = Phpfox_Plugin::get('announcement.component_ajax_setactive__end')) ? eval($sPlugin) : false);
		return false;
	}

	/**
	 * Hides the announcement block from the Dashboard
	 */
	public function hideAnnouncement()
	{
		(($sPlugin = Phpfox_Plugin::get('announcement.component_ajax_hide__start')) ? eval($sPlugin) : false);
		if (Phpfox::getUserParam('announcement.can_close_announcement') == true)
		{
			if(Announcement_Service_Process::instance()->hide($this->get('id')))
			{
				$this->call(' $("#announcement").remove();');
				return true;
			}
		}
		(($sPlugin = Phpfox_Plugin::get('announcement.component_ajax_hide__end')) ? eval($sPlugin) : false);
		$this->alert(_p('im_afraid_you_are_not_allowed_to_close_this_announcement'));
		return false;
		
	}
}