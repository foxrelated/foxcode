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
 * @version 		$Id: info.class.php 1339 2009-12-19 00:37:55Z Raymond_Benc $
 */
class Core_Component_Block_Info extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aUser = User_Service_User::instance()->get(Phpfox::getUserId(), true);
		$aGroup = User_Service_Group_Group::instance()->getGroup($aUser['user_group_id']);
		$aInfo = array(
			_p('membership') => (empty($aGroup['icon_ext']) ? '' : '<img src="' . Phpfox::getParam('core.url_icon') . $aGroup['icon_ext'] . '" class="v_middle" alt="' . Phpfox_Locale::instance()->convert($aGroup['title']) . '" title="' . Phpfox_Locale::instance()->convert($aGroup['title']) . '" /> ') . $aGroup['prefix'] . Phpfox_Locale::instance()->convert($aGroup['title']) . $aGroup['suffix'],
			_p('activity_points') => $aUser['activity_points'],
			_p('profile_views') => $aUser['total_view'],
			_p('space_used') => (Phpfox::getUserParam('user.total_upload_space') === 0 ? _p('space_total_out_of_unlimited', array('space_total' => Phpfox_File::instance()->filesize($aUser['space_total']))) : _p('space_total_out_of_total', array('space_total' => Phpfox_File::instance()->filesize($aUser['space_total']), 'total' => Phpfox::getUserParam('user.total_upload_space')))),
			_p('member_since') => Phpfox::getLib('date')->convertTime($aUser['joined'], 'core.profile_time_stamps')
		);
		
		if (Phpfox::isModule('rss'))
		{
			$aInfo[_p('rss_subscribers')] = '<a href="#" onclick="tb_show(\'' . _p('rss_subscribers_log') . '\', $.ajaxBox(\'rss.log\', \'height=500&amp;width=500\')); return false;">' . $aUser['rss_count'] . '</a>';
		}
		
		$this->template()->assign(array(
				'aInfos' => $aInfo
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('core.component_block_info_clean')) ? eval($sPlugin) : false);
	}
}