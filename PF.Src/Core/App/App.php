<?php
namespace Core\App;

use Core\App\Install\Setting;
use Core\App\Install\Phrase;
use Core\App\Install\Database;
use Phpfox;
use Phpfox_Installer;
use User_Service_Group_Group;
use Admincp_Service_Menu_Process;

/**
 * Class App
 * @author  Neil
 * @version 4.5.0
 * @package Core\App
 */
abstract class App
{
    /** an app CAN'T change this ID, if change, it will become another app
     *
     * @var string
     */
    public $id;

    /**
     * @var string is alias name of this App. This value shouldn't change
     */
    public $alias;

    /**
     * @var string is name of this App. You can change this value, but we don't recommend
     */
    public $name;

    /**
     * @var string is version of this App. We recommend you use 4.x or 4.x.x
     */
    public $version;

    /**
     * @var string is icon for this app. It can a link of image, a font-awesome class. If it empty or not set, we will
     *      use file "icon.png" is root path of your app. If file "icon.png" doesn't exist, we auto generate two
     *      characters for your icon.
     */
    public $icon;

    /**
     * @var string
     */
    public $admincp_route;

    /**
     * @var string
     */
    public $admincp_menu;

    /**
     * @var string
     */
    public $admincp_help;

    /**
     * @var string
     */
    public $admincp_action_menu;

    /**
     * @var string
     */
    public $map;

    /**
     * @var string
     */
    public $map_search;

    /**
     * @var array
     */
    public $menu = [];

    /**
     * @var array
     */
    public $settings;

    /**
     * @var Setting\Site
     */
    protected $_settings;

    /**
     * @var Setting\Groups
     */
    protected $_user_group_settings;

    /**
     * @var array
     */
    public $user_group_settings;

    /**
     * @var
     */
    public $notifications;

    /**
     * @var
     */
    public $database;

    /**
     * @var
     */
    public $component;

    /**
     * @var
     */
    public $component_block;

    /**
     * @var
     */
    public $routes;

    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $start_support_version = '';

    /**
     * @var string
     */
    public $end_support_version = '';

    /**
     * @var array
     */
    public $phrase = [];

    /**
     * @var Phrase\Phrase
     */
    private $_phrase;

    /**
     * Name of publisher
     *
     * @var string
     */
    public $_publisher = 'n/a';

    /**
     * Home page of publisher
     *
     * @var string
     */
    public $_publisher_url = '';

    /**
     * @var array store errors from this app
     */
    protected $_errors;

    /**
     * @var array of core apps
     */
    private $_aCores = [
        'PHPfox_Core',
        'PHPfox_Flavors',
    ];

    /**
     * @var bool
     */
    public $_admin_cp_menu_ajax = true;

    private $_aOfficial = [
        'PHPfox_AmazonS3',
        'PHPfox_CDN',
        'PHPfox_CDN_Service',
        'PHPfox_Core',
        'PHPfox_Facebook',
        'PHPfox_Flavors',
        'PHPfox_Groups',
        'PHPfox_IM',
        'PHPfox_reCAPTCHA',
        'PHPfox_Twemoji_Awesome',
        'PHPfox_Videos'
    ];

    /**
     * App constructor.
     */
    public function __construct()
    {
        $this->_phrase = new Phrase\Phrase();
        $this->setId();
        $this->path = PHPFOX_DIR_SITE . 'Apps' . PHPFOX_DS . $this->id . PHPFOX_DS;
        $this->setAlias();
        $this->setName();
        $this->setSupportVersion();
        $this->setVersion();
        $this->setSettings();
        $this->setUserGroupSettings();
        $this->setComponent();
        $this->setComponentBlock();
        $this->setOthers();
        $this->initData();
        $this->addPhrases($this->phrase);
        if (!is_array($this->admincp_menu)) {
            $this->admincp_menu = [
                $this->name => '#'
            ];
        }

        $attributes = get_object_vars($this);
        foreach ($attributes as $iKey => $attribute) {
            if (is_array($attribute) && substr($iKey, 0, 1) != '_') {
                $this->{$iKey} = json_decode(json_encode($attribute));
            }
        }
        //add phrase from json
        $sPath = PHPFOX_DIR_SITE . 'Apps' . PHPFOX_DS . $this->id . PHPFOX_DS . 'phrase.json';
        if (file_exists($sPath)) {
            $aJsonPhrases = json_decode(file_get_contents($sPath), true);
            $this->addPhrases($aJsonPhrases);
        }
    }

    /**
     * Set ID
     */
    abstract protected function setId();

    /**
     * Set start and end support version of your App.
     * @example   $this->start_support_version = 4.2.0
     * @example   $this->end_support_version = 4.5.0
     * @see       list of our verson at PF.Base/install/include/installer.class.php ($_aVersions)
     * @important You DO NOT ALLOW to set current version of phpFox for start_support_version and end_support_version. We will reject of app if you use current version of phpFox for these variable. These variables help clients know their sites is work with your app or not.
     */
    abstract protected function setSupportVersion();

