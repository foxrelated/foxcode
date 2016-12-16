<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Input Parser
 * Class is used to parse all incoming data sent by end users via HTML forms.
 * Goal is to remove any "evil" data and convert it into safe HTML. This allows
 * us to store the original data and a safe version.
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: input.class.php 6978 2013-12-09 14:40:17Z Fern $
 */
class Phpfox_Parse_Input
{
	/**
	 * Invalid events we need to remove.
	 *
	 * @var array
	 */
	private $_aEvilEvents = array(
		'onActivate',
        'onAfterPrint',
		'onBeforePrint',
        'onAfterUpdate',
        'onBeforeUpdate',
        'onErrorUpdate',
        'onAbort',
        'onBeforeDeactivate',
        'onDeactivate',
        'onBeforeCopy',
        'onBeforeCut',
        'onBeforeEditFocus',
        'onBeforePaste',
        'onBeforeUnload',
        'onBlur',
        'onBounce',
        'onChange',
        'onClick',
        'onControlSelect',
        'onCopy',
        'onCut',
        'onDblClick',
        'onDrag',
        'onDragEnter',
        'onDragLeave',
        'onDragOver',
        'onDragStart',
        'onDrop',
        'onFilterChange',
        'onDragDrop',
        'onError',
        'onFilterChange',
        'onFinish',
        'onFocus',
        'onHelp',
        'onKeyDown',
        'onKeyPress',
        'onKeyUp',
        'onLoad',
        'OnLoseCapture',
        'onMouseDown',
        'onMouseEnter',
        'onMouseLeave',
        'onMouseMove',
        'onMouseOut',
        'onMouseOver',
        'onMouseUp',
        'onMove',
        'onPaste',
        'onPropertyChange',
        'onReadyStateChange',
        'onReset',
        'onResize',
        'onResizeEnd',
        'onResizeStart',
        'onScroll',
        'onSelectStart',
        'onSelect',
        'onSelectionChange',
        'onStart',
        'onStop',
        'onSubmit',
        'onUnload',
        'class',
        'style'
	);

	/**
	 * Invalid CSS properties we need to remove.
	 *
	 * @var array
	 */
	private $_aEvilCss = array(
		'position',
		'top',
		'left',
		'background',
		'background-image',
		'background-color',
		'width',
		'height',
		'behavior'
	);

	/**
	 * REGEX used to get params from within {}
	 *
	 * @var string
	 */
	private $_sDbQstrRegexp = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"';

	/**
	 * REGEX used to get params from within {}
	 *
	 * @var string
	 */	
	private $_sSiQstrRegexp = '\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'';
	
	/**
	 * Store text we are parsing temp.
	 *
	 * @var string
	 */
	private $_sText = '';
	
	/**
	 * HTML tags we allow.
	 *
	 * @var array
	 */
	private $_aAllowedTags = array();

	/**
	 * Class constructor. Prepare regex for usgae a little later on in the script.
	 *
	 */
	public function __construct()
	{
		(($sPlugin = Phpfox_Plugin::get('parse_input_construct')) ? eval($sPlugin) : null);
		
		$this->_sQstrRegexp = '(?:' . $this->_sDbQstrRegexp . '|' . $this->_sSiQstrRegexp . ')';
	}

	/**
	 * @return Phpfox_Parse_Input
	 */
	public static function instance() {
		return Phpfox::getLib('parse.input');
	}

	/**
	 * Parse and clean a string. We mainly use this for a title of an item, which
	 * does not allow any HTML. It can also be used to shorten a string bassed on 
	 * the numerical value passed by the 2nd argument.
	 *
	 * @param string $sTxt Text to parse.
	 * @param int $iShorten (Optional) Define how short you want the string to be.
	 * @return string Returns the new parsed string.
	 */
	public function clean($sTxt, $iShorten = null)
	{
        if (!defined('PHPFOX_INSTALLER') && Phpfox::getParam('language.no_string_restriction')) {
            $iShorten = null;
        }

		$sTxt = Phpfox::getLib('parse.output')->htmlspecialchars($sTxt);
        
        // Parse for language package
        if ($iShorten !== null) {
            $sTxt = $this->_shorten($sTxt, $iShorten);
        }
		
		return $sTxt;
	}

