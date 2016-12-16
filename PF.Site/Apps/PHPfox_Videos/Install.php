<?php
namespace Apps\PHPfox_Videos;

use Core\App;
use Phpfox;
use Core\App\Install\Setting;

/**
 * Class Install
 * @author  Neil
 * @version 4.5.0
 * @package Apps\PHPfox_Videos
 */
class Install extends App\App
{
    private $_app_phrases = [
    
    ];
    
    protected function setId()
    {
        $this->id = 'PHPfox_Videos';
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
    
    protected function setAlias()
    {
        $this->alias = 'v';
    }
    
    protected function setName()
    {
        $this->name = 'Videos';
    }
    
    protected function setVersion()
    {
        $this->version = Phpfox::getVersion();
    }
    
    protected function setSettings()
    {
        $this->settings = [
            'pf_video_enabled'   => [
                'var_name'    => "pf_video_enabled",
                'info'        => "Video App Enabled",
                'type'        => Setting\Site::TYPE_RADIO,
                "value"       => "1",
                "js_variable" => true
            ],
            'pf_video_key'       => [
                'var_name' => "pf_video_key",
                'info'     => "Zencoder API Key",
            ],
            'pf_video_s3_key'    => [
                'var_name' => "pf_video_s3_key",
                'info'     => "Amazon S3 Access Key",
            ],
            'pf_video_s3_secret' => [
                'var_name' => "pf_video_s3_secret",
                'info'     => "Amazon S3 Secret",
            ],
            'pf_video_s3_bucket' => [
                'var_name' => "pf_video_s3_bucket",
                'info'     => "Amazon S3 Bucket",
            ],
            'pf_video_s3_url'    => [
                'var_name'    => "pf_video_s3_url",
                'info'        => "Provide the S3, CloudFront or Custom URL",
                "js_variable" => true
            ],
        ];
    }
    
    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [
            "pf_video_share"     => [
                "var_name" => "pf_video_share",
                "info"     => "Can share/upload a video?",
                "type"     => "input:radio",
                "value"    => "1",
                "options"  => Setting\Groups::$OPTION_YES_NO
            ],
            "pf_video_file_size" => [
                "var_name" => "pf_video_file_size",
                "info"     => "Max file size (MB)",
                "type"     => "input:text",
                "value"    => "10"
            ],
            "pf_video_view"      => [
                "var_name" => "pf_video_view",
                "info"     => "Can view a video?",
                "type"     => "input:radio",
                "value"    => "1",
                "options"  => Setting\Groups::$OPTION_YES_NO
            ]
        ];
    }
    
    protected function setComponent() { }
    
    protected function setComponentBlock() { }
    
    protected function setPhrase()
    {
        $this->addPhrases($this->_app_phrases);
    }
    
    protected function setOthers()
    {
        $this->notifications = [
            "video_ready"   => [
                "message" => "Your video is ready!",
                "url"     => "/v/play/:id",
                "icon"    => "fa-video-camera"
            ],
            "video_ready_p" => [
                "message" => "Your video is ready!",
                "url"     => "/v/play/p-:id",
                "icon"    => "fa-video-camera"
            ],
            "__like"        => [
                "message" => "{{ user_full_name }} liked your video",
                "url"     => "/v/play/:id",
                "icon"    => "fa-video-camera"
            ],
            "__comment"     => [
                "message" => "{{ user_full_name }} commented on your video",
                "url"     => "/v/play/:id",
                "icon"    => "fa-video-camera"
            ]
        ];
        $this->admincp_route = "/v/admincp";
        $this->admincp_menu = [
            "Categories" => "#"
        ];
        $this->admincp_help = "admincp_help";
        $this->admincp_action_menu = [
            "/v/admincp/category" => "New Category"
        ];
        $this->map = [
            "title"     => "caption",
            "link"      => "/v/play/:id",
            "content"   => "@view/feed.html",
            "feed_info" => "shared a video"
        ];
        $this->map_search = [
            "title" => "caption",
            "link"  => "/v/play/:id",
            "info"  => "Video"
        ];
        $this->menu = [
            "name" => "Videos",
            "url"  => "/v",
            "icon" => "video-camera"
        ];
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
    }
}