    /**
     * Set Alias
     */
    abstract protected function setAlias();

    /**
     * Set name
     */
    abstract protected function setName();

    /**
     * Set version
     */
    abstract protected function setVersion();

    /**
     * Set phrases
     */
    abstract protected function setPhrase();

    /**
     * Set settings
     */
    abstract protected function setSettings();

    /**
     * Set user group settings
     */
    abstract protected function setUserGroupSettings();

    /**
     * Set component
     */
    abstract protected function setComponent();

    /**
     * Set component block
     */
    abstract protected function setComponentBlock();

    /*
     * Set other attributes for this app
     */
    abstract protected function setOthers();

    /**
     * Check this App is valid
     *
     * @return bool
     */
    public function isValid()
    {
        if (empty($this->id)) {
            return false;
        }

        if (empty($this->name)) {
            return false;
        }

        if (empty($this->version)) {
            return false;
        }

        //We have to have alias for remove/disable menu when uninstall/disable
        if (count($this->menu) && empty($this->alias)) {
            return false;
        }

        return true;
    }

    /**
     * Init data for this app
     */
    private function initData()
    {
        //Setting
        if (is_array($this->settings)) {
            if (count($this->settings)) {
                foreach ($this->settings as $key => $setting) {
                    if (!isset($setting['var_name'])) {
                        $setting['var_name'] = $key;
                    }
                    $oSetting = new Setting\Site($setting);
                    if ($oSetting->isValid()) {
                        $this->_phrase->addPhrase($oSetting->getPhraseVarName(), $oSetting->getPhraseValue());
                        $this->_settings[] = $oSetting;
                    } else {
                        $this->_errors[] = $oSetting->getError();
                    }
                }
            }
        }

        //User groups setting
        if (count($this->user_group_settings)) {
            foreach ($this->user_group_settings as $key => $group_setting) {
                if (!isset($group_setting['var_name'])) {
                    $group_setting['var_name'] = $key;
                }
                $oGroupSetting = new Setting\Groups($group_setting);
                if ($oGroupSetting->isValid()) {
                    $this->_phrase->addPhrase($oGroupSetting->getPhraseVarName(), $oGroupSetting->getPhraseValue());
                    $this->_user_group_settings[] = $oGroupSetting;
                } else {
                    $this->_errors[] = $oGroupSetting->getError();
                }
            }
        }
        //Icon
        if (!isset($this->icon) || empty($this->icon)) {
            if (file_exists($this->path . 'icon.png')) {
                $this->icon = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/' . $this->id . '/icon.png';
            }
        } elseif (filter_var($this->icon, FILTER_VALIDATE_URL) === false && strpos($this->icon, 'fa') !== false) {
            $name = $this->name[0];
            $parts = explode(' ', $this->name);
            if (isset($parts[1])) {
                $name .= trim($parts[1])[0];
            } elseif (isset($this->name[1])) {
                $name .= $this->name[1];
            }
            $class_color = '_' . $name;
            $name = '<i class="fa ' . $this->icon . '" aria-hidden="true"></i></i>';
            $this->icon = '<b class="app_icons"><i class="app_icon ' . strtolower($class_color) . '">' . $name . '</i></b>';
        }
        //phrase
        $this->setPhrase();
    }

    /**
     * Add a database table for this app
     *
     * @param $database Database\Table
     */
    protected function addDatabase($database)
    {
        if ($database - $this->isValid()) {
            $this->database[] = $database;
        }
    }

    /**
     * Add new phrases
     *
     * @param array $aParams
     */
    protected function addPhrases($aParams)
    {
        foreach ($aParams as $var_name => $value) {
            $this->_phrase->addPhrase($var_name, $value);
        }
    }

    /**
     * Get all phrases of this app
     *
     * @return array
     */
    public function getPhrases()
    {
        return $this->_phrase->all();
    }

    public function uninstall()
    {
        if (is_array($this->database) && count($this->database)) {
            foreach ($this->database as $database) {
                $sNamespace = "\\Apps\\" . $this->id . "\\Installation\\Database\\" . $database;
                if (class_exists($sNamespace)) {
                    /**
                     * @var $oDatabase \Core\App\Install\Database\Table
                     */
                    $oDatabase = new $sNamespace();
                    $oDatabase->drop();
                }
            }
        }
        if (isset($this->alias) && !empty($this->alias)) {
            //Delete block, component
            db()->delete(':block', "module_id='" . $this->alias . "'");
            db()->delete(':component', "module_id='" . $this->alias . "'");
            //delete alias
            db()->delete(':module', "module_id='" . $this->alias . "'");
        }
        db()->delete(':apps', 'apps_id="' . $this->id . '"');
    }