	/**
	 * Shortens a string respecting non english characters
	 * @param string $sTxt string to shorten
	 * @param int $iLetters how many characters must the resulting string have
	 * @return string shortened string
	 */
	private function _shorten($sTxt, $iLetters)
	{
	    $sText = mb_substr($sTxt, 0, $iLetters);
        if (strlen($sText) <= $iLetters){
            return $sText;
        }
        $iLowLetter = 1;
        $iHighLetter = $iLetters - 1;
        $iSafeCheck = 0;
        while (1) {
            $iSafeCheck++;
            $iMid = floor(($iLowLetter + $iHighLetter) / 2);
            $sText = mb_substr($sTxt, 0, $iMid);
            if (abs($iLowLetter - $iHighLetter) < 2) {
                break;
            }
            if (strlen($sText) == $iLetters){
                break;
            } elseif (strlen($sText) > $iLetters){
                $iHighLetter = $iMid;
            } else {
                $iLowLetter = $iMid;
            }
            if ($iSafeCheck == 1000){
                break;
            }
        }
        if (strlen($sText) > $iLetters){
            //Can't get shorten
            return '';
        } else {
            return $sText;
        }
	}
	
	/**
	 * Parse and clean a title of an item and convert it into a URL title string.
	 * Example if you had:
	 * <code>
	 *  this is a TEST string!!!
	 * </code>
	 * It would convert it to:
	 * <code>
	 * this-is-a-test-string
	 * </code>
	 * Which, we would then use in a URL:
	 * <code>
	 * http://www.yoursite.com/this-is-a-test-string/
	 * </code>
	 *
	 * @param string $sUrls String to convert into a URL.
	 * @return string Converted URL.
	 */
	public function cleanTitle($sUrls)
	{
		$sUrls = trim(strip_tags($sUrls));
		$sUrls = $this->_utf8ToUnicode($sUrls, true);		
		$sUrls = preg_replace("/ +/", "-", $sUrls);		
		$sUrls = rawurlencode($sUrls);		
		$sUrls = str_replace(array('"', "'"), '', $sUrls);
		$sUrls = str_replace(' ', '-', $sUrls);
		$sUrls = str_replace(array('-----', '----', '---', '--'), '-', $sUrls);
		$sUrls = rtrim($sUrls, '-');
		$sUrls = ltrim($sUrls, '-');
		
		if (empty($sUrls))
		{
			$sUrls = PHPFOX_TIME;
		}
		
		$sUrls = strtolower($sUrls);

		return $sUrls;
	}
	
	/**
	 * Cleans a file name and removes any non-latin characters.
	 *
	 * @param string $sName Name of the file.
	 * @return string Clean file name.
	 */
	public function cleanFileName($sName)
	{
		$sName = preg_replace( '/ +/', '-', preg_replace('/[^0-9a-zA-Z]+/', '-', $sName));
		$sName = strtolower($sName);
		$sName = rtrim($sName, '-');
		
		return $sName;	
	}

	/**
	 * Checks if a title of the item can be used in the sites root. This is mainly used
	 * to check a persons vanity URL name. Since if a user would use the name "friend"
	 * it would cause problems when trying to visit anything related to the "friend" module
	 * as it would instead load this persons profile. With this check it makes sure that the 
	 * name or title being used is not a module, a folder in the sites root directory, rewrite rule, page
	 * or the user name isn't already in use.
	 *
	 * @param string $sTitle Title to check.
	 * @param string $sErrorMessage Error message you want to return in case there is an error.
	 * @return bool TRUE if the title is allowed otherwise FALSE.
	 */
	public function allowTitle($sTitle, $sErrorMessage)
	{
		$bIsOk = true;

		$aStaticFolders = ['_ajax', 'file', 'static', 'module', 'apps', 'Apps', 'themes', 'groups'];
		if (in_array($sTitle, $aStaticFolders)) {
			$bIsOk = false;
		}

		$hDir = opendir(PHPFOX_DIR_MODULE);
		while ($sFile = readdir($hDir))
		{
			if ($sFile == '.' || $sFile == '..')
			{
				continue;
			}

			if (strtolower($sFile) === strtolower($sTitle))
			{
				$bIsOk = false;
					
				break;
			}
		}
		closedir($hDir);

        if (in_array($sTitle, array_keys(\Core\Route::$routes))) {
            $bIsOk = false;
        }
		
		$aRewrites = Phpfox_Url::instance()->getRewrite();
		if (is_array($aRewrites) && !empty($aRewrites))
		{
			foreach ($aRewrites as $sUrl => $aRow)
			{		    
				if (strtolower($sUrl) == strtolower($sTitle) || (is_string($aRow) && strtolower($aRow) == strtolower($sTitle)) || (isset($aRow['module']) && strtolower($aRow['module']) == strtolower($sTitle)))
				{
					$bIsOk = false;
					break;
				}
			}
		}
		
		if (User_Service_User::instance()->isUser($sTitle))
		{
			$bIsOk = false;
		}	
		
		if (Phpfox::isModule('pages') && Pages_Service_Pages::instance()->isPage($sTitle))
		{
			$bIsOk = false;
		}			
		
		$hDir = opendir(PHPFOX_DIR);
		while ($sFile = readdir($hDir))
		{
			if ($sFile == '.' || $sFile == '..')
			{
				continue;
			}

			if (strtolower($sFile) === strtolower($sTitle))
			{
				$bIsOk = false;
					
				break;
			}
		}
		closedir($hDir);		

		return ($bIsOk ? true : Phpfox_Error::set($sErrorMessage));
	}

