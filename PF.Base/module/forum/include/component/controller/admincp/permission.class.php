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
 * @version 		$Id: permission.class.php 1678 2010-07-20 11:05:43Z Raymond_Benc $
 */
class Forum_Component_Controller_Admincp_Permission extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aForum = Forum_Service_Forum::instance()->id($this->request()->getInt('id'))->getForum();
		
		$this->template()->setTitle(_p('manage_permissions'))
			->setBreadCrumb(_p('manage_forums'), $this->url()->makeUrl('admincp.forum'))
			->setBreadCrumb(_p('manage_permissions') . ': ' . $aForum['name'], null, true)
			->assign(array(
					'aUserGroups' => User_Service_Group_Group::instance()->get(),
					'iForumId' => $this->request()->getInt('id')					
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('forum.component_controller_admincp_permission_clean')) ? eval($sPlugin) : false);
	}
}