    /**
     * Process install/upgrade for this app
     * @return bool
     */
    public function processInstall()
    {
        if (!$this->isValid()) {
            $this->_errors = "Not a valid app";
            return false;
        }

        $iCnt = db()->select('COUNT(*)')
            ->from(':apps')
            ->where('apps_id="' . $this->id . '"')
            ->execute('getSlaveField');

        if (!$iCnt) {
            db()->insert(':apps', [
                'apps_id' => $this->id,
                'apps_name' => $this->name,
                'version' => $this->version,
                'apps_alias' => isset($this->alias) ? $this->alias : '',
                'author' => $this->_publisher,
                'vendor' => $this->_publisher_url,
                'apps_icon' => $this->icon,
                'type' => ($this->isCore()) ? 1 : 2,
                'is_active' => 1,
            ]);
        } else {
            //upgrade case
            $aUpdate = [
                'apps_name' => $this->name,
                'version' => $this->version,
                'apps_alias' => isset($this->alias) ? $this->alias : '',
                'author' => $this->_publisher,
                'vendor' => $this->_publisher_url,
                'apps_icon' => $this->icon,
            ];
            db()->update(':apps', $aUpdate, 'apps_id="' . $this->id . '"');
        }

        if (is_array($this->database) && count($this->database)) {
            foreach ($this->database as $database) {
                $sNamespace = "\\Apps\\" . $this->id . "\\Installation\\Database\\" . $database;
                if (class_exists($sNamespace)) {
                    /**
                     * @var $oDatabase \Core\App\Install\Database\Table
                     */
                    $oDatabase = new $sNamespace();
                    $oDatabase->install();
                }
            }
        }
        //Add Phrase
        $aPhrases = $this->_phrase->all();
        \Core\Lib::phrase()->addPhrase($aPhrases);

        //Add Alias to table :module
        if (isset($this->alias)) {
            //Check Alias is exist
            $iCnt = db()->select('COUNT(*)')
                ->from(':module')
                ->where('module_id = "' . $this->alias . '"')
                ->execute('getSlaveField');
            if (!$iCnt) {
                $aInsert = [
                    'module_id' => $this->alias,
                    'product_id' => 'phpfox',
                    'is_core' => '0',
                    'is_active' => '1',
                    'is_menu' => '0',
                    'menu' => '',
                    'phrase_var_name' => 'module_apps'
                ];
                db()->insert(':module', $aInsert);
            } elseif (!defined('PHPFOX_INSTALLER') || !PHPFOX_INSTALLER) {
                $this->_errors = "Duplicate App alias";
            }
        }

        //Add Component
        if (isset($this->component)) {
            $InsertData = [];
            if (isset($this->component->block)) {
                foreach ($this->component->block as $key => $value) {
                    //Check is exist
                    $iCnt = db()->select('COUNT(*)')
                        ->from(':component')
                        ->where('component="' . $key . '" AND m_connection="' . $value . '" AND module_id="' . $this->alias . '" AND is_controller=0')
                        ->executeField();
                    if ($iCnt) {
                        //Do not add duplicate component
                        continue;
                    }
                    $InsertData[] = [
                        $key, //component
                        $value,//m_connection
                        $this->alias,//module_id
                        'phpfox',//product_id
                        0,//is_controller
                        1,//is_block
                        1,//is_active
                    ];
                }
            }
            if (isset($this->component->controller)) {
                foreach ($this->component->controller as $key => $value) {
                    //Check is exist
                    $iCnt = db()->select('COUNT(*)')
                        ->from(':component')
                        ->where('component="' . $key . '" AND m_connection="' . $value . '" AND module_id="' . $this->alias . '" AND is_controller=1')
                        ->executeField();
                    if ($iCnt) {
                        //Do not add duplicate component
                        continue;
                    }
                    $InsertData[] = [
                        $key, //component
                        $value,//m_connection
                        $this->alias,//module_id
                        'phpfox',//product_id
                        1,//is_controller
                        0,//is_block
                        1,//is_active
                    ];
                }
            }
            if (count($InsertData)) {
                db()->multiInsert(Phpfox::getT('component'), [
                    'component',
                    'm_connection',
                    'module_id',
                    'product_id',
                    'is_controller',
                    'is_block',
                    'is_active',
                ], $InsertData);
            }
        }
        //Add component block
        if (isset($this->component_block)) {
            $InsertData = [];
            foreach ($this->component_block as $key => $value) {
                $sModuleId = (isset($value->module_id) ? $value->module_id : $this->alias);
                //Check block is exist
                $iCnt = db()->select('COUNT(*)')
                    ->from(':block')
                    ->where('m_connection="' . $value->m_connection . '" AND component= "' . $value->component . '" AND module_id="' . $sModuleId . '"')
                    ->executeField();
                if ($iCnt) {
                    //this block is exist
                    continue;
                }
                $InsertData[] = [
                    $key, //title
                    $value->type_id,//type_id
                    $value->m_connection,//m_connection
                    $sModuleId,//module_id
                    'phpfox',//product_id
                    $value->component,//component
                    $value->location,//location
                    $value->is_active,//is_active
                    $value->ordering,//ordering
                    (isset($value->disallow_access) ? $value->disallow_access : null),//disallow_access
                    (isset($value->can_move) ? $value->can_move : '0'),//can_move
                    (isset($value->version_id) ? $value->version_id : null),//version_id
                ];
            }
            if (count($InsertData)) {
                db()->multiInsert(Phpfox::getT('block'), [
                    'title',
                    'type_id',
                    'm_connection',
                    'module_id',
                    'product_id',
                    'component',
                    'location',
                    'is_active',
                    'ordering',
                    'disallow_access',
                    'can_move',
                    'version_id',
                ], $InsertData);
            }
        }
        //Add user group setting
        if (isset($this->user_group_settings)) {
            $userGroups = User_Service_Group_Group::instance()->get();
            $userGroupSettings = [];
            foreach ($userGroups as $group) {
                $aUserGroupValue = [];
                foreach ($this->user_group_settings as $key => $value) {
                    if (!isset($value->value)) {
                        $value->value = '';
                    }
                    if (is_object($value->value) && isset($value->value->{$group['user_group_id']})) {
                        $aUserGroupValue[$key] = $value->value->{$group['user_group_id']};
                    } elseif (is_object($value->value)) {
                        $aUserGroupValue[$key] = $value->value->{2};
                    } else {
                        $aUserGroupValue[$key] = $value->value;
                    }
                }
                $userGroupSettings[$group['user_group_id']] = $aUserGroupValue;
            }
            foreach ($userGroupSettings as $group_id => $values) {
                foreach ($values as $key => $value) {
                    db()->delete(':user_group_custom', [
                        'user_group_id' => $group_id,
                        'module_id' => 'app_' . $this->id,
                        'name' => $key
                    ]);
                    db()->insert(':user_group_custom', [
                        'user_group_id' => $group_id,
                        'module_id' => 'app_' . $this->id,
                        'name' => $key,
                        'default_value' => $value
                    ]);
                }
            }
        }
        //Add menu
        if (count($this->menu)) {
            $iCnt = db()->select('COUNT(*)')
                ->from(':menu')
                ->where('module_id= "' . $this->alias . '" AND m_connection="main" AND url_value="' . $this->menu->url . '"')
                ->executeField();
            if (!$iCnt) {
                Admincp_Service_Menu_Process::instance()->add([
                    'm_connection' => 'main',
                    'module_id' => $this->alias,
                    'product_id' => 'phpfox',
                    'allow_all' => true,
                    'mobile_icon' => (isset($this->menu->icon) ? $this->menu->icon : null),
                    'url_value' => $this->menu->url,
                    'text' => ['en' => $this->menu->name]
                ], false, true);
            }
        }

        if (file_exists($this->path . 'installer.php')) {
            Installer::$method = 'onInstall';
            Installer::$basePath = $this->path;
            require_once($this->path . 'installer.php');
        }
        return true;
    }

