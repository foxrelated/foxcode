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
 * @version 		$Id: block.class.php 103 2009-01-27 11:32:36Z Raymond_Benc $
 */
class User_Component_Block_Custom extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$this->setParam('aUser', array(
				'user_id' => $this->request()->get('user_id'),
				'user_group_id' => $this->request()->get('user_group_id')
			)
		);
		
		$this->template()->assign(array(
				'aSettings' => Custom_Service_Custom::instance()->getForEdit(array('user_main', 'user_panel', 'profile_panel'), $this->request()->get('user_id'), $this->request()->get('user_group_id'))
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_block_custom_clean')) ? eval($sPlugin) : false);
	}
}