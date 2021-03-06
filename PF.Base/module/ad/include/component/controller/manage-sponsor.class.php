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
 * @version 		$Id: manage-sponsor.class.php 6440 2013-08-12 11:44:19Z Raymond_Benc $
 */
class Ad_Component_Controller_Manage_Sponsor extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		
		$sView = $this->request()->get('view');
		
		$aCond = array();
		switch ($sView)
		{
			case 'pending':
				$aCond[] = 'AND s.is_custom = 2'; // pending approval
				break;
			case 'payment':
				$aCond[] = 'AND s.is_custom = 0';
				break;				
			case 'denied':
				$aCond[] = 'AND s.is_custom = 4';
				break;				
			default:
				$aCond[] = 'AND s.is_custom = 3';
				break;	
		}		
		$aCond[] = 'AND s.user_id = ' . Phpfox::getUserId();
		
		Ad_Service_Ad::instance()->getSectionMenu();
		
		$aAds = Ad_Service_Ad::instance()->getSponsorForUser($aCond);
		
		$this->template()->setTitle(_p('ad_management'))
			->setFullSite()
			->setBreadCrumb(_p('advertise'), $this->url()->makeUrl('ad'))
			->setBreadCrumb(_p('sponsorships'), $this->url()->makeUrl('ad.manage-sponsor'), true)
			->setHeader(array(
					'table.css' => 'style_css',
					'manage.js' => 'module_ad'
				)
			)
			->assign(array(
					'aAds' => $aAds,
					'sView' => $sView
				)
			);			
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('ad.component_controller_manage_clean')) ? eval($sPlugin) : false);
	}
}