<?php

namespace Apps\PHPfox_Groups\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class CategoryController extends Phpfox_Component
{
	public function process()
	{
		$bSubCategory = false;
		if (($iId = $this->request()->getInt('sub'))) {
			$bSubCategory = true;
			if (($iDelete = $this->request()->getInt('delete'))) {
				if (Phpfox::getService('groups.process')->deleteCategory($iDelete, true)) {
					$this->url()->send('admincp.app', ['id' => 'PHPfox_Groups', 'val[sub]' => $iId], _p('Successfully deleted the category.'));
				}
			}
		} else {
			if (($iDelete = $this->request()->getInt('delete'))) {
				if (Phpfox::getService('groups.process')->deleteCategory($iDelete)) {
					$this->url()->send('admincp.app', ['id' => 'PHPfox_Groups'], _p('Successfully deleted the category.'));
				}
			}
		}

		$this->template()->setTitle(($bSubCategory ? _p('Manage Sub-Categories') : _p('Manage categories')))
			->setBreadCrumb(($bSubCategory ? _p('Manage Sub-Categories') : _p('Manage categories')))
			->assign([
					'bSubCategory' => $bSubCategory,
					'aCategories'  => ($bSubCategory ? Phpfox::getService('groups.category')->getForAdmin($iId) : Phpfox::getService('groups.type')->getForAdmin()),
				]
			);
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('groups.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}