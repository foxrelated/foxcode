<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
define('PHPFOX_NO_APPS', true);
/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: installer.class.php 7281 2014-04-23 13:44:32Z Fern $
 */
class Phpfox_Installer
{
	private $_oTpl = null;
	private $_oReq = null;
	private $_sUrl = 'install';
	private $_sStep = 'start';
	private $_bUpgrade = false;

	private static $_aPhrases = array();

	private $_aSteps = array(
		'start',
		'key',
		'license',
		'requirement',
        'apps',
		'configuration',
		'process',
		'import',
		'update',
		'language',
		'module',
		'post',
		'final',
		'completed'
	);

	private $_aModuleInstalls = array(
		'video'
	);

	private $_aVersions = array(
		'3.0.0beta1',
		'3.0.0beta2',
		'3.0.0beta3',
		'3.0.0beta4',
		'3.0.0beta5',
		'3.0.0rc1',
		'3.0.0rc2',
		'3.0.0rc3',
		'3.0.0',
		'3.0.1',
		'3.0.2',


		'3.1.0beta1',
		'3.1.0rc1',
		'3.1.0',

		'3.2.0beta1',
		'3.2.0rc1',
		'3.2.0',

		'3.3.0beta1',
		'3.3.0beta2',
		'3.3.0rc1',
		'3.3.0',

		'3.4.0beta1',
		'3.4.0beta2',
		'3.4.0rc1',
		'3.4.0',
		'3.4.1',

		'3.5.0beta1',
		'3.5.0beta2',
		'3.5.0rc1',
		'3.5.0',
		'3.5.1',

		'3.6.0beta1',
		'3.6.0beta2',
		'3.6.0beta3',
		'3.6.0rc1',
		'3.6.0',

		'3.7.0beta1',
		'3.7.0beta2',
		'3.7.0rc1',
		'3.7.0',
		'3.7.1',
		'3.7.2',
		'3.7.3',
		'3.7.4',
		'3.7.5',

		'3.7.6beta1',
		'3.7.6rc1',
		'3.7.6',
		'3.7.7',

		'3.8.0',

		'3.9.0',

		'4.0.0rc1',
		'4.0.0rc2',
		'4.0.0',
		'4.0.1',
		'4.0.2',
		'4.0.3',
		'4.0.4',
		'4.0.5',
		'4.0.6',
		'4.0.7',
		'4.0.8',
		'4.0.9',
		'4.0.10',

		'4.1.0',
		'4.1.1',
		'4.1.2',

        '4.2.0',
		'4.2.1',
		'4.2.2',

		'4.3.0',
		'4.3.1',
		'4.3.2',

		'4.4.0',
		'4.4.1',
        '4.4.2',
        '4.4.3',

        '4.5.0',
	);

	private $_sTempDir = '';

	private $_sSessionFile = '';

	private $_hFile = null;

	private $_aOldConfig = array();

	private $_sPage = '';

	private static $_sSessionId = null;

	/**
	 * @var Phpfox_Database_Driver_Mysql
	 */
	public $db;

	public function __construct()
	{
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');

		session_start();

		$this->_oTpl = Phpfox_Template::instance();
		$this->_oReq = Phpfox_Request::instance();
		$this->_oUrl = Phpfox_Url::instance();

		$this->_sTempDir = Phpfox_File::instance()->getTempDir();

		$this->_sPage = $this->_oReq->get('page');
		$this->_sUrl = ($this->_oReq->get('req1') == 'upgrade' ? 'upgrade' : 'install');
		self::$_sSessionId = ($this->_oReq->get('sessionid') ? $this->_oReq->get('sessionid') : uniqid());

		if (defined('PHPFOX_IS_UPGRADE'))
		{
			$this->_oTpl->assign('bIsUprade', true);
			$this->_bUpgrade = true;

			if (file_exists(PHPFOX_DIR . 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'server.sett.php'))
			{
				$_CONF = [];
				require_once(PHPFOX_DIR . 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'server.sett.php');

				$this->_aOldConfig = $_CONF;
			}
		}

		if (!Phpfox_File::instance()->isWritable($this->_sTempDir))
		{
			if (PHPFOX_SAFE_MODE)
			{
				$this->_sTempDir = PHPFOX_DIR_FILE . 'log' . PHPFOX_DS;
				if (!Phpfox_File::instance()->isWritable($this->_sTempDir))
				{
					exit('Unable to write to temporary folder: ' . $this->_sTempDir);
				}
			}
			else
			{
				exit('Unable to write to temporary folder: ' . $this->_sTempDir);
			}
		}

		$this->_sSessionFile = $this->_sTempDir . 'installer_' . ($this->_bUpgrade ? 'upgrade_' : '') . '_' . self::$_sSessionId . '_' . 'phpfox.log';

		$this->_hFile = fopen($this->_sSessionFile, 'a');

		if ($this->_sUrl == 'install' && $this->_oReq->get('req2') == '')
		{
			if (file_exists(PHPFOX_DIR_SETTING . 'server.sett.php'))
			{
				require(PHPFOX_DIR_SETTING . 'server.sett.php');

				if (isset($_CONF['core.is_installed']) && $_CONF['core.is_installed'] === true)
				{
					$this->_oUrl->forward('../install/index.php?' . PHPFOX_GET_METHOD . '=/upgrade/');
				}
			}

			if (file_exists(PHPFOX_DIR . 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'server.sett.php'))
			{
				$this->_oUrl->forward('../install/index.php?' . PHPFOX_GET_METHOD . '=/upgrade/');
			}
		}

		// Define some needed params
		Phpfox::getLib('setting')->setParam(array(
				'core.path' => self::getHostPath(),
				'core.url_static_script' => self::getHostPath() . 'static/jscript/',
				'core.url_static_css' => self::getHostPath() . 'static/style/',
				'core.url_static_image' => self::getHostPath() . 'static/image/',
				'sCookiePath' => '/',
				'sCookieDomain' => '',
				'bAllowHtml' => false,
				'core.url_rewrite' => '2'
			)
		);

	}

	public static function getSessionId()
	{
		return self::$_sSessionId;
	}

	public static function getHostPath()
	{
		$protocol = 'http';
		if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
			$protocol = 'https';
		}
		$parts = explode('index.php', $_SERVER['PHP_SELF']);
		$url = $protocol . '://' . $_SERVER['HTTP_HOST'] . $parts[0];

