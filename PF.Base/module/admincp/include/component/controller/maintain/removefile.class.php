<?php
defined('PHPFOX') or exit('NO DICE!');

class Admincp_Component_Controller_Maintain_Removefile extends Phpfox_Component {

    public function process() {
        $aFiles = Admincp_Service_Maintain_Deletefiles::instance()->getListFiles();
        $ssh = $this->request()->get('ssh');
        if (count($aFiles) && $ssh){
            header("Content-type: text/plain");
            header("Content-Disposition: attachment; filename=delete_file.sh");
            foreach ($aFiles as $sFile){
                echo 'rm -f ' . dirname(PHPFOX_DIR) . PHPFOX_DS . $sFile . "\n";
            }
            exit();
        }
        $currentHostName = Phpfox::getParam('core.ftp_host_name');
        $currentPort = Phpfox::getParam('core.ftp_port');
        $currentUsername = Phpfox::getParam('core.ftp_user_name');
        $currentPassword = Phpfox::getParam('core.ftp_password');
        $aVals = $this->request()->get('val');
        if (count($aFiles) && isset($aVals['submit'])){
            $manager = new \Core\Installation\Manager($aVals);
            if ($manager->verifyFtpAccount()) {
                try {
                    foreach ($aFiles as $sFile){
                        $manager->deleteFile(dirname(PHPFOX_DIR) . PHPFOX_DS . $sFile);
                    }
                } catch (\Exception $ex) {
                    if (PHPFOX_DEBUG) {
                        throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
                    }
                    return \Phpfox_Error::set($ex->getMessage());
                }

            } else {
                return Phpfox_Error::set(_p('Your ftp account don\'t work'));
            }
            Phpfox::addMessage(_p("All old files deleted"));
            $this->url()->send('admincp.maintain.removefile');
        }
        $this->template()->setTitle(_p("Remove files no longer used"))
            ->setSectionTitle(_p("Remove files no longer used"))
            ->assign([
                    'aFiles' => $aFiles,
                    'site_path' => dirname(PHPFOX_DIR) . PHPFOX_DS,
                    'currentHostName'     => $currentHostName,
                    'currentPort'         => $currentPort,
                    'currentUsername'     => $currentUsername,
                    'currentPassword'     => $currentPassword,
                ])
            ->setHeader([
                'bootstrap.min.css' => "style_css",
                'bootstrap.min.js' => "static_script"
            ]);
        return null;
    }
    public function clean() {
        (($sPlugin = Phpfox_Plugin::get('admincp.component_controller_maintain_removefile_clean')) ? eval($sPlugin) : false);
    }
}