	/**
	 * Remove evil attributes from JavaScript.
	 *
	 * @param string $sTxt JavaScript code to parse.
	 * @return string Parsed JavaScript code.
	 */
	public function jsClean($sTxt)
	{
		return $this->_removeEvilAttributes($sTxt, true);
	}
	
	/**
	 * Reverse prepares strings based on what we converted with the method prepare().
	 *
	 * @see self::prepare()
	 * @param string $sTxt String to parse.
	 * @return string Parsed string.
	 */
	public function reversePrepare($sTxt)
	{
		$sTxt = html_entity_decode($sTxt);
		
		return $this->prepare($sTxt);
	}
	
	/**
	 * Prepare text strings. Used to prepare all data that can contain HTML. Not only does
	 * it protect against harmful HTML and CSS, it also has support for BBCode conversion.
	 *
	 * @param string $sTxt Text to parse.
	 * @return string Parsed string.
	 */
	public function prepare($sTxt, $bNoClean = true, $extra = [])
	{
		(($sPlugin = Phpfox_Plugin::get('parse_input_prepare')) ? eval($sPlugin) : null);

		if (isset($extra['comment'])) {
			$mentions = Phpfox_Parse_Output::instance()->mentionsRegex($sTxt);
			$link = Phpfox_Url::instance()->makeUrl('comment.view.' . $extra['comment']);
			foreach ($mentions as $user) {
				Phpfox_Mail::instance()->to(Phpfox::getUserBy('email'))
					->subject($user->name . ' mentioned you in a post.')
					->message($user->name . ' mentioned you in a post. <a href="' . $link . '">Check it out</a>')
					->send();
			}
		}

		if (isset($override) && is_callable($override)) {
			return call_user_func($override, $sTxt);
		}

		if ($bNoClean) return $sTxt;
		return Phpfox_Parse_Output::instance()->htmlspecialchars($sTxt);
	}

	/**
	 * Converts a string that contains non-latin characters into UNICODE.
	 *
	 * @see self::_utf8ToUnicode()
	 * @param string $sTxt Text to convert to UNICODE.
	 * @return string Converted text.
	 */
	public function convert($sTxt)
	{
		return $this->_utf8ToUnicode($sTxt);
	}

	/**
	 * Fixes any odd HTML, mainly dealing with HTML output from TinyMCE.
	 *
	 * @param string $sTxt Text to parse.
	 * @return string Parsed text.
	 */
	public function fixHtml($sTxt)
	{
		// Replacements done because of TinyMCE
		$sTxt = preg_replace("/\&lt;(.*?)\&gt;/si","<\\1>",$sTxt);
		$sTxt = str_replace("&quot;",'"',$sTxt);
		$sTxt = preg_replace("/ href=\"(.*?)\" mce_href=\"(.*?)\"/i", " href=\"\\2\"", $sTxt);
		$sTxt = preg_replace("/ src=\"(.*?)\" mce_src=\"(.*?)\"/i", " src=\"\\2\"", $sTxt);
		
		return $sTxt;
	}

