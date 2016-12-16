<?php

namespace Apps\PHPfox_Groups\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AddCategoryController extends Phpfox_Component
{
	public function process()
	{
		$bIsEdit = false;
		$bIsSub = false;
        $aLanguages = \Language_Service_Language::instance()->getAll();
		if (($iEditId = $this->request()->getInt('id'))) {
			$aRow = Phpfox::getService('groups.type')->getForEdit($iEditId);
			$bIsEdit = true;
			$this->template()->assign([
					'aForms'  => $aRow,
					'iEditId' => $iEditId,
				]
			);
		}

		if (($iSubtEditId = $this->request()->getInt('sub'))) {
			$aRow = Phpfox::getService('groups.category')->getForEdit($iSubtEditId);
			$iEditId = $iSubtEditId;
			$bIsEdit = true;
			$bIsSub = true;
			$this->template()->assign([
					'aForms'  => $aRow,
					'iEditId' => $iEditId,
				]
			);
		}

		if (($aVals = $this->request()->getArray('val'))) {
			if ($bIsEdit) {
				if (Phpfox::getService('groups.process')->updateCategory($iEditId, $aVals)) {
					if ($bIsSub) {
						$this->url()->send('admincp.app', ['id' => 'PHPfox_Groups', 'val[sub]' => $aVals['type_id']], _p('Successfully updated the category.'));
					} else {
						$this->url()->send('admincp.app', ['id' => 'PHPfox_Groups'], _p('Successfully updated the category.'));
					}
				}
			} else {
				if (Phpfox::getService('groups.process')->addCategory($aVals)) {
					$this->url()->send('admincp.app', ['id' => 'PHPfox_Groups'], _p('Successfully created a new category.'));
				}
			}
		}

		$this->template()->setTitle(_p('Add category'))
			->setBreadCrumb(_p('Add category'))
			->assign([
					'bIsEdit' => $bIsEdit,
					'aTypes'  => Phpfox::getService('groups.type')->getForAdmin(true),
                    'aLanguages' => $aLanguages
				]
			);
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('groups.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
	}
}