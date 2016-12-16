<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Admincp
 * @version 		$Id: index.class.php 7202 2014-03-18 13:38:56Z Raymond_Benc $
 */
class Admincp_Component_Controller_Index extends Phpfox_Component
{
	private $_sController = 'index';
    
	private $_sModule;

	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);

		if (Phpfox::getParam('core.admincp_http_auth')) {
			$aAuthUsers = Phpfox::getParam('core.admincp_http_auth_users');

			if((isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && isset($aAuthUsers[Phpfox::getUserId()])) && (($_SERVER['PHP_AUTH_USER'] == $aAuthUsers[Phpfox::getUserId()]['name']) && ($_SERVER['PHP_AUTH_PW'] == $aAuthUsers[Phpfox::getUserId()]['password']))) {
			} else {
				header("WWW-Authenticate: Basic realm=\"AdminCP\"");
				header("HTTP/1.0 401 Unauthorized");
				exit("NO DICE!");
			}
		}

		if (Phpfox::getParam('admincp.admin_cp') != $this->request()->get('req1')) {
			return Phpfox_Module::instance()->setController('error.404');
		}

		if (!User_Service_Auth::instance()->isActiveAdminSession()) {
			return Phpfox_Module::instance()->setController('admincp.login');
		}

		if ($this->request()->get('upgraded')) {
			Phpfox::getLib('cache')->remove();
			Phpfox::getLib('template.cache')->remove();

			$this->url()->send('admincp');
		}

		$this->_sModule = (($sReq2 = $this->request()->get('req2')) ? strtolower($sReq2) : Phpfox::getParam('admincp.admin_cp'));
		if ($this->_sModule == 'logout') {
			$this->_sController = $this->_sModule;
			$this->_sModule = 'admincp';
		} else {
			$this->_sController = (($sReq3 = $this->request()->get('req3')) ? $sReq3 : $this->_sController);
		}
		if ($sReq4 = $this->request()->get('req4')) {
			$sReq4 = str_replace(' ', '', strtolower(str_replace('-', ' ', $sReq4)));
		}
		$sReq5 = $this->request()->get('req5');

		$bPass = false;
		if (file_exists(PHPFOX_DIR_MODULE . $this->_sModule . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . 'admincp' . PHPFOX_DS . $this->_sController . '.class.php')) {
			$this->_sController = 'admincp.' . $this->_sController;
			$bPass = true;
		}

		if (!$bPass && $sReq5 && file_exists(PHPFOX_DIR_MODULE . $this->_sModule . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . 'admincp' . PHPFOX_DS . $this->_sController . PHPFOX_DS . $sReq4 . PHPFOX_DS . $sReq5 . '.class.php')) {
			$this->_sController = 'admincp.' . $this->_sController . '.' . $sReq4 . '.' . $sReq5;
			$bPass = true;
		}

		if (!$bPass && $sReq4 && file_exists(PHPFOX_DIR_MODULE . $this->_sModule . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . 'admincp' . PHPFOX_DS . $this->_sController . PHPFOX_DS . $sReq4 . '.class.php')) {
			$this->_sController = 'admincp.' . $this->_sController . '.' . $sReq4;
			$bPass = true;
		}

		if (!$bPass && file_exists(PHPFOX_DIR_MODULE . $this->_sModule . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . 'admincp' . PHPFOX_DS . $this->_sController . PHPFOX_DS . $this->_sController . '.class.php')) {
			$this->_sController = 'admincp.' . $this->_sController . '.' . $this->_sController;
			$bPass = true;
		}

		if (!$bPass && $sReq4 && file_exists(PHPFOX_DIR_MODULE . $this->_sModule . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . 'admincp' . PHPFOX_DS . $this->_sController . PHPFOX_DS . $sReq4 . '.class.php'))
		{
			$this->_sController = 'admincp.' . $this->_sController . '.' . $sReq4;
			$bPass = true;
		}

		if (!$bPass && $sReq4 && file_exists(PHPFOX_DIR_MODULE . $this->_sModule . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . 'admincp' . PHPFOX_DS . $this->_sController . PHPFOX_DS . $sReq4 . PHPFOX_DS . 'index.class.php'))
		{
			$this->_sController = 'admincp.' . $this->_sController . '.' . $sReq4 . '.index';
			$bPass = true;
		}

		if (!$bPass && file_exists(PHPFOX_DIR_MODULE . $this->_sModule . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . 'admincp' . PHPFOX_DS . $this->_sController . PHPFOX_DS . 'index.class.php'))
		{
			$this->_sController = 'admincp.' . $this->_sController . '.index';
			$bPass = true;
		}

		if (!$bPass && file_exists(PHPFOX_DIR_MODULE . 'admincp' . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . $this->_sModule . PHPFOX_DS . $this->_sController . '.class.php'))
		{
			$this->_sController = $this->_sModule . '.' . $this->_sController;
			$this->_sModule = 'admincp';
			$bPass = true;
		}

		if (!$bPass && $sReq4 && file_exists(PHPFOX_DIR_MODULE . 'admincp' . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . $this->_sModule . PHPFOX_DS . $this->_sController . PHPFOX_DS . $sReq4 . '.class.php'))
		{
			$this->_sController = $this->_sModule . '.' . $this->_sController . '.' . $sReq4;
			$this->_sModule = 'admincp';
			$bPass = true;
		}

		if (!$bPass && Phpfox::getParam('admincp.admin_cp') != 'admincp' && file_exists(PHPFOX_DIR_MODULE . $this->_sModule . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . $this->_sController . '.class.php'))
		{
			$bPass = true;
		} elseif (Phpfox::isAppAlias($this->request()->get('req2'))) {
            $this->_sController = 'admincp.' . $this->_sController;
            $bPass = true;
        }

		if (!$bPass && Phpfox::isModule($this->request()->segment('req2'))) {
			$this->_sModule = 'admincp';
			$this->_sController = 'app.index';
			$bPass = true;
		}

		// Create AdminCP menu
		$aMenus = [
			'<i class="fa fa-dashboard"></i>' . _p('dashboard') => 'admincp',
			'<i class="fa fa-cubes"></i>' . _p('apps') => 'admincp.apps',
			'<i class="fa fa-paint-brush"></i>' . _p('themes') => 'admincp.theme',
			'<i class="fa fa-language"></i>' . _p('Languages') => 'admincp.language',
            _p('members'),
			'<i class="fa fa-search"></i>' . _p('search') => 'admincp.user.browse',
			'<i class="fa fa-users"></i>'. _p('user_groups') => 'admincp.user.group',
			'<i class="fa fa-diamond"></i>' . _p('promotions') => 'admincp.user.promotion',
			'<i class="fa fa-th-list"></i>' . _p('custom_fields')=> 'admincp.custom',
            _p('site'),
			'<i class="fa fa-file-text-o"></i>' . _p('pages') => 'admincp.page',
			'<i class="fa fa-bars"></i>' . _p('menus') => 'admincp.menu',
			'<i class="fa fa-th"></i>' . _p('blocks') => 'admincp.block',
			'<i class="fa fa-quote-right"></i>' . _p('phrases') => 'admincp.language.phrase',

            _p('tools'),
            _p('settings') => [
				// 'Site &amp; Server',
                _p('countries') => 'admincp.core.country',
                _p('currencies') => 'admincp.core.currency',
                _p('attachments') => 'admincp.attachment',
                _p('payment_gateways_menu') => 'admincp.api.gateway',
                _p('short_urls') => 'admincp.setting.url',
	            _p('URL Match') => 'admincp.setting.redirection',
                _p('seo') => $this->url()->makeUrl('admincp.setting.edit', ['group-id' => 'seo']),
	            _p('Performance') => url('/admincp/app/settings', ['id' => 'PHPfox_Core', 'group' => 'core_redis']),
	            _p('Data Cache') => url('/admincp/app/settings', ['id' => 'PHPfox_Core', 'group' => 'core_cache_driver']),
	            _p('Cron') => $this->url()->makeUrl('admincp.cron'),

                _p('user'),
                _p('settings') => $this->url()->makeUrl('admincp.setting.edit', ['module-id' => 'user']),
                _p('registration') => $this->url()->makeUrl('admincp.setting.edit', ['group-id' => 'registration']),
                _p('relationship_statues') => 'admincp.custom.relationships',
                _p('cancellation_options') => 'admincp.user.cancellations.manage',
                _p('subscription_packages') => 'admincp.subscribe',
                _p('anti_spam_questions') => 'admincp.user.spam',
			],
			'<i class="fa fa-info"></i>' . _p('status') => array(
				_p('site_statistics') => 'admincp.core.stat',
				_p('admincp_menu_system_overview') => 'admincp.core.system',
				_p('inactive_members') => 'admincp.user.inactivereminder',
                _p('cancelled_members') => 'admincp.user.cancellations.feedback'
			),
			'<i class="fa fa-server"></i>' . _p('maintenance') => array(
				_p('menu_cache_manager') => 'admincp.maintain.cache',
				_p('reported_items') => 'admincp.report',
				_p('admincp_menu_reparser') => 'admincp.maintain.reparser',
				_p('remove_duplicates') => 'admincp.maintain.duplicate',
				_p('Remove files no longer used') => 'admincp.maintain.removefile',
				_p('counters') => 'admincp.maintain.counter',
				_p('check_modified_files') => 'admincp.checksum.modified',
				_p('check_unknown_files') => 'admincp.checksum.unknown',
				_p('find_missing_settings') => 'admincp.setting.missing',
				_p('Rebuild Core Theme') => url('/admincp/theme/bootstrap/rebuild')
			),
			'<i class="fa fa-ban"></i>' . _p('ban_filters') => array(
				_p('ban_filter_username') => 'admincp.ban.username',
                _p('ban_filter_email') => 'admincp.ban.email',
                _p('ban_filter_display_name') => 'admincp.ban.display',
                _p('ban_filter_ip') => 'admincp.ban.ip',
                _p('ban_filter_word') => 'admincp.ban.word'
			)
		];

		list($aGroups, $aModules, ) = Admincp_Service_Setting_Group_Group::instance()->get();

		$aCache = $aGroups;
		$aGroups = [];

		foreach ($aCache as $key => $value) {

			$n = $key;
			switch ($value['group_id']) {
				case 'cookie':
					$n = _p('browser_cookies');
					break;
				case 'site_offline_online':
					$n = _p('toggle_site');
					break;
				case 'general':
					$n = _p('site_settings');
					break;
				case 'mail':
					$n = _p('mail_server');
					break;
				case 'spam':
					$n = _p('spam_assistance');
					break;
				case 'registration':
					continue 2;
					break;
			}

			$aGroups[$n] = $value;
		}
		ksort($aGroups);


		$aSettings = [];
		foreach ($aGroups as $sGroupName => $aGroupValues) {
			$aSettings[$sGroupName] = $this->url()->makeUrl('admincp.setting.edit', ['group-id' => $aGroupValues['group_id']]);
		}

		$aCache = $aMenus;
		$aMenus = [];
		foreach ($aCache as $sKey => $mValue) {
			if ($sKey === _p('settings') ) {
				$sKey = '<i class="fa fa-cog"></i>' .  _p('settings') ;
                $mValue = array_merge($aSettings, $mValue);
			}

			$aMenus[$sKey] = $mValue;

			if (is_string($mValue) && $mValue == 'admincp.language' && PHPFOX_IS_TECHIE) {
				$aMenus['<i class="fa fa-sheqel"></i>'. _p('techie')] = [
					_p('products') => 'admincp.product',
					_p('plugins') => 'admincp.plugin',
                    _p('components') => 'admincp.component',
				];
                //Add menu to put license key.
                if (defined('PHPFOX_LICENSE_ID') && PHPFOX_LICENSE_ID == 'techie'){
                    $aMenus['<i class="fa fa-key"></i>'. _p('license_key')] = 'admincp.setting.license';
                }
			}
		}
		(($sPlugin = Phpfox_Plugin::get('admincp.component_controller_index_process_menu')) ? eval($sPlugin) : false);

		$aUser = Phpfox::getUserBy();

		$sSectionTitle = '';
		$app = $this->request()->get('req2');
		if ($app == 'app') {
			$app = str_replace('__module_', '', $this->request()->get('id'));
		}

		$is_settings = false;
		if ($this->url()->getUrl() == 'admincp/setting/edit') {
			$app = $this->request()->get('module-id');
			$is_settings = true;
		}

		$aSkipModules = [
//			'feed',
			'user',
			'admincp',
			'theme',
			'like',
			'core',
			'language'
		];

		$sCoreModules = [
			'user',
			'feed',
			'theme',
			'core',
			'language',
			'announcement'
		];

		$searchSettings = Admincp_Service_Setting_Setting::instance()->getForSearch([]);
		$this->template()->setHeader('<script>var admincpSettings = ' . json_encode($searchSettings) . ';</script>');

		if (in_array($app, $aSkipModules) && !(in_array($app, $sCoreModules))) {
			$this->url()->send('admincp');
		}

		if ($app && Phpfox::isModule($app) && !in_array($app, $aSkipModules)) {
			$oApp = (new Core\App())->get('__module_'.$app);
			$app = Phpfox_Module::instance()->get($app);
			$name = ($oApp && $oApp->name) ? $oApp->name : Phpfox_Locale::instance()->translate($app['module_id'], 'module');
			$sSectionTitle = $name;
			$menu = unserialize($app['menu']);
			$menus = [];
			$current = $this->url()->getUrl();
			$infoActive = false;

			if ($this->request()->get('req2') == 'app') {
				$infoActive = true;
			}

			if (Admincp_Service_Setting_Setting::instance()->moduleHasSettings($app['module_id'])) {
				$menus[_p('settings')] = [
					'is_active' => $is_settings,
					'url' => $this->url()->makeUrl('admincp.setting.edit', ['module-id' => $app['module_id']])
				];
			}

			if (is_array($menu) && count($menu)) {
				foreach ($menu as $key => $value) {
					$is_active = false;
					$url = 'admincp.' . implode('.', $value['url']);
					if ($current == str_replace('.', '/', $url)) {
						$is_active = true;
						if ($infoActive) {
							$menus['Info']['is_active'] = false;
						}
					}

					$menus[_p($key)] = [
						'url' => $url,
						'is_active' => $is_active
					];
				}
			}
			$this->template()->assign([
				'aSectionAppMenus' => $menus,
				'ActiveApp' => (new Core\App())->get('__module_' . $app['module_id'])
			]);
		}

        $bAutoSaveSettings = true;
        $cache = storage()->get('admincp/settings/autosave');
        if ($cache)
        {
            $bAutoSaveSettings = $cache->value;
        }

        $this->template()->assign(['bAutoSaveSettings' => $bAutoSaveSettings]);
        $this->template()->setHeader([
            "<script>var bAutoSaveSettings = " .$bAutoSaveSettings . ";</script>"
        ]);
        
        $this->template()->assign([
            'sSectionTitle'     => $sSectionTitle,
            'aModulesMenu'      => $aModules,
            'aAdminMenus'       => $aMenus,
            'aUserDetails'      => $aUser,
            'sPhpfoxVersion'    => Phpfox::getVersion(),
            'sSiteTitle'        => Phpfox::getParam('core.site_title'),
            'bAutoSaveSettings' => $bAutoSaveSettings
        ])->setHeader([
            'menu.css'                               => 'style_css',
            'menu.js'                                => 'style_script',
            'admin.js'                               => 'static_script',
            'jquery/plugin/jquery.mosaicflow.min.js' => 'static_script'
        ])->setTitle(_p('admin_cp'));

        if (Phpfox::demoMode()) {
            return Phpfox_Module::instance()->setController('admincp.demo');
        }
        
        if (in_array($app, ['plugin', 'module', 'component'])) {
			$this->template()->setSectionTitle(_p('techie').': ' . ucwords($app));
			$this->template()->setActionMenu([
				_p('New ') . ucwords($app) => [
					'url' => $this->url()->makeUrl('admincp.' . $app . '.add'),
					'class' => 'popup'
				]
			]);
		}
		if ($bPass)
		{
		    if (Phpfox::isModule($this->_sModule) || Phpfox::isAppAlias($this->_sModule)) {
                Phpfox_Module::instance()->setController($this->_sModule . '.' . $this->_sController);
            } else {
                $this->url()->send('admincp.apps');
            }

			$sMenuController = str_replace(array('.index', '.phrase'), '', 'admincp.' . ($this->_sModule != 'admincp' ? $this->_sModule . '.' . str_replace('admincp.', '', $this->_sController) : $this->_sController));
			$aCachedSubMenus = array();
			$sActiveSideBar = '';

			if ($sMenuController == 'admincp.setting.edit') {
				$sMenuController = 'admincp.setting';
			}

			if ($this->_getMenuName() !== null) {
				$sMenuController = $this->_getMenuName();
			}

			$this->template()->assign([
                'aCachedSubMenus' => $aCachedSubMenus,
                'sActiveSideBar' => $sActiveSideBar,
                'bIsModuleConnection' => false,
                'sMenuController' => $sMenuController,
                'aActiveMenus' => ((false && isset($aCachedSubMenus[$sActiveSideBar])) ? $aCachedSubMenus[$sActiveSideBar] : array())
			]);
		} else {
			if ($this->_sModule != Phpfox::getParam('admincp.admin_cp'))
			{
				Phpfox_Module::instance()->setController('error.404');
			} else {
                Admincp_Service_Admincp::instance()->check();

                $expires = 0;
                if (defined('PHPFOX_TRIAL_EXPIRES')) {
                    $expires = PHPFOX_TRIAL_EXPIRES;
                }

				$this->template()->setBreadCrumb(_p('dashboard'))
					->setTitle(_p('dashboard'))
					->assign(array(
						'bIsModuleConnection' => false,
						'bIsDashboard' => true,
						'aNewProducts' => Admincp_Service_Product_Product::instance()->getNewProductsForInstall(),
                            'is_trial_mode' => defined('PHPFOX_TRIAL_MODE'),
                            'expires' => $expires
					)
				);
			}
		}
        $this->template()->setHeader([
            'bootstrap.min.js' => "static_script"
        ]);

        return null;
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('admincp.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}