	/**
	 * Add line breaks. Unlike the PHP version of this function it looks into not adding
	 * line breaks within HTML and PHP code.
	 *
	 * @param string $sTxt String we need to parse.
	 * @return string Parsed string with newly added breaks.
	 */
	public function addBreak($sTxt)
	{		
		$sTxt = str_replace("\n", "[pf_break]", $sTxt);		
		$sTxt = preg_replace_callback('/\{php\}(.*?)\{\/php\}/is', array(&$this, '_addBreak'), $sTxt);
		$sTxt = preg_replace_callback('/<(.*?)>/is', array(&$this, '_addBreak'), $sTxt);
		
		$sTxt = str_replace('[pf_break]', "<br class=\"pf_break\" />", $sTxt);		
		$sTxt = stripslashes($sTxt);

		return $sTxt;
	}	
	
	/**
	 * Get params within SMARTY {} tags.
	 *
	 * @param string $sTxt String we need to parse.
	 * @return array ARRAY matches are returned.
	 */
	public function getParams($sTxt)
	{
        $aResult = array();
		if (preg_match_all('/(?:' . $this->_sQstrRegexp . ' | (?>[^"\'=\s]+))+|[=]/x', $sTxt, $aMatches))
		{
			$iState = 0;
			foreach($aMatches[0] as $mValue)
			{				
				switch($iState)
				{
					case 0:
						if (is_string($mValue))
						{		
							$sName = $mValue;							
							$iState = 1;
						}
						break;
					case 1:
						if ($mValue == '=')
						{
							$iState = 2;
						}
						break;
					case 2:
						if ($mValue != '=')
						{
							$iState = 0;
							$aResult[strtolower($sName)] = $mValue;
						}
						break;
				}
				$sLastValue = $mValue;
			}		
		}
		
		return $aResult;
	}
	
	/**
	 * Removes evit attributes within HTML.
	 *
	 * @see self::_removeEvilAttributes()
	 * @param string $sTxt String to parse.
	 * @return string Parsed string.
	 */
	public function removeEvilAttributes($sTxt)
	{
		return $this->_removeEvilAttributes($sTxt);
	}
	
	/**
	 * Preparing a URL title. Will be used to replace a title "this is a TITLE" to
	 * "this-is-a-title".
	 * 
	 * Example:
	 * <code>
	 * Phpfox::getLib('parse.input')->prepareTitle('photo', $aVals['title'], 'name_url', Phpfox::getUserId(), Phpfox::getT('photo_album'));
	 * </code>
	 *
	 * @param string $sModule Module ID.
	 * @param string $sTitle Title to parse and fix.
	 * @param string $sField Database field to check if such titles already exist.
	 * @param int $iUserId User ID to check 
	 * @param string $sTable Name of the database table.
	 * @param mixed $mCondition Database WHERE condition.
	 * @param boolean $bCleanOnly Return true if you want to return the clean title without running the existing title check.
	 * @param array $bCache FALSE will force a new check, while default TRUE will cache previous checks.
	 * @return string New fixed title.
	 */
	public function prepareTitle($sModule, $sTitle, $sField, $iUserId = null, $sTable, $mCondition = null, $bCleanOnly = false, $bCache = true)
	{
		static $aTitle = array();
		static $iCacheCount = 0;
		
		if (defined('PHPFOX_INSTALLER'))
		{
			$bCache = false;
		}
		
		if ($bCache && isset($aTitle[$sTitle]))
		{
			return $aTitle[$sTitle];
		}		

		$sNewTitle = $this->cleanTitle($sTitle);

		if ($bCleanOnly)
		{
			return $sNewTitle;
		}

		if (!defined('PHPFOX_INSTALLER'))
		{
		    $sNewTitle = substr($sNewTitle, 0, Phpfox::getParam('core.crop_seo_url'));
		}

		$oDb = Phpfox_Database::instance();
		$aOlds = $oDb->select($sField . ' AS title_url')
			->from($sTable)
			->where(($mCondition === null ? $sField . ' LIKE \'%' . $oDb->escape($sNewTitle) . '%\'' : $mCondition))
			->execute('getRows');	
					
		$iTotal = 0;
		$aNumbers = array();
		$aIntNumbers = array();
		foreach ($aOlds as $aOld)
		{			
			if (preg_match("/(.*)-([0-9]+)/i", $aOld['title_url'], $aMatches))
			{
				$aIntNumbers[] = $aMatches[2];
			}
			
			if ($aOld['title_url'] === $sNewTitle)
			{
				$aNumbers[] = $sNewTitle;
			}
		}
				
		// Is this a valid module?
		if (Phpfox::isModule($sModule))
		{
			// Open the modules controller directory
			$hDir = opendir(PHPFOX_DIR_MODULE . $sModule . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS);
			while ($sFile = readdir($hDir))
			{
				if ($sFile == '.' || $sFile == '..' || $sFile == '.svn')
				{
					continue;
				}
				
				// Put the directory name within an array
				if (is_dir(PHPFOX_DIR_MODULE . $sModule . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . $sFile) && $sNewTitle === $sFile)
				{
					$aNumbers[] = $sFile;
				}
				
				// Put the file name within an array
				if (preg_match("/(.*?)\.class\.php/i", $sFile, $aMatches) && $sNewTitle === $aMatches[1])
				{
					$aNumbers[] = $aMatches[1];					
				}
			}
		}
		
		if (count($aIntNumbers))
		{
			$iTotal = max($aIntNumbers) + 1;
		}
		else
		{
			if (count($aNumbers))
			{
				arsort($aNumbers);
				$iTotal = (count($aNumbers) + 1);
			}
		}
		if (!$bCache && isset($aTitle[$sTitle]))
		{	
			$iCacheCount++;

			return $aTitle[$sTitle] . '-' . $iCacheCount;	
		}		
	
		// Do we have any titles that match and if we do add a new count after it.
		$aTitle[$sTitle] =  $sNewTitle . ($iTotal > 0 ? '-' . $iTotal : '');		
		
		return $aTitle[$sTitle];
	}	
	
