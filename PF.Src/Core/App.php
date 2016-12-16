<?php
namespace Core;

use Phpfox_Module;
use Admincp_Service_Module_Process;

/**
 * Class App
 * @package Core
 */
class App
{
    /**
     * @var array
     */
	public static $routes = [];

    /**
     * @var array|null
     */
	private static $_apps = null;

    /**
     * List of core apps/modules. It doesn't display anywhere on backend.
     * @var array
     */
    private $_aCoreApps = [
        'like',
//        'notification',
        'admincp',
        'api',
        'ban',
        'core',
        'custom',
        'error',
        'language',
        'link',
        'log',
        'page',
        'privacy',
        'profile',
        'report',
        'request',
        'search',
        'share',
        'theme',
        'user'
    ];

    private $_NotAllowDisable = [
        'photo'
    ];

	public function __construct($refresh = false) {
		if (defined('PHPFOX_NO_APPS')) {
			self::$_apps = [];
			return;
		}

		$base = PHPFOX_DIR_SITE . 'Apps' . PHPFOX_DS;
		if (!is_dir($base)) {
			self::$_apps = [];
			return;
		}

		if (self::$_apps !== null && !$refresh) {
			return;
		}

		self::$_apps = [];
		$cache = new \Core\Cache();

		foreach (scandir($base) as $app) {
			if ($app == '.' || $app == '..') {
				continue;
			}

			$path = $base . $app . PHPFOX_DS;

			if (!file_exists($path . 'app.lock')) {
				continue;
			}
            
            if (in_array($app, ['PHPfox_IM', 'PHPfox_Videos', 'PHPfox_CDN_Service'])) {
                if (!\Phpfox::isPackage(3)) {
                    if (\Phpfox::isUser() && \Phpfox::getUserParam('admincp.has_admin_access'))
                    {
                        Admincp_Service_Module_Process::instance()->updateActivity($app, 0, false);
                    }

                    continue;
                }
            }
            
			if (!$appClass = Lib::appInit($app)) {
			    continue;
            }
            
            if (!$appClass->isActive()) {
                if (!((defined('PHPFOX_INSTALLER') && PHPFOX_INSTALLER))){
                    continue;
                }
            }
			if (isset($appClass->routes)) {
				foreach ((array) $appClass->routes as $key => $route) {
					$orig = $route;
					$route = (array) $route;
					$route['id'] = $appClass->id;
					if (is_string($orig)) {
						$route['url'] = $orig;
					}
					Route::$routes = array_merge(Route::$routes, [$key => $route]);
				}
			}


			$vendor = $appClass->path . 'vendor/autoload.php';
			if (file_exists($vendor)) {
				require_once($vendor);
			}

			self::$_apps[$appClass->id] = $appClass;
		}

		$reset = false;
		$settings = [];

		if (is_bool($cache->get('app_settings'))) {
			$reset = true;
		}

		foreach ($this->all() as $app) {
			if ($app->blocks) {
				$blocks = [];
				foreach ($app->blocks as $block) {
					$blocks[$block->route][$block->location][] = $block->callback;
				}
				\Core\Block\Group::make($blocks);
			}

			if ($reset && $app->settings) {
				foreach ($app->settings as $key => $value) {
					$thisValue = (isset($value->value) ? $value->value : null);
					$value = (new \Core\Db())->select('*')->from(':setting')->where(['var_name' => $key])->get();
					if (isset($value['value_actual'])) {
						$thisValue = $value['value_actual'];
					}
					$settings[$key] = $thisValue;
				}
			}
		}

		if ($reset) {
			$cache->set('app_settings', $settings);

			new \Core\Setting($cache->get('app_settings'));
		}
		foreach ($this->all() as $data) {
		    if ($oAppObject = Lib::appInit($data->id)) {
		        if ($oAppObject->isActive()) {
                    \Core\Route\Controller::$active = $data->path;
                    \Core\Route\Controller::$activeId = $data->id;
                    if (file_exists($data->path . 'start.php')) {
                        $callback = require_once($data->path . 'start.php');
                        if (is_callable($callback)) {
                            $View = new \Core\View();
                            $viewEnv = null;
                            if (is_dir($data->path . 'views/')) {
                                $View->loader()->addPath($data->path . 'views/', $data->id);
                                $viewEnv = $View->env();
                            }
                            call_user_func($callback, $this->get($data->id), $viewEnv);
                        }
                    }
                }
            }
		}

        if (function_exists('flavor')) {
            if (flavor()->active) {
                $start = flavor()->active->path . 'start.php';
                if (file_exists($start)) {
                    require_once($start);
                }
            }
        }
	}
    
