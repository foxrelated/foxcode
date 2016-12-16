<?php
namespace Apps\PHPfox_Facebook;

use Core\App;
use Phpfox;
use Core\App\Install\Setting;

/**
 * Class Install
 * @author  Neil
 * @version 4.5.0
 * @package Apps\PHPfox_Facebook
 */
class Install extends App\App
{
    private $_app_phrases = [
    
    ];
    
    protected function setId()
    {
        $this->id = 'PHPfox_Facebook';
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
        $this->name = 'Facebook Base';
    }
    
    protected function setVersion()
    {
        $this->version = Phpfox::getVersion();
    }
    
    protected function setSettings()
    {
        $this->settings = [
            "m9_facebook_enabled"       => [
                "var_name" => "m9_facebook_enabled",
                "info"     => "Facebook Login Enabled",
                "type"     => Setting\Site::TYPE_RADIO,
                "value"    => "0",
            ],
            "m9_facebook_app_id"        => [
                "var_name" => "m9_facebook_app_id",
                "info"     => "Facebook Application ID",
            ],
            "m9_facebook_app_secret"    => [
                "var_name" => "m9_facebook_app_secret",
                "info"     => "Facebook App Secret",
            ],
            "m9_facebook_require_email" => [
                "var_name" => "m9_facebook_require_email",
                "info"     => "Require Email",
                "type"     => Setting\Site::TYPE_RADIO,
                "value"    => "0",
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