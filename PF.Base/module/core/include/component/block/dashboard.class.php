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
 * @version 		$Id: dashboard.class.php 5616 2013-04-10 07:54:55Z Miguel_Espinoza $
 */
class Core_Component_Block_Dashboard extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{		
		$sImage = Phpfox::getLib('image.helper')->display(array_merge(array('user' => User_Service_User::instance()->getUserFields(true)), array(
					'path' => 'core.url_user',
					'file' => Phpfox::getUserBy('user_image'),
					'suffix' => '_120',
					'max_width' => 100,
					'max_height' => 100
				)
			)
		);
		
		$aGroup = User_Service_Group_Group::instance()->getGroup(Phpfox::getUserBy('user_group_id'));
		
		$this->template()->assign(array(
				'aUserGroup' => $aGroup,
				'sImage' => $sImage,				
				'aDashboards' => Core_Service_Core::instance()->getDashboardLinks(),
				'sBlockLocation' => Phpfox_Module::instance()->getBlockLocation('core.dashboard'),
				'sTotalUserViews' => Phpfox::getUserBy('total_view'),
				'sLastLogin' => Phpfox::getLib('date')->convertTime(Phpfox::getUserBy('last_login'), 'core.profile_time_stamps')
			)
		);		
		
		if (!PHPFOX_IS_AJAX)
		{
			$aMenus = array();
			foreach (Core_Service_Core::instance()->getDashboardMenus() as $sPhrase => $sLink)
			{
				$aMenus[_p($sPhrase)] = $sLink;
			}
			
			$this->template()->assign(array(
					'sHeader' => _p('dashboard'),
					'aMenu' => $aMenus
				)
			);			
			
			return 'block';
		}
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('core.component_block_dashboard_clean')) ? eval($sPlugin) : false);
	}
	
	public function widget()
	{
		return true;	
	}
}