<?php
namespace Apps\PHPfox_CDN;

use Core\App;
use Phpfox;
use Core\App\Install\Setting;

/**
 * Class Install
 * @author  Neil
 * @version 4.5.0
 * @package Apps\PHPfox_CDN
 */
class Install extends App\App
{
    private $_app_phrases = [
    
    ];
    
    public function __construct()
    {
        parent::__construct();
    }
    
    protected function setId()
    {
        $this->id = 'PHPfox_CDN';
    }
    
    /**
     * Set start and end support version of your App.
     * @example   $this->start_support_version = 4.2.0
     * @example   $this->end_support_version = 4.5.0
     * @see       list of our verson at PF.Base/install/include/installer.class.php ($_aVersions)
     * @important You DO NOT ALLOW to set current version of phpFox for start_support_version and end_support_version. We will reject of app if you use current version of phpFox for these variable. These variables help clients know their sites is work with your app or not.
     */
    protected function setSupportVersion()
    {
        $this->start_support_version = Phpfox::getVersion();
        $this->end_support_version = Phpfox::getVersion();
    }
    
    protected function setAlias() { }
    
    protected function setName()
    {
        $this->name = 'phpFox CDN';
    }
    
    protected function setVersion()
    {
        $this->version = Phpfox::getVersion();
    }
    
    protected function setSettings()
    {
        $this->settings = [
            'pf_cdn_enabled' => [
                'var_name' => 'pf_cdn_enabled',
                'info'     => 'Enable CDN',
                'type'     => Setting\Site::TYPE_RADIO,
                'value'    => '0',
            ],
        ];
    }
    
    protected function setUserGroupSettings() { }
    
    protected function setComponent() { }
    
    protected function setComponentBlock() { }
    
    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }
    
    protected function setOthers()
    {
        $this->admincp_route = "/pfcdn/acp";
        $this->admincp_menu = [
            "Servers" => "#"
        ];
        $this->admincp_action_menu = [
            "/pfcdn/acp/server" => "New Server"
        ];
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
    }
}