<?php
namespace Apps\PHPfox_AmazonS3;

use Core\App;
use Phpfox;
use Core\App\Install\Setting;

/**
 * Class Install
 * @author  Neil
 * @version 4.5.0
 * @package Apps\PHPfox_AmazonS3
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
        $this->id = 'PHPfox_AmazonS3';
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
        $this->name = 'Amazon CDN';
    }
    
    protected function setVersion()
    {
        $this->version = Phpfox::getVersion();
    }
    
    protected function setSettings()
    {
        $this->settings = [
            'cdn_enabled'        => [
                'var_name' => 'cdn_enabled',
                'info'     => 'Enable CDN',
                'type'     => Setting\Site::TYPE_RADIO,
                'value'    => '0'
            ],
            'cdn_amazon_id'      => [
                'var_name' => 'cdn_amazon_id',
                'info'     => 'Enable CDN',
            ],
            'cdn_amazon_secret'  => [
                'var_name' => 'cdn_amazon_secret',
                'info'     => 'Amazon Secret Key',
                'type'     => Setting\Site::TYPE_PASSWORD
            ],
            'cdn_bucket'         => [
                'var_name' => 'cdn_bucket',
                'info'     => 'Bucket Name',
            ],
            'cdn_cloudfront_url' => [
                'var_name' => 'cdn_cloudfront_url',
                'info'     => 'CloudFront URL',
            ]
        ];
    }
    
    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }
    
    protected function setUserGroupSettings() { }
    
    protected function setComponent() { }
    
    protected function setComponentBlock() { }
    
    protected function setOthers()
    {
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
    }
}