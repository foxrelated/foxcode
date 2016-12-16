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
 * @version 		$Id: index.class.php 6113 2013-06-21 13:58:40Z Raymond_Benc $
 */
class Rss_Component_Controller_Admincp_Group_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (($iDeleteId = $this->request()->get('delete')))
		{
			if (Rss_Service_Group_Process::instance()->delete($iDeleteId))
			{
				$this->url()->send('admincp.rss.group', null, _p('group_successfully_deleted'));
			}
		}
		
		$this->template()->setTitle(_p('manage_groups'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
			->setBreadCrumb(_p('manage_groups'), $this->url()->makeUrl('admincp.rss.group'))
			->setHeader(array(
					'drag.js' => 'static_script',
					'<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'rss.groupOrdering\'}); }</script>'
				)
			)
			->assign(array(
					'aGroups' => Rss_Service_Group_Group::instance()->get()
				)
			);			
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('rss.component_controller_admincp_group_index_clean')) ? eval($sPlugin) : false);
	}
}