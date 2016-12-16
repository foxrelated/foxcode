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
 * @package  		Module_Blog
 * @version 		$Id: process.class.php 3072 2011-09-12 13:23:50Z Raymond_Benc $
 */
class Blog_Service_Category_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('blog_category');
	}
    
    /**
     * @param int $iId
     * @param array $aVals
     *
     * @return string
     */
	public function update($iId, $aVals) {
        $aLanguages = Language_Service_Language::instance()->getAll();
        if (Core\Lib::phrase()->isPhrase($aVals['name'])){
            $finalPhrase = $aVals['name'];
            //Update phrase
            foreach ($aLanguages as $aLanguage){
                if (isset($aVals['name_' . $aLanguage['language_id']])){
                    $name = $aVals['name_' . $aLanguage['language_id']];
                    Language_Service_Phrase_Process::instance()->updateVarName($aLanguage['language_id'], $aVals['name'], $name);
                }
            }
        } else {
            $name = $aVals['name_' . $aLanguages[0]['language_id']];
            $phrase_var_name = 'blog_category_' . md5('Blog Category'. $name . PHPFOX_TIME);
            //Add phrase
            $aText = [];
            foreach ($aLanguages as $aLanguage){
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])){
                    $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
                } else {
                    Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
                }
            }
            $aValsPhrase = [
                'var_name' => $phrase_var_name,
                'text' => $aText
            ];
            $finalPhrase = Language_Service_Phrase_Process::instance()->add($aValsPhrase);
            $this->database()->update($this->_sTable, array(
                'name' => $finalPhrase
            ), 'category_id = ' . (int) $iId);
        }

		return $finalPhrase;
	}
    
    /**
     * @param array    $aVals
     * @param null|int $iUserId
     *
     * @return int
     */
	public function add($aVals, $iUserId = null) {
        //Add phrase for category
        $aLanguages = Language_Service_Language::instance()->getAll();
        $name = $aVals['name_' . $aLanguages[0]['language_id']];
        $phrase_var_name = 'blog_category_' . md5('Blog Category'. $name . PHPFOX_TIME);
        //Add phrases
        $aText = [];
        foreach ($aLanguages as $aLanguage){
            if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])){
                $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
            } else {
                Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
            }
        }
        $aValsPhrase = [
            'var_name' => $phrase_var_name,
            'text' => $aText
        ];
        $finalPhrase = Language_Service_Phrase_Process::instance()->add($aValsPhrase);
		$iId = $this->database()->insert(Phpfox::getT('blog_category'), array(
            'name' => $finalPhrase,
            'user_id' => ($iUserId === null ? Phpfox::getUserId() : $iUserId),
            'added' => PHPFOX_TIME
        ));
		
		return $iId;
	}
    
    /**
     * @deprecated
     *
     * @param array $aIds
     *
     * @return true
     */
	public function deleteMultiple($aIds)
	{
		foreach ($aIds as $iId) {
			$this->delete($iId);
		}
		return true;
	}
    
    /**
     * @param int $iCategoryId
     *
     * @return true
     */
    public function delete($iCategoryId){
        $aCategory = $this->database()->select('*')
            ->from(':blog_category')
            ->where('category_id=' . (int) $iCategoryId)
            ->execute('getSlaveRow');
        if (isset($aCategory['name']) && Phpfox::isPhrase($aCategory['name'])){
            Language_Service_Phrase_Process::instance()->delete($aCategory['name'], true);
        }
        $this->database()->delete($this->_sTable, 'category_id = ' . (int) $iCategoryId);
        $this->database()->delete(Phpfox::getT('blog_category_data'), 'category_id = ' . (int) $iCategoryId);
        return true;
    }
    
    /**
     * Active or De-active a blog category
     *
     * @param int $iCategoryId
     * @param int $iActive
     */
    public function toggleCategory($iCategoryId, $iActive)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('admincp.has_admin_access', true);
    
        $this->database()->update(Phpfox::getT('blog_category'), [
            'is_active' => (int)($iActive == '1' ? 1 : 0)
        ], 'category_id = ' . (int)$iCategoryId);
    
        $this->cache()->remove('blog_category', 'substr');
    }
    
    /**
     * @param int   $iBlogId
     * @param array $aCategories
     * @param bool  $bUpdateUsageCount
     */
    public function addCategoryForBlog($iBlogId, $aCategories, $bUpdateUsageCount = true)
    {
        if (count($aCategories))
        {
            $aCache = array();
            foreach ($aCategories as $iKey => $iId)
            {
                if (!is_numeric($iId)) {
                    continue;
                }
                
                if (isset($aCache[$iId])) {
                    continue;
                }
                
                $aCache[$iId] = true;
                
                $this->database()->insert(Phpfox::getT('blog_category_data'), array('blog_id' => $iBlogId, 'category_id' => $iId));
                if ($bUpdateUsageCount === true)
                {
                    $this->database()->updateCount('blog_category_data', 'category_id = ' . (int) $iId, 'used', 'blog_category', 'category_id = ' . (int) $iId);
                }
            }
        }
    }
    
    /**
     * @param int    $iBlogId
     * @param array  $aCategories
     * @param bool   $bUpdateUsageCount
     * @param bool   $bDecreaseUsageCount
     */
    public function updateCategoryForBlog($iBlogId, $aCategories, $bUpdateUsageCount, $bDecreaseUsageCount = true)
    {
        $aRows = $this->database()->select('category_id')
            ->from(Phpfox::getT('blog_category_data'))
            ->where('blog_id = ' . (int) $iBlogId)
            ->execute('getSlaveRows');
        
        if (count($aRows))
        {
            foreach ($aRows as $aRow)
            {
                $this->database()->delete(Phpfox::getT('blog_category_data'), "blog_id = " . (int) $iBlogId . " AND category_id = " . (int) $aRow["category_id"]);
                if ($bDecreaseUsageCount && $bUpdateUsageCount) {
                    $this->database()->update(Phpfox::getT('blog_category'), ['used' => 'used - 1'], ['category_id' => $aRow["category_id"]], false);
                }
            }
        }
        
        $this->addCategoryForBlog($iBlogId, $aCategories, $bUpdateUsageCount);
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
		if ($sPlugin = Phpfox_Plugin::get('blog.service_category_process__call'))
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