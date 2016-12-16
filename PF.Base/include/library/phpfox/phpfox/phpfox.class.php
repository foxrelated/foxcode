<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * phpFox Engine
 * All interactions with anything phpFox related is executed via this class.
 * It is the engine that runs phpFox and all of the other libraries and modules.
 * All methods, variables and constants are static.
 *
 * All libraries are located within the folder: include/library/phpfox/
 * Example of connect to request library:
 * <code>
 * $oObject = Phpfox_Request::instance();
 * </code>
 *
 * @copyright         [PHPFOX_COPYRIGHT]
 * @author            Raymond Benc
 * @package           Phpfox
 * @version           $Id: phpfox.class.php 7299 2014-05-06 15:41:28Z Fern $
 */
class Phpfox
{
	/**
	 * Product Version : major.minor.maintenance [alphaX, betaX or rcX]
	 */
	const VERSION = '4.5.0';

	/**
	 * Product Code Name
	 *
	 */
	const CODE_NAME = 'Neutron';

	/**
	 * Browser agent used with API curl requests.
	 *
	 */
	const BROWSER_AGENT = 'phpFox';

	/**
	 * Product build number.
	 *
	 */
	const PRODUCT_BUILD = '2';

	/**
	 * phpFox API server.
	 *
	 */
	const PHPFOX_API = 'http://api.phpfox.com/deepspace/';

	/**
	 * phpFox package ID.
	 *
	 */
	const PHPFOX_PACKAGE = '[PHPFOX_PACKAGE_NAME]';

	/**
	 * ARRAY of objects initiated. Used to keep a static history
	 * so we don't call the same class more then once.
	 *
	 * @var array
	 */
	private static $_aObject = [];

	/**
	 * ARRAY of libraries being loaded.
	 *
	 * @var array
	 */
	private static $_aLibs = [];

	/**
	 * Used to keep a static variable to see if we are within the AdminCP.
	 *
	 * @var bool
	 */
	private static $_bIsAdminCp = false;

	/**
	 * History of any logs we save for debug purposes.
	 *
	 * @var array
	 */
	private static $_aLogs = [];

	/**
	 * Get the current phpFox version.
	 *
	 * @return string
	 */
	public static function getVersion()
	{
		if (defined('PHPFOX_INSTALLER')) {
			return self::VERSION;
		}

		return self::VERSION;
	}

	public static function isTrial()
	{
		return ((function_exists('ioncube_file_info') && is_array(ioncube_file_info())) ? true : false);
	}

	/**
	 * Get the current phpFox version ID.
	 *
	 * @return int
	 */
	public static function getId()
	{
		return self::getVersion();
	}

	/**
	 * Get the products code name.
	 *
	 * @return string
	 */
	public static function getCodeName()
	{
		return self::CODE_NAME;
	}

	/**
	 * Get the products build number.
	 *
	 * @return int
	 */
	public static function getBuild()
	{
		return self::PRODUCT_BUILD;
	}

	public static function getFullVersion() {
		return self::getCleanVersion() . '-' . self::getBuild();
	}

	/**
	 * Get the clean numerical value of the phpFox version.
	 *
	 * @return int
	 */
	public static function getCleanVersion()
	{
		return str_replace('.', '', self::VERSION);
	}

	public static function internalVersion()
	{
		$version = self::getCleanVersion();
		$version .= Phpfox::getParam('core.css_edit_id');
		if (defined('PHPFOX_NO_CSS_CACHE')) {
			return Phpfox::getTime();
		}

		return $version;
	}

	/**
	 * Check if a feature can be used based on the package the client
	 * has installed.
	 *
	 * Example (STRING):
	 * <code>
	 * if (Phpfox::isPackage('1') { }
	 * </code>
	 *
	 * Example (ARRAY):
	 * <code>
	 * if (Phpfox::isPackage(array('1', '2')) { }
	 * </code>
	 *
	 * @param mixed $mPackage STRING can be used to pass the package ID, or an ARRAY to pass multiple packages.
	 *
	 * @return bool
	 */
	public static function isPackage($mPackage)
	{
		$iPackageId = 3;
        
		if (!is_array($mPackage)) {
			$mPackage = [$mPackage];
		}

		if (!defined('PHPFOX_INSTALLER') && PHPFOX_LICENSE_ID != 'techie') {
			$iPackageId = PHPFOX_PACKAGE_ID;
		}

		return (in_array($iPackageId, $mPackage) ? true : false);
	}

	/**
	 * Provide "powered by" link.
	 *
	 * @param bool $bLink    TRUE to include a link to phpFox.
	 * @param bool $bVersion TRUE to include the version being used.
	 *
	 * @return string Powered by phpFox string returned.
	 */
	public static function link($bLink = true, $bVersion = true)
	{
		if (Phpfox::getParam('core.branding')) {
			return '';
		}

		return '' . ($bLink ? '<a href="http://www.phpfox.com/">' : '') . 'Powered By PHPFox' . ($bVersion ? ' Version ' . Phpfox::getVersion() : '') . ($bLink ? '</a>' : '');
	}

	/**
	 * Gets and creates an object for a class.
	 *
	 * @param string $sClass  Class name.
	 * @param array  $aParams Params to pass to the class.
	 *
	 * @return object Object created will be returned.
	 */
	public static function &getObject($sClass, $aParams = [])
	{
		$sHash = md5($sClass . serialize($aParams));

		if (isset(self::$_aObject[ $sHash ])) {
			return self::$_aObject[ $sHash ];
		}

		(PHPFOX_DEBUG ? Phpfox_Debug::start('object') : false);

		$sClass = str_replace(['.', '-'], '_', $sClass);

		if (!class_exists($sClass)) {
			Phpfox_Error::trigger('Unable to call class: ' . $sClass, E_USER_ERROR);
		}

		if ($aParams) {
			self::$_aObject[ $sHash ] = new $sClass($aParams);
		} else {
			self::$_aObject[ $sHash ] = new $sClass();
		}

		(PHPFOX_DEBUG ? Phpfox_Debug::end('object', ['name' => $sClass]) : false);

		if (method_exists(self::$_aObject[ $sHash ], 'getInstance')) {
			return self::$_aObject[ $sHash ]->getInstance();
		}

		return self::$_aObject[ $sHash ];
	}

	/**
	 * @see Phpfox_Setting::getParam()
	 *
	 * @param string $sVar
	 *
	 * @return mixed
	 */
    public static function getParam($sVar, $mVarDefault = null)
    {
        if ($sVar == 'language.lang_pack_helper' && defined('PHPFOX_LANGUAGE_HELPER')) {
            return true;
        }
        return Phpfox::getLib('setting')->getParam($sVar, $mVarDefault);
    }

    public static function demoModeActive() {
        if (defined('PHPFOX_DEMO_MODE')) {
            return true;
        }

        return false;
    }