	/**
	 * Removes HTML found within HTML.
	 *
	 * @see self::_stripInnerHtml()
	 * @param string $sText Text to parse.
	 * @return string Returns parsed text.
	 */
	public function stripInnerHtml($sText)
	{
		$this->_sText = $sText;
		
		preg_replace('/<(.*?)>/ise', "'<'.\$this->_stripInnerHtml('\\1').'>'", $sText);
		
		$this->_sText = strip_tags($this->_sText);
		
		return $this->_sText;
	}
	
	/**
	 * Removes HTML found within HTML.
	 *
	 * @see self::stripInnerHtml()
	 * @param string $sText Text to parse.
	 * @return string Returns parsed text.
	 */	
	private function _stripInnerHtml($sTag)
	{	
		if (substr($sTag, 0, 1) != '/')
		{
			$aPart = explode(' ', $sTag);
			$sTag = $aPart[0];
		}
		$iLength = strlen($sTag);	
		
		$sLowerText = strtolower($this->_sText);
		$aStartPos = array();
		$iCurPos = 0;
		do
		{
			$sPos = strpos($sLowerText, '<' . $sTag . '', $iCurPos);		
			if ($sPos !== false)
			{
				$aStartPos[$sPos] = 'start';
			}
	
			$iCurPos = ($sPos + $iLength + 1);		
		}
		while ($sPos !== false);
	
		if (sizeof($aStartPos) == 0)
		{
			return false;
		}
	
		$aEndPos = array();
		$iCurPos = 0;
		do
		{
			$sPos = strpos($sLowerText, '</' . $sTag . '>', $iCurPos);
			if ($sPos !== false)
			{
				$aEndPos[$sPos] = 'end';
				$iCurPos = ($sPos + $iLength + 3);
			}
		}
		while ($sPos !== false);
	
		if (sizeof($aEndPos) == 0)
		{
			return false;
		}
	
		$aPosList = $aStartPos + $aEndPos;
		ksort($aPosList);
	
		do
		{
			$aStack = array();
			$sNewText = '';
			$iSubstrPos = 0;
			foreach ($aPosList AS $sPos => $sType)
			{
				$aStacksize = sizeof($aStack);
				if ($sType == 'start')
				{
					if ($aStacksize == 0)
					{
						$sNewText .= substr($this->_sText, $iSubstrPos, $sPos - $iSubstrPos);
					}
					array_push($aStack, $sPos);
				}
				else
				{
					if ($aStacksize)
					{
						array_pop($aStack);
						$iSubstrPos = ($sPos + $iLength + 3);
					}
				}
			}
	
			$sNewText .= substr($this->_sText, $iSubstrPos);
	
			if ($aStack)
			{
				foreach ($aStack AS $sPos)
				{
					unset($aPosList[$sPos]);
				}
			}
		}
		while ($aStack);
	
		$this->_sText = $sNewText;
	}	
	
