<?php

namespace Core;

use Phpfox_Cache;
use Phpfox_Locale;
use Phpfox;
use Phpfox_Url;
use Phpfox_Parse_Input;

class Phrase
{
    /**
     * stored all phrases of our site.
     *
     * @var array
     */
    private $_aAllPhrases = [];

    /**
     * stored all phrase of Apps (included core) registered in json files.
     *
     * @var null|array
     */
    private $_registeredPhrase = null;

    /**
     * Remove phrases cached
     */
    public function clearCache()
    {
        $this->_aAllPhrases = [];
        //This cache use in many process. For safety, we delete it in the end of process
        register_shutdown_function(function () {
            Phpfox_Cache::instance()->remove('language', 'substr');
            Phpfox_Cache::instance()->remove('locale', 'substr');
        });
    }

    /**
     * Check a Hash Name is defined.
     *
     * @param string $sHash
     *
     * @return bool
     */
    private function getRegisteredPhrase($sHash)
    {
        if (!is_array($this->_registeredPhrase) || count($this->_registeredPhrase)) {
            $sCacheId = Phpfox_Cache::instance()->set('language_app_json_data');
            if (!$aRegisteredPhrases = Phpfox_Cache::instance()->get($sCacheId)) {
                $aRegisteredPhrases = [];
                $aApps = (new \Core\App())->all();
                foreach ($aApps as $aApp) {
                    $filePath = $aApp->path . 'phrase.json';
                    if (file_exists($filePath)) {
                        $appPhrase = json_decode(file_get_contents($aApp->path . 'phrase.json'), true);
                        if (is_array($appPhrase)) {
                            $aRegisteredPhrases = array_merge($aRegisteredPhrases, $appPhrase);
                        }
                    }
                }
                foreach ($aRegisteredPhrases as $sKey => $aValue) {
                    if (is_array($aValue)) {
                        if (isset($aValue['en']) && !empty($aValue['en'])) {
                            $aRegisteredPhrases[$sKey] = $aValue;
                        } else {
                            $sNewKey = 'app_' . md5($sKey);
                            $aValue['en'] = $sKey;
                            $aRegisteredPhrases[$sNewKey] = $aValue;
                        }
                    } else if (empty($aValue)) {
                        $sNewKey = 'app_' . md5($sKey);
                        $aRegisteredPhrases[$sNewKey] = $sKey;
                        unset($aRegisteredPhrases[$sKey]);
                    } else {
                        $aRegisteredPhrases[$sKey] = $aValue;
                    }
                }
                Phpfox_Cache::instance()->save($sCacheId, $aRegisteredPhrases);
            }
            $this->_registeredPhrase = $aRegisteredPhrases;
        }
        return (isset($this->_registeredPhrase[$sHash])) ? $this->_registeredPhrase[$sHash] : false;
    }

