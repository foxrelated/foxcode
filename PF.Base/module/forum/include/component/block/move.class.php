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
class Forum_Component_Block_Move extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aThread = Forum_Service_Thread_Thread::instance()->getActualThread($this->request()->get('thread_id'));
		
		if (!isset($aThread['thread_id']))
		{
			return Phpfox_Error::display(_p('not_a_valid_thread_to_move'));
		}
		
		$this->template()->assign(array(
				'sForums' => Forum_Service_Forum::instance()->active($aThread['forum_id'])->getJumpTool(true),
				'aThread' => $aThread
			)
		);
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('forum.component_block_move_clean')) ? eval($sPlugin) : false);
	}
}