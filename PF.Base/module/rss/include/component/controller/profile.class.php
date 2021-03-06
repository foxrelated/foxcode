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
 * @version 		$Id: profile.class.php 1179 2009-10-12 13:56:40Z Raymond_Benc $
 */
class Rss_Component_Controller_Profile extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{		
		$aUser = $this->getParam('aUser');
		
		if (!User_Service_Privacy_Privacy::instance()->hasAccess($aUser['user_id'], 'rss.can_subscribe_profile'))
		{
			return Phpfox_Error::display(_p('user_has_disabled_rss_feeds'));
		}
		
		if (($sContent = Rss_Service_Rss::instance()->getUserFeed($aUser)))
		{			
			header('Content-type: text/xml; charset=utf-8');
			echo $sContent;
			exit;
		}		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('rss.component_controller_profile_clean')) ? eval($sPlugin) : false);
	}
}