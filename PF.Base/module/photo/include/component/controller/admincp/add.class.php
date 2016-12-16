<?php

defined('PHPFOX') or exit('NO DICE!');

class Photo_Component_Controller_Admincp_Add extends Phpfox_Component
{
    public function process(){
        $bIsEdit = false;
        $iDeleteId = $this->request()->getInt('delete');
        if ($iDeleteId && Phpfox::getUserParam('photo.can_edit_photo_categories', true)) {
            if (Photo_Service_Category_Process::instance()->delete($iDeleteId)) {
                $this->url()->send('admincp.photo', null, _p('Category successfully deleted'));
            }
        }
        $aLanguages = Language_Service_Language::instance()->getAll();
        if (($iEditId = $this->request()->getInt('id'))) {
            $aRow = Photo_Service_Category_Category::instance()->getCategory($iEditId);
            $bIsEdit = true;
            $this->template()->assign(array(
                    'aForms' => $aRow,
                    'iEditId' => $iEditId
                )
            );
        }
        $aValidation = [];
        foreach ($aLanguages as $aLanguage){
            $aValidation['name_' . $aLanguage['language_id']] = _p('provide_a_name_for_your_photo_category') . ' ('. $aLanguage['title'] . ')';
        }

        $oValid = Phpfox_Validator::instance()->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));

        if ($aVals = $this->request()->getArray('val')) {
            $aVals['parent_id'] = (int) $aVals['parent_id'];
            if ($aVals['parent_id'] > 0){
                $aRedirectParam = ['parent' => $aVals['parent_id']];
            } else {
                $aRedirectParam = [];
            }
            if ($oValid->isValid($aVals)) {
                if ($bIsEdit) {
                    Phpfox::getUserParam('photo.can_edit_photo_categories', true);
                    if (Photo_Service_Category_Process::instance()->update($aVals)) {
                        $this->url()->send('admincp.photo', $aRedirectParam, _p('photo_category_successfully_updated'));
                    }
                } else {
                    Phpfox::getUserParam('photo.can_add_public_categories', true);
                    if (Photo_Service_Category_Process::instance()->add($aVals)) {
                        $this->url()->send('admincp.photo', $aRedirectParam, _p('photo_category_successfully_added'));
                    }
                }
            }
        }

        $this->template()->setTitle(_p('Add photo categories'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("Photo"), $this->url()->makeUrl('admincp.photo'))
            ->setBreadCrumb(_p('Add photo categories'), $this->url()->makeUrl('admincp.photo.add'))
            ->assign([
                'sCreateJs' => $oValid->createJS(),
                'sGetJsForm' => $oValid->getJsForm(),
                'aLanguages' => $aLanguages,
                'bIsEdit' => $bIsEdit
            ]);
        return null;
    }
    
    public function clean(){
        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}