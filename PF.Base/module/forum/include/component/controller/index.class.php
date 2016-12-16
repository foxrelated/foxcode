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
 * @package  		Module_Forum
 * @version 		$Id: index.class.php 5219 2013-01-28 12:15:53Z Miguel_Espinoza $
 */
class Forum_Component_Controller_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (($sLegacyTitle = $this->request()->get('req2')) && !empty($sLegacyTitle))
		{
			if (($sLegacyThread = $this->request()->get('req3')) && !empty($sLegacyThread) && !is_numeric($sLegacyTitle))
			{
				$aLegacyItem = Core_Service_Core::instance()->getLegacyItem(array(
						'field' => array('thread_id', 'title'),
						'table' => 'forum_thread',		
						'redirect' => 'forum.thread',
						'title' => $sLegacyThread
					)
				);				
			}
			else
			{
				$aForumParts = explode('-', $sLegacyTitle);
				if (isset($aForumParts[1]))
				{
					$aLegacyItem = Core_Service_Core::instance()->getLegacyItem(array(
							'field' => array('forum_id', 'name'),
							'table' => 'forum',		
							'redirect' => 'forum',
							'search' => 'forum_id',
							'title' => $aForumParts[1]
						)
					);
				}
			}
		}			
		
		Phpfox::getUserParam('forum.can_view_forum', true);
		
		$aParentModule = $this->getParam('aParentModule');

		if (Phpfox::getParam('core.phpfox_is_hosted') && empty($aParentModule))
		{
			$this->url()->send('');
		}
		else if (empty($aParentModule) && $this->request()->get('view') == 'new')
		{
		    $aDo = explode('/',$this->request()->get('do'));
		    if ($aDo[0] == 'mobile' || (isset($aDo[1]) && $aDo[1] == 'mobile'))
		    {
				Phpfox_Module::instance()->getComponent('forum.forum', array('bNoTemplate' => true), 'controller');
				return null;
		    }		    
		}
		    
		if ($this->request()->get('req2') == 'topics' || $this->request()->get('req2') == 'posts')
		{
			return Phpfox_Module::instance()->setController('error.404');
		}
				
		$this->template()->setBreadCrumb(_p('forum'), $this->url()->makeUrl('forum'))
			->setPhrase(array(
					'provide_a_reply',
					'adding_your_reply',
					'are_you_sure',
					'post_successfully_deleted',
					'reply_multi_quoting'
				)
			)			
			->setHeader('cache', array(
					'forum.js' => 'module_forum'
				)
			);
		
		if ($aParentModule !== null)
		{
			Phpfox_Module::instance()->getComponent('forum.forum', array('bNoTemplate' => true), 'controller');

			return null;
		}
		
		if ($this->request()->getInt('req2') > 0)
		{
			return Phpfox_Module::instance()->setController('forum.forum');
		}

		if ($aParentModule === null) {
			$oSearch = Forum_Service_Forum::instance()->getSearchFilter();
		}
		
		$this->setParam('bIsForum', true);

		// $aIds = [];
		if (redis()->enabled() && redis()->exists('forums')) {
			$aForums = redis()->get_as_array('forums');
			foreach ($aForums as $key => $value) {
				$aForums[$key]['total_thread'] = redis()->get('threads/total_thread/' . $value['forum_id']);
				$aForums[$key]['total_post'] = redis()->get('threads/total_post/' . $value['forum_id']);

				if (isset($value['sub_forum'])) {
					foreach ($value['sub_forum'] as $sub_key => $sub_value) {
						$aForums[$key]['sub_forum'][$sub_key]['total_thread'] = redis()->get('threads/total_thread/' . $sub_value['forum_id']);
						$aForums[$key]['sub_forum'][$sub_key]['total_post'] = redis()->get('threads/total_post/' . $sub_value['forum_id']);
					}
				}
			}

		} else {
			$aForums = Forum_Service_Forum::instance()->live()->getForums();

			if (redis()->enabled()) {
				redis()->set('forums', $aForums);
			}
		}

		$this->template()->setTitle(_p('forum'))
			->assign(array(
				 'aForums' => $aForums,
				 'bHasCategory' => Forum_Service_Forum::instance()->hasCategory(),
				 'aCallback' => null,
				 'aSearchValues' => array(
					 'user' => '',
					 'adv_search' => 0
				 ),
				 'sForumList' => Forum_Service_Forum::instance()->getJumpTool(true, false, array(), true)
			)
		);
		
		Forum_Service_Forum::instance()->buildMenu();
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('forum.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}