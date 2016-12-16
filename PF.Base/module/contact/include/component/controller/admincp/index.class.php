<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_Contact
 * @version 		$Id: index.class.php 6113 2013-06-21 13:58:40Z Raymond_Benc $
 */
class Contact_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$this->template()->setTitle(_p('categories'))
		->setBreadCrumb(_p('categories'), $this->url()->makeUrl('admincp.contact'))
		->assign(array(
				'aCategories' => Contact_Service_Contact::instance()->getCategories(),
				'aLanguages' => Language_Service_Language::instance()->getAll()
			)
		)
		->setHeader('cache', array(
				'drag.js' => 'static_script',
				'<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'contact.manageOrdering\'}); }</script>'
			)
		);

		// is it adding a new category
		if ("add" == $this->request()->get('action'))
		{
			if (Contact_Service_Process::instance()->addCategory($this->request()->get('new_category')))
			{

				$this->url()->send('admincp.contact',null,_p('category_succesfully_added'));
			}
			else
			{
				$this->url()->send('admincp.contact',null,_p('category_could_not_be_added'));
			}
		}

		// is it deleting categories
		if ($this->request()->get('delete') && $aDeleteIds = $this->request()->getArray('id'))
		{
			if (Contact_Service_Process::instance()->deleteMultiple($aDeleteIds))
			{
				$this->url()->send('admincp.contact', null, _p('categories_successfully_deleted'));
			}
		}

		// is it updating categories
		if ($this->request()->get('update'))
		{
			$aUpdatedCategories = array();
			foreach ($this->request()->getArray('id') as $iId)
			{
				$aUpdatedCategories[] = array(
					'category_id' => (int)$iId,
					'ordering' => $this->request()->get('order_id_'.$iId),
					'title' => $this->request()->get('title_'.$iId)
				);
			}
			
			if (Contact_Service_Process::instance()->updateMultiple($aUpdatedCategories))
			{
				$this->url()->send('admincp.contact', null, _p('categories_successfully_edited'));
			}
			else
			{
				$this->url()->send('admincp.contact', null, _p('categories_not_edited'));
			}
		}
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	(($sPlugin = Phpfox_Plugin::get('contact.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}