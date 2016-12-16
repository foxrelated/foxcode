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
 * @package  		Module_Blog
 * @version 		$Id: add.class.php 1522 2010-03-11 17:56:49Z Miguel_Espinoza $
 */
class Blog_Component_Controller_Admincp_Add extends Phpfox_Component
{
	public function process() {
        $bIsEdit = false;
        if ($iDeleteId = $this->request()->getInt('delete')) {
            if (Blog_Service_Category_Process::instance()->delete($iDeleteId)) {
                $this->url()->send('admincp.blog', null, _p('Category successfully deleted'));
            }
        }
        $aLanguages = Language_Service_Language::instance()->getAll();
        if (($iEditId = $this->request()->getInt('id'))) {
            $aRow = Blog_Service_Category_Category::instance()->getForEdit($iEditId);
            $bIsEdit = true;
            $this->template()->assign(array(
                    'aForms' => $aRow,
                    'iEditId' => $iEditId
                )
            );
        }
        $aValidation = [];
        foreach ($aLanguages as $aLanguage){
            $aValidation['name_' . $aLanguage['language_id']] = _p('provide_blog_category') . ' ('. $aLanguage['title'] . ')';
        }

		$oValid = Phpfox_Validator::instance()->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));

		if ($aVals = $this->request()->getArray('val')) {
			if ($oValid->isValid($aVals)) {
                if ($bIsEdit){
                    Blog_Service_Category_Process::instance()->update($iEditId, $aVals);
                    $this->url()->send('admincp.blog', null, _p('Category successfully updated'));
                } elseif (Blog_Service_Category_Process::instance()->add($aVals, '0')) {
					$this->url()->send('admincp.blog', null, _p('category_successfully_added'));
				}
			}
		}		
		$this->template()->setTitle(_p('add_category'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
			->setBreadCrumb(_p('blog'), $this->url()->makeUrl('admincp.blog'))
			->setBreadCrumb(_p('add_category'), $this->url()->makeUrl('admincp.blog.add'))
			->assign(array(
				'sCreateJs' => $oValid->createJS(),
				'sGetJsForm' => $oValid->getJsForm(),
                'aLanguages' => $aLanguages,
                'bIsEdit' => $bIsEdit
			)
		);		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
	}
}