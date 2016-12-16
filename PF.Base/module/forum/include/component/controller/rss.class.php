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
 * @version 		$Id: rss.class.php 3990 2012-03-09 15:28:08Z Raymond_Benc $
 */
class Forum_Component_Controller_Rss extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if ($this->request()->getInt('forum'))
		{
			if (!Phpfox::getParam('forum.rss_feed_on_each_forum'))
			{
				return Phpfox_Error::set(_p('rss_feeds_are_disabled_for_threads'));
			}		
			
			if (!Forum_Service_Forum::instance()->hasAccess($this->request()->getInt('forum'), 'can_view_forum'))
			{
				return Phpfox_Error::set(_p('rss_feeds_are_disabled_for_threads'));
			}
			
			$aRss = Forum_Service_Forum::instance()->getForRss($this->request()->getInt('forum'));
		}
		elseif ($this->request()->getInt('thread'))
		{
			if (!Phpfox::getParam('forum.enable_rss_on_threads'))
			{
				return Phpfox_Error::set(_p('rss_feeds_are_disabled_for_threads'));
			}		
			
			if (!Forum_Service_Forum::instance()->hasAccess($this->request()->getInt('thread'), 'can_view_thread_content'))
			{
				return Phpfox_Error::set(_p('rss_feeds_are_disabled_for_threads'));
			}			
			
			$aRss = Forum_Service_Post_Post::instance()->getForRss($this->request()->getInt('thread'));
			
			if (isset($aRss['items']) && is_array($aRss['items']) && count($aRss['items']))	
			{
				if (!Forum_Service_Forum::instance()->hasAccess($aRss['items'][0]['forum_id'], 'can_view_forum'))
				{
					return Phpfox_Error::set(_p('rss_feeds_are_disabled_for_threads'));
				}
			}
		}
		elseif ($this->request()->getInt('pages'))
		{
			if (!Phpfox::getParam('forum.rss_feed_on_each_forum'))
			{
				return Phpfox_Error::set(_p('rss_feeds_are_disabled_for_threads'));
			}		
			
			$aGroup = Pages_Service_Pages::instance()->getPage($this->request()->getInt('pages'));
			
			if (!isset($aGroup['page_id']))
			{
				return Phpfox_Error::set(_p('not_a_valid_group'));
			}

			$aItems = Forum_Service_Thread_Thread::instance()->getForRss(Phpfox::getParam('rss.total_rss_display'), null, $aGroup['page_id']);
			
			$aRss = array(
				'href' => '',
				'title' => _p('latest_threads_in_group_forum') . ': ' . $aGroup['title'],
				'description' => _p('latest_threads_on') . ': ' . $aGroup['title'],
				'items' => $aItems
			);	
		}
		
		isset($aRss) && Rss_Service_Rss::instance()->output($aRss);
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('forum.component_controller_rss_clean')) ? eval($sPlugin) : false);
	}
}