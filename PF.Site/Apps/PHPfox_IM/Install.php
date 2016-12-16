<?php
namespace Apps\PHPfox_IM;

use Core\App;
use Phpfox;
use Core\App\Install\Setting;

/**
 * Class Install
 * @author  Neil
 * @version 4.5.0
 * @package Apps\PHPfox_IM
 */
class Install extends App\App
{
    private $_app_phrases = [
    
    ];
    
    public $js = ["https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"];
    
    public $js_phrases = [
        "messenger"     => "Messenger",
        "conversations" => "Conversations",
        "friends"       => "Friends"
    ];
    
    protected function setId()
    {
        $this->id = 'PHPfox_IM';
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
        $this->name = 'Instant Messaging';
    }
    
    protected function setVersion()
    {
        $this->version = '4.0.1';
    }
    
    protected function setSettings()
    {
        $this->settings = [
            'pf_im_enabled'     => [
                'var_name'    => "pf_im_enabled",
                'info'        => "IM App Enabled",
                'type'        => Setting\Site::TYPE_RADIO,
                "value"       => 0,
                "js_variable" => true
            ],
            'pf_im_node_server' => [
                'var_name'    => "pf_im_node_server",
                'info'        => "Provide your Node JS server",
                "js_variable" => true
            ]
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
        $this->admincp_menu = [
          'Instant Messaging' => '#'
        ];
        $this->admincp_route = "/im/admincp";
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
    }
}