    private function isValidLanguageId($sLanguageId)
    {
        $sCacheId = Phpfox_Cache::instance()->set('language_id');
        if (!$aLanguage = Phpfox_Cache::instance()->get($sCacheId)) {
            $aLanguageData = db()->select('*')
                ->from(':language')
                ->execute('getSlaveRows');
            $aLanguage = [];
            foreach ($aLanguageData as $aData) {
                $aLanguage[$aData['language_id']] = $aData;
            }
            Phpfox_Cache::instance()->save($sCacheId, $aLanguage);
        }
        if (isset($aLanguage[$sLanguageId])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get all phrases of a language
     *
     * @param string $sLanguageId is language_id
     * @param bool $bForce if true, ignore cache and get from database
     *
     * @return array of all phrases
     */
    private function getAllPhrases($sLanguageId = 'en', $bForce = false)
    {
        //Check is a valid language_id
        if (!$this->isValidLanguageId($sLanguageId)) {
            $sLanguageId = 'en';
        }
        $aCoreModule = [
            'ad',
            'admincp',
            'announcement',
            'api',
            'attachment',
            'ban',
            'blog',
            'captcha',
            'comment',
            'core',
            'custom',
            'egift',
            'error',
            'feed',
            'forum',
            'friend',
            'invite',
            'language',
            'like',
            'link',
            'log',
            'mail',
            'marketplace',
            'like',
            'music',
            'newsletter',
            'notification',
            'page',
            'pages',
            'photo',
            'poke',
            'poll',
            'privacy',
            'profile',
            'quiz',
            'report',
            'request',
            'rss',
            'search',
            'subscribe',
            'tag',
            'theme',
            'track',
            'user',
        ];
        if ($bForce || !isset($this->_aAllPhrases[$sLanguageId])) {
            $sCacheAllPhrase = Phpfox_Cache::instance()->set('language_phrase_all_' . $sLanguageId);
            if ($bForce || !$aAllPhrase = Phpfox_Cache::instance()->get($sCacheAllPhrase)) {
                $aGetPhrases = db()->select('*')
                    ->from(':language_phrase')
                    ->where(['language_id' => $sLanguageId])
                    ->execute('getSlaveRows');
                $aAllPhrase = [];
                $aArrayMerge = [];
                foreach ($aGetPhrases as $aPhrase) {
                    if (isset($aPhrase['module_id']) && Phpfox::isModule($aPhrase['module_id']) && !in_array($aPhrase['module_id'], $aCoreModule)) {
                        $aAllPhrase[$aPhrase['module_id'] . '.' . $aPhrase['var_name']] = $aPhrase['text'];
                        $aArrayMerge[$aPhrase['var_name']] = $aPhrase['text'];
                    } else {
                        $aAllPhrase[$aPhrase['var_name']] = $aPhrase['text'];
                    }
                }
                $aAllPhrase = array_merge($aArrayMerge, $aAllPhrase);
                Phpfox_Cache::instance()->save($sCacheAllPhrase, $aAllPhrase);
            }
            $this->_aAllPhrases[$sLanguageId] = $aAllPhrase;
        }
        return $this->_aAllPhrases[$sLanguageId];
    }

    /**
     * Get phrase name
     *
     * @param null|array $args
     *
     * @return string
     */
    public function get($args = null)
    {
        $phrase = $args[0];

        if (isset($args[2]) && !empty($args[2])) {
            $language_id = $args[2];
        } else {
            $language_id = Phpfox_Locale::instance()->getLangId();
        }
        if (isset($args[1])) {
            $params = $args[1];
            if (isset($params['user'])) {

                if (!is_array($params['user'])) {
                    error('The key "user" needs to be an array of the users details.');
                }

                $sUserPrefix = (isset($params['user_prefix']) ? $params['user_prefix'] : '');

                $aUser = $params['user'];
                $aUser['user_link'] = '<a href="' . Phpfox_Url::instance()
                        ->makeUrl($aUser[$sUserPrefix . 'user_name']) . '">' . Phpfox::getLib('parse.output')
                        ->clean($aUser[$sUserPrefix . 'full_name']) . '</a>';
                unset($params['user']);
                $params = array_merge($params, $aUser);
            }
        } else {
            $params = [];
        }
        //Support legacy phrase. End support from 4.7.0
        if (!$this->isPhrase($phrase)) {
            $phrase = $this->correctLegacyPhrase($phrase);
        }
        //End support legacy
        if ($this->isPhrase($phrase)) {
            $hash = $phrase;
        } else {
            $hash = 'app_' . md5($phrase);
        }
        $aAllPhrases = $this->getAllPhrases($language_id);
        if (isset($aAllPhrases[$hash])) {
            $phrase = $aAllPhrases[$hash];
        } else {//New phrase or phrase not exist
            if (!defined('PHPFOX_INSTALLER') && !PHPFOX_IS_TECHIE) {
                if (defined('PHPFOX_DEBUG_PHRASE') && PHPFOX_DEBUG_PHRASE) {
                    error('Unable to load phrase: ' . $phrase);
                }
            } else if ($textPhrase = $this->getRegisteredPhrase($hash)) {
                if (is_array($textPhrase)) {
                    if (isset($textPhrase[$language_id])) {
                        $textPhrase = $textPhrase[$language_id];
                    } else {
                        $textPhrase = $textPhrase['en'];
                    }
                }

                //Check phrase exist before insert
                $iCnt = db()->select('COUNT(*)')
                    ->from(':language_phrase')
                    ->where(['module_id' => 'language', 'language_id' => $language_id, 'var_name' => $hash])
                    ->execute('getSlaveField');
                if ($iCnt == 0) {
                    db()->insert(':language_phrase', [
                        'language_id' => $language_id,
                        'var_name' => $hash,
                        'text' => Phpfox_Parse_Input::instance()->clean($textPhrase),
                        'text_default' => Phpfox_Parse_Input::instance()->clean($textPhrase),
                        'added' => moment()->now()
                    ]);
                }
                $this->clearCache();
                $aAllPhrases = $this->getAllPhrases($language_id);
                if (!isset($aAllPhrases[$hash]) && defined('PHPFOX_DEBUG_PHRASE') && PHPFOX_DEBUG_PHRASE) {
                    error('Unable to load phrase: ' . $phrase);
                }
                $phrase = $aAllPhrases[$hash];
            } elseif (defined('PHPFOX_DEBUG_PHRASE') && PHPFOX_DEBUG_PHRASE) {
                return error('Unable to load phrase: ' . $phrase);
            }
        }
        //process phrase before return
        if (count($params)) {
            $aFind = [];
            $aReplace = [];
            foreach ($params as $key => $value) {
                if (is_array($value)) {
                    continue;
                }
                $phrase = str_replace('{{ ' . $key . ' }}', $value, $phrase);
                $aFind[] = '{' . $key . '}';
                $aReplace[] = '' . $value . '';
            }
            if (count($aFind)) {
                $phrase = str_replace($aFind, $aReplace, $phrase);
            }
        }

        if (Phpfox::getParam('language.lang_pack_helper')) {
            $phrase = '{' . $phrase . '}';
        }
        return htmlspecialchars_decode($phrase);
    }

    /**
     * find all phrases defined in phrase.json file
     *
     * @return array
     */
    public function findDefinedPhrasesFromJSon()
    {
        $aAllPhrases = [];
        $aApps = Lib::app()->all();

        // Get all defined phrases
        foreach ($aApps as $aApp) {
            $filePath = $aApp->path . 'phrase.json';
            if (file_exists($filePath)) {
                $appPhrase = json_decode(file_get_contents($aApp->path . 'phrase.json'), true);
                if (is_array($appPhrase)) {
                    $aAllPhrases = array_merge($aAllPhrases, $appPhrase);
                }
            }
        }

        //Get all defined phrases from modules
        $aDirs = scandir(PHPFOX_DIR . "module");
        foreach ($aDirs as $sDir) {
            $jsonFile = PHPFOX_DIR . "module" . PHPFOX_DS . $sDir . PHPFOX_DS . 'phrase.json';
            if (file_exists($jsonFile)) {
                $aAllPhrases = array_merge($aAllPhrases, json_decode(file_get_contents($jsonFile), true));
            }
        }

        return $aAllPhrases;
    }

    /**
     * find all phrase (3rd party + modules, app ) then add to database.
     *
     * @param string $sLanguageId
     */
    public function findMissingPhrases($sLanguageId = 'en')
    {

        $aAllPhrases = $this->findDefinedPhrasesFromJSon();

        $aDefaultPhrases = db()->select('var_name, text')
            ->from(':language_phrase')
            ->where(['language_id' => 'en'])
            ->execute('getSlaveRows');

        foreach ($aAllPhrases as $sKey => $aValue) {
            if (is_array($aValue)) {
                if (isset($aValue[$sLanguageId]) && !empty($aValue[$sLanguageId])) {
                    $aAllPhrases[$sKey] = $aValue[$sLanguageId];
                } else if (isset($aValue['en']) && !empty($aValue['en'])) {
                    $aAllPhrases[$sKey] = $aValue['en'];
                } else {
                    $sNewKey = 'app_' . md5($sKey);
                    $aAllPhrases[$sNewKey] = $sKey;
                }
            } else if (empty($aValue)) {
                $sNewKey = 'app_' . md5($sKey);
                $aAllPhrases[$sNewKey] = $sKey;
                unset($aAllPhrases[$sKey]);
            } else {
                $aAllPhrases[$sKey] = $aValue;
            }
        }

        foreach ($aDefaultPhrases as $row) {
            $sVarName = $row['var_name'];
            if (!isset($aAllPhrases[$sVarName])) {
                $aAllPhrases[$sVarName] = $row['text'];
            }
        }

        $aExistsPhrase = db()->select('var_name')
            ->from(':language_phrase')
            ->where(['language_id' => $sLanguageId])
            ->execute('getSlaveRows');

        foreach ($aExistsPhrase as $row) {
            unset($aAllPhrases[$row['var_name']]);
        }

        foreach ($aAllPhrases as $sKey => $sText) {
            db()->insert(':language_phrase', [
                'language_id' => $sLanguageId,
                'var_name' => $sKey,
                'text' => $sText,
                'text_default' => $sText,
                'added' => PHPFOX_TIME
            ]);
        }

        $this->clearCache();
    }

    /**
     * Check is a var_name is phrase or not
     *
     * @param $var_name
     *
     * @return bool
     */
    public function isPhrase($var_name)
    {
        $aAllPhrases = $this->getAllPhrases();
        if (isset($aAllPhrases[$var_name])) {
            return true;
        } else {
            //var_name don't have uppercase
            if ((strtolower($var_name) != $var_name) || strpos($var_name, ' ') !== false) {
                return false;
            }
            $iCnt = db()->select('COUNT(*)')
                ->from(':language_phrase')
                ->where(['var_name' => Phpfox_Parse_Input::instance()->clean($var_name)])
                ->execute('getSlaveField');
            if ($iCnt) {
                $this->clearCache();//Cache not up to date
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Add new phrase, if first param is array => add multiple phrases, if it string, it is var_name => add a single phrase
     *
     * @param string|array $sVarName
     * @param string|array $sValue
     *
     * @return bool
     */
    public function addPhrase($sVarName, $sValue = '')
    {
        if (is_array($sVarName)) {
            $aAllPhrases = $sVarName;
        } elseif (!empty($sValue)) {
            $aAllPhrases = [
                $sVarName => $sValue
            ];
        } else {
            return false;
        }

        foreach ($aAllPhrases as $sKey => $aValue) {
            if (is_array($aValue)) {
                if (isset($aValue['en']) && !empty($aValue['en'])) {
                    $aAllPhrases[$sKey] = $aValue;
                } else {
                    $sNewKey = 'app_' . md5($sKey);
                    $aValue['en'] = $sKey;
                    $aAllPhrases[$sNewKey] = $aValue;
                }
            } else if (empty($aValue)) {
                $sNewKey = 'app_' . md5($sKey);
                $aAllPhrases[$sNewKey] = ['en' => $sKey];
                unset($aAllPhrases[$sKey]);
            } else {
                $aAllPhrases[$sKey] = ['en' => $aValue];
            }
        }

        $aLanguages = db()->select('*')
            ->from(':language')
            ->executeRows();

        foreach ($aLanguages as $aLanguage) {
            //Get all phrases from each language package
            $aGetPhrases = db()->select('*')
                ->from(':language_phrase')
                ->where(['language_id' => $aLanguage['language_id']])
                ->executeRows();
            $aCheckPhrases = [];
            foreach ($aGetPhrases as $aGetPhrase) {
                if (isset($aGetPhrase['module_id']) && Phpfox::isModule($aGetPhrase['module_id'])) {
                    //Do not remove duplicate phrase from module, it still uses module_id
                    continue;
                }
                if (isset($aCheckPhrases[$aGetPhrase['var_name']])) {
                    //Remove duplicate phrase
                    db()->delete(':language_phrase', ['phrase_id' => (int)$aGetPhrase['phrase_id']]);
                } else {
                    $aCheckPhrases[$aGetPhrase['var_name']] = $aGetPhrase;
                }
            }

            foreach ($aAllPhrases as $sKey => $aPhrase) {
                $sNewText = (isset($aPhrase[$aLanguage['language_id']])) ? $aPhrase[$aLanguage['language_id']] : $aPhrase['en'];
                if (isset($aCheckPhrases[$sKey])) {//Old phrase Exist
                    //If language_id = en, and the phrase is not change => update to new phrase
                    if (($aLanguage['language_id'] == 'en') && ($aCheckPhrases[$sKey]['text'] == $aCheckPhrases[$sKey]['text_default'])) {
                        db()->update(':language_phrase', [
                            'text' => $sNewText,
                            'text_default' => $sNewText
                        ], [
                            'language_id' => $aLanguage['language_id'],
                            'var_name' => $sKey
                        ]);
                    }
                } else {//This is new phrase
                    db()->insert(':language_phrase', [
                        'language_id' => $aLanguage['language_id'],
                        'var_name' => $sKey,
                        'text' => $sNewText,
                        'text_default' => $sNewText,
                        'added' => PHPFOX_TIME
                    ]);
                }
            }
        }
        $this->clearCache();
        return true;
    }

    /**
     * Correct legacy phrase.
     * Example core.are_you_sure become  are_you_sure
     * are_you_sure become are_you_sure
     *
     * @param string $sPhrase
     *
     * @return string mixed
     */
    public function correctLegacyPhrase($sPhrase)
    {
        $aParts = explode('.', $sPhrase);

        if (isset($aParts[2])) {
            return $sPhrase;
        }
        if (isset($aParts[1]) && !empty($aParts[1])) {
            if (strpos($aParts[1], ' ') !== false) {
                return $sPhrase;
            }
            if (Phpfox::isModule($aParts[0])) {
                return $aParts[1];
            }
        }
        return $sPhrase;
    }
}