	public function vendor()
    {

	}

	public function make($name) {
		ignore_user_abort(true);

		$base = PHPFOX_DIR_SITE . 'Apps/';
		$isGit = false;
		$gitFile = null;
		$git = '';
		$url = '';

		if (substr($name, 0, 8) == 'https://') {
			$isGit = true;
			$git = $name;

			$url = substr_replace(str_replace(['github.com'], ['raw.githubusercontent.com'], $git), '', -4) . '/master/Install.php';
			$gitFile = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . md5($git) . '.log';
			if (file_exists($gitFile)) {
				unlink($gitFile);
			}

			$headers = @get_headers($url);
			if ($headers[0] != 'HTTP/1.1 200 OK') {
				throw error('Unable to load the URL "%s"', $url);
			}

			file_put_contents($gitFile, "## Github Headers: ##\n" . print_r($headers, true) . "\n\n");

			$json = json_decode(file_get_contents($url . '?v=' . PHPFOX_TIME));
			if (!isset($json->id)) {
				throw error('Not a valid JSON file. Missing App ID.');
			}
			$name = $json->id;
			file_put_contents($gitFile, "## App JSON File: ##\n" . print_r($json, true) . file_get_contents($gitFile) . "\n\n");
		}

		if (!preg_match('/^[a-zA-Z\_0-9]+$/', $name)) {
			throw new \Exception('Product name can only contain alphanumeric characters and/or an underscore.');
		}

		$appBase = $base . $name . '/';
		if (is_dir($appBase)) {
			throw new \Exception('App already exists.');
		}

		if ($isGit && function_exists('shell_exec')) {
			$out = shell_exec('git --version');
			if (!preg_match('/git version ([0-9\.]+)(.*?)/', $out)) {
				throw new \Exception('Server does not support git.');
			}
			file_put_contents($gitFile, "## git version: ##\n" . $out . file_get_contents($gitFile) . "\n\n");
		}

		try {
			if (!$isGit) {
				throw error('not_git');
			}

			$out = shell_exec('git clone ' . $git . ' ' . $appBase . ' 2>&1');
			if (!file_exists($appBase . 'Install.php')) {
				throw error('Not a valid Git app.');
			}
			file_put_contents($gitFile, "## Running git clone: ##\n" . $out . file_get_contents($gitFile) . "\n\n");

			$headers = @get_headers(str_replace('Install.php', 'composer.json', $url));
			if ($headers[0] == 'HTTP/1.1 200 OK') {
				$composer = $appBase . 'composer.phar';
				if (!file_exists($composer)) {
					file_put_contents($composer, file_get_contents('https://getcomposer.org/composer.phar'));
				}
				chdir($appBase);
				$out = shell_exec('php composer.phar install 2>&1');
				file_put_contents($gitFile, "## Running composer: ##\n" . $out . file_get_contents($gitFile));
				chdir(PHPFOX_DIR);
			}

			$this->processJson($json, $appBase);
		}
		catch (\Exception $e) {
			if ($e->getMessage() != 'not_git') {
				throw new \Exception($e->getMessage(), $e->getCode(), $e);
			}

			$dirs = [
				'assets',
				'hooks',
				'notifications',
				'views'
			];
			foreach ($dirs as $dir) {
				$path = $appBase . $dir;
				if (!is_dir($path)) {
					mkdir($path, 0777, true);
				}
			}
            
            \Core\App\Migrate::migrate($name, true);

			file_put_contents($appBase . 'assets/autoload.js', "\n\$Ready(function() {\n\n});");
			file_put_contents($appBase . 'assets/autoload.css', "\n");
			file_put_contents($appBase . 'start.php', "<?php\n");
		}

		$lockPath = $appBase . 'app.lock';
		$lock = json_encode(['installed' => PHPFOX_TIME, 'version' => 0], JSON_PRETTY_PRINT);
		file_put_contents($lockPath, $lock);

		(new \Core\Cache())->purge();

		$App = new App(true);

		$Object = $App->get($name);

		$this->makeKey($Object, md5(uniqid()), md5(uniqid() . rand(0, 10000)));
        
		return $Object;
	}

	public function makeKey(App\Object $App, $id, $key, $internalId = 0) {
		$file = PHPFOX_DIR_SETTINGS . md5($App->id . \Phpfox::getParam('core.salt')) . '.php';

		$response = [
			'id' => $id,
			'key' => $key,
			'version' => $App->version,
			'internal_id' => $internalId
		];
		$paste = "<?php\nreturn " . var_export((array) $response, true) . ';';

		file_put_contents($file, $paste);
	}

