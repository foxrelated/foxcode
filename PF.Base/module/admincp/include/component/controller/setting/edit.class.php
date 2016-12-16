<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Admincp
 * @version 		$Id: edit.class.php 2825 2011-08-09 20:14:13Z Raymond_Benc $
 */
class Admincp_Component_Controller_Setting_Edit extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		list($aGroups, $aModules, $aProductGroups) = Admincp_Service_Setting_Group_Group::instance()->get();
        $aCond = [];
        $sSettingTitle = '';

		if ($this->request()->get('setting-id')) {
			$this->url()->send('admincp');
		}

		if (!$this->request()->get('module-id') && !$this->request()->get('group-id')) {
			$this->url()->send('admincp');
		}
		
		if (($sSettingId = $this->request()->get('setting-id')))
		{
			$aCond[] = " AND setting.setting_id = " . (int) $sSettingId;
		}
		
		if (($sGroupId = $this->request()->get('group-id')))
		{
			$aCond[] = " AND setting.group_id = '" . Phpfox_Database::instance()->escape($sGroupId) . "' AND setting.is_hidden = 0 ";
            foreach ($aGroups as $aGroup) {
                if ($aGroup['group_id'] == $sGroupId) {
                    $sSettingTitle = $aGroup['var_name'];
                    break;
                }
            }
		}
        if ($sGroupId == 'mail' && $this->request()->get('test')){
            $aVals = $this->request()->getArray('val');
            if (isset($aVals['email_send_test'])){
                if (filter_var($aVals['email_send_test'], FILTER_VALIDATE_EMAIL)) {
                    Phpfox_Mail::instance()->to($aVals['email_send_test'])
                        ->fromEmail(Phpfox::getParam('core.email_from_email'))
                        ->fromName(Phpfox::getParam('core.mail_from_name'))
                        ->subject(_p("Test setup email"))
                        ->message(_p("Congratulations, your configuration worked"))
                        ->send();
                    Phpfox::addMessage(_p("Email sent."));
                    $this->url()->send('admincp.setting.edit', ['group-id' => 'mail']);
                } else {
                    return Phpfox_Error::set(_p("Not a valid test email address"));
                }
            }
        }
		
		if (($iModuleId = $this->request()->get('module-id')))
		{
			$aCond[] = " AND setting.module_id = '" . Phpfox_Database::instance()->escape($iModuleId) . "' AND setting.is_hidden = 0 ";
            foreach ($aModules as $aModule) {
                if ($aModule['module_id'] == $iModuleId) {
                    $sSettingTitle = $aModule['module_id'];
                    break;
                }
            }
		}

		if (($sProductId = $this->request()->get('product-id')))
		{
			$aCond[] = " AND setting.product_id = '" . Phpfox_Database::instance()->escape($sProductId) . "' AND setting.is_hidden = 0 ";
            foreach ($aProductGroups as $aProduct) {
                if ($aProduct['product_id'] == $sProductId) {
                    $sSettingTitle = $aProduct['var_name'];
                    break;
                }
            }
		}
		
		$aSettings = Admincp_Service_Setting_Setting::instance()->get($aCond);
        
        if ($aVals = $this->request()->getArray('val')) {
            if (Admincp_Service_Setting_Process::instance()->update($aVals)) {
                return [
                    'updated' => true,
                    'message' => _p('Your changes have been saved!')
                ];
            }
        }

		$sWatermarkImage = Phpfox::getParam('core.url_watermark') . sprintf(Phpfox::getParam('core.watermark_image'), '') . '?v=' . uniqid();
		if(!file_exists(Phpfox::getParam('core.dir_watermark') . sprintf(Phpfox::getParam('core.watermark_image'), '')) && Phpfox::getParam('core.allow_cdn'))
		{
			$sWatermarkImage = Phpfox::getLib('cdn')->getUrl(str_replace(PHPFOX_DIR, '', $sWatermarkImage));
		}

		if (Phpfox::isModule($sSettingTitle)) {
			$oApp = Core\Lib::app()->get('__module_'.$sSettingTitle);
			$sSettingTitle = ($oApp && $oApp->name) ? $oApp->name : Phpfox_Locale::instance()->translate($sSettingTitle, 'module');
		}
		$this->template()->setSectionTitle($sSettingTitle)
            ->setBreadCrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
			->setBreadCrumb(Phpfox::getPhraseT($sSettingTitle, 'module'), $this->url()->makeUrl('admincp.app',['id' => '__module_' .$iModuleId]))
            ->setBreadCrumb(_p('manage_settings'))
			->setTitle(_p('manage_settings'))->assign([
                'aGroups'         => $aGroups,
                'aModules'        => $aModules,
                'aProductGroups'  => $aProductGroups,
                'aSettings'       => $aSettings,
                'sSettingTitle'   => $sSettingTitle,
                'sWatermarkImage' => $sWatermarkImage,
                'sGroupId'        => $sGroupId
            ]);
		
		(($sPlugin = Phpfox_Plugin::get('admincp.component_controller_setting_edit_process')) ? eval($sPlugin) : false);
        
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('admincp.component_controller_setting_edit_clean')) ? eval($sPlugin) : false);
	}
}