	/**
	 * Common routine we run when using the prepare() method to clean HTML to make sure it is safe.
	 *
	 * @param string $sTxt Text to parse.
	 * @return string Parsed text.
	 */
	private function _cleanHtml($sTxt)
	{		
		$this->fixHtml($sTxt);		
		
		if (!defined('PHPFOX_DO_NOT_PARSE_BREAK'))
		{
			$sTxt = str_replace("\n", '[br]', $sTxt);	
		}
		
		preg_match_all('/<\/?\w+((\s+\w+(\s*=\s*(?:".*?"|\'.*?\'|[^\'">\s]+))?)+\s*|\s*)\/?>/i', $sTxt, $aMatched);
		
		$iUnid1 = ' ' .uniqid();
		$iUnid2 = uniqid() . ' ';
		$aAllowed = Phpfox::getParam('core.allowed_html');
		$sAllowed = '';
		foreach ($aAllowed as $sVal => $iNum)
		{
			$sAllowed .= '<' . $sVal.'> </'.$sVal .'> <'.$sVal .'/> <'.$sVal .' /> ';
		}
		
		foreach ($aMatched[0] as $iKey => $sMatch)
		{
			$sTemp = str_replace($aMatched[1][$iKey], '', $sMatch);
			$sTemp = strtolower($sTemp);

			if (strpos($sAllowed, $sTemp) !== false)
			{				
				$sMatch2 = str_replace($aMatched[1][$iKey], $this->_removeEvilAttributes($aMatched[1][$iKey], true), $sMatch);
				$sNew = str_replace(array('<', '>'),array($iUnid1, $iUnid2), $sMatch2);
				$sTxt = str_replace($sMatch, $sNew, $sTxt);
				continue;
			}
			
		}
		
		$sTxt = str_replace(array('<', '>'), array('&lt;', '&gt;'), $sTxt);
		$sTxt = str_replace(array($iUnid1, $iUnid2), array('<', '>'), $sTxt);		
		
		if (!defined('PHPFOX_DO_NOT_PARSE_BREAK'))
		{
			$sTxt = str_replace('[br]', '<br />', $sTxt);
		}
		return $sTxt;
		
	}	
	
	/**
	 * Our method of PHP strip_tags().
	 *
	 * @deprecated 2.0.0rc1
	 * @see strip_tags()
	 * @param string $sTxt String to parse.
	 * @return string Parsed string.
	 */
	private function _stripTags($sTxt)
	{
		foreach ($this->_aAllowedTags as $sAllowTag)
		{
			$sAllowTag = preg_replace("/^<(.*?)>$/i", "\\1", $sAllowTag);
			$sAllowTag = str_replace(' ', '', $sAllowTag);
			if (substr($sAllowTag, -1) == '/')
			{
				$sTxt = preg_replace("/&lt;" . substr_replace($sAllowTag, '', -1) . "(.*)&gt;/ise", "''.\$this->_cacheHtml('\\1', null, '$sAllowTag', true).''", $sTxt);
			}
			else 
			{
				$sAllowTag = str_replace('/', '\/', $sAllowTag);				
				$sTxt = preg_replace("/&lt;{$sAllowTag}(.*?)&gt;(.*)&lt;\/{$sAllowTag}&gt;/ise", "''.\$this->_cacheHtml('\\1', '\\2', '$sAllowTag').''", $sTxt);
			}
		}
		
		return $sTxt;	
	}
	
	/**
	 * Part of the method _stripTags() to remove unwanted HTML tags.
	 *
	 * @deprecated 2.0.0rc1
	 * @see self::_stripTags()
	 * @param string $sAttr HTML attributes.
	 * @param string $sTxt HTML inner text.
	 * @param string $sTag HTML tag.
	 * @param bool $bClose TRUE will close the tag, FALSE will leave it open.
	 * @return string Returns fixed HTML tags with clean attributes and inner content.
	 */
	private function _cacheHtml($sAttr, $sTxt = null, $sTag, $bClose = false)
	{		
		if ($bClose)
		{
			return '<' . substr_replace($sTag, '', -1) . ' ' . stripslashes($sAttr) . '>';
		}
		else 
		{
			return '<' . $sTag . stripslashes($sAttr) . '>' . $this->_stripTags(stripslashes($sTxt)) . '</' . $sTag . '>';		
		}
	}	
	