	/**
	 * @param null $zip
	 * @return App\Object
	 * @throws mixed
	 */
	public function import($zip = null, $download = false, $isUpgrade = false) {
		if ($zip === null || empty($zip)) {
			$zip = PHPFOX_DIR_FILE . 'static'.PHPFOX_DS.'import-' . uniqid() . '.zip';
			register_shutdown_function(function() use($zip) {
				unlink($zip);
			});

			if (isset($_FILES['ajax_upload'])) {
				file_put_contents($zip, file_get_contents($_FILES['ajax_upload']['tmp_name']));
			}
			else {
				file_put_contents($zip, file_get_contents('php://input'));
			}
		}

		if ($download) {
			$zipUrl = $zip;
			$zip = PHPFOX_DIR_FILE . 'static'.PHPFOX_DS.'import-' . uniqid() . '.zip';
			register_shutdown_function(function() use($zip) {
//				unlink($zip);
			});

			file_put_contents($zip, file_get_contents($zipUrl));
		}

		$fromWindows = false;
		$archive = new \ZipArchive();
		$archive->open($zip);
		$json = $archive->getFromName('/Install.php');

		if (!$json) {
			$json = $archive->getFromName('Install.php');
		}

		if (!$json) {
			$json = $archive->getFromName('\\Install.php');
			$fromWindows = true;
		}

		$json = json_decode($json);
		if (!isset($json->id)) {
			throw error('Not a valid App to install.');
		}

		$base = PHPFOX_DIR_SITE . 'Apps/' . $json->id . '/';
		if (!is_dir($base)) {
			mkdir($base, 0777, true);
		}

		$archive->close();
		$appPath = $base . 'import-' . uniqid() . '.zip';
		copy($zip, $appPath);

		$newZip = new \ZipArchive();
		$newZip->open($appPath);
		$newZip->extractTo($base);
		$newZip->close();

		register_shutdown_function(function() use($appPath) {
			unlink($appPath);
		});

		$check = $base . 'app.json';
		if (!file_exists($check)) {
			throw new \Exception('App was unable to install.');
		}

		$lockPath = $base . 'app.lock';
		if (!$isUpgrade && file_exists($lockPath)) {
			unlink($lockPath);
		}

		$isNew = false;
		if (file_exists($lockPath)) {
			$lock = json_decode(file_get_contents($lockPath));
			$lock->updated = PHPFOX_TIME;
			file_put_contents($lockPath, json_encode($lock, JSON_PRETTY_PRINT));
		}
		else {
			$isNew = true;

			$this->processJson($json, $base);

			$lock = json_encode(['installed' => PHPFOX_TIME, 'version' => $json->version], JSON_PRETTY_PRINT);
			file_put_contents($lockPath, $lock);
		}

		$CoreApp = new \Core\App(true);
		$Object = $CoreApp->get($json->id);

		if ($isNew) {
			$Request = \Phpfox_Request::instance();
			$internalId = 0;
			if ($Request->get('product')) {
				$product = json_decode($Request->get('product'));
				$internalId = $product->id;
			}
			$this->makeKey($Object, $Request->get('auth_id'), $Request->get('auth_key'), $internalId);
		}

		return $Object;
	}

	public function processUpgrade($json, $base) {
		if (file_exists($base . 'installer.php')) {
			\Core\App\Installer::$method = 'onInstall';
			\Core\App\Installer::$basePath = $base;

			require_once($base . 'installer.php');
		}
	}
    
    /**
     * @deprecated
     * @param $json
     * @param $base
     *
     * @return bool
     */
	public function processJson($json, $base) {
	    return false;
	}

	/**
	 * @param $id
	 * @return App\Object|null
	 */
	public function getByInternalId($id) {
		foreach ($this->all() as $app) {
			if ($app->internal_id == $id) {
				return $app;
			}
		}
        return null;
	}

