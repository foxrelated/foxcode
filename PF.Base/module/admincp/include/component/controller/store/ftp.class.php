<?php
defined('PHPFOX') or exit('NO DICE!');
define('PHPFOX_APP_INSTALLING', true);
/**
 * Class Admincp_Component_Controller_Store_Ftp
 */
class Admincp_Component_Controller_Store_Ftp extends Phpfox_Component
{
    public function process()
    {
        $listMethod = [
            "sftp_ssh" => _p('sftp_ssh'),
            "ftp" => _p('ftp'),
            "file_system" => _p('file_system')
        ];

        $currentUploadMethod = Phpfox::getParam('core.upload_method');
        $currentHostName = Phpfox::getParam('core.ftp_host_name');
        $currentPort = Phpfox::getParam('core.ftp_port');
        $currentUsername = Phpfox::getParam('core.ftp_user_name');
        $currentPassword = Phpfox::getParam('core.ftp_password');
        $type = $this->request()->get('type');
        $productName = $this->request()->get('productName');
        $productId = $this->request()->get('productId');
        $extraBase64 = $this->request()->get('extraBase64');
        $extra = json_decode(base64_decode($extraBase64), true);

	    $this->template()->setSectionTitle('<a href="' . $this->url()->current() . '">' . _p('Install Method') . '</a>');

        $this->template()->assign([
            'productId'           => $productId,
            'listMethod'          => $listMethod,
            'currentUploadMethod' => $currentUploadMethod,
            'currentHostName'     => $currentHostName,
            'currentPort'         => $currentPort,
            'currentUsername'     => $currentUsername,
            'currentPassword'     => $currentPassword,
            'type'                => $type,
            'productName'         => $productName,
            'extraBase64'         => $extraBase64,
            'targetDirectory' => $this->request()->get('targetDirectory')
        ]);

        //get account info
        if ($aVals = $this->request()->getArray('val')) {
            //update setting value
            Admincp_Service_Store_Verify::instance()->updateSetting($aVals);

            $aVals['extra'] = json_decode(base64_decode($aVals['extraBase64']), true);

            $manager = new \Core\Installation\Manager($aVals);
            if ($manager->verifyFtpAccount()) {
                try {

	                if (is_numeric($aVals['productName'])) {
		                if (is_numeric($productName)) {
			                foreach ((new \Core\App())->all() as $app) {
				                if (isset($app->store_id) && $app->store_id == $aVals['productName']) {
					                $aVals['productName'] = $app->id;
					                break;
				                }
			                }
		                }
	                }
                    $url = $manager->install($aVals);
                    echo '<script>window.top.location.href = \'' . $url . '\';</script>';
                    exit;
                } catch (\Exception $ex) {
	                if (PHPFOX_DEBUG) {
		                throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
	                }
                    return \Phpfox_Error::set($ex->getMessage());
                }

            } else {
                return Phpfox_Error::set(_p('Your ftp account don\'t work'));
            }
        }
        return null;
    }
}