	/**
	 * Removes all evil attributes in a string to make sure the data returned is safe to output on the site.
	 *
	 * @param string $sTxt Text we need to parse.
	 * @param bool $bCleanOnly TRUE to just clean the text, FALSE to clean and make sure HTML tags are valid.
	 * @return string Parsed string with all evil attributes removed.
	 */
    private function _removeEvilAttributes($sTxt, $bCleanOnly = false)
    {
    	$sTxt = stripslashes($sTxt);
    	$sTxt = str_replace("xhref","href",$sTxt);
    	$sTxt = str_replace("xsrc","src",$sTxt);
        $sTxt = strip_tags($sTxt);
        $sTxt = str_replace('[br]', '', $sTxt);
        
        $aParts = explode(' ', $sTxt);
        
        foreach ($this->_aEvilEvents as $sRemove)
        {
            $sTxt = preg_replace('#'.$sRemove.'#i', 'title', $sTxt);
        }

        $sTxt = preg_replace('#javascript:#i', '', $sTxt);
		
        foreach($this->_aEvilCss as $sCss)
        {
			$sTxt = preg_replace('/'. $sCss .':(.*?)/i', 'replaced:', $sTxt);
       	}      
		
		$aParams = $this->getParams(substr_replace($sTxt, '', 0, strlen($aParts[0]))); 
		foreach ($aParams as $sKey => $mValue)
        {
			if ($sKey == 'src')
			{
                if (substr($mValue, 1, strlen('//www.')) == '//www.')
                {
                    $mValue = substr($mValue,0,1) . 'http:' . substr($mValue,1);
                }
				if (!preg_match('/(http|https):\/\//is', $mValue))
				{
					$sTxt = preg_replace('/'. $sKey .'=' . preg_quote($mValue, '/')  . '/is', 'replaced=""', $sTxt);
				}
			}			
		}
       	
       	if ($bCleanOnly === true)
       	{
       		return $sTxt;
       	}       	

       	$bFailed = false;       	
        switch (strtolower($aParts[0]))
        {
        	case 'object':
        	case 'embed':
        	case 'param':
        	case 'iframe':
  
        		break;   
        	case 'font':
        		$aParams = $this->getParams(substr_replace($sTxt, '', 0, strlen($aParts[0]))); 		
        		$sTxt = 'font';
        		foreach ($aParams as $sKey => $mValue)
        		{ 			
        			if (!in_array($sKey, array(
        						'color',
        						'size',
        						'face'
        					)
        				)
        			)
        			{
        				continue;
        			}
        			$sTxt .= ' ' . $sKey . '=' . $mValue . '';
        		}
        		break;
        	case 'img':		
				$aParams = $this->getParams(substr_replace($sTxt, '', 0, strlen($aParts[0])));	
				$sTempSource = '';
				if (isset($aParams['src']))
				{
					$sTempSource = str_replace(array('"', "'"), '', $aParams['src']);			
				}
        		if (!empty($sTempSource) && preg_match('/http:\/\//i', $sTempSource))
        		{
        			$sTxt = 'img src=' . $aParams['src'];    
        			if (isset($aParams['alt']))
        			{ 
        				$sTxt .= ' alt=' . $aParams['alt'];
        			}
        			$sTxt .= ' class="parsed_image" /';        			
        		}
        		else 
        		{
        			$bFailed = true;
        		}
        		break;
        	case 'a':        		
        		$aParams = $this->getParams(substr_replace($sTxt, '', 0, strlen($aParts[0])));         	
        		if (isset($aParams['href']))
        		{
        			$sTxt = 'a href=' . $aParams['href'];
        		}
        		else 
        		{
        			$bFailed = true;
        		}
        		break;
        	default:        	

        		break;
        }
        
        $aHtmlTags = Phpfox::getParam('core.allowed_html');  
        
        (($sPlugin = Phpfox_Plugin::get('parse_input__removeevilattributes')) ? eval($sPlugin) : null);     
		
		if ((isset($aHtmlTags[strtolower($aParts[0])]) || isset($aHtmlTags[substr_replace(strtolower($aParts[0]), '', 0, 1)])) && $bFailed === false)
		{			
			return '<' . $sTxt . '>';
		}
		else 
		{			
			return '&lt;' . $sTxt . '&gt;';
		}        
    }