	public function get($id) {
		if (substr($id, 0, 9) == '__module_') {
			$id = substr_replace($id, '', 0, 9);
			$db = new \Core\Db();
			$module = $db->select('m.*')
				->from(':module', 'm')
				->where(['m.module_id' => $id])
				->get();

			if ($module['product_id'] == 'phpfox') {
				$module['version'] = \Phpfox::getVersion();
			}

			$app = [
				'id' => '__module_' . $id,
				'name' => ($module['phrase_var_name'] && ($module['product_id'] != 'phpfox')) ? _p($module['phrase_var_name']) : \Phpfox_Locale::instance()->translate($id, 'module'),
				'path' => null,
                'is_active' => $module['is_active'],
                'module_id' => $id,
				'is_module' => true,
				'version' => $module['version'],
				'icon' => (!empty($module['apps_icon'])) ? $module['apps_icon'] : null,
				'vendor' => (!empty($module['vendor'])) ? $module['vendor'] : null,
			];
		}
		else {
			if (!isset(self::$_apps[$id])) {
				throw new \Exception('App not found "' . $id . '".');
			}

			$app = self::$_apps[$id];
		}
		return new App\Object($app);
	}

    /**
     * @param bool|string $includeModules
     *
     * @return App\Object[]
     */
	public function all($includeModules = false)
    {
		$apps = [];
		if ($includeModules) {
			$modules = Phpfox_Module::instance()->all();
			$skip = $this->_aCoreApps;
			foreach ($modules as $module_id) {
				if (in_array($module_id, $skip)) {
					continue;
				}

				$coreFile = PHPFOX_DIR_MODULE . $module_id . '/install/version/v3.phpfox';
				if ($includeModules == '__core') {
					if (!file_exists($coreFile)) {
						continue;
					}
				}
				else if ($includeModules == '__not_core' || $includeModules == '__remove_core') {
					if (file_exists($coreFile)) {
						continue;
					}
				}

				$aModule = \Admincp_Service_Module_Module::instance()->getForEdit($module_id);
                if ($aModule['phrase_var_name'] == 'module_apps'){
                    continue;
                }
				$aProduct = ($aModule && !empty($aModule['product_id'])) ? \Admincp_Service_Product_Product::instance()->getForEdit($aModule['product_id']) : array();
				$app = [
					'id' => '__module_' . $module_id,
					'name' => ($aProduct && ($aModule['product_id'] != 'phpfox') && $aProduct['title']) ? $aProduct['title'] : \Phpfox_Locale::instance()->translate($module_id, 'module'),
					'path' => null,
					'is_module' => true,
					'icon' => (!empty($aProduct['icon'])) ? $aProduct['icon'] : null,
					'vendor' => (!empty($aProduct['vendor'])) ? $aProduct['vendor'] : null
				];

				$apps[] = new App\Object($app);
			}

			if ($includeModules == '__core' || $includeModules == '__not_core') {
				return $apps;
			}
		}

		foreach (self::$_apps as $app) {
			$apps[] = new App\Object($app);
		}

		return $apps;
	}

	public function processRow($app){
        if($app['type'] ==  'module'){
            $oAppDetail = [
                    'id' => '__module_' . $app['id'],
                    'name' => _p($app['name']),
                    'path' => null,
                    'is_module' => true,
                    'is_active' => $app['is_active'],
                    'icon' => (!empty($app['icon'])) ? $app['icon'] : null,
                    'vendor' => (!empty($app['vendor'])) ? $app['vendor'] : null,
                    'publisher' => $app['publisher'],
                    'allow_disable' => (in_array($app['id'], $this->_NotAllowDisable)) ? false : true,
                    'version' => $app['version']
                ];
        }
        else {
            $oAppDetail = Lib::appInit($app['id']);
        }

        $oAppObject = new App\Object($oAppDetail);
        $oAppObject->version = $app['version'];
        if (!empty($app['publisher'])) {
            $oAppObject->publisher = $app['publisher'];
        }
        $oAppObject->publisher_url = $app['vendor'];
        return $oAppObject;
    }

    /**
     * Get all modules and apps (included disabled)
     *
     * @return array
     */
	public function getForManage()
    {
        $sCoreApps = implode($this->_aCoreApps, "','");
        $oDb = db();
        $oDb->select('apps_icon as icon, module_id AS id, version, author as publisher, vendor, phrase_var_name as name, is_active, \'module\' AS type')
            ->from(":module")
            ->where("module_id NOT IN ('" . $sCoreApps . "') AND phrase_var_name!='module_apps'")
            ->union();
        $oDb->select('apps_icon as icon, apps_id as id, version, author as publisher, vendor,  apps_name as name, is_active, \'app\' AS type')
            ->from(':apps')
            ->union();


        return array_map(function($item){
            return $this->processRow($item);
        },$oDb->executeRows());
    }

	public function exists($id, $bReturnId = false) {
		return (isset(self::$_apps[$id]) ? ($bReturnId ? $id : true) : false);
	}
}