    /**
     * Check this app is compatible with current phpFox version
     *
     * @return bool
     */
    public function isCompatible()
    {
        $aVersions = (new Phpfox_Installer())->getVersionList();
        $iStart = array_search($this->start_support_version, $aVersions);
        $iCurrent = array_search(Phpfox::getVersion(), $aVersions);
        $iEnd = array_search($this->end_support_version, $aVersions);
        if (($iStart <= $iCurrent) && ($iCurrent <= $iEnd)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Disable this app
     */
    public function disable()
    {
        db()->update(Phpfox::getT('apps'), [
            'is_active' => 0
        ], 'apps_id="' . $this->id . '"');
    }

    /**
     * Enable this app
     */
    public function enable()
    {
        db()->update(Phpfox::getT('apps'), [
            'is_active' => 1
        ], 'apps_id="' . $this->id . '"');
    }

    /**
     * Check this app is a core app or not
     *
     * @return bool
     */
    public function isCore()
    {
        return in_array($this->id, $this->_aOfficial);
    }

    /**
     * Check this app is active
     *
     * @return bool
     */
    public function isActive()
    {
        if (in_array($this->id, $this->_aCores)) {
            return true;
        }
        if (defined('PHPFOX_APP_INSTALLING') && PHPFOX_APP_INSTALLING) {
            return true;//installing app
        }
        $iActive = db()->select('is_active')
            ->from(':apps')
            ->where('apps_id=\'' . $this->id . '\'')
            ->execute('getSlaveField');
        return ($iActive) ? true : false;
    }
}