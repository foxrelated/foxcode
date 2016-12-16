<?php
namespace Apps\PHPfox_Groups;

use Core\App;
use Phpfox;
use Core\App\Install\Setting;

/**
 * Class Install
 * @author  Neil
 * @version 4.5.0
 * @package Apps\PHPfox_Groups
 */
class Install extends App\App
{
    private $_app_phrases = [
    
    ];
    
    protected function setId()
    {
        $this->id = 'PHPfox_Groups';
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
        $this->alias = 'groups';
    }
    
    public function setName()
    {
        $this->name = 'Groups';
    }
    
    public function setVersion()
    {
        $this->version = Phpfox::getVersion();
    }
    
    public function setSettings()
    {
        $this->settings = [
            'pf_group_enabled'     => [
                'var_name'    => 'pf_group_enabled',
                'info'        => 'Groups App Enabled',
                'type'        => Setting\Site::TYPE_RADIO,
                'value'       => '1',
                'js_variable' => true
            ],
            'pf_group_show_admins' => [
                'var_name'    => 'pf_group_show_admins',
                'info'        => 'Show group admins',
                'type'        => Setting\Site::TYPE_RADIO,
                'value'       => '0',
                'js_variable' => true
            ],
        ];
    }
    
    public function setUserGroupSettings()
    {
        $this->user_group_settings = [
            'pf_group_browse'          => [
                'var_name' => 'pf_group_browse',
                'info'     => 'Can browse groups?',
                'type'     => Setting\Groups::TYPE_RADIO,
                'value'    => [
                    "1" => "1",
                    "2" => "1",
                    "3" => "1",
                    "4" => "1",
                    "5" => "0"
                ],
                'options'  => Setting\Groups::$OPTION_YES_NO
            ],
            'pf_group_add_cover_photo' => [
                'var_name' => 'pf_group_add_cover_photo',
                'info'     => 'Can add a cover photo on groups?',
                'type'     => Setting\Groups::TYPE_RADIO,
                'value'    => [
                    "1" => "1",
                    "2" => "1",
                    "3" => "1",
                    "4" => "1",
                    "5" => "0"
                ],
                'options'  => Setting\Groups::$OPTION_YES_NO
            ],
            'pf_group_moderate'        => [
                'var_name' => 'pf_group_moderate',
                'info'     => 'Can moderate groups?',
                'type'     => Setting\Groups::TYPE_RADIO,
                'value'    => [
                    "1" => "1",
                    "2" => "0",
                    "3" => "0",
                    "4" => "1",
                    "5" => "0"
                ],
                'options'  => Setting\Groups::$OPTION_YES_NO
            ],
            'pf_group_add'             => [
                'var_name' => 'pf_group_add',
                'info'     => 'Can add groups?',
                'type'     => Setting\Groups::TYPE_RADIO,
                'value'    => [
                    "1" => "1",
                    "2" => "1",
                    "3" => "0",
                    "4" => "1",
                    "5" => "0"
                ],
                'options'  => Setting\Groups::$OPTION_YES_NO
            ],
            'pf_group_max_upload_size' => [
                'var_name' => 'pf_group_max_upload_size',
                'info'     => 'Max file size for upload files in kilobits (kb). For unlimited add "0" without quotes.',
                'type'     => Setting\Groups::TYPE_TEXT,
                'value'    => 500,
            ],
            'pf_group_approve_groups'  => [
                'var_name' => 'pf_group_approve_groups',
                'info'     => 'Approve a new group before it is displayed publicly?',
                'type'     => Setting\Groups::TYPE_RADIO,
                'value'    => 0,
                'options'  => Setting\Groups::$OPTION_YES_NO
            ],
            'pf_group_points_groups'   => [
                'var_name' => 'pf_group_points_groups',
                'info'     => 'Activity points received when creating a new group.',
                'type'     => Setting\Groups::TYPE_TEXT,
                'value'    => 1
            ],
        ];
    }
    
    public function setComponent()
    {
        $this->component = [
            "block"      => [
                "about"    => "",
                "admin"    => "",
                "category" => "",
                "events"   => "",
                "members"  => "",
                "menu"     => "",
                "photo"    => "",
                "profile"  => "",
                "widget"   => "",
                "cropme"   => ""
            ],
            "controller" => [
                "index"   => "groups.index",
                "add"     => "groups.add",
                "all"     => "groups.all",
                "view"    => "groups.view",
                "profile" => "groups.profile"
            ]
        ];
    }
    
    public function setComponentBlock()
    {
        $this->component_block = [
            "Groups Likes/Members" => [
                "type_id"      => "0",
                "m_connection" => "groups.view",
                "component"    => "members",
                "location"     => "3",
                "is_active"    => "1",
                "ordering"     => "3"
            ],
            "Groups Info"          => [
                "type_id"      => "0",
                "m_connection" => "groups.view",
                "component"    => "about",
                "location"     => "1",
                "is_active"    => "1",
                "ordering"     => "3"
            ],
            "Groups Mini Menu"     => [
                "type_id"      => "0",
                "m_connection" => "groups.view",
                "component"    => "menu",
                "location"     => "1",
                "is_active"    => "0",
                "ordering"     => "4"
            ],
            "Groups Widget"        => [
                "type_id"      => "0",
                "m_connection" => "groups.view",
                "component"    => "widget",
                "location"     => "1",
                "is_active"    => "1",
                "ordering"     => "5"
            ],
            "Groups"               => [
                "type_id"      => "0",
                "m_connection" => "profile.index",
                "component"    => "profile",
                "location"     => "1",
                "is_active"    => "1",
                "ordering"     => "4"
            ],
            "Groups Admin"         => [
                "type_id"      => "0",
                "m_connection" => "groups.view",
                "component"    => "admin",
                "location"     => "3",
                "is_active"    => "1",
                "ordering"     => "6"
            ],
            "Categories"           => [
                "type_id"      => "0",
                "m_connection" => "groups.index",
                "component"    => "category",
                "location"     => "1",
                "is_active"    => "1",
                "ordering"     => "10"
            ],
            "Feed display"         => [
                "type_id"      => "0",
                "m_connection" => "groups.view",
                "component"    => "display",
                "location"     => "2",
                "is_active"    => "1",
                "ordering"     => "10",
                "module_id"    => "feed"
            ],
            "Group Events"         => [
                "type_id"      => "0",
                "m_connection" => "groups.view",
                "component"    => "events",
                "location"     => "3",
                "is_active"    => "1",
                "ordering"     => "7"
            ]
        ];
    }
    
    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }
    
    protected function setOthers()
    {
        $this->admincp_action_menu = [
            "/groups/admincp/add-category" => "New Category"
        ];
        $this->admincp_route = "/groups/admincp";
        $this->admincp_menu = [
            "Categories"         => "#",
            "Convert old groups" => "groups.convert"
        ];
        $this->menu = [
            "name" => "Groups",
            "url"  => "/groups",
            "icon" => "users"
        ];
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
        $this->_admin_cp_menu_ajax = false;
    }
}