    /**
     * Converts a string with non-latin characters into UNICODE. We convert all strings
     * before we enter them into the database so clients do not have to worry about database
     * collations and website encoding as all common browsers have no problems displaying UNICODE.
     *
     * @see self::_unicodeToEntitiesPreservingAscii()
     * @param string $str String we need to parse.
     * @param bool $bForUrl TRUE for URL strings, FALSE for general usage.
     * @return string Returns string that has been converted.
     */
    private function _utf8ToUnicode($str, $bForUrl = false)
    {
        $unicode = array();
        $values = array();
        $lookingFor = 1;
        
        if(defined('PHPFOX_UNICODE_JSON') && PHPFOX_UNICODE_JSON === true)
        {
            $aUnicodes = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
            foreach($aUnicodes as $char)
            {
                $thisValue = ord($char);
                if ($thisValue < 128)
                {
                    $unicode[] = $thisValue;
                }
                else
                {
                    $unicode[] = hexdec(trim(trim(json_encode($char), '"'), '\u'));
                }
            }
        }
        else
        {
            for ($i = 0; $i < strlen( $str ); $i++ )
            {
                $thisValue = ord( $str[ $i ] );

                if ( $thisValue < 128 )
                {
                    $unicode[] = $thisValue;
                }
                else
                {
                    if ( count( $values ) == 0 ) $lookingFor = ( $thisValue < 224 ) ? 2 : 3;

                    $values[] = $thisValue;

                    if ( count( $values ) == $lookingFor ) 
                    {
                        $number = ( $lookingFor == 3 ) ?
                            ( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
                            ( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );

                        $unicode[] = $number;
                        $values = array();
                        $lookingFor = 1;
                    }
                }
            }
        }

        return $this->_unicodeToEntitiesPreservingAscii($unicode, $bForUrl);
    }

    /**
     * Converts a string with non-latin characters into UNICODE. This method is used with the method _utf8ToUnicode().
     *
     * @see self::_utf8ToUnicode()
     * @param array $unicode ARRAY of unicode values.
     * @param bool $bForUrl TRUE for URL strings, FALSE for general usage.
     * @return string Returns string that has been converted.
     */
    private function _unicodeToEntitiesPreservingAscii($unicode, $bForUrl = false)
    {
        $entities = '';
        foreach( $unicode as $value )
        {
        	if ($bForUrl === true)
        	{
        		if ($value == 42 || $value > 127)
        		{
        			$sCacheValue = '&#' . $value . ';';
        		
        			$entities .= (preg_match('/[^a-zA-Z]+/', $sCacheValue) ? '-' . $value : $sCacheValue);   			
        		}
        		else 
        		{
        			$entities .= (preg_match('/[^0-9a-zA-Z]+/', chr($value)) ? ' ' : chr($value));
        		}        		
        	}
        	else 
        	{
        		$entities .= ($value == 42 ? '&#' . $value . ';' : ( $value > 127 ) ? '&#' . $value . ';' : chr($value));
        	}
        }
		$entities = str_replace("'", '&#039;', $entities);
        return $entities;
    }

	/**
	 * Cleans HTML object to make it XHTML valid
	 *
	 * @param string $sObject is the object data
	 * @param string $sEmbed is all the <embed> tags found withing a <object>
	 * @return string New <object>
	 */
	private function _cleanObject($sObject, $sEmbed)
	{
		$sObject = stripslashes($sObject);
		$sObject = str_replace("'", "\'", $sObject);

		$sEmbed = stripslashes($sEmbed);
		$sEmbed = str_replace("/", '\/', $sEmbed);
		$sEmbed = str_replace("'", "\'", $sEmbed);

		$sTxt = '<script type="text/javascript">document.write(\'<object' . $sObject . '>' . $sEmbed . '<\/object>\');</script>';

		return $sTxt;
	}
	
	/**
	 * Add a line break.
	 *
	 * @see self::addBreak()
	 * @param array $aMatches Matches from regex found in public method addBreak().
	 * @return string Return the string match of the text with removing the BBCode break we placed in earlier.
	 */
	private function _addBreak($aMatches)
	{
		$aMatches[0] = str_replace('[pf_break]', '', $aMatches[0]);
		
		return $aMatches[0];
	}	
}