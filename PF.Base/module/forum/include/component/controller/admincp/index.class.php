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
 * @version 		$Id: index.class.php 6081 2013-06-17 14:34:34Z Raymond_Benc $
 */
class Forum_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (($aOrder = $this->request()->getArray('order')) && Forum_Service_Process::instance()->updateOrder($aOrder))
		{
			$this->url()->send('admincp.forum', null, _p('forum_order_successfully_updated'));
		}
		
		if ($iId = $this->request()->getInt('view'))
		{
			if ($sUrl = Forum_Service_Forum::instance()->getForumUrl($iId))
			{
				$this->url()->send('forum', $sUrl . '-' . $iId);
			}
		}
		
		$this->template()->setTitle(_p('manage_forums'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
			->setBreadCrumb(_p('manage_forums'), $this->url()->makeUrl('admincp.forum'))
			->setPhrase(array(
					'global_moderator_permissions',
					'moderator_permissions',
					'cancel'
				)
			)
			->setHeader(array(										
					'admin.js' => 'module_forum',
					'jquery/ui.js' => 'static_script',
					'<script type="text/javascript">$Behavior.postLoadForm = function() { $Core.forum.init({url: \'' . $this->url()->makeUrl('admincp.forum') . '\'}); }</script>'
				)
			)
			->assign(array(			
				'sForumList' => Forum_Service_Forum::instance()->getAdminCpList()
			)
		);		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('forum.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}