		return $url;
	}

	public static function getPhrase($sVar)
	{
		return (isset(self::$_aPhrases[$sVar]) ? self::$_aPhrases[$sVar] : '');
	}

	public function run()
	{
		if (!is_dir(PHPFOX_DIR . 'file/')) {
			mkdir(PHPFOX_DIR . 'file/', 0777);
            file_put_contents(PHPFOX_DIR . 'file/.htaccess','Options -Indexes');
            touch(PHPFOX_DIR . 'file/index.html');
		}

		if ($this->_bUpgrade
			&& (int) substr($this->_getCurrentVersion(), 0, 1) < 2
			&& file_exists(PHPFOX_DIR . '.htaccess')
		)
		{
			$sHtaccessContent = file_get_contents(PHPFOX_DIR . '.htaccess');
			if (preg_match('/RewriteEngine/i', $sHtaccessContent))
			{
				exit('In order for us to continue with the upgrade you will need to rename or remove the file ".htaccess".');
			}
		}

		$sStep = ($this->_oReq->get('step') ? strtolower($this->_oReq->get('step')) : 'start');

		$bPass = false;
		if (!in_array($sStep, $this->_aSteps))
		{
			if (in_array($sStep, $this->_aModuleInstalls))
			{
				$bPass = true;
			}
			else
			{
				exit('Invalid step.');
			}
		}

		$sMethod = '_' . $sStep;

		$iStep = 0;
		foreach ($this->_aSteps as $iKey => $sMyStep)
		{
			if ($sMyStep === $sStep)
			{
				$iStep = ($iKey - 1);
				break;
			}
		}

		if ($bPass === false && isset($this->_aSteps[$iStep]) && !$this->_isPassed($this->_aSteps[$iStep]))
		{
			$this->_oUrl->forward($this->_step($this->_aSteps[$iStep]));
		}

		$this->_sStep = $sStep;

		$this->_oTpl->assign([
			'sUrl' => $this->_sUrl
		]);

		if (method_exists($this, $sMethod))
		{
			$data = call_user_func(array(&$this, $sMethod));
			if (!Phpfox_Error::isPassed()) {
				$data = [
					'errors' => Phpfox_Error::get()
				];
			}

			if ($sStep != 'start' && !is_array($data)) {
				$content = $this->_oTpl->getLayout($sStep, true);
				$data = [
					'content' => $content
				];
			}

			if (is_array($data)) {
				header('Content-type: application/json');
				echo json_encode($data);
				exit;
			}
		} else {
			$sStep = 'start';
		}

		if (!file_exists($this->_oTpl->getLayoutFile($sStep))) {
			$sStep = 'default';
		}

		list($aBreadCrumbs, $aBreadCrumbTitle) = $this->_oTpl->getBreadCrumb();

		$base = self::getHostPath() . 'PF.Base/';
		$version = Phpfox::getVersion();
		if ($this->_bUpgrade) {
			$version = 'Upgrading from ' . $this->_getCurrentVersion() . ' to ' . $version;
		}
		$this->_oTpl->setHeader(array(
				'<script>var BasePath = \'' . self::getHostPath() . '\';</script>',
				'<link href="' . $base . 'theme/install/default/style/default/css/layout.css" rel="stylesheet">',
				'<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">',
				'<script src="' . $base . 'static/jscript/jquery/jquery.js"></script>',
				'<script src="' . $base . 'static/jscript/install.js"></script>'
			)
		)
			->assign(array(
					'sTemplate' => $sStep,
					'sLocaleDirection' => 'ltr',
					'sLocaleCode' => 'en',
					'sUrl' => $this->_sUrl,
					'aErrors' => Phpfox_Error::get(),
					'sPublicMessage' => Phpfox::getMessage(),
					'aBreadCrumbs' => $aBreadCrumbs,
					'aBreadCrumbTitle' => $aBreadCrumbTitle,
					'aSteps' => $this->_getSteps(),
					'sCurrentVersion' => $version . (defined('PHPFOX_TRIAL_MODE') ? ' Trial' : '')
				)
			);

		if ($this->_bUpgrade)
		{
			$this->_oTpl->setTitle('Upgrading from: ' . $this->_getCurrentVersion());
		}

		$this->_oTpl->getLayout('template');

		Phpfox::clearMessage();
	}

	########################
	# Special Module Install Routines
	########################

	private function _video($bInstall = false)
	{
		$sFfmpeg = '';
		$sMencoder = '';
		$iPass = 0;
		if (!PHPFOX_SAFE_MODE)
		{
			if (($aVals = $this->_oReq->getArray('val')))
			{
				if (!empty($aVals['ffmpeg']))
				{
					exec($aVals['ffmpeg'] . ' 2>&1', $aOutput);

					if (preg_match("/FFmpeg version/", $aOutput[0]))
					{
						if ($bInstall === true)
						{
							$this->_db()->update(Phpfox::getT('setting'), array('value_actual' => $aVals['ffmpeg']), 'module_id = \'video\' AND var_name = \'ffmpeg_path\'');
						}
						else
						{
							$_SESSION[Phpfox::getParam('core.session_prefix')]['installer_ffmpeg'] = $aVals['ffmpeg'];
						}

						$iPass++;
					}
					else
					{
						Phpfox_Error::set($aOutput[0]);
					}
					unset($aOutput);
				}

				if (!empty($aVals['mencoder']))
				{
					exec($aVals['mencoder'] . ' 2>&1', $aOutput);

					if (preg_match("/MPlayer Team/", $aOutput[0]))
					{
						if ($bInstall === true)
						{
							$this->_db()->update(Phpfox::getT('setting'), array('value_actual' => $aVals['mencoder']), 'module_id = \'video\' AND var_name = \'mencoder_path\'');
						}
						else
						{
							$_SESSION[Phpfox::getParam('core.session_prefix')]['installer_mencoder'] = $aVals['mencoder'];
						}

						$iPass++;
					}
					else
					{
						Phpfox_Error::set($aOutput[0]);
					}
					unset($aOutput);
				}
			}

			if (PHP_OS == 'Linux' && !preg_match('/shell_exec/', ini_get('disable_functions')))
			{
				$sOutput = shell_exec('whereis ffmpeg 2>&1');
				$aOutput = explode("\n", $sOutput);
				if (isset($aOutput[0]))
				{
					$aParts = explode('ffmpeg:', $aOutput[0]);
					if (isset($aParts[1]))
					{
						$aSubParts = explode(' ', trim($aParts[1]));
						if (isset($aSubParts[0]) && !empty($aSubParts[0]))
						{
							if (PHPFOX_OPEN_BASE_DIR || (!PHPFOX_OPEN_BASE_DIR && file_exists($aSubParts[0])))
							{
								$sFfmpeg = $aSubParts[0];
							}

						}
					}
				}
				unset($aOutput);

				$sOutput = shell_exec('whereis mencoder 2>&1');
				$aOutput = explode("\n", $sOutput);
				if (isset($aOutput[0]))
				{
					$aParts = explode('mencoder:', $aOutput[0]);
					if (isset($aParts[1]))
					{
						$aSubParts = explode(' ', trim($aParts[1]));
						if (isset($aSubParts[0]) && !empty($aSubParts[0]))
						{
							if (PHPFOX_OPEN_BASE_DIR || (!PHPFOX_OPEN_BASE_DIR && file_exists($aSubParts[0])))
							{
								$sMencoder = $aSubParts[0];
							}

						}
					}
				}
				unset($aOutput);
			}
		}

		if (!empty($_SESSION[Phpfox::getParam('core.session_prefix')]['installer_ffmpeg']))
		{
			$sFfmpeg = $_SESSION[Phpfox::getParam('core.session_prefix')]['installer_ffmpeg'];
		}

		if (!empty($_SESSION[Phpfox::getParam('core.session_prefix')]['installer_mencoder']))
		{
			$sMencoder = $_SESSION[Phpfox::getParam('core.session_prefix')]['installer_mencoder'];
		}

		$aForms = array(
			'ffmpeg' => $sFfmpeg,
			'mencoder' => $sMencoder
		);

		return $aForms;
	}

	########################
	# Install/Upgrade Steps
	########################
	private function _start()
	{
		$this->_oTpl->setTitle('phpFox ' . Phpfox::getVersion());

		$errors = $this->_requirement();
		if (is_array($errors)) {
			$this->_oTpl->assign([
				'requirementErrors' => $errors
			]);
		}

		if ($_POST && is_array($errors)) {
			foreach ($errors as $error) {
				Phpfox_Error::set($error);
			}
		}

		if (!is_array($errors) && $_POST) {
			return [
				'message' => 'Checking requirements',
				'next' => 'configuration'
			];
		}
	}

	private function _key()
	{
        $is_trial = (defined('PHPFOX_TRIAL_MODE') ? true : false);

		$oValid = Phpfox_Validator::instance()->set(array('sFormName' => 'js_form', 'aParams' => array(
                    'license_id' => 'Provide a license ID.',
                    'license_key' => 'Provide a license key.'
			    )
			)
		);

		if (($aVals = $this->_oReq->getArray('val')) || $is_trial)
		{
			if ($is_trial || $oValid->isValid($aVals))
			{
				if ($aVals['license_id'] == 'techie' && $aVals['license_key'] == 'techie') {
					$response = new stdClass();
					$response->valid = true;
                    $response->license = new stdClass();
                    $response->license->package_id = 3;
				}
                else if ($is_trial) {
                    $response = new stdClass();
                    $response->valid = true;
                    $response->license = new stdClass();
                    $response->license->package_id = 3;
                    $aVals = [
                        'is_trial' => true,
                        'license_id' => '',
                        'license_key' => ''
                    ];
                }
				else {
					try {
						$Home = new Core\Home($aVals['license_id'], $aVals['license_key']);
						$response = $Home->verify([
							'url' => $this->getHostPath()
						]);
					} catch (\Exception $e) {
						$response = new stdClass();
						$response->error = $e->getMessage();
					}
				}

				// Connect to phpFox and verify the license
				if (isset($response->valid))
				{
					$data = "<?php define('PHPFOX_LICENSE_ID', '{$aVals['license_id']}'); define('PHPFOX_LICENSE_KEY', '{$aVals['license_key']}');";
                    $data .= "\n\nif (!defined('PHPFOX_PACKAGE_ID')) {define('PHPFOX_PACKAGE_ID', '{$response->license->package_id}');}";

					file_put_contents(PHPFOX_DIR_SETTINGS . 'license.php', $data);

					if ($this->_bUpgrade) {
						return [
							'message' => 'Check Apps Compatible',
							'next' => 'apps'
						];
					}

					return [
						'message' => 'Verifying license',
						'next' => 'configuration'
					];
				}
				else
				{
					if (!is_object($response)) {
						$info = $response;
						$response = new stdClass();
						$response->error = $info;
					}

					Phpfox_Error::set($response->error);
					$this->_oTpl->assign(array(
							'sError' => $response->error
						)
					);
				}
			}
		}

		$this->_oTpl->assign(array(
				'sCreateJs' => $oValid->createJS(),
				'sGetJsForm' => $oValid->getJsForm(),
				'bHasCurl' => (function_exists('curl_init') ? true : false)
			)
		);
	}

	private function _apps()
    {
        $base = PHPFOX_DIR_SITE . 'Apps' . PHPFOX_DS;
        if ($aVals = $this->_oReq->getArray('val')) {
            foreach (scandir($base) as $app) {
                if (!$appClass = \Core\Lib::appInit($app)) {
                    continue;
                }
//                if (!$appClass->isCompatible()) {
//                    $appClass->disable();
//                }
            }
            return [
                'message' => 'Updating',
                'next'    => 'update'
            ];
        }
        //These line for 4.5.0 only
        \Core\App\Migrate::run();
        //End 4.5.0
        $Apps = [];
        foreach (scandir($base) as $app) {
            if (!$appClass = \Core\Lib::appInit($app)) {
                continue;
            }
            if ($appClass->isCore()) {
                continue;
            }
            $Apps[$appClass->name] = $appClass->isCompatible() ? "Yes" : "No";
        }
        if (count($Apps)) {
            $this->_oTpl->assign([
                'apps' =>$Apps
            ]);
        } else {
            return [
                'message' => 'Updating',
                'next'    => 'update'
            ];
        }

    }

	private function _license()
	{
		if ($this->_oReq->get('agree'))
		{
			$this->_pass('requirement');
		}

		$this->_oTpl->assign(array(
				'bIsUpgrade' => ($this->_sUrl == 'upgrade' ? true : false)
			)
		);
	}

	private function _requirement()
	{
		$errors = [];
		$aVerify = array(
			'<a href="http://php.net/manual/en/book.mbstring.php" target="_blank">Multibyte String</a>' => (function_exists('mb_substr') ? true : false),
			'<a href="http://php.net/manual/en/book.xml.php" target="_blank">XML Parser</a>' => (function_exists('xml_set_element_handler') ? true : false),
			'<a href="http://php.net/manual/en/book.image.php" target="_blank">PHP GD</a>' => ((extension_loaded('gd') && function_exists('gd_info')) ? true : false),
			'<a href="http://php.net/manual/en/function.mysqli-connect.php" target="_blank">PHP Mysqli</a>' => ((function_exists('mysqli_connect')) ? true : false),
			'<a href="http://php.net/manual/en/class.ziparchive.php" target="_blank">PHP ZipArchive</a>' => ((class_exists('ZipArchive')) ? true : false)
		);

		foreach ($aVerify as $sCheck => $bPassed)
		{
			if ($bPassed === false)
			{
				$errors[] = 'PHP module "' . $sCheck . '" is missing.';
			}
		}

		$aDrivers = Phpfox::getLib('database.support')->getSupported();
		$aDbChecks = array();
		$iDbs = 0;
		foreach ($aDrivers as $aDriver)
		{
			$aDbChecks[$aDriver['label']] = $aDriver['available'];
			if ($aDriver['available'])
			{
				$iDbs++;
			}
		}

		if (!$iDbs)
		{
			$errors[] = 'No database driver found.';
		}

		if ($this->_bUpgrade && version_compare($this->_getCurrentVersion(), '4.0.0', '>')) {

		}
		else {
			$parent = dirname(dirname(dirname(dirname(__FILE__))));
			$dirs = [PHPFOX_DIR, PHPFOX_DIR_SITE];
			foreach ($dirs as $dir) {
				if (@!is_writable($dir)) {
					$dir = str_replace($parent, '', $dir);
					$dir = str_replace('/PF.Base/../', '/', $dir);

					$errors[] = "Directory needs to be writable: {$dir}";
				}
			}
		}

		if (count($errors)) {
			return $errors;
		}

		$aModuleLists = Phpfox_Module::instance()->getModuleFiles();
		$aModules = array_merge($aModuleLists['core'], $aModuleLists['plugin']);
		foreach ($aModules as $aModule)
		{
			if (($aFiles = Phpfox_Module::instance()->init($aModule['name'], 'aInstallWritable')))
			{
				foreach ($aFiles as $sDir)
				{
					$sDir = str_replace('/', PHPFOX_DS, $sDir);
					if (!is_dir(PHPFOX_DIR . $sDir)) {
						Phpfox_File::instance()->mkdir(PHPFOX_DIR . $sDir, true, 0777);
					}
				}
			}
		}

		if (!is_dir(PHPFOX_DIR_SITE . 'Apps')) {
			mkdir(PHPFOX_DIR_SITE . 'Apps');
			@chmod(PHPFOX_DIR_SITE . 'Apps',0777);
		}

		if (!is_dir(PHPFOX_DIR_SITE . 'flavors')) {
			mkdir(PHPFOX_DIR_SITE . 'flavors');
			@chmod(PHPFOX_DIR_SITE . 'flavors',0777);
		}

		return true;
	}

	/**
	 * the host name only if its not oracle.
	 *
	 */
	private function _configuration()
	{
		Phpfox::getLib('cache')->remove();

		$aExists = array();
		$aForms = array();

		if (defined('PHPFOX_INSTALL_HOST'))
		{
			$aForms['host'] = PHPFOX_INSTALL_HOST;
			$aForms['name'] = PHPFOX_INSTALL_NAME;
			$aForms['user_name'] = PHPFOX_INSTALL_USER;
		}

		// Get supported database drivers
		$aDrivers = Phpfox::getLib('database.support')->getSupported(true);

		$oValid = Phpfox_Validator::instance()->set(array('sFormName' => 'js_form', 'aParams' => array(
					'prefix' => 'No database prefix provided.',
					'sitename' => 'Provide your Site name',
				)
			)
		);

		if ($aVals = $this->_oReq->getArray('val'))
		{
			if ($oValid->isValid($aVals))
			{
				Phpfox::getLibClass('phpfox.database.dba');

				$sDriver = 'phpfox.database.driver.' . strtolower(preg_replace("/\W/i", "", $aVals['driver']));
				if (Phpfox::getLibClass($sDriver))
				{
					$oDb = Phpfox::getLib($sDriver);

					if ($oDb->connect($aVals['host'], $aVals['user_name'], $aVals['password'], $aVals['name'], $aVals['port']))
					{
						Phpfox::getLib('session')->set('installer_db', $aVals);
						// Drop database tables, only if user allows us too
						if (isset($aVals['drop']) && ($aDrops = $this->_oReq->getArray('table')))
						{
							$oDb->dropTables($aDrops, $aVals);
						}

						$oDbSupport = Phpfox::getLib('database.support');

						$aTables = $oDbSupport->getTables($aVals['driver'], $oDb);

						$aSql = Phpfox_Module::instance()->getModuleTables($aVals['prefix']);

						foreach ($aSql as $sSql)
						{
							if (in_array($sSql, $aTables))
							{
								$aExists[] = $sSql;
							}
						}

						if (count($aExists))
						{
							Phpfox_Error::set('We have found that the following table(s) already exist:');
						}
						else
						{
							$aForms = array_merge($this->_video(), $aForms);

							if (Phpfox_Error::isPassed())
							{
								// Cache modules we need to install
								$sCacheModules = PHPFOX_DIR_FILE . 'log' . PHPFOX_DS . 'installer_modules.php';
								if (!empty($_POST['__is_cli'])) {
									$aVals['module'] = [];
									$base = PHPFOX_DIR . 'module/';
									foreach (scandir($base) as $module) {
										if (file_exists($base . $module . '/install/phpfox.xml.php')) {
											$aVals['module'][] = $module;
										}
									}
								}

								if (file_exists($sCacheModules))
								{
									unlink($sCacheModules);
								}

								$sData = '<?php' . "\n";
								$sData .= '$aModules = ';
								$sData .= var_export($aVals['module'], true);
                                $sData .= ";\n\$aSiteConfig = ";
                                $aSiteConfig = [
                                    'site_name' => $aVals['sitename']
                                ];
                                $sData .= var_export($aSiteConfig, true);
								$sData .= ";\n?>";
								Phpfox_File::instance()->write($sCacheModules, $sData);
								unset($aVals['module']);
								unset($aVals['sitename']);

								if ($this->_saveSettings($aVals))
								{
									return [
										'message' => 'Installing app tables',
										'next' => 'process'
									];
								}
							}
						}
					}
				}
			}
		}
		else
		{
			$aForms = array_merge($this->_video(), $aForms);
		}

		$aModules = Phpfox_Module::instance()->getModuleFiles();
		sort($aModules['core']);
		sort($aModules['plugin']);

		$this->_oTpl->setTitle('Configuration')
			->setBreadCrumb('Configuration')
			->assign(array(
					'aDrivers' => $aDrivers,
					'sCreateJs' => $oValid->createJS(),
					'sGetJsForm' => $oValid->getJsForm(false),
					'aTables' => $aExists,
					'aModules' => $aModules,
					'aForms' => $aForms
				)
			);
	}

	private function _process()
	{
		Phpfox::getLibClass('phpfox.database.dba');

		if ( strtolower(preg_replace("/\W/i", "", Phpfox::getParam(array('db', 'driver')))) == 'database_driver')
		{
			$aVals = Phpfox::getLib('session')->get('installer_db');

			if (isset($aVals['driver']))
			{
				unset($aVals['module']);
				unset($aVals['drop']);
				$aVals['user'] = $aVals['user_name'];
				$aVals['pass'] = $aVals['password'];
				$aT = array();
				$aT['db'] = $aVals;
				Phpfox::getLib('setting')->setParam( $aT );
				unset($aT);
			}
			unset($aVals);
			Phpfox::getLib('session')->remove('installer_db');
		}
		$sDriver = 'phpfox.database.driver.' . strtolower(preg_replace("/\W/i", "", Phpfox::getParam(array('db', 'driver'))));

		if (Phpfox::getLibClass($sDriver))
		{
			$oDb = Phpfox::getLib($sDriver);

			if ($oDb->connect(Phpfox::getParam(array('db', 'host')), Phpfox::getParam(array('db', 'user')), Phpfox::getParam(array('db', 'pass')), Phpfox::getParam(array('db', 'name')), Phpfox::getParam(array('db', 'port'))))
			{

			}
		}

		$oDbSupport = Phpfox::getLib('database.support');

		$sCacheModules = PHPFOX_DIR_FILE . 'log' . PHPFOX_DS . 'installer_modules.php';
		if (!file_exists($sCacheModules))
		{
			// Something went wrong...
		}

		$aModules = [];
		require_once($sCacheModules);

		$oModuleProcess = Admincp_Service_Module_Process::instance();

		foreach ($aModules as $iKey => $sModule)
		{
			if ($sModule == 'core')
			{
				unset($aModules[$iKey]);
			}
		}

		$aModules = array_merge(array('core'), $aModules);

		foreach ($aModules as $sModule)
		{
			if ($sModule == 'phpfoxsample' || $sModule == 'phpfox')
			{
				continue;
			}

			$oModuleProcess->install($sModule, array(
					'table' => true
				)
			);
		}

		$sModuleLog = PHPFOX_DIR_CACHE . 'installer_completed_modules.log';
		if (file_exists($sModuleLog))
		{
			unlink($sModuleLog);
		}

		return [
			'message' => 'Importing language package',
			'next' => 'import'
		];
	}

	private function _import()
	{
		Phpfox::getLib('phpfox.process')->import(Phpfox::getLib('xml.parser')->parse(PHPFOX_DIR_XML . 'version' . PHPFOX_XML_SUFFIX));
        Core_Service_Country_Process::instance()->importForInstall(Phpfox::getLib('xml.parser')->parse(PHPFOX_DIR_XML . 'country' . PHPFOX_XML_SUFFIX));

		return [
			'message' => 'Importing language package',
			'next' => 'language'
		];
	}

	private function _language()
	{
		$this->_db()->insert(Phpfox::getT('language'), array(
				'language_id' => 'en',
				'title' => 'English (US)',
				'user_select' => '1',
				'language_code' => 'en',
				'charset' => 'UTF-8',
				'direction' => 'ltr',
				'flag_id' => 'png',
				'time_stamp' => '1184048203',
				'created' => 'N/A (Core)',
				'site' => '',
				'is_default' => '1',
				'is_master' => '1'
			)
		);

		$themeId = $this->_db()->insert(Phpfox::getT('theme'), [
			'name' => 'Default',
			'folder' => 'default',
			'created' => PHPFOX_TIME,
			'is_active' => 1,
			'is_default' => 0
		]);

		$this->_db()->insert(Phpfox::getT('theme_style'), [
			'theme_id' => $themeId,
			'is_active' => 1,
			'is_default' => 1,
			'name' => 'Default',
			'folder' => 'default',
			'created' => PHPFOX_TIME
		]);

		return [
			'message' => 'Setting up apps',
                'next' => 'module'
		];
	}

	private function _module()
	{
		// Load the cached module list
		$sCacheModules = PHPFOX_DIR_FILE . 'log' . PHPFOX_DS . 'installer_modules.php';
		if (!file_exists($sCacheModules))
		{
			// Something went wrong...
		}
		$aModules = [];
		require_once($sCacheModules);

		$sModuleLog = PHPFOX_DIR_CACHE . 'installer_completed_modules.log';
		$aInstalled = array();
		if (file_exists($sModuleLog))
		{
			$aLines = file($sModuleLog);
			foreach ($aLines as $sLine)
			{
				$sLine = trim($sLine);

				if (empty($sLine))
				{
					continue;
				}

				$aInstalled[$sLine] = true;
			}
		}

		$bInstallAll = ((defined('PHPFOX_INSTALL_ALL_MODULES') || !empty($_POST['__is_cli'])) ? true : false);
		$oModuleProcess = Admincp_Service_Module_Process::instance();
		$hFile = fopen($sModuleLog, 'a+');
		$iCnt = 0;
		$sMessage = '';
		$sInstalledModule = '';
		$totalModules = count($aModules);
		$installedModules = 0;
		foreach ($aModules as $sModule)
		{
			if (isset($aInstalled[$sModule]))
			{
				$installedModules++;
				continue;
			}
		}

		foreach ($aModules as $sModule)
		{
			if (isset($aInstalled[$sModule]))
			{
				continue;
			}

			$iCnt++;
			$sInstalledModule .= $sModule . "\n";
			$sMessage .= "<li>" . $sModule . "</li>";

			$oModuleProcess->install($sModule, array('insert' => true));

			if ($bInstallAll === false && $iCnt == 5)
			{
				break;
			}
		}
		fwrite($hFile, $sInstalledModule);
		fclose($hFile);

		if ($this->_bUpgrade)
		{
			return ($iCnt === 0 ? true : false);
		}

		// No more modules to install then lets send them to the final step
		if ($iCnt === 0 || defined('PHPFOX_INSTALL_ALL_MODULES'))
		{
			$this->_pass();

			unlink($sModuleLog);
			return [
				'message' => 'Checking install',
				'next' => 'post'
			];
		}
		else
		{
			return [
				'message' => 'Setting up apps (' . $installedModules . ' out of ' . $totalModules . ')',
				'next' => 'module'
			];
		}
	}

	private function _post()
	{
		$aModules = [];
		// Load the cached module list
		$sCacheModules = PHPFOX_DIR_FILE . 'log' . PHPFOX_DS . 'installer_modules.php';
		if (!file_exists($sCacheModules))
		{
			// Something went wrong...
		}
		require_once($sCacheModules);

		$oModuleProcess = Admincp_Service_Module_Process::instance();
		foreach ($aModules as $sModule)
		{
			$oModuleProcess->install($sModule, array('post_install' => true));
		}

		return [
			'next' => 'final'
		];
	}

	private function _final()
	{
		$aForms = array();
		$aValidation = array(
			'email' => array(
				'def' => 'email',
				'title' => 'Provide a valid email.'
			),
			'password' => array(
				'def' => 'password',
				'title' => 'Provide a valid password.'
			)
		);

		$oValid = Phpfox_Validator::instance()->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));

		if ($aVals = $this->_oReq->getArray('val'))
		{
            $aVals['full_name'] = 'Admin';
            $aVals['user_name'] = 'admin';

			User_Service_Validate::instance()->user($aVals['user_name'])->email($aVals['email']);

			if ($oValid->isValid($aVals))
			{
				if (($iUserId = User_Service_Process::instance()->add($aVals, ADMIN_USER_ID)))
				{
					list($bLogin, $aUser) = User_Service_Auth::instance()->login($aVals['email'], $aVals['password'], true, 'email');
					if ($bLogin || isset($aVals['skip_user_login']))
					{
						define('PHPFOX_FEED_NO_CHECK', true);
						User_Service_Auth::instance()->setUserId($iUserId);
						$this->_db()->update(Phpfox::getT('user_field'), array('in_admincp' => PHPFOX_TIME), 'user_id = ' . $iUserId);
						$this->_db()->update(Phpfox::getT('setting'), array('value_actual' => Phpfox::getVersion()), 'var_name = \'phpfox_version\'');
						if (!$this->_db()->isField(Phpfox::getT('feed'), 'total_view')) {
                            $this->_db()->addField(['table' => Phpfox::getT('feed'), 'field' => 'total_view', 'type' => 'INT', 'attribute' => 'UNSIGNED', 'null' => false, 'default' => '0', 'after' => 'content']);
						}

						$this->_video(true);

						User_Service_Process::instance()->updateStatus([
							'user_status' => 'Hello World!'
						]);

                        if (function_exists('ioncube_file_info') && is_array(ioncube_file_info())) {
                            (new Core\Home())->trial([
                                'email' => $aVals['email'],
                                'url' => $this->getHostPath()
                            ]);
                        }

						return [
							'next' => 'completed'
						];
					}
				}
			}
		} else {
			$aForms = array_merge($this->_video(), $aForms);
		}

		$this->_oTpl->assign(array(
				'sCreateJs' => $oValid->createJS(),
				'sGetJsForm' => $oValid->getJsForm(false),
				'aForms' => $aForms
			)
		);
	}

	private function _update()
	{
		$version = $this->_oReq->get('version');
		if (!$version) {
			foreach ($this->_aVersions as $sVersion) {
				if (isset($checkVersion)) {
					$version = $sVersion;
					break;
				}

				if ($sVersion == $this->_getCurrentVersion()) {
					$checkVersion = true;
				}
			}
		}
		$nextVersion = false;
		$upgradedVersion = null;
		foreach ($this->_aVersions as $sVersion) {
			if ($nextVersion === true) {
				return [
					'message' => 'Upgraded to ' . $upgradedVersion,
					'next' => 'update',
					'extra' => 'version=' . $sVersion
				];
			}

			if ($version == $sVersion) {
				if (file_exists(__DIR__ . PHPFOX_DS . 'version' . PHPFOX_DS . $sVersion . '.php')) {
					$callback = require(__DIR__ . PHPFOX_DS . 'version' . PHPFOX_DS . $sVersion . '.php');
					if ($callback instanceof Closure) {
						$this->db = Phpfox_Database::instance();

						$reset = false;
						$return = call_user_func($callback, $this);
						if (is_array($return) && isset($return)) {
							$reset = true;
						}

						$this->_upgradeDatabase($sVersion, $reset);
						$bCompleted = true;
					}
					$nextVersion = true;
					$upgradedVersion = $sVersion;
				}
			}
		}

		return [
			'next' => 'completed'
		];
	}

	private function _completed()
	{
	    //Add table phpfox_apps
        (new \Apps\PHPfox_Core\Installation\Database\Apps())->install();
		if (Phpfox_File::instance()->isWritable(PHPFOX_DIR_SETTINGS . 'server.sett.php'))
		{
			$sContent = file_get_contents(PHPFOX_DIR_SETTINGS . 'server.sett.php');
			$sContent = preg_replace("/\\\$_CONF\['core.is_installed'\] = (.*?);/i", "\\\$_CONF['core.is_installed'] = true;", $sContent);
			if ($hServerConf = @fopen(PHPFOX_DIR_SETTINGS . 'server.sett.php', 'w'))
			{
				fwrite($hServerConf, $sContent);
				fclose($hServerConf);
			}
		}

		$license = file_get_contents(PHPFOX_DIR_SETTINGS . 'license.php');
		file_put_contents(PHPFOX_DIR_SETTINGS . 'license.sett.php', $license);
		unlink(PHPFOX_DIR_SETTINGS . 'license.php');

		$old = realpath(PHPFOX_DIR. '..' . PHPFOX_DS . 'include' . PHPFOX_DS . 'setting' . PHPFOX_DS . 'server.sett.php');
		if (file_exists($old)) {
			unlink($old);
		}

		file_put_contents(PHPFOX_DIR_SETTINGS . 'version.sett.php', "<?php\nreturn " . var_export(['version' => Phpfox::getVersion(), 'timestamp' => PHPFOX_TIME], true) . ";\n");

		$this->_db()->update(Phpfox::getT('user_group_setting'), ['is_hidden' => '1'], ['name' => 'custom_table_name']);
		$columns =  Phpfox::getLib('database.support')->getColumns(Phpfox::getT('product'));
		if (!array_key_exists('icon', $columns))
            $this->_db()->addField(['table' => Phpfox::getT('product'), 'field' => 'icon', 'type' => 'VARCHAR:250', 'null' => true]);
		if (!array_key_exists('vendor', $columns))
            $this->_db()->addField(['table' => Phpfox::getT('product'), 'field' => 'vendor', 'type' => 'VARCHAR:250', 'null' => true]);

		if (!$this->_bUpgrade)
		{
            // Groups
            $this->_db()->addField(['table' => Phpfox::getT('pages'), 'field' => 'item_type', 'type' => 'TINT:1', 'null' => false, 'attribute' => 'UNSIGNED', 'default' => '0']);
            $this->_db()->addField(['table' => Phpfox::getT('pages_type'), 'field' => 'item_type', 'type' => 'TINT:1', 'null' => false, 'attribute' => 'UNSIGNED', 'default' => '0', 'after' => 'is_active']);
			$this->_db()->update(Phpfox::getT('setting'), ['value_actual' => date('j/n/Y', PHPFOX_TIME)], ['var_name' => 'official_launch_of_site']);

            $this->_db()->addField(['table' => Phpfox::getT('pages_feed'), 'field' => 'content', 'type' => 'TEXT', 'null' => true, 'default' => 'NULL', 'after' => 'time_update']);
            $this->_db()->addField(['table' => Phpfox::getT('pages_feed'), 'field' => 'total_view', 'type' => 'INT', 'attribute' => 'UNSIGNED', 'null' => false, 'default' => '0', 'after' => 'content']);
            $this->_db()->addField(['table' => Phpfox::getT('user_space'), 'field' => 'space_groups', 'type' => 'INT:10', 'attribute' => 'UNSIGNED', 'null' => false, 'default' => '0']);
            $this->_db()->addField(['table' => Phpfox::getT('user_field'), 'field' => 'total_groups', 'type' => 'INT:10', 'attribute' => 'UNSIGNED', 'null' => false, 'default' => '0']);
            $this->_db()->addField(['table' => Phpfox::getT('user_activity'), 'field' => 'activity_groups', 'type' => 'INT:10', 'attribute' => 'UNSIGNED', 'null' => false, 'default' => '0']);

            foreach (scandir(PHPFOX_DIR_MODULE) as $module) {
                if ($module == '.' || $module == '..') {
                    continue;
                }

                $on_success = PHPFOX_DIR_MODULE . $module . PHPFOX_DS . 'install' . PHPFOX_DS . 'on_success.php';
                if (file_exists($on_success)) {
                    $on_success = require($on_success);

                    if (!$this->db) {
                        $this->db = Phpfox_Database::instance();
                    }

                    call_user_func($on_success, $this);
                }
            }
		}
		else {
			$this->_db()->update(Phpfox::getT('setting'), array('value_actual' => Phpfox::getVersion()), 'var_name = \'phpfox_version\'');
		}

		$base = PHPFOX_DIR_SITE . 'Apps' . PHPFOX_DS;
		if (!defined('PHPFOX_NO_APPS')) {
			define('PHPFOX_NO_APPS', true);
		}
		foreach (scandir($base) as $app) {
			if ($app == '.' || $app == '..') {
				continue;
			}
            if ($appInit = \Core\Lib::appInit($app)) {
                if ($appInit->isCore()) {
                    $appInit->processInstall();
                }
            }
		}

		if (!is_dir(PHPFOX_DIR_SITE . 'flavors' . PHPFOX_DS)) {
			mkdir(PHPFOX_DIR_SITE . 'flavors' . PHPFOX_DS);
		}
		$dir = PHPFOX_DIR_SITE . 'flavors'. PHPFOX_DS . 'bootstrap' . PHPFOX_DS;
		if (!is_dir($dir)) {
			mkdir($dir, 0777);
		}
		if (!is_dir($dir . 'assets' .PHPFOX_DS)) mkdir($dir . 'assets' . PHPFOX_DS, 0777);
		if (!is_dir($dir . 'html' . PHPFOX_DS)) mkdir($dir . 'html' . PHPFOX_DS, 0777);
		if (!is_dir($dir . 'flavor' . PHPFOX_DS)) mkdir($dir . 'flavor' . PHPFOX_DS, 0777);

		$bootstrap = json_decode(file_get_contents(PHPFOX_DIR_SITE . 'Apps' . PHPFOX_DS . 'PHPfox_Flavors' . PHPFOX_DS . 'flavors' . PHPFOX_DS . 'bootstrap.json'));
		foreach ($bootstrap as $file => $content) {
			if (preg_match('/^(.*)\.(gif|jpg|jpeg|png)$/i', $file)) {
				$content = base64_decode($content);
			}
			$file = str_replace('/', PHPFOX_DS, $file);
			file_put_contents($dir . ltrim($file, PHPFOX_DS), $content);
		}

		// Install bootstrap template
		$Theme = new Core\Theme();
		$newTheme = $Theme->make([
			'name' => 'Bootstrap'
		], null, false, 'bootstrap');

		$this->_db()->update(Phpfox::getT('theme'), ['is_default' => 1], ['theme_id' => $newTheme->theme_id]);
		Phpfox::getLib('cache')->remove();

		$this->_oTpl->assign(array(
				'bIsUpgrade' => $this->_bUpgrade,
				'sUpgradeVersion' => Phpfox::getVersion()
			)
		);
	}

	########################
	# Private Methods
	########################

	private function _getCurrentVersion()
	{
		static $sVersion = null;

		if ($sVersion !== null)
		{
			return $sVersion;
		}

		$newFile = PHPFOX_DIR_SETTINGS . 'version.sett.php';
		if (file_exists($newFile)) {
			$object = (object) require($newFile);
			if (isset($object->version)) {
				$sVersion = $object->version;

				return $sVersion;
			}
		}

		$bIsLegacy = true;
		if (file_exists(PHPFOX_DIR . 'include' . PHPFOX_DS . 'setting' . PHPFOX_DS . 'server.sett.php'))
		{
			$_CONF = [];
			require(PHPFOX_DIR . 'include' . PHPFOX_DS . 'setting' . PHPFOX_DS . 'server.sett.php');

			if ($_CONF['core.is_installed'] === true)
			{
				$aRow = Phpfox_Database::instance()->select('value_actual')->from(Phpfox::getT('setting'))->where('var_name = \'phpfox_version\'')->execute('getRow');
				if (isset($aRow['value_actual']))
				{
					$sVersion = $aRow['value_actual'];

					return $aRow['value_actual'];
				}
			}
		}

		if (file_exists(PHPFOX_DIR . 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'version.php'))
		{
			$_CONF = [];
			require_once(PHPFOX_DIR . 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'version.php');

			$sVersion = $_CONF['info.version'];

			return $_CONF['info.version'];
		}
		else
		{
			$aRow = Phpfox_Database::instance()->select('value_actual')->from(Phpfox::getT('setting'))->where('var_name = \'phpfox_version\'')->execute('getRow');
			if (isset($aRow['value_actual']))
			{
				$sVersion = $aRow['value_actual'];

				return $aRow['value_actual'];
			}
		}

		return Phpfox_Error::set('Unknown version.', E_USER_ERROR);
	}

	/**
	 * @todo We need to work on this routine, not working very well.
	 */
	private function _isPassed($sStep)
	{
		return true;
	}

	private function _pass($sForward = null)
	{
		fwrite($this->_hFile, "\n" . $this->_sStep);

		if ($sForward !== null)
		{
			fclose($this->_hFile);

			$this->_oUrl->forward($this->_step($sForward));
		}

		fclose($this->_hFile);

		return true;
	}

	private function _getOldT($sTable)
	{
		return (isset($this->_aOldConfig['db']['prefix']) ? $this->_aOldConfig['db']['prefix'] : '') . $sTable;
	}

	private function _db()
	{
		return Phpfox_Database::instance();
	}

	private function _step($aParams)
	{
		if (is_array($aParams))
		{
			$aParams['sessionid'] = self::$_sSessionId;
		}
		else
		{
			$aParams = array($aParams, 'sessionid' => self::$_sSessionId);
		}

		return $this->_oUrl->makeUrl($this->_sUrl, $aParams);
	}

	private function _saveSettings($aVals)
	{
		// Get sub-folder
		$sSubfolder = str_replace(['index.php/', 'index.php'], '', $_SERVER['PHP_SELF']);

		// Get the settings content
		$sContent = file_get_contents(PHPFOX_DIR_SETTING . 'server.sett.php.new');

		// Trim and add slashes to each value since we are writing to a file
		foreach ($aVals as $iKey => $sVal)
		{
			$aVals[$iKey] = addslashes(trim($sVal));
		}

		$aFind = array(
			"/\\\$_CONF\['db'\]\['driver'\] = (.*?);/i",
			"/\\\$_CONF\['db'\]\['host'\] = (.*?);/i",
			"/\\\$_CONF\['db'\]\['user'\] = (.*?);/i",
			"/\\\$_CONF\['db'\]\['pass'\] = (.*?);/i",
			"/\\\$_CONF\['db'\]\['name'\] = (.*?);/i",
			"/\\\$_CONF\['db'\]\['prefix'\] = (.*?);/i",
			"/\\\$_CONF\['db'\]\['port'\] = (.*?);/i",
			"/\\\$_CONF\['core.host'\] = (.*?);/i",
			"/\\\$_CONF\['core.folder'\] = (.*?);/i",
			"/\\\$_CONF\['core.url_rewrite'\] = (.*?);/i",
			"/\\\$_CONF\['core.salt'\] = (.*?);/i",
			"/\\\$_CONF\['core.cache_suffix'\] = (.*?);/i"
		);

		$aReplace = array(
			"\\\$_CONF['db']['driver'] = '{$aVals['driver']}';",
			"\\\$_CONF['db']['host'] = '{$aVals['host']}';",
			"\\\$_CONF['db']['user'] = '{$aVals['user_name']}';",
			"\\\$_CONF['db']['pass'] = '{$aVals['password']}';",
			"\\\$_CONF['db']['name'] = '{$aVals['name']}';",
			"\\\$_CONF['db']['prefix'] = '" . (!empty($aVals['prefix']) ? $aVals['prefix'] : 'phpfox_') . "';",
			"\\\$_CONF['db']['port'] = '{$aVals['port']}';",
			"\\\$_CONF['core.host'] = '{$_SERVER['HTTP_HOST']}';",
			"\\\$_CONF['core.folder'] = '{$sSubfolder}';",
			"\\\$_CONF['core.url_rewrite'] = '" . ((isset($aVals['rewrite']) && $aVals['rewrite'] === true) ? '1' : '2') . "';",
			"\\\$_CONF['core.salt'] = '" . md5(uniqid(rand(), true)) . "';",
			"\\\$_CONF['core.cache_suffix'] = '.php';"
		);

		$sContent = preg_replace($aFind, $aReplace, $sContent);

		if ($hServerConf = @fopen(PHPFOX_DIR_SETTINGS . 'server.sett.php', 'w'))
		{
			fwrite($hServerConf, $sContent);
			fclose($hServerConf);

			return true;
		}

		return Phpfox_Error::set('Unable to open config file.');
	}

	private function _getSteps()
	{
		$aSteps = array();
		$iCnt = 0;
		foreach ($this->_aSteps as $sStep)
		{
			$sStepName = $sStep;
			switch ($sStep)
			{
				case 'key':
					$sStepName = 'Verification';
					break;
				case 'license':
					$sStepName = 'License Agreement';
					break;
				case 'requirement':
					$sStepName = 'Requirement Check';
					break;
				case 'update':
					$sStepName = 'Updates';
					break;
				case 'completed':
					$sStepName = 'Completed';
					break;
				case 'configuration':
					$sStepName = 'Configuration';
					break;
				case 'process':
					$sStepName = 'Preparing Installation';
					break;
				case 'import':
					$sStepName = 'Importing Data';
					break;
				case 'language':
					$sStepName = 'Installing Default Language';
					break;
				case 'module':
					$sStepName = 'Installing Modules';
					break;
				case 'post':
					$sStepName = 'Checking Install';
					break;
				case 'final':
					$sStepName = 'Create an Admin';
					break;
			}

			$iCnt++;
			$aSteps[] = array(
				'name' => $sStepName,
				'is_active' => ($this->_sStep == $sStep ? true : false),
				'count' => $iCnt
			);
		}

		return $aSteps;
	}

	private function _upgradeDatabase($sVersion, $reset = false)
	{
		if ((int) substr($this->_getCurrentVersion(), 0, 1) <= 1)
		{
			return;
		}

		if (!defined('PHPFOX_UPGRADE_MODULE_XML'))
		{
			define('PHPFOX_UPGRADE_MODULE_XML', true);
		}

		if ($reset) {
			define('PHPFOX_PRODUCT_UPGRADE_CHECK', true);
		}

		$hDir = opendir(PHPFOX_DIR_MODULE);
		while ($sModule = readdir($hDir))
		{
			if ($sModule == '.' || $sModule == '..')
			{
				continue;
			}

			if ($sModule == 'phpfox' || $sModule == 'emoticon')
			{
				continue;
			}

			if (file_exists(PHPFOX_DIR_MODULE . $sModule . PHPFOX_DS . 'install' . PHPFOX_DS . 'phpfox.xml.php'))
			{
				$aModule = Phpfox::getLib('xml.parser')->parse(PHPFOX_DIR_MODULE . $sModule . PHPFOX_DS . 'install' . PHPFOX_DS . 'phpfox.xml.php');

				if (isset($aModule['tables']))
				{
					$oPhpfoxDatabaseExport = Phpfox::getLib('database.support');
					$aTables = unserialize(trim($aModule['tables']));
					if (empty($aTables)) continue;
					$sQueries = Phpfox::getLib('database.export')->process(Phpfox::getParam(array('db', 'driver')), $aTables);
					$aDriver = $oPhpfoxDatabaseExport->getDriver(Phpfox::getParam(array('db', 'driver')));

					$sQueries = preg_replace('#phpfox_#i', Phpfox::getParam(array('db', 'prefix')), $sQueries);

					if ($aDriver['comments'] == 'remove_comments')
					{
						$oPhpfoxDatabaseExport->removeComments($sQueries);
					}
					else
					{
						$oPhpfoxDatabaseExport->removeRemarks($sQueries);
					}

					$aSql = $oPhpfoxDatabaseExport->splitSqlFile($sQueries, $aDriver['delim']);

					foreach ($aSql as $sSql)
					{
						$sSql = preg_replace('/CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS', $sSql);

						$this->_db()->query($sSql);
					}
				}
			}
		}

		$hDir = opendir(PHPFOX_DIR_MODULE);
		while ($sModule = readdir($hDir))
		{
			if ($sModule == '.' || $sModule == '..')
			{
				continue;
			}

			if ($sModule == 'phpfox' || $sModule == 'emoticon')
			{
				continue;
			}

			$bIsNewModule = false;
			if (file_exists(PHPFOX_DIR_MODULE . $sModule . PHPFOX_DS . 'install' . PHPFOX_DS . 'phpfox.xml.php'))
			{
				$aModule = Phpfox::getLib('xml.parser')->parse(PHPFOX_DIR_MODULE . $sModule . PHPFOX_DS . 'install' . PHPFOX_DS . 'phpfox.xml.php');
				if ($reset) {
					Admincp_Service_Module_Process::instance()->install($sModule, array('insert' => true), 'phpfox', $aModule);
					continue;
				}

				if (isset($aModule['data']['module_id']))
				{
					$iIsModule = $this->_db()->select('COUNT(*)')
						->from(Phpfox::getT('module'))
						->where('module_id = \'' . $this->_db()->escape($aModule['data']['module_id']) . '\'')
						->execute('getField');

					if (!$iIsModule)
					{
						$bIsNewModule = true;
						$this->_db()->insert(Phpfox::getT('module'), array(
								'module_id' => $aModule['data']['module_id'],
								'product_id' => 'phpfox',
								'is_core' => $aModule['data']['is_core'],
								'is_active' => 1,
                                'version' => Phpfox::VERSION,
                                'author' => 'phpFox',
                                'vendor' => 'https://store.phpfox.com/',
								'is_menu' => $aModule['data']['is_menu'],
								'menu' => $aModule['data']['menu'],
								'phrase_var_name' => $aModule['data']['phrase_var_name']
							)
						);
						Admincp_Service_Module_Process::instance()->install(null, array('insert' => true), 'phpfox', $aModule);
					}
				}

				if (!empty($aModule['data']['menu']))
				{
					$aModuleCheck = $this->_db()->select('module_id, menu')
						->from(Phpfox::getT('module'))
						->where('module_id = \'' . $this->_db()->escape($aModule['data']['module_id']) . '\'')
						->execute('getRow');

					if (isset($aModuleCheck['module_id']) && $aModuleCheck['menu'] != $aModule['data']['menu'])
					{
						$this->_db()->update(Phpfox::getT('module'), array('menu' => $aModule['data']['menu']), 'module_id = \'' . $this->_db()->escape($aModuleCheck['module_id']) . '\'');
					}
				}
			}

			if (file_exists(PHPFOX_DIR_MODULE . $sModule . PHPFOX_DS . 'install' . PHPFOX_DS . 'version' . PHPFOX_DS . $sVersion . '.xml.php'))
			{
				$aUpgradeModule = Phpfox::getLib('xml.parser')->parse(PHPFOX_DIR_MODULE . $sModule . PHPFOX_DS . 'install' . PHPFOX_DS . 'version' . PHPFOX_DS . $sVersion . '.xml.php');

				if (isset($aUpgradeModule['sql']))
				{
					$sSqlQuery = Phpfox::getLib('database.export')->processAlter(Phpfox::getParam(array('db', 'driver')), unserialize($aUpgradeModule['sql']), false, true);
					$aDriver = $oPhpfoxDatabaseExport->getDriver(Phpfox::getParam(array('db', 'driver')));

					$aSql = $oPhpfoxDatabaseExport->splitSqlFile($sSqlQuery, $aDriver['delim']);

					foreach ($aSql as $sSql)
					{
						$this->_db()->query($sSql);
					}
				}

				if ($bIsNewModule === false)
				{
                    Admincp_Service_Module_Process::instance()->install(null, array('insert' => true), 'phpfox', $aUpgradeModule);
				}
			}
		}
		closedir($hDir);
	}

	public function getVersionList()
    {
        return $this->_aVersions;
    }
}