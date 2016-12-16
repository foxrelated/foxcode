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
 * @version 		$Id: setting.class.php 6545 2013-08-30 08:41:44Z Raymond_Benc $
 */
class Admincp_Service_Setting_Setting extends Phpfox_Service 
{
    private $_aPasswordSettings = [
        'core.mail_smtp_password'
    ];
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('setting');
	}
    
    /**
     * @param array $aSkipModules
     *
     * @return array
     */
	public function getForSearch($aSkipModules = [])
	{
	    $sCacheId = $this->cache()->set('admincp_setting_' . md5(serialize($aSkipModules)));
        if (!$aReturn = $this->cache()->get($sCacheId)) {
            $aRows = $this->database()
                ->select('s.*, lp.text AS language_var_name')
                ->from($this->_sTable, 's')
                ->group('setting_id', true)
                ->where('s.is_hidden = 0')
                ->leftJoin(Phpfox::getT('language_phrase'), 'lp', [
                        "lp.language_id = '" . Phpfox_Locale::instance()
                            ->getLangId() . "'",
                        "AND lp.var_name = s.phrase_var_name"
                    ])
                ->execute('getSlaveRows');
    
            $aNotAllowedToEdit = [];
            foreach (Phpfox_Setting::instance()->override as $key => $value) {
                $aNotAllowedToEdit[] = $key;
            }
    
            $aReturn = [];
            foreach ($aRows as $iKey => $aRow) {
                if (!empty($aRow['language_var_name'])) {
                    if ($aSkipModules && in_array($aRow['module_id'], $aSkipModules)) {
                        continue;
                    }
            
                    if (in_array($aRow['module_id'] . '.' . $aRow['var_name'], $aNotAllowedToEdit) || in_array($aRow['module_id'] . '.' . $aRow['var_name'], Phpfox_Setting::instance()->hide)) {
                        continue;
                    }
            
                    $aParts = explode('</title><info>', $aRow['language_var_name']);
                    $aReturn[] = [
                        'module_id' => $aRow['module_id'],
                        'link'      => Phpfox_Url::instance()
                                ->makeUrl('admincp.setting.edit', ['module-id' => $aRow['module_id']]) . '#' . $aRow['var_name'],
                        'type'      => 'Global Setting',
                        'title'     => str_replace('<title>', '', $aParts[0])
                    ];
                }
            }
            $this->cache()->save($sCacheId, $aReturn);
        }
        return $aReturn;
	}
    
    /**
     * @param $iId
     *
     * @return array|bool
     */
	public function getForEdit($iId)
	{
		if (!PHPFOX_DEBUG) {
			return false;	
		}
		
		$aSetting = $this->database()->select('s.*, lp.text AS language_var_name')
			->from($this->_sTable, 's')
			->leftJoin(Phpfox::getT('language_phrase'), 'lp', array(				
					"lp.language_id = '" . Phpfox_Locale::instance()->getLangId() . "'",
					"AND lp.var_name = s.phrase_var_name"
				)
			)			
			->where('s.setting_id = ' . (int) $iId)
			->execute('getSlaveRow');
			
		if (!$aSetting['setting_id'])
		{
			return false;	
		}			
		
		$aSetting['value'] = $aSetting['value_actual'];
		$aSetting['type'] = $aSetting['type_id'];

		if (!empty($aSetting['language_var_name']))
		{
			$aParts = explode('</title><info>', $aSetting['language_var_name']);			
			$aSetting['title'] = str_replace('<title>', '', $aParts[0]);
			$aSetting['info'] = str_replace(array("\n", '</info>'), array("<br />", ''), $aParts[1]);			
		}			
				
		return $aSetting;
	}
    
    /**
     * @param string $sVarName
     *
     * @return bool
     */
	public function isSetting($sVarName)
	{
	    $sCacheId = $this->cache()->set('admincp_setting_' . md5($sVarName));
        if (!$aRow = $this->cache()->get($sCacheId)) {
            $aRow = $this->database()
                ->select('setting.var_name')
                ->from($this->_sTable, 'setting')
                ->where("setting.var_name = '" . $this->database()
                        ->escape($sVarName) . "'")
                ->execute('getSlaveRow');
    
            if (!isset($aRow['var_name'])) {
                return false;
            }
            $this->cache()->save($sCacheId, $aRow);
        }
		return $aRow['var_name'];
	}
    
    /**
     * @param array $aCond
     *
     * @return array
     */
	public function get($aCond = array())
	{
	    $sCacheId = $this->cache()->set('admincp_setting_' . md5(serialize($aCond)));
        if (!$aRows = $this->cache()->get($sCacheId)) {
            if (PHPFOX_DEBUG) {
                $this->database()
                    ->select('lp2.text AS language_var_name, ')
                    ->leftJoin(Phpfox::getT('setting_group'), 'sg', 'sg.group_id = setting.group_id')
                    ->leftJoin(Phpfox::getT('language_phrase'), 'lp2', [
                            "lp2.language_id = '" . Phpfox_Locale::instance()
                                ->getLangId() . "'",
                            "AND lp2.var_name = sg.var_name"
                        ]);
            }
    
            $aRows = $this->database()
                ->select("setting.*, language_phrase.text AS title")
                ->from($this->_sTable, 'setting')
                ->join(Phpfox::getT('module'), 'm', 'm.module_id = setting.module_id AND m.is_active = 1')
                ->leftJoin(Phpfox::getT('language_phrase'), 'language_phrase', [
                        "language_phrase.language_id = '" . Phpfox_Locale::instance()
                            ->getLangId() . "'",
                        "AND language_phrase.var_name = setting.phrase_var_name"
                    ])
                ->where($aCond)
                ->group('setting.setting_id', true)
                ->order("setting.ordering ASC")
                ->execute('getSlaveRows');
    
            // Load all fonts used for CAPTCHA
            $sFontDir = Phpfox::getParam('core.dir_static') . 'image' . PHPFOX_DS . 'font' . PHPFOX_DS;
            $aFonts = [];
            $hDir = opendir($sFontDir);
            while ($sFile = readdir($hDir)) {
                if (!preg_match("/ttf/i", substr($sFile, -3))) {
                    continue;
                }
        
                $aFonts[] = $sFile;
            }
            closedir($hDir);
    
            // Load all the editors that are valid
            $aTimezones = Core_Service_Core::instance()->getTimeZones();
    
            $aNotAllowedToEdit = [];
            foreach (Phpfox_Setting::instance()->override as $key => $value) {
                $aNotAllowedToEdit[] = $key;
            }
    
            $aCacheSetting = [];
            foreach ($aRows as $iKey => $aRow) {
                if (isset($aCacheSetting[$aRow['var_name']])) {
                    unset($aRows[$iKey]);
            
                    continue;
                }
        
                if (in_array($aRow['module_id'] . '.' . $aRow['var_name'], $aNotAllowedToEdit) || in_array($aRow['module_id'] . '.' . $aRow['var_name'], Phpfox_Setting::instance()->hide)) {
                    unset($aRows[$iKey]);
            
                    continue;
                }
                if (in_array($aRow['module_id'] . '.' . $aRow['var_name'], $this->_aPasswordSettings)) {
                    $aRows[$iKey]['type_id'] = 'password';
                }
                $aCacheSetting[$aRow['var_name']] = true;
        
                if (!empty($aRow['language_var_name'])) {
                    $aRow['language_var_name'] = htmlspecialchars_decode($aRow['language_var_name']);
                    $aParts = explode('</title><info>', $aRow['language_var_name']);
                    $aRows[$iKey]['group_title'] = str_replace('<title>', '', $aParts[0]);
                }
        
                if ($aRow['type_id'] == 'drop') {
                    $aArray = unserialize($aRow['value_actual']);
                    $aRows[$iKey]['values'] = $aArray;
                    $aRows[$iKey]['value_actual'] = implode(',', $aRows[$iKey]['values']['values']);
                }
        
                if ($aRow['type_id'] == 'array') {
                    if (!empty($aRow['value_actual'])) {
                        $aRow['value_actual'] = preg_replace_callback("/s:(.*):\"(.*?)\";/is", function ($matches) {
                            return "s:" . strlen($matches[2]) . ":\"{$matches[2]}\";";
                    
                        }, $aRow['value_actual']);
                
                        eval("\$aRows[\$iKey]['value_actual'] = " . unserialize($aRow['value_actual']) . "");
                    }
                }
        
                if (!empty($aRow['title'])) {
                    $aRow['title'] = htmlspecialchars_decode($aRow['title']);
                    $aParts = explode('</title><info>', $aRow['title']);
                } else {
                    if (!empty($aRow['phrase_var_name']) && !empty($aRow['module_id'])) {
                
                        $aParts = explode('</title><info>', _p($aRow['phrase_var_name']));
                    }
                }
        
                if (isset($aParts[0])) {
                    $aRows[$iKey]['setting_title'] = (isset($aParts[0]) ? str_replace('<title>', '', $aParts[0]) : '');
                    if (isset($aParts[1])) {
                        $aParts[1] = Phpfox::getLib('parse.bbcode')
                            ->preParse($aParts[1]);
                        $aParts[1] = Phpfox::getLib('parse.bbcode')
                            ->parse($aParts[1]);
                    }
                    $aRows[$iKey]['setting_info'] = (isset($aParts[1]) ? str_replace([
                        "\n",
                        '</info>'
                    ], [
                        "<br />",
                        ''
                    ], $aParts[1]) : '');
                    if ($aRows[$iKey]['setting_info']) {
                        $aRows[$iKey]['setting_info'] = preg_replace("/<setting>([a-z\._]+)<\/setting>/i", "<a href=\"" . Phpfox_Url::instance()
                                ->makeUrl('admincp', [
                                    'setting',
                                    'search',
                                    'var' => '$1'
                                ]) . "\">$1</a>", $aRows[$iKey]['setting_info']);
                        $aRows[$iKey]['setting_info'] = preg_replace("/\{url link\='(.*?)'\}/is", "" . Phpfox_Url::instance()
                                ->makeUrl('$1') . "", $aRows[$iKey]['setting_info']);
                    }
                }
        
                unset($aRows[$iKey]['title']);
        
                if ($aRow['var_name'] == 'on_signup_new_friend' || ($aRow['var_name'] == 'admin_in_charge_of_page_claims')) {
                    $aUserArray = [];
                    $aUsers = $this->database()
                        ->select('user_id, full_name')
                        ->from(Phpfox::getT('user'))
                        ->where('user_group_id = ' . ADMIN_USER_ID)
                        ->execute('getSlaveRows');
                    $aUserArray[0] = _p('none');
                    foreach ($aUsers as $aUser) {
                        $aUserArray[$aUser['user_id']] = $aUser['full_name'];
                    }
                    $aRows[$iKey]['type_id'] = 'drop_with_key';
                    $aRows[$iKey]['values'] = $aUserArray;
                }
        
                if ($aRow['var_name'] == 'captcha_font') {
                    $aRows[$iKey]['type_id'] = 'drop';
                    $aRows[$iKey]['values'] = [
                        'default' => $aRow['value_actual'],
                        'values'  => $aFonts
                    ];
                    $aRows[$iKey]['value_actual'] = implode(',', $aFonts);
                }
        
                if ($aRow['var_name'] == 'default_time_zone_offset') {
                    $aRows[$iKey]['type_id'] = 'drop_with_key';
                    $aRows[$iKey]['values'] = $aTimezones;
                }
        
                if ($aRow['var_name'] == 'ip_check') {
                    $aIpCheck = [
                        '0' => '255.255.255.255',
                        '1' => '255.255.255.0',
                        '2' => '255.255.0.0'
                    ];
                    $aRows[$iKey]['type_id'] = 'drop_with_key';
                    $aRows[$iKey]['values'] = $aIpCheck;
                }
        
                if ($aRow['var_name'] == 'ftp_password') {
                    $aRows[$iKey]['value_actual'] = substr_replace(base64_decode(base64_decode($aRow['value_actual'])), '', -32);
                }
        
                if ($aRow['var_name'] == 'points_conversion_rate') {
                    $aValueActuals = [];
                    if (!empty($aRow['value_actual'])) {
                        $aValueActuals = json_decode($aRow['value_actual'], true);
                    }
                    $aCurrencies = Core_Service_Currency_Currency::instance()->get();
                    $aDisplayValues = [];
                    foreach ($aCurrencies as $sCurrencyKey => $aCurrencyValue) {
                        $aDisplayValues[$sCurrencyKey] = (isset($aValueActuals[$sCurrencyKey]) ? $aValueActuals[$sCurrencyKey] : '');
                    }
            
                    $aRows[$iKey]['type_id'] = 'multi_text';
                    $aRows[$iKey]['values'] = $aDisplayValues;
                }
            }
    
            (($sPlugin = Phpfox_Plugin::get('admincp.service_setting_setting_get')) ? eval($sPlugin) : false);
            $this->cache()->set($sCacheId, $aRows);
        }
        return $aRows;
    }
    
    /**
     * @param string      $sProductId
     * @param null|string $sModuleId
     * @param bool        $bCore
     *
     * @return bool
     */
    public function export($sProductId, $sModuleId = null, $bCore = false)
	{
		$aWhere = array();
		$aWhere[] = "setting.product_id = '" . $sProductId . "'";
		if ($sModuleId !== null)
		{
			$aWhere[] = " AND setting.module_id = '" . $sModuleId . "'";
		}
		
		$aRows = $this->database()->select('setting.*, product.title AS product_name, m.module_id AS module_name, setting_group.group_id AS group_name')
			->from($this->_sTable, 'setting')
			->leftJoin(Phpfox::getT('product'), 'product', 'product.product_id = setting.product_id')
			->leftJoin(Phpfox::getT('module'), 'm', 'm.module_id = setting.module_id')
			->leftJoin(Phpfox::getT('setting_group'), 'setting_group', 'setting_group.group_id = setting.group_id')
			->where($aWhere)
			->execute('getSlaveRows');
        
        if (!isset($aRows[0]['product_name'])) {
            return Phpfox_Error::set(_p('product_does_not_have_any_settings'));
        }
        
        if (!count($aRows)) {
            return false;
        }
		
		$oXmlBuilder = Phpfox::getLib('xml.builder');
		$oXmlBuilder->addGroup('settings');
			
		$aCache = array();
		foreach ($aRows as $aSetting)
		{
			if (isset($aCache[$aSetting['var_name']]))
			{
				continue;
			}
			$aCache[$aSetting['var_name']] = $aSetting['var_name'];

			$aSetting[($bCore ? 'value_default' : 'value_actual')] = str_replace("\r\n", "\n", $aSetting[($bCore ? 'value_default' : 'value_actual')]);
			$oXmlBuilder->addTag('setting', $aSetting[($bCore ? 'value_default' : 'value_actual')], array(
					'group' => $aSetting['group_name'],
					'module_id' => $aSetting['module_name'],
					'is_hidden' => $aSetting['is_hidden'],
					'type' => $aSetting['type_id'],
					'var_name' => $aSetting['var_name'],
					'phrase_var_name' => $aSetting['phrase_var_name'],
					'ordering' => $aSetting['ordering'],
					'version_id' => $aSetting['version_id']
				)
			);			
		}	
		$oXmlBuilder->closeGroup();
				
		return true;
	}
    
    /**
     * @param string      $sProductId
     * @param null|string $sModuleId
     *
     * @return bool
     */
	public function exportGroup($sProductId, $sModuleId = null)
	{
		$aSql = array();
		if ($sModuleId !== null)
		{
			$aSql[] = "setting_group.module_id = '" . $sModuleId . "' AND";
		}
		$aSql[] = "setting_group.product_id = '" . $sProductId . "'";
		
		$aRows = $this->database()->select('setting_group.*, product.title AS product_name')
			->from(Phpfox::getT('setting_group'), 'setting_group')
			->leftJoin(Phpfox::getT('product'), 'product', 'product.product_id = setting_group.product_id')
			->where($aSql)
			->execute('getSlaveRows');
        
        if (!isset($aRows[0]['product_name'])) {
            return Phpfox_Error::set(_p('product_does_not_have_any_settings'));
        }
        
        if (!count($aRows)) {
            return false;
        }
		
		$oXmlBuilder = Phpfox::getLib('xml.builder');
		$oXmlBuilder->addGroup('setting_groups');
			
		$aCache = array();
		foreach ($aRows as $aSetting)
		{
            if (isset($aCache[$aSetting['var_name']])) {
                continue;
            }
			$aCache[$aSetting['var_name']] = $aSetting['var_name'];			
			$oXmlBuilder->addTag('name', $aSetting['group_id'], array(
					'module_id' => $aSetting['module_id'],
					'version_id' => $aSetting['version_id'],
					'var_name' => $aSetting['var_name']
				)
			);			
		}	
		$oXmlBuilder->closeGroup();
				
		return true;
	}
    
    /**
     * @param string $module
     *
     * @return bool
     */
	public function moduleHasSettings($module) {
		$total = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('setting'))
			->where(['module_id' => $module])
			->execute('getSlaveField');

		return ($total ? true : false);
	}
    
    /**
     * @param array $aCond
     *
     * @return array
     */
	public function search($aCond = array())
	{
	    $sCacheId = $this->cache()->set('admincp_setting_' . md5(serialize($aCond)));
        if (!$aRows = $this->cache()->get($sCacheId)) {
            (($sPlugin = Phpfox_Plugin::get('admincp.service_setting_setting_search')) ? eval($sPlugin) : false);
    
            $aRows = $this->database()
                ->select('setting.*')
                ->from($this->_sTable, 'setting')
                ->where($aCond)
                ->execute('getSlaveRows');
            $this->cache()->save($sCacheId, $aRows);
        }
		return $aRows;	
	}
    
    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod    is the name of the method
     * @param array  $aArguments is the array of arguments of being passed
     *
     * @return null
     */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
        if ($sPlugin = Phpfox_Plugin::get('admincp.service_setting_setting___call')) {
            eval($sPlugin);
            return null;
        }
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
}