    public static function demoMode($module = '', $method = '') {
        if (self::demoModeActive()) {
            $message = 'AdminCP is set to "Demo Mode". This action is not permitted when the site is in this mode.';
            $req = Phpfox_Request::instance();
            $val = Phpfox_Request::instance()->get('val');
            if (Phpfox_Request::instance()->method() == 'POST') {
                if ($module) {
                    $ajax = Phpfox_Ajax::instance();
                    $ajax->call('Admin_Demo_Message(\'' . $message . '\');');
                }
                else if (Phpfox_Request::instance()->get('is_ajax_post')
                    || !empty($_FILES['ajax_upload'])
                    || ($req->segment(2) == 'menu' && count($val) && !$req->get('id') && !$req->segment(3))
                ) {
                // else {
                    header('Content-Type: application/json');
                    echo json_encode(['run' => 'Admin_Demo_Message(\'' . $message . '\');']);
                    exit;
                }
                else if (Phpfox_Request::instance()->segment(2) == 'setting'
                    || (Phpfox_Request::instance()->segment(2) == 'page' && Phpfox_Request::instance()->segment(3) == 'add')
                ) {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => $message]);
                    exit;
                }
                
                return true;
            }
            else if (Phpfox_Request::instance()->method() == 'GET') {
                $route = trim(Phpfox_Url::instance()->getUri(), '/');
                $sections = [
                    'admincp/store'
                ];
                if (in_array($route, $sections)) {
                    return true;
                }
                else if (($req->segment(2) == 'menu' || $req->segment(2) == 'block') && $req->get('delete')) {
                    return true;
                }
            }
        }

        return false;
    }

	/**
	 * Fine and load a library class and make sure it exists.
	 *
	 * @param string $sClass Library class name.
	 *
	 * @return bool TRUE if library has loaded, FALSE if not.
	 */
	public static function getLibClass($sClass)
	{
		(class_exists('Phpfox_Plugin') && ($sPlugin = Phpfox_Plugin::get('library_phpfox_getlibclass_0')) ? eval($sPlugin) : false);
		if (isset(self::$_aLibs[ $sClass ])) {
			return true;
		}

		self::$_aLibs[ $sClass ] = md5($sClass);

		$sClass = str_replace('.', PHPFOX_DS, $sClass);
		$sFile = PHPFOX_DIR_LIB . $sClass . '.class.php';

		if (file_exists($sFile)) {
			require($sFile);

			return true;
		}

		$aParts = explode(PHPFOX_DS, $sClass);
		if (isset($aParts[1])) {
			$sSubClassFile = PHPFOX_DIR_LIB . $sClass . PHPFOX_DS . $aParts[1] . '.class.php';
			if (file_exists($sSubClassFile)) {
				require($sSubClassFile);

				return true;
			}
		}

		if (class_exists($sClass)) {
			return true;
		}

		(($sPlugin = Phpfox_Plugin::get('library_phpfox_getlibclass_1')) ? eval($sPlugin) : false);
		if (isset($mPluginReturn)) {
			return $mPluginReturn;
		}
		Phpfox_Error::trigger('Unable to load class: ' . $sClass, E_USER_ERROR);

		return false;
	}

	/**
	 * Get a phpFox library. This includes the class file and creates the object for you.
	 *
	 * Example usage:
	 * <code>
	 * Phpfox_Url::instance()->makeUrl('test');
	 * </code>
	 * In the example we called the URL library found in the folder: include/library/phpfox/url/url.class.php
	 * then created an object for it so we could directly call the method "makeUrl".
	 *
	 * @param string $sClass  Library class name.
	 * @param array  $aParams ARRAY of params you can pass to the library.
	 *
	 * @return object Object of the library class is returned.
	 */
	public static function &getLib($sClass, $aParams = [])
	{
		static $loaded =  false;
		static $sPlugin;

		if(!$loaded and class_exists('Phpfox_Plugin')){
			$loaded = true;
			$sPlugin = Phpfox_Plugin::get('library_phpfox_getlib_0');
		}

		if($loaded and $sPlugin){
			eval($sPlugin);
		}

		if ((substr($sClass, 0, 7) != 'phpfox.') || ($sClass == 'phpfox.api' || $sClass == 'phpfox.process')) {
			$sClass = 'phpfox.' . $sClass;
		}

		$sHash = md5($sClass . serialize($aParams));

		if (isset(self::$_aObject[ $sHash ])) {
			return self::$_aObject[ $sHash ];
		}

		Phpfox::getLibClass($sClass);

		$sClass = str_replace('phpfox.phpfox.', 'phpfox.', $sClass);

		self::$_aObject[ $sHash ] = Phpfox::getObject($sClass, $aParams);

		return self::$_aObject[ $sHash ];
	}

	/**
	 * @see Phpfox_Module::isModule()
	 *
	 * @param string $sModule
	 *
	 * @return bool
	 */
	public static function isModule($sModule)
	{
		return Phpfox_Module::instance()->isModule($sModule);
	}

    /**
     * Check is module or apps
     * @param string $sName
     * @param bool $bNoCheckModule
     *
     * @return bool
     */
    public static function isApps($sName, $bNoCheckModule = true)
    {
        return Phpfox_Module::instance()->isApps($sName, false, $bNoCheckModule);
    }

    /**
     * Check a name is a alias of app or not
     *
     * @param string $sName
     * @param bool $bReturnId
     *
     * @return bool|string
     */
    public static function isAppAlias($sName = '', $bReturnId = false)
    {
        if (empty($sName)) {
            return false;
        }
        $sAppId = db()->select('apps_id')
            ->from(':apps')
            ->where('apps_alias=\'' . db()->escape($sName) .'\'')
            ->executeField();
        if ($bReturnId) {
            return (!empty($sAppId)) ? $sAppId : false;
        } else {
            return (!empty($sAppId)) ? true : false;
        }
    }

	/**
	 * @see Phpfox_Module::getComponent()
	 *
	 * @param string $sClass
	 * @param array  $aParams
     * @param bool $bTemplateParams
	 *
	 * @return object
	 */
	public static function getBlock($sClass, $aParams = [], $bTemplateParams = false)
	{
		if ($sClass instanceof \Closure) {
			$content = call_user_func($sClass);

			echo $content;

			return null;
		}
		else if (is_array($sClass)) {
			if (isset($sClass['callback'])) {
				$content = call_user_func($sClass['callback'], $sClass['object']);
			} else {
				$content = $sClass[0];
			}

			if (empty($content)) {
				$obj = $sClass['object'];
				if ($obj instanceof \Core\Block) {
					if (empty($html)) {
						$content = '
						<div class="block">
							' . ($obj->get('title') ? '<div class="title">' . $obj->get('title') . '</div>' : '') . '
							<div class="content">
								' . $obj->get('content') . '
							</div>
						</div>
						';
					}
				}
			}
            if (file_exists($content)){
                require_once $content;
            } else {
                echo $content;
            }

			return null;
		}

		return Phpfox_Module::instance()->getComponent($sClass, $aParams, 'block', $bTemplateParams);
	}

	/**
	 * @see Phpfox_Module::callback()
	 *
	 * @param string $sCall
	 *
	 * @return mixed
	 */
	public static function callback($sCall)
	{
		if (func_num_args() > 1) {
			$aParams = func_get_args();
			return Phpfox_Module::instance()->callback($sCall, $aParams);
		}

		return Phpfox_Module::instance()->callback($sCall);
	}

	/**
	 * @see Phpfox_Module::massCallback()
	 *
	 * @param string $sMethod
	 *
	 * @return mixed
	 */
	public static function massCallback($sMethod)
	{
		if (func_num_args() > 1) {
			$aParams = func_get_args();

			return Phpfox_Module::instance()->massCallback($sMethod, $aParams);
		}

		return Phpfox_Module::instance()->massCallback($sMethod);
	}

	/**
	 * @see Phpfox_Module::hasCallback()
	 *
	 * @param string $sModule
	 * @param string $sMethod
	 *
	 * @return bool
	 */
	public static function hasCallback($sModule, $sMethod)
	{
		return Phpfox_Module::instance()->hasCallback($sModule, $sMethod);
	}

	/**
	 * @see Phpfox_Module::getComponent()
	 *
	 * @param string $sClass  Class name.
	 * @param array  $aParams ARRAY of params you can pass to the component.
	 * @param string $sType   Type of component (block or controller).
	 *
	 * @return object We return the object of the component class.
	 */
	public static function getComponent($sClass, $aParams = [], $sType = 'block', $bTemplateParams = false)
	{
		return Phpfox_Module::instance()->getComponent($sClass, $aParams, $sType, $bTemplateParams);
	}

	/**
	 * @see Phpfox_Module::getComponentSetting()
	 *
	 * @param int    $iUserId
	 * @param string $sVarName
	 * @param mixed  $mDefaultValue
	 *
	 * @return mixed
	 */
	public static function getComponentSetting($iUserId, $sVarName, $mDefaultValue)
	{
		return Phpfox_Module::instance()->getComponentSetting($iUserId, $sVarName, $mDefaultValue);
	}

	/**
	 * Returns the token name for forms
	 */
	public static function getTokenName()
	{
		return 'core';
	}

	/**
	 * @see Phpfox_Module::getService()
	 *
	 * @param string $sClass
	 * @param array  $aParams
	 *
	 * @return object
	 */
	public static function getService($sClass, $aParams = [])
	{
		return Phpfox_Module::instance()->getService($sClass, $aParams);
	}

	/**
	 * Builds a database table prefix.
	 *
	 * @param string $sTable Database table name.
	 *
	 * @return string Returns the table name with the clients prefix.
	 */
	public static function getT($sTable)
	{
		return Phpfox::getParam(['db', 'prefix']) . $sTable;
	}

	/**
	 * @see User_Service_Auth::getUserId()
	 * @return int
	 */
	public static function getUserId()
	{

		if (isset($_REQUEST['custom_pages_post_as_page']) && (int)$_REQUEST['custom_pages_post_as_page'] > 0
		) {
			$aPage = Phpfox_Database::instance()->getRow('
				SELECT p.page_id, p.user_id AS owner_user_id, u.user_id
				FROM ' . Phpfox::getT('pages') . ' AS p
				JOIN ' . Phpfox::getT('user') . ' AS u ON(u.profile_page_id = p.page_id)
				WHERE p.item_type = 0 AND p.page_id = ' . (int)$_REQUEST['custom_pages_post_as_page'] . '
			');

			$iActualUserId = User_Service_Auth::instance()->getUserId();

			if (!defined('PHPFOX_POSTING_AS_PAGE')) {
				define('PHPFOX_POSTING_AS_PAGE', true);
			}

			if (isset($aPage['page_id'])) {
				$bPass = false;

                //check isAdmin
                if (Phpfox::getService('user')->isAdminUser($iActualUserId, true))
                {
                    $bPass = true;
                }

				if (!$bPass && $aPage['owner_user_id'] == $iActualUserId) {
					$bPass = true;
				}

				if (!$bPass) {
					$aAdmin = Phpfox_Database::instance()->getRow('
						SELECT page_id
						FROM ' . Phpfox::getT('pages_admin') . '
						WHERE page_id = ' . (int)$aPage['page_id'] . ' AND user_id = ' . (int)$iActualUserId . '
					');

					if (isset($aAdmin['page_id'])) {
						$bPass = true;
					}
				}

				if ($bPass) {
					return $aPage['user_id'];
				}
			}
		}

		if ($sPlugin = Phpfox_Plugin::get('library_phpfox_phpfox_getuserid__1')) {
			eval($sPlugin);
		}

		if (defined('PHPFOX_APP_USER_ID')) {
			return PHPFOX_APP_USER_ID;
		}

		return User_Service_Auth::instance()->getUserId();
	}

	/**
	 * @see User_Service_Auth::getUserBy()
	 * @return string
	 */
	public static function getUserBy($sVar = null)
	{
		return User_Service_Auth::instance()->getUserBy($sVar);
	}

	/**
	 * @see Phpfox_Request::isMobile()
	 * @return bool
     * @deprecated 4.0.0
	 */
	public static function isMobile($bRedirect = true)
	{
		return false;
	}

	/**
	 * @see Phpfox_Request::getIp()
	 * @return string
	 */
	public static function getIp($bReturnNum = false)
	{
		return Phpfox_Request::instance()->getIp($bReturnNum);
	}

	/**
	 * Checks to see if the user that is logged in has been marked as a spammer.
	 *
	 * @return bool TRUE is a spammer, FALSE if not a spammer.
	 */
	public static function isSpammer()
	{
		if (Phpfox::getUserParam('core.is_spam_free')) {
			return false;
		}

		if (!Phpfox::getParam('core.enable_spam_check')) {
			return false;
		}

		if (Phpfox::isUser() && Phpfox::getUserBy('total_spam') > Phpfox::getParam('core.auto_deny_items')) {
			return true;
		}

		return false;
	}
    
    /**
     * Get all the user fields when joining with the user database table.
     *
     * @param string $sAlias  Table alias. User table alias by default is "u".
     * @param string $sPrefix Prefix for each of the fields.
     *
     * @return string Returns SQL SELECT for user fields.
     */
	public static function getUserField($sAlias = 'u', $sPrefix = '')
	{
		static $aValues = [];

		// Create hash
		$sHash = md5($sAlias . $sPrefix);

		// Have we already cached it? We do not want to run an extra foreach() for nothing.
		if (isset($aValues[ $sHash ])) {
			return $aValues[ $sHash ];
		}

		$aFields = User_Service_User::instance()->getUserFields();

		$aValues[ $sHash ] = '';
		foreach ($aFields as $sField) {
			$aValues[ $sHash ] .= ", {$sAlias}.{$sField}";

			if ($sAlias == 'u' && $sField == 'server_id') {
				$aValues[ $sHash ] .= " AS user_{$sPrefix}{$sField}";
				continue;
			}

			if (!empty($sPrefix)) {
				$aValues[ $sHash ] .= " AS {$sPrefix}{$sField}";
			}
		}
		$aValues[ $sHash ] = ltrim($aValues[ $sHash ], ',');

		return $aValues[ $sHash ];
	}

	/**
	 * @see Phpfox_Date::getTimeZone()
	 *
	 * @param bool $bDst
	 *
	 * @return string
	 */
	public static function getTimeZone($bDst = true)
	{
		return Phpfox::getLib('date')->getTimeZone($bDst);
	}

	/**
	 * Gets a time stamp, Works similar to PHP date() function.
	 * We also take into account locale and time zone settings.
	 *
	 * @see date()
	 *
	 * @param string $sStamp Time stamp format.
	 * @param int    $iTime  UNIX epoch time stamp.
	 *
	 * @return string Time stamp value based on locale.
	 */
	public static function getTime($sStamp = null, $iTime = PHPFOX_TIME, $bTimeZone = true)
	{
		static $sUserOffSet;

		if ($bTimeZone) {
			if (!$sUserOffSet) {
				$sUserOffSet = Phpfox::getTimeZone();
			}
			if (!preg_match('/z[0-9]+/i', $sUserOffSet, $aMatch)) {
				// try to find it in the cache
				$aTZ = Core_Service_Core::instance()->getTimeZones();
				$sTz = array_search($sUserOffSet, $aTZ);
				if ($sTz !== false) {
					$sUserOffSet = $sTz;
				}
			}
			if (substr($sUserOffSet, 0, 1) == 'z' && PHPFOX_USE_DATE_TIME) {
				// we are using DateTime
				// get the offset to use based on the time zone index code
				if (!isset($aTZ)) {
					$aTZ = Core_Service_Core::instance()->getTimeZones();
				}
				if (isset($aTZ[ $sUserOffSet ])) {
					$oTZ = new DateTimeZone($aTZ[ $sUserOffSet ]);
					$oDateTime = new DateTime(null, $oTZ);
					$oDateTime->setTimestamp($iTime);
					$sUserOffSet = $aTZ[ $sUserOffSet ];
					if ($sStamp !== null) {
						$iNewTime = $oDateTime->format($sStamp);
						$bSet = true;
					}
				}
			}

			if ($sStamp === null) {
				return (!empty($sUserOffSet) ? (substr($sUserOffSet, 0, 1) == '-' ? ($iTime - (substr($sUserOffSet, 1) * 3600)) : ($sUserOffSet * 3600) + $iTime) : $iTime);
			} elseif (!isset($bSet)) {
				$iNewTime = (!empty($sUserOffSet) ? date($sStamp, (substr($sUserOffSet, 0, 1) == '-' ? ($iTime - (substr($sUserOffSet, 1) * 3600)) : ($sUserOffSet * 3600) + $iTime)) : date($sStamp, $iTime));

			}
		} else {
			$iNewTime = date($sStamp, $iTime);
		}

		$aFind = [
				'Monday',
				'Tuesday',
				'Wednesday',
				'Thursday',
				'Friday',
				'Saturday',
				'Sunday',
				'January',
				'February',
				'March',
				'April',
				'May',
				'June',
				'July',
				'August',
				'September',
				'October',
				'November',
				'December'
		];

		$aReplace = [
				_p('monday'),
				_p('tuesday'),
				_p('wednesday'),
				_p('thursday'),
				_p('friday'),
				_p('saturday'),
				_p('sunday'),
				_p('january'),
				_p('february'),
				_p('march'),
				_p('april'),
				_p('may'),
				_p('june'),
				_p('july'),
				_p('august'),
				_p('september'),
				_p('october'),
				_p('november'),
				_p('december')
		];

		$iNewTime = str_replace('Mon', 'Monday', $iNewTime);
		$iNewTime = str_replace('Tue', 'Tuesday', $iNewTime);
		$iNewTime = str_replace('Wed', 'Wednesday', $iNewTime);
		$iNewTime = str_replace('Thu', 'Thursday', $iNewTime);
		$iNewTime = str_replace('Fri', 'Friday', $iNewTime);
		$iNewTime = str_replace('Sat', 'Saturday', $iNewTime);
		$iNewTime = str_replace('Sun', 'Sunday', $iNewTime);
		$iNewTime = str_replace('Jan', 'January', $iNewTime);
		$iNewTime = str_replace('Feb', 'February', $iNewTime);
		$iNewTime = str_replace('Mar', 'March', $iNewTime);
		$iNewTime = str_replace('Apr', 'April', $iNewTime);
		$iNewTime = str_replace('May', 'May', $iNewTime);
		$iNewTime = str_replace('Jun', 'June', $iNewTime);
		$iNewTime = str_replace('Jul', 'July', $iNewTime);
		$iNewTime = str_replace('Aug', 'August', $iNewTime);
		$iNewTime = str_replace('Sep', 'September', $iNewTime);
		$iNewTime = str_replace('Oct', 'October', $iNewTime);
		$iNewTime = str_replace('Nov', 'November', $iNewTime);
		$iNewTime = str_replace('Dec', 'December', $iNewTime);

		$iNewTime = str_replace('Mondayday', 'Monday', $iNewTime);
		$iNewTime = str_replace('Tuesdaysday', 'Tuesday', $iNewTime);
		$iNewTime = str_replace('Wednesdaynesday', 'Wednesday', $iNewTime);
		$iNewTime = str_replace('Thursdayrsday', 'Thursday', $iNewTime);
		$iNewTime = str_replace('Fridayday', 'Friday', $iNewTime);
		$iNewTime = str_replace('Saturdayurday', 'Saturday', $iNewTime);
		$iNewTime = str_replace('Sundayday', 'Sunday', $iNewTime);
		$iNewTime = str_replace('Januaryuary', 'January', $iNewTime);
		$iNewTime = str_replace('Februaryruary', 'February', $iNewTime);
		$iNewTime = str_replace('Marchch', 'March', $iNewTime);
		$iNewTime = str_replace('Aprilil', 'April', $iNewTime);
		$iNewTime = str_replace('Junee', 'June', $iNewTime);
		$iNewTime = str_replace('Julyy', 'July', $iNewTime);
		$iNewTime = str_replace('Augustust', 'August', $iNewTime);
		$iNewTime = str_replace('Septembertember', 'September', $iNewTime);
		$iNewTime = str_replace('Octoberober', 'October', $iNewTime);
		$iNewTime = str_replace('Novemberember', 'November', $iNewTime);
		$iNewTime = str_replace('Decemberember', 'December', $iNewTime);

		$iNewTime = str_replace($aFind, $aReplace, $iNewTime);
		$iNewTime = str_replace(['PM', 'pm'], _p('pm'), $iNewTime);
		$iNewTime = str_replace(['AM', 'am'], _p('am'), $iNewTime);

		return $iNewTime;
	}

	/**
	 * Used to see if a user is logged in or not. By passing the first argument as TRUE
	 * we can also do an auto redirect to guide the user to login first before using a
	 * feature.
	 *
	 * @param bool $bRedirect User will be redirected to the login page if they are not logged int.
	 *
	 * @return bool If the 1st argument is FALSE, it will return a BOOL TRUE if the user is logged in, otherwise FALSE.
	 */
	public static function isUser($bRedirect = false)
	{
		if (defined('PHPFOX_APP_USER_ID')) {
			return true;
		}
		$bIsUser = User_Service_Auth::instance()->isUser();

		if ($bRedirect && !$bIsUser) {
			if (PHPFOX_IS_AJAX || PHPFOX_IS_AJAX_PAGE) {
				return Phpfox_Ajax::instance()->isUser();
			} else {
				// Create a session so we know where we plan to redirect the user after they login
				$url = Phpfox_Url::instance()->getFullUrl();
				Phpfox::getLib('session')->set('redirect', $url);
				Phpfox_Url::instance()->send('user.login');
			}
		}

		return $bIsUser;
	}

	/**
	 * Used to see if a user is an Admin. By passing the first argument as TRUE
	 * we can also do an auto redirect to guide the user to login first before using a
	 * feature in the AdminCP.
	 *
	 * @param bool $bRedirect User will be redirected to the AdminCP login page if they are not logged int.
	 *
	 * @return bool If the 1st argument is FALSE, it will return a BOOL TRUE if the user is logged in, otherwise FALSE.
	 */
	public static function isAdmin($bRedirect = false)
	{
		if (!Phpfox::isUser($bRedirect)) {
			return false;
		}

		if (!Phpfox::getUserParam('admincp.has_admin_access', $bRedirect)) {
			return false;
		}

		return true;
	}

	/**
	 * Creates a URL for an item that is connected with a users profile.
	 *
	 * @deprecated 2.0.0beta1
	 *
	 * @param string $sUrl      URL
	 * @param array  $mParams   URL params.
	 * @param string $sUserName Users vanity user name.
	 *
	 * @return string Items URL now connected with a persons profile.
	 */
	public static function itemUrl($sUrl, $mParams, $sUserName)
	{
		$bUserProfileUrl = true;

		if ($bUserProfileUrl) {
			return self::getLib('phpfox.url')->makeUrl($sUserName, array_merge([$sUrl], (is_array($mParams) ? $mParams : [$mParams])));
		}

		return self::getLib('phpfox.url')->makeUrl($sUrl, $mParams);
	}

	/**
	 * @see User_Service_Group_Setting_Setting::getGroupParam()
	 *
	 * @param int    $iGroupId
	 * @param string $sName
	 *
	 * @return mixed
	 */
	public static function getUserGroupParam($iGroupId, $sName)
	{
		return User_Service_Group_Setting_Setting::instance()->getGroupParam($iGroupId, $sName);
	}

	/**
	 * Get a user group setting.
	 *
	 * @see User_Service_Group_Setting_Setting::getParam()
	 *
	 * @param string $sName     User group param name.
	 * @param bool   $bRedirect TRUE will redirect the user to a subscribtion page if they do not have access to the param.
	 * @param mixed  $sJsCall   NULL will do nothing, however a STRING JavaScript code will run the code instead of a redirection.
	 *
	 * @return bool
	 */
	public static function getUserParam($sName, $bRedirect = false, $sJsCall = null)
	{
		if (defined('PHPFOX_INSTALLER')) {
			return true;
		}
        $bPass = false;
		// Is this an array
		if (is_array($sName)) {
			// Get the array key
			$sKey = array_keys($sName);

			// Get the setting value
			$sValue = User_Service_Group_Setting_Setting::instance()->getParam($sKey[0]);

			// Do the evil eval to get our new value
			eval('$bPass = (' . $sValue . ' ' . $sName[ $sKey[0] ][0] . ' ' . $sName[ $sKey[0] ][1] . ');');
		} else {
			$bPass = (User_Service_Group_Setting_Setting::instance()->getParam($sName) ? true : false);
			if ($sName == 'admincp.has_admin_access' && Phpfox::getParam('core.protect_admincp_with_ips') != '') {
				$bPass = false;
				$aIps = explode(',', Phpfox::getParam('core.protect_admincp_with_ips'));
				foreach ($aIps as $sIp) {
					$sIp = trim($sIp);
					if (empty($sIp)) {
						continue;
					}

					if ($sIp == Phpfox_Request::instance()->getServer('REMOTE_ADDR'))
					{
						$bPass = true;
						break;
					}
				}
			}
		}

		if (!$bPass && $bRedirect ) {
			self::redirectByPermissionDenied();
			return true;
		} else {
			if (is_array($sName)) {
				return $bPass;
			} else {
				return User_Service_Group_Setting_Setting::instance()->getParam($sName);
			}
		}
	}

	public static function redirectByPermissionDenied()
	{
		$sJsCall =  null;
		if (PHPFOX_IS_AJAX) {
			if (!Phpfox::isUser()) {
				// Are we using thickbox?
				if (Phpfox_Request::instance()->get('tb')) {
					Phpfox::getBlock('user.login-ajax');
				} else {
					// If we passed an AJAX call we execute it
					if ($sJsCall !== null) {
						echo $sJsCall;
					}
					echo "tb_show('" . _p('login_title') . "', \$.ajaxBox('user.login', 'height=250&width=400'));";
				}
			} else {
				// Are we using thickbox?
				if (Phpfox_Request::instance()->get('tb')) {
					Phpfox::getBlock('subscribe.message');
				} else {
					// If we passed an AJAX call we execute it
					if ($sJsCall !== null) {
						// echo $sJsCall;
					}
					echo "/*<script type='text/javascript'>*/window.location.href = '" . Phpfox_Url::instance()->makeUrl('subscribe.message') . "';/*</script>*/";
				}
			}
			exit;
		} else {
			if (!Phpfox::isUser()) {
				// Create a session so we know where we plan to redirect the user after they login
				Phpfox::getLib('session')->set('redirect', Phpfox_Url::instance()->getFullUrl(true));

				// Okay thats it lets send them away so they can login
				Phpfox_Url::instance()->send('user.login');
			} else {
				Phpfox_Url::instance()->send('subscribe');
			}
		}
	}

	/**
	 * Check to see if we are in the AdminCP or not.
	 *
	 * @return bool if we are, FALSE if we are not.
	 */
	public static function isAdminPanel()
	{
		return (self::$_bIsAdminCp ? true : false);
	}

	/**
	 * Set to AdminCP.
	 *
	 * @return TRUE if we can, FALSE if we can not.
	 */
	public static function setAdminPanel()
	{
		if (Phpfox::isAdmin()) {
			self::$_bIsAdminCp = true;
			return true;
		}
		return false;
	}
	/**
	 * Returns an array with the css and js files to be loaded in every controller
	 */
	public static function getMasterFiles()
	{
		$aOut = [
				'font-awesome/css/font-awesome.min.css' => 'style_css',
				'jquery/jquery.js'                         => 'static_script',
				'jquery/ui.js'                             => 'static_script',
				'jquery/plugin/jquery.nanoscroller.min.js' => 'static_script',
				'common.js'                                => 'static_script',
				'main.js'                                  => 'static_script',
				'ajax.js'                                  => 'static_script',
				'thickbox/thickbox.js'                     => 'static_script',
				'search.js'                                => 'module_friend',
                'progress.js'                             => 'static_script',
                'nprogress.js'                             => 'static_script',
                'quick_edit.js'                            => 'static_script',
				'feed.js'                                  => 'module_feed'
		];

		if (Phpfox::isAdminPanel()) {
			$aOut = array_merge([
				'bootstrap.min.css' => 'style_css',
				'layout.css'   => 'style_css',
				'common.css'   => 'style_css',
				'thickbox.css' => 'style_css',
				'jquery.css'   => 'style_css',
				'comment.css'  => 'style_css',
				'pager.css'    => 'style_css'], $aOut);
		}

		(($sPlugin = Phpfox_Plugin::get('get_master_files')) ? eval($sPlugin) : false);

		return $aOut;
	}

    /**
     * @return array
     */
	public static function getMasterPhrase()
	{
		$aOut = [
            'search_for_your_friends_dot',
            'save',
            'changes_you_made_may_not_be_saved',
            'search_friends_dot_dot_dot'
        ];
        if (Phpfox::isModule('comment')){
            $aOut[] = 'view_previous_comments';
        }
        if (Phpfox::isModule('feed')){
            $aOut[] = 'show_more';
        }

		(($sPlugin = Phpfox_Plugin::get('get_master_phrases')) ? eval($sPlugin) : false);

		return $aOut;
	}

    public static function getPagesType($iId)
    {
        $iTypeId = db()->select('item_type')
            ->from(':pages')
            ->where('page_id=' . (int) $iId)
            ->executeField();
        return ($iTypeId) ? 'groups' : 'pages';
    }

	/**
	 * Starts the phpFox engine. Used to get and display the pages controller.
	 *
	 */
	public static function run()
	{
		if (isset($_REQUEST['m9callback'])) {
			header('Content-type: application/json');
			try {
				$Home = new Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY);
				$callback = $_REQUEST['m9callback'];
				unset($_GET['m9callback'], $_GET['do']);
				if (!$_GET) {
					$_GET = [];
				}
				echo json_encode(call_user_func([$Home, $callback], $_GET));
			} catch (\Exception $e) {
				echo json_encode(['error' => $e->getMessage()]);
			}
			exit;
		}

		$oTpl = Phpfox_Template::instance();
		$aLocale = Phpfox_Locale::instance()->getLang();
		$oReq = Phpfox_Request::instance();
		$oModule = Phpfox_Module::instance();
		if ($oReq->segment(1) == 'favicon.ico') {
			header('Content-type: image/x-icon');
			if (file_exists(PHPFOX_DIR . '../favicon.ico')) {
				echo file_get_contents(PHPFOX_DIR . '../favicon.ico');
			} else {
                $opts = array();
                $http_headers  = array();
                $http_headers[] = 'Expect:';
                $opts[CURLOPT_URL] = 'https://www.phpfox.com/favicon.ico';
                $opts[CURLOPT_HTTPHEADER] = $http_headers;
                $opts[CURLOPT_CONNECTTIMEOUT]  = 10;
                $opts[CURLOPT_TIMEOUT] = 60;
                $opts[CURLOPT_HEADER] = FALSE;
                $opts[CURLOPT_BINARYTRANSFER] = TRUE;
                $opts[CURLOPT_VERBOSE] = FALSE;
                $opts[CURLOPT_SSL_VERIFYPEER] = FALSE;
                $opts[CURLOPT_SSL_VERIFYHOST] = 2;
                $opts[CURLOPT_RETURNTRANSFER] = TRUE;
                $opts[CURLOPT_FOLLOWLOCATION] = TRUE;
                $opts[CURLOPT_MAXREDIRS] = 2;
                $opts[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
                $ch = curl_init();
                curl_setopt_array($ch, $opts);
                $content = curl_exec($ch);
                curl_close($ch);
                echo $content;
			}
			exit;
		} elseif (Phpfox::getParam('core.url_rewrite') == 1 && $oReq->segment(1) == 'index.php'){
            $sUrl = Phpfox_Url::instance()->getFullUrl();
            $sUrl = str_replace('/index.php','', $sUrl);
            $sUrl = str_replace('/index/php','', $sUrl);
            Phpfox_Url::instance()->send($sUrl);
        }

		$aStaticFolders = ['_ajax', 'file', 'static', 'module', 'apps', 'Apps', 'themes'];
		if (in_array($oReq->segment(1), $aStaticFolders) ||
				(
						$oReq->segment(1) == 'theme' && $oReq->segment(2) != 'demo'
						&& $oReq->segment(1) == 'theme' && $oReq->segment(2) != 'sample'
				)
		) {
			$sUri = Phpfox_Url::instance()->getUri();
			if ($sUri == '/_ajax/') {
				$oAjax = Phpfox_Ajax::instance();
				$oAjax->process();
				echo $oAjax->getData();
				exit;
			}

			$sDir = PHPFOX_DIR;
			if ($oReq->segment(1) == 'Apps' || $oReq->segment(1) == 'apps' || $oReq->segment(1) == 'themes') {
				$sDir = PHPFOX_DIR_SITE;
			}
			$sPath = $sDir . ltrim($sUri, '/');

			if ($oReq->segment(1) == 'themes' && $oReq->segment(2) == 'default') {
				$sPath = PHPFOX_DIR . str_replace('themes/default', 'theme/default', $sUri);
			}

			$sType = Phpfox_File::instance()->mime($sUri);
			$sExt = Phpfox_File::instance()->extension($sUri);

			if (!file_exists($sPath)) {
				$sPath = str_replace('PF.Base', 'PF.Base/..', $sPath);
				if (!file_exists($sPath)) {
					header("HTTP/1.0 404 Not Found");
					header('Content-type: application/json');
					echo json_encode([
							'error' => 404
					]);
					exit;
				}
			}

			if ($oReq->segment(1) == 'themes') {
				$Theme = $oTpl->theme()->get();
				$Service = new Core\Theme\Service($Theme);
				if ($sType == 'text/css') {
					if (file_exists($sPath)) {
						echo @file_get_contents($sPath);
					} else {
						echo $Service->css()->getParsed();
					}
				} else {
					echo $Service->js()->get();
				}
			} else {
				echo @file_get_contents($sPath);
			}
			exit;
		}


		(($sPlugin = Phpfox_Plugin::get('run_start')) ? eval($sPlugin) : false);

		if (strtolower(Phpfox_Request::instance()->get('req1')) == Phpfox::getParam('admincp.admin_cp')) {
			self::$_bIsAdminCp = true;
			if (!defined('PHPFOX_ADMIN_PANEL'))
			{
				define('PHPFOX_ADMIN_PANEL', true);
			}
		}

		// Load module blocks
		$oModule->loadBlocks();

		if (!Phpfox::getParam('core.branding')) {
			$oTpl->setHeader(['<meta name="author" content="phpFox" />']);
		}


		$View = $oModule->setController();

		if ($View instanceof Core\View) {

		} else {
			if (!self::$_bIsAdminCp) {
				$View = new Core\View();
			}
		}

		(($sPlugin = Phpfox_Plugin::get('run_set_controller')) ? eval($sPlugin) : false);

		if (!PHPFOX_IS_AJAX_PAGE) {
			$oTpl->setImage([
							'ajax_small'        => 'ajax/small.gif',
							'ajax_large'        => 'ajax/large.gif',
							'loading_animation' => 'misc/loading_animation.gif',
							'close'             => 'misc/close.gif',
							'move'              => 'misc/move.png',
							'calendar'          => 'jquery/calendar.gif'
            ]);

			$favicon = Phpfox::getParam('core.path') . 'favicon.ico?v=' . $oTpl->getStaticVersion();
			if ($sPlugin = Phpfox_Plugin::get('favicon')) {
				eval($sPlugin);
			}

			$oTpl->setHeader([
							'<meta http-equiv="X-UA-Compatible" content="IE=edge">',
							'<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">',
							'<meta http-equiv="Content-Type" content="text/html;charset=' . $aLocale['charset'] . '" />',
							'<link rel="shortcut icon" type="image/x-icon" href="' . $favicon . '" />'
					]
			)
					->setMeta('keywords', Phpfox_Locale::instance()->convert(Phpfox::getParam('core.keywords')))
					->setMeta('robots', 'index,follow');

            $oTpl->setPhrase(Phpfox::getMasterPhrase());
			
			if (Phpfox::isModule('friend')) {
				$oTpl->setPhrase(['show_more_results_for_search_term']);
			}

			if (Phpfox::isModule('mail')) {
				$oTpl->setPhrase(['can_not_send_empty_message']);
			}

			if (PHPFOX_DEBUG && self::isAdminPanel()) {
				$oTpl->setHeader('cache', ['debug.css' => 'style_css']);
			}

			if (Phpfox::isUser() && Phpfox::getParam('user.enable_user_tooltip')) {
				$oTpl->setHeader('cache', [
								'user_info.js' => 'static_script'
						]
				);
			}
		}

		if ($sPlugin = Phpfox_Plugin::get('get_controller')) {
			eval($sPlugin);
		}


		$oTpl->assign([
				'aGlobalUser'   => (Phpfox::isUser() ? Phpfox::getUserBy(null) : []),
				'bIsDetailPage' => false
		]);

		$oModule->getController();

		Admincp_Service_Seo_Seo::instance()->setHeaders();

		if (!defined('PHPFOX_DONT_SAVE_PAGE')) {
			Phpfox::getLib('session')->set('redirect', Phpfox_Url::instance()->getFullUrl(true));
		}

		if (!defined('PHPFOX_NO_CSRF')) {
            Log_Service_Session::instance()->verifyToken();
		}

		(($sPlugin = Phpfox_Plugin::get('run')) ? eval($sPlugin) : false);

		$sMenuSelected = '';
		if (!self::isAdminPanel()) {
			if (!PHPFOX_IS_AJAX_PAGE && Phpfox::isModule('rss') && !defined('PHPFOX_IS_USER_PROFILE')) {
				$aFeeds = Rss_Service_Rss::instance()->getLinks();
				if (is_array($aFeeds) && count($aFeeds)) {
					foreach ($aFeeds as $sLink => $sPhrase) {
						$oTpl->setHeader('<link rel="alternate" type="application/rss+xml" title="' . $sPhrase . '" href="' . $sLink . '" />');
					}
				}
			}

			$aPageLastLogin = ((Phpfox::isModule('pages') && Phpfox::getUserBy('profile_page_id')) ? Pages_Service_Pages::instance()->getLastLogin() : false);

			$aSubMenus = $oTpl->getMenu();

			if (defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE')) {
				$sPagesSection = Phpfox::getNonRewritten(PHPFOX_PAGES_ITEM_TYPE);
				$sModule = (Phpfox_Request::instance()->get('req1') == $sPagesSection ? Phpfox_Request::instance()->get('req3') : Phpfox_Request::instance()->get('req2'));
				$sModule = Phpfox_Url::instance()->reverseRewrite($sModule);
				$sSubMenuCallback = 'get' . PHPFOX_PAGES_ITEM_TYPE . 'SubMenu';
				switch (PHPFOX_PAGES_ITEM_TYPE) {
					case 'pages':
						$sSubMenuCallback = 'getPageSubMenu';
						break;
					case 'groups':
						$sSubMenuCallback = 'getGroupSubMenu';
						break;

				}
				if (Phpfox::isModule($sModule) && Phpfox::hasCallback($sModule, $sSubMenuCallback)) {

					$aPage = $oTpl->getVar('aPage');

					$aMenu = Phpfox::callback($sModule . '.' . $sSubMenuCallback, $aPage);
					if (is_array($aMenu)) {
						foreach ($aMenu as $iKey => $aSubMenu) {
							$aMenu[ $iKey ]['module'] = $sModule;
							if (isset($aSubMenu['phrase'])) {
								if (Phpfox_Locale::instance()->isPhrase($sModule . '.' . $aSubMenu['phrase'])) {
									$aMenu[ $iKey ]['var_name'] = $aSubMenu['phrase'];
								} else {
									$aMenu[ $iKey ]['text'] = $aSubMenu['phrase'];
								}
								continue;
							}
							switch ($sModule) {
								case 'event':
									$aMenu[ $iKey ]['var_name'] = 'menu_create_new_' . $sModule;
									break;
								case 'forum':
									$aMenu[ $iKey ]['var_name'] = 'post_a_new_thread';
									break;
								case 'music':
									$aMenu[ $iKey ]['var_name'] = 'menu_upload_a_song';
									break;
								case 'photo':
									$aMenu[ $iKey ]['var_name'] = 'upload_a_new_image';
									break;
								default:
									$aMenu[ $iKey ]['var_name'] = 'menu_add_new_' . $sModule;
							}
						}
					}
					$aSubMenus = $aMenu;
				}
			}
			$oTpl->assign([
							'aMainMenus'              => $oTpl->getMenu('main'),
							'aSubMenus'               => $aSubMenus,
							'bIsUsersProfilePage'     => (defined('PHPFOX_IS_USER_PROFILE') ? true : false),
							'sGlobalUserFullName'     => (Phpfox::isUser() ? Phpfox::getUserBy('full_name') : null),
							'sFullControllerName'     => str_replace(['.', '/'], '_', Phpfox_Module::instance()->getFullControllerName()),
							'iGlobalProfilePageId'    => Phpfox::getUserBy('profile_page_id'),
							'aGlobalProfilePageLogin' => $aPageLastLogin,
					]
			);

			foreach ($oTpl->getMenu('main') as $aMenu) {
				if (isset($aMenu['is_selected']) && $aMenu['is_selected']) {
					$sMenuSelected = $aMenu['menu_id'];
					break;
				}
			}

			$oTpl->setEditor();

			if (Phpfox::isModule('notification') && Phpfox::isUser() && Phpfox::getParam('notification.notify_on_new_request')) {
				$oTpl->setHeader('cache', ['update.js' => 'module_notification']);
			}
		}

		if (!PHPFOX_IS_AJAX_PAGE && ($sHeaderFile = $oTpl->getHeaderFile())) {
			(($sPlugin = Phpfox_Plugin::get('run_get_header_file_1')) ? eval($sPlugin) : false);
			require_once($sHeaderFile);
		}

		list($aBreadCrumbs, $aBreadCrumbTitle) = $oTpl->getBreadCrumb();


		$bIsDetailPage = false;
		$fullControllerName = $oModule->getFullControllerName();

		foreach([
						'.view','.detail','.edit','.delete','.add','.thread','.create', '.post', '.upload', '.album'
				] as $name){
			if(strpos($fullControllerName,$name)){
				$bIsDetailPage = true;
			}
		}

		(($sPlugin = \Phpfox_Plugin::get('phpfox_assign_ajax_browsing')) ? eval($sPlugin) : false);

		$oTpl->assign([
					'bIsDetailPage'=>$bIsDetailPage,
					'aErrors'          => (Phpfox_Error::getDisplay() ? Phpfox_Error::get() : []),
					'sPublicMessage'   => Phpfox::getMessage(),
					'sLocaleDirection' => $aLocale['direction'],
					'sLocaleCode'      => $aLocale['language_code'],
					'sLocaleFlagId'    => $aLocale['image'],
					'sLocaleName'      => $aLocale['title'],
					'aBreadCrumbs'     => $aBreadCrumbs,
					'aBreadCrumbTitle' => $aBreadCrumbTitle,
					'sCopyright'       => '&copy; ' . _p('copyright') . ' ' . Phpfox::getParam('core.site_copyright')
				]
		);

		Phpfox::clearMessage();

		unset($_SESSION['phpfox']['image']);

		if ($oReq->isPost()) {
			header('X-Is-Posted: true');
			exit;
		}

		if ($oReq->get('is_ajax_get')) {
			header('X-Is-Get: true');
			exit;
		}

		if (defined('PHPFOX_SITE_IS_OFFLINE')) {
			$oTpl->sDisplayLayout = 'blank';
			unset($View);
		}

		if ((!PHPFOX_IS_AJAX_PAGE && $oTpl->sDisplayLayout && !isset($View))
				|| (!PHPFOX_IS_AJAX_PAGE && self::isAdminPanel())
		) {
			$oTpl->getLayout($oTpl->sDisplayLayout);
		}

		if (PHPFOX_IS_AJAX_PAGE) {
			header('Content-type: application/json; charset=utf-8');
			if ($View instanceof \Core\View) {
				$content = $View->getContent();
			} else {
				Phpfox_Module::instance()->getControllerTemplate();
				$content = ob_get_contents();
				ob_clean();
			}

			$oTpl->getLayout('breadcrumb');
			$breadcrumb = ob_get_contents();
			ob_clean();

			$aHeaderFiles = Phpfox_Template::instance()->getHeader(true);
			$aCss = [];
			$aLoadFiles = [];
			foreach ($aHeaderFiles as $sHeaderFile) {
				if (!is_string($sHeaderFile)) {
					continue;
				}

				if (preg_match('/<style(.*)>(.*)<\/style>/i', $sHeaderFile)) {
					$aCss[] = strip_tags($sHeaderFile);

					continue;
				}

				if (preg_match_all('/href=(["\']?([^"\'>]+)["\']?)/m', $sHeaderFile, $aMatches) > 0) {
					foreach($aMatches[1] as $aMatch){
						if(strpos($aMatch, '.css') !== false){
							$sHeaderFile = str_replace(['"', "'"], ['',''], $aMatch);
							$sHeaderFile = substr($sHeaderFile, 0, strpos($sHeaderFile, '?'));
							$sNew = preg_replace('/\s+/', '', $sHeaderFile);
							if (empty($sNew)) {
								continue;
							}

							$aLoadFiles[] = $sHeaderFile;
						}
					}
					continue;
				}

				$sHeaderFile = strip_tags($sHeaderFile);

				$sNew = preg_replace('/\s+/', '', $sHeaderFile);
				if (empty($sNew)) {
					continue;
				}

				$aLoadFiles[] = $sHeaderFile;
			}

			$blocks = [];
			foreach (range(1, 12) as $location) {
				if ($location == 3) {
					echo \Phpfox_Template::instance()->getSubMenu();
				}
				$aBlocks = Phpfox_Module::instance()->getModuleBlocks($location);
				$blocks[ $location ] = [];
				foreach ($aBlocks as $sBlock) {
					Phpfox::getBlock($sBlock);
					$blocks[ $location ][] = ob_get_contents();
					ob_clean();
				}
			}

			$oTpl->getLayout('search');
			$search = ob_get_contents();
			ob_clean();

			Phpfox::getBlock('core.template-menusub');
			$menuSub = ob_get_contents();
			ob_clean();

			$h1 = '';
			if (isset($aBreadCrumbTitle[1])) {
				$h1 .= '<h1><a href="' . $aBreadCrumbTitle[1] . '">' . Phpfox_Parse_Output::instance()->clean($aBreadCrumbTitle[0]) . '</a></h1>';
			}

			$oTpl->getLayout('error');
			$error = ob_get_contents();
			ob_clean();

			$controller = Phpfox_Module::instance()->getFullControllerName();

			/**
			 * Commented by Robert
			 * This code causes the issue (YPFOXNEUTRON-1406): "Duplicate content of block 2 and block 4 when load page by ajax"
			 */

		//	$content = (isset($blocks[2]) ? implode('', $blocks[2]) : '') . $content . (isset($blocks[4]) ? implode('', $blocks[4]) : '');

			$data = json_encode([
					/**
					 * Changes made by: Ray
					 *
					 * I needed to change this to fix this issue: https://github.com/moxi9/phpfox/issues/945
					 * Is there a reason we needed the convert() method to run in AJAX page calls?
					 */
					'content' => str_replace(['&#039;'], ["'"], $content),
					'title'        => html_entity_decode($oTpl->instance()->getTitle()),
					'actual_title' => $oTpl->instance()->getActualTitle(),
					'phrases'      => Phpfox_Template::instance()->getPhrases(),
					'files'        => $aLoadFiles,
					'css'          => $aCss,
					'breadcrumb'   => $breadcrumb,
					'blocks'       => $blocks,
					'search'       => $search,
					'menuSub'      => $menuSub,
					'id'           => Phpfox_Module::instance()->getPageId(),
					'class'        => Phpfox_Module::instance()->getPageClass(),
					'h1'           => $h1,
					'h1_clean'     => strip_tags($h1),
					'error'        => $error,
					'has_left'     => !(empty($blocks['1']) && empty($blocks['9'])),
					'has_right'    => !(empty($blocks['3']) && empty($blocks['10'])),
					'controller_e' => (Phpfox::isAdmin() ? Phpfox_Url::instance()->makeUrl('admincp.element.edit', ['controller' => base64_encode(Phpfox_Module::instance()->getFullControllerName())]) : null),
					'meta'         => Phpfox_Template::instance()->getPageMeta(),
					'keep_body'    => Phpfox_Template::instance()->keepBody(),
					'selected_menu' => $sMenuSelected,
					'is_sample' => Phpfox_Template::instance()->bIsSample,
                    'iNumberRequest' => ((Phpfox::isUser() && Phpfox::isModule('friend'))  ? Friend_Service_Request_Request::instance()->getUnseenTotal() : -1),
                    'iNumberNotification' => ((Phpfox::isUser() && Phpfox::isModule('notification')) ? Notification_Service_Notification::instance()->getUnseenTotal() : -1),
                    'iNumberMessage' => ((Phpfox::isUser() && Phpfox::isModule('mail')) ? Mail_Service_Mail::instance()->getUnseenTotal() : -1)
			]);
			echo $data;
		} else {
			if (isset($View) && $View instanceof Core\View) {
				echo $View->getContent();
			}
		}

		http_cache()->run();

		if (PHPFOX_DEBUG && !PHPFOX_IS_AJAX && !PHPFOX_IS_AJAX_PAGE) {
			echo Phpfox_Debug::getDetails();
		}
	}

	/**
     * @deprecated from 4.6.0
	 * @see Phpfox_Local::getPhrase()
	 *
	 * @param string $sParam
	 * @param array  $aParams
	 * @param bool   $bNoDebug
	 * @param string $sDefault
	 * @param string $sLang
	 *
	 * @return string
	 */
	public static function getPhrase($sParam, $aParams = [], $bNoDebug = false, $sDefault = null, $sLang = '')
	{
		return Phpfox_Locale::instance()->getPhrase($sParam, $aParams, $bNoDebug, $sDefault, $sLang);
	}
    
    /**
     * return a phrase value if is a phrase, return param if not a phrase
     *
     * @param string $sParam
     * @param array  $aParams
     * @param bool   $bNoDebug @deprecated from 4.6.0
     * @param null   $sDefault @deprecated from 4.6.0
     * @param string $sLang
     *
     * @return string
     */
    public static function getSoftPhrase($sParam, $aParams = [], $bNoDebug = false, $sDefault = null, $sLang = '') {
        if (Core\Lib::phrase()->isPhrase($sParam)){
            return _p($sParam, $aParams, $sLang);
        } else {
            return $sParam;
        }
	}

    public static function getLanguageId(){
        return Phpfox_Locale::instance()->getLangId();
    }

	/**
     * @deprecated from 4.6.0
	 * @see Phpfox_Local::isPhrase()
	 *
	 * @param string $sParam
	 *
	 * @return bool
	 */
	public static function isPhrase($sParam)
	{
		return Core\Lib::phrase()->isPhrase($sParam);
	}

	/**
	 * @see Phpfox_Locale::translate()
	 *
	 * @param string $sParam
	 * @param string $sPrefix
	 *
	 * @return string
	 */
	public static function getPhraseT($sParam, $sPrefix)
	{
		return Phpfox_Locale::instance()->translate($sParam, $sPrefix);
	}

	/**
	 * Add a public message which can be used later on to display information to a user.
	 * Message gets stored in a $_SESSION so the message can be viewed after page reload in case
	 * it is used with a HTML form.
	 *
	 * @see Phpfox_Session::set()
	 *
	 * @param string $sMsg Message we plan to display to the user
	 */
	public static function addMessage($sMsg)
	{
		Phpfox::getLib('session')->set('message', $sMsg);
	}

	/**
	 * Get the public message we setup earlier
	 *
	 * @see Phpfox_Session::get()
	 * @return string|void Return the public message, or return nothing if no public message is set
	 */
	public static function getMessage()
	{
		return Phpfox::getLib('session')->get('message');
	}

	/**
	 * Clear the public message we set earlier
	 *
	 * @see Phpfox_Session::remove()
	 */
	public static function clearMessage()
	{
		Phpfox::getLib('session')->remove('message');
	}

	/**
	 * Set a cookie with PHP setcookie()
	 *
	 * @see setcookie()
	 *
	 * @param string $sName   The name of the cookie.
	 * @param string $sValue  The value of the cookie.
	 * @param int    $iExpire The time the cookie expires. This is a Unix timestamp so is in number of seconds since the epoch.
	 */
	public static function setCookie($sName, $sValue, $iExpire = 0, $bSecure = false, $bHttpOnly = true)
	{
		$sName = Phpfox::getParam('core.session_prefix') . $sName;
        if (($iExpire - PHPFOX_TIME) > 0){
            $iRealExpire = $iExpire;
        } else {
            $iRealExpire = (($iExpire <= 0) ? null : (PHPFOX_TIME + (60 * 60 * 24 * $iExpire)));
        }
		if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
			setcookie($sName, $sValue, $iRealExpire, Phpfox::getParam('core.cookie_path'), Phpfox::getParam('core.cookie_domain'), $bSecure, $bHttpOnly);
		} else {
			setcookie($sName, $sValue, $iRealExpire, Phpfox::getParam('core.cookie_path'), Phpfox::getParam('core.cookie_domain'), $bSecure);
		}
	}

	/**
	 * Gets a cookie set by the method self::setCookie().
	 *
	 * @param string $sName Name of the cookie.
	 *
	 * @return string Value of the cookie.
	 */
	public static function getCookie($sName)
	{
		$sName = Phpfox::getParam('core.session_prefix') . $sName;

		return (isset($_COOKIE[ $sName ]) ? $_COOKIE[ $sName ] : '');
	}

    public static function removeCookie($sName){
        $sName = Phpfox::getParam('core.session_prefix') . $sName;
        if (isset($_COOKIE[$sName])){
            unset($_COOKIE[$sName]);
        }
    }

	/**
	 * Start a new log.
	 *
	 * @param string $sLog Message to the log.
	 */
	public static function startLog($sLog = null)
	{
		self::$_aLogs[] = [];

		if ($sLog !== null) {
			self::log($sLog);
		}
	}

	/**
	 * Log a message.
	 *
	 * @param string $sLog Message to the log.
	 */
	public static function log($sLog)
	{
		self::$_aLogs[] = $sLog;
	}

	/**
	 * End the log and get it.
	 *
	 * @return array Returns the log.
	 */
	public static function endLog()
	{
		return self::$_aLogs;
	}

	/**
	 * Permalink for items.
	 *
	 * @return    string    Returns the full URL of the link.
	 */
	public static function permalink($sLink, $iId, $sTitle = null, $bRedirect = false, $sMessage = null, $aExtra = [])
	{
		return Phpfox_Url::instance()->permalink($sLink, $iId, $sTitle, $bRedirect, $sMessage, $aExtra);
	}

	/**
	 * Get CDN path
	 *
	 * @return string Returns CDN full URL
	 */
	public static function getCdnPath()
	{
		return 'http://cdn.oncloud.ly/' . self::getVersion() . '/';
	}

	/**
	 * Since we allow urls to be rewritten we use this function to get the original value no matter what
	 *
	 * @param $sSection <string>
	 *
	 * @return <string>
	 */
	public static function getNonRewritten($sSection)
	{
		$aRewrites = Core_Service_Redirect_Redirect::instance()->getRewrites();
		foreach ($aRewrites as $aRewrite) {
			if ($aRewrite['url'] == $sSection) {
				return $aRewrite['replacement'];
			}
		}

		return $sSection;
	}

	/**
	 * Get base url
	 * strip "index.php" from core.path
	 *
	 * @return string
	 */
	public static function getBaseUrl()
	{
		return str_replace('/index.php/', '/', Phpfox::getParam('core.path'));
	}
}