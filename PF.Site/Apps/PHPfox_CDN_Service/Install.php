<?php
namespace Apps\PHPfox_CDN_Service;

use Core\App;
use Phpfox;
use Core\App\Install\Setting;

/**
 * Class Install
 * @author  Neil
 * @version 4.5.0
 * @package Apps\PHPfox_CDN_Service
 */
class Install extends App\App
{
    private $_app_phrases = [
    
    ];
    
    protected function setId()
    {
        $this->id = 'PHPfox_CDN_Service';
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
        $this->name = 'phpFox CDN Service';
    }
    
    protected function setVersion()
    {
        $this->version = Phpfox::getVersion();
    }
    
    protected function setSettings()
    {
        $this->settings = [
            'pf_cdn_service_enabled' => [
                'var_name' => 'pf_cdn_service_enabled',
                'info'     => 'Enable CDN Service',
                'type'     => Setting\Site::TYPE_RADIO,
                'value'    => '0',
            ],
            'pf_cdn_service_url'     => [
                'var_name' => 'pf_cdn_service_url',
                'info'     => 'CDN URL',
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
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
    }
}