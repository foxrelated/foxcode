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
 * @version 		$Id: read.class.php 1603 2010-05-30 06:57:25Z Raymond_Benc $
 */
class Forum_Component_Controller_Read extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		
		if ($this->request()->getInt('forum'))
		{		
			$aForum = Forum_Service_Forum::instance()->id($this->request()->getInt('forum'))->getForum();
					
			if (!isset($aForum['forum_id']))
			{				
				return Phpfox_Error::display(_p('not_a_valid_forum'));
			}		
	
			if (Forum_Service_Thread_Process::instance()->markRead($aForum['forum_id']))
			{
				$this->url()->send('forum', array($aForum['name_url'] . '-' . $aForum['forum_id']), _p('forum_successfully_marked_as_read'));
			}
		}
		elseif (($sModule = $this->request()->get('module')) && ($iItemId = $this->request()->getInt('item')))
		{
			$aCallback = Phpfox::callback($sModule . '.addForum', $iItemId);
			if (isset($aCallback['module']))
			{
				if (Forum_Service_Thread_Process::instance()->markRead(0, $aCallback['item']))
				{
					$this->url()->send($aCallback['url_home'], array('forum'), _p('forum_successfully_marked_as_read'));
				}				
			}
		}
		else 
		{
			$aForums = Forum_Service_Forum::instance()->live()->getForums();
			foreach ($aForums as $aForum)
			{
                Forum_Service_Thread_Process::instance()->markRead($aForum['forum_id']);
				
				$aChildrens = Forum_Service_Forum::instance()->id($aForum['forum_id'])->getChildren();
				
				if (!is_array($aChildrens))
				{
					continue;
				}
				
				foreach ($aChildrens as $iForumid)
				{
                    Forum_Service_Thread_Process::instance()->markRead($iForumid);
				}
			}
			
			$this->url()->send('forum', null, _p('forum_successfully_marked_as_read'));
		}
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('forum.component_controller_read_clean')) ? eval($sPlugin) : false);
	}
}