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
 * @version 		$Id: index.class.php 1014 2009-09-20 10:22:25Z Raymond_Benc $
 */
class Attachment_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (($sDeleteId = $this->request()->get('delete')))
		{
			if (Attachment_Service_Process::instance()->deleteType($sDeleteId))
			{
				$this->url()->send('admincp.attachment', null, _p('attachment_successfully_deleted'));
			}
		}
		
		$this->template()->setTitle(_p('attachments_title'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
			->setBreadCrumb(_p('attachments_title'), $this->url()->makeUrl('admincp.attachment'))
			->setSectionTitle(_p('attachment_file_types'))
			->setActionMenu([
                _p('admin_menu_add_new_type') => [
					'url' => $this->url()->makeUrl('admincp.attachment.add'),
					'class' => 'popup'
				]
			])
			->assign(array(
					'aRows' => Attachment_Service_Type::instance()->get()
				)
			);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('attachment.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}