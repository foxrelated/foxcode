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
 * @package 		Phpfox_Service
 * @version 		$Id: process.class.php 2525 2011-04-13 18:03:20Z Raymond_Benc $
 */
class Music_Service_Genre_Process extends Phpfox_Service 
{
    protected $_sTable;
    protected $_aLanguages;
    protected $_sModule = 'music';
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('music_genre');
        $this->_aLanguages = Language_Service_Language::instance()->getAll();
	}
	
	public function add($aVals)
	{
        $aFirstLang = current($this->_aLanguages);
        //Add phrases
        $aText = [];
        //Verify name
        foreach ($this->_aLanguages as $aLanguage){
            if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])){
                $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
            } else {
                return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
            }
        }
        $name = $aVals['name_' . $aFirstLang['language_id']];
        $phrase_var_name = $this->_sModule . '_genre_' . md5($this->_sModule . ' Category'. $name . PHPFOX_TIME);

        $aValsPhrase = [
            'var_name' => $phrase_var_name,
            'text' => $aText
        ];

        $finalPhrase = Language_Service_Phrase_Process::instance()->add($aValsPhrase);

        $iId = $this->database()->insert($this->_sTable, [
            'name' => $finalPhrase,
        ]);

        $this->cache()->remove($this->_sModule . '_genre', 'substr');
        return $iId;
	}
	
	public function update($aVals)
	{
        //Verify data
        if (!isset($aVals['edit_id'])){
            return false;
        }

        if (isset($aVals['name']) && Phpfox::isPhrase($aVals['name'])){
            $finalPhrase = $aVals['name'];
            //Update phrase
            foreach ($this->_aLanguages as $aLanguage){
                if (isset($aVals['name_' . $aLanguage['language_id']])){
                    $name = $aVals['name_' . $aLanguage['language_id']];
                    Language_Service_Phrase_Process::instance()->updateVarName($aLanguage['language_id'], $aVals['name'], $name);
                }
            }
        } else {
            //Verify name
            $aFirstLang = current($this->_aLanguages);
            $aText = [];
            foreach ($this->_aLanguages as $aLanguage){
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])){
                    $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
                } else {
                    return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
                }
            }
            $name = $aVals['name_' . $aFirstLang['language_id']];
            $phrase_var_name = $this->_sModule . '_genre_' . md5($this->_sModule . ' Category'. $name . PHPFOX_TIME);

            $aValsPhrase = [
                'var_name' => $phrase_var_name,
                'text' => $aText
            ];

            $finalPhrase = Language_Service_Phrase_Process::instance()->add($aValsPhrase);
        }
        $this->database()->update($this->_sTable, [
            'name' => $finalPhrase,
        ], 'genre_id = ' . $aVals['edit_id']
        );

        $this->cache()->remove($this->_sModule . '_genre', 'substr');
		return true;
	}
	
	public function delete($iId)
	{
        $sGenreName = $this->database()->select('name')
            ->from($this->_sTable)
            ->where('genre_id = ' . (int) $iId)
            ->execute('getSlaveField');
        if (Phpfox::isPhrase($sGenreName)){
            Language_Service_Phrase_Process::instance()->delete($sGenreName, true);
        }
        $this->database()->delete($this->_sTable, 'genre_id = ' . (int) $iId);
        $this->database()->update(':music_song', [
            'genre_id' => 0
        ], 'genre_id = ' . (int) $iId);

        $this->cache()->remove($this->_sModule . '_genre', 'substr');
		return true;
	}
    
    /**
     * Active or De-active a Music Genre
     *
     * @param int $iGenreID
     * @param int $iActive
     */
    public function toggleGenre($iGenreID, $iActive)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('admincp.has_admin_access', true);
        
        $this->database()->update(Phpfox::getT('music_genre'), [
            'is_active' => (int)($iActive == '1' ? 1 : 0)
        ], 'genre_id = ' . (int)$iGenreID);
        
        $this->cache()->remove('music_genre', 'substr');
    }
    
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 * @return mixed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('music.service_genre_process__call'))
		{
			eval($sPlugin);
            return null;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}