<?php
namespace Apps\PHPfox_Twemoji_Awesome;

use Core\App;
use Phpfox;

/**
 * Class Install
 * @author  Neil
 * @version 4.5.0
 * @package Apps\PHPfox_Twemoji_Awesome
 */
class Install extends App\App
{
    private $_app_phrases = [
    
    ];
    
    public $credits = [
        "Elle Kasai" => "https://github.com/ellekasai/twemoji-awesome",
        "Twitter"    => "http://twitter.github.io/twemoji/",
        "linyows"    => "https://github.com/linyows/jquery-emoji"
    ];
    
    protected function setId()
    {
        $this->id = 'PHPfox_Twemoji_Awesome';
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
        $this->name = 'Twemoji Awesome';
    }
    
    protected function setVersion()
    {
        $this->version = Phpfox::getVersion();
    }
    
    protected function setSettings()
    {
        $this->settings = [
            'twemoji_selectors' => [
                'var_name'    => "twemoji_selectors",
                'info'        => "CSS Selectors",
                "value"       => ".panel_rows_preview, .mail_text, .activity_feed_content_status, .comment_mini_text, .item_content, .item_view_content, .activity_feed_content_display, .forum_mini_post ._c",
                "js_variable" => true
            ],
        ];
    }
    
    protected function setUserGroupSettings() { }
    
    protected function setComponent() { }
    
    protected function setComponentBlock() { }
    
    protected function setPhrase()
    {
        $this->addPhrases($this->_app_phrases);
    }
    
    protected function setOthers()
    {
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
    }
}