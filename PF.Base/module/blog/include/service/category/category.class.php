<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Raymond Benc
 * @package          Module_Blog
 * @version          $Id: category.class.php 3917 2012-02-20 18:21:08Z Raymond_Benc $
 */
class Blog_Service_Category_Category extends Phpfox_Service
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
     *
     * @return bool|array
     */
	public function getCategory($iId)
	{
	    $sCacheId = $this->cache()->set('blog_category_' . $iId);
        if (!$aCategory = $this->cache()->get($sCacheId)) {
            $aCategory = $this->database()->select('*')
                ->from(Phpfox::getT('blog_category'))
                ->where('category_id = ' . (int)$iId)
                ->execute('getSlaveRow');
            $this->cache()->save($sCacheId, $aCategory);
        }
		return (isset($aCategory['category_id']) ? $aCategory : false);
	}
    
    /**
     * Get all blog categories for admin
     *
     * @param array|string  $aConds
     * @param string $sSort
     *
     * @return array
     */
	public function get($aConds = 'true', $sSort = 'c.ordering ASC')
	{
        (($sPlugin = Phpfox_Plugin::get('blog.service_category_category_get_start')) ? eval($sPlugin) : false);

        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('blog_category'), 'c')
            ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->where($aConds)
            ->execute('getSlaveField');

        $aItems = array();
        if ($iCnt) {
            $aItems = $this->database()->select('c.*, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('blog_category'), 'c')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
                ->where($aConds)
                ->order($sSort)
                ->execute('getSlaveRows');
            foreach ($aItems as $iKey => $aItem) {
                $aItems[$iKey]['link'] = ($aItem['user_id'] ? Phpfox_Url::instance()->permalink($aItem['user_name'] . '.blog.category', $aItem['category_id'], $aItem['name']) : Phpfox_Url::instance()->permalink('blog.category', $aItem['category_id'], $aItem['name']));
            }
        }

        (($sPlugin = Phpfox_Plugin::get('blog.service_category_category_get_end')) ? eval($sPlugin) : false);

        return [$iCnt, $aItems];
	}
    
    /**
     * @param array  $aConds
     * @param string $sSort
     *
     * @return array
     */
	public function getCategories($aConds, $sSort = 'c.ordering ASC')
	{
	    $sCacheId = $this->cache()->set('blog_category_get_' . md5(serialize($aConds) . $sSort));
        if (!$aItems = $this->cache()->get($sCacheId)) {
            
            (($sPlugin = Phpfox_Plugin::get('blog.service_category_category_getcategories_start')) ? eval($sPlugin) : false);
    
            $aItems = $this->database()->select('c.category_id, c.name, c.name, c.user_id')
                ->from(Phpfox::getT('blog_category'), 'c')
                ->where($aConds)
                ->group('c.category_id', true)
                ->order($sSort)
                ->execute('getSlaveRows');
    
            (($sPlugin = Phpfox_Plugin::get('blog.service_category_category_getcategories_end')) ? eval($sPlugin) : false);
            $this->cache()->save($sCacheId, $aItems);
        }
        
		return $aItems;
	}
    
    /**
     * Get Categories by list of Id
     *
     * @param string $sId list of categories ID
     *
     * @return array
     */
	public function getCategoriesById($sId)
	{
        if (!$sId) {
            return [];
        }
        $aItems = $this->database()->select('d.blog_id, d.category_id, c.name AS category_name, c.user_id')
            ->from(Phpfox::getT('blog_category_data'), 'd')
            ->join(Phpfox::getT('blog_category'), 'c', 'd.category_id = c.category_id')
            ->where("c.is_active = 1 AND d.blog_id IN(" . $sId . ")")
            ->execute('getSlaveRows');

        $aCategories = [];
        foreach ($aItems as $aItem) {
            $aCategories[$aItem['blog_id']][] = $aItem;
        }
        return $aCategories;
	}
    
    /**
     * @todo this function might not use anymore
     * @param string $sName
     * @param int    $iUserId
     * @param array  $aConds
     * @param string $sSort
     * @param string $iPage
     * @param string $sLimit
     *
     * @return array
     */
	public function getBlogsByCategory($sName, $iUserId, $aConds = array(), $sSort = '', $iPage = '', $sLimit = '')
	{
		$aConds = array_merge(array("AND blog_category.user_id = " . (int) $iUserId), $aConds);
		$aConds = array_merge(array("AND (blog_category.category_id = " . $sName . " OR blog_category.name = '" . $this->database()->escape($sName) . "') "), $aConds);
		
		$aItems = array();
		(($sPlugin = Phpfox_Plugin::get('blog.service_category_category_getblogsbycategory_count')) ? eval($sPlugin) : false);
        
		$iCnt = $this->database()->select('COUNT(DISTINCT blog.blog_id)')
			->from(Phpfox::getT('blog'), 'blog')
			->innerJoin(Phpfox::getT('blog_category_data'), 'blog_category_data', 'blog_category_data.blog_id = blog.blog_id')
			->innerJoin(Phpfox::getT('blog_category'), 'blog_category', 'blog_category.category_id = blog_category_data.category_id')
			->where($aConds)
			->execute('getSlaveField');

		if ($iCnt)
		{
			(($sPlugin = Phpfox_Plugin::get('blog.service_category_category_getblogsbycategory_query')) ? eval($sPlugin) : false);
			$aItems = $this->database()->select("blog.*, " . (Phpfox::getParam('core.allow_html') ? "blog_text.text_parsed" : "blog_text.text") ." AS text, blog_category.category_id AS category_id, blog_category.name AS category_name, " . Phpfox::getUserField())
				->from(Phpfox::getT('blog'), 'blog')
				->innerJoin(Phpfox::getT('blog_category_data'), 'blog_category_data', 'blog_category_data.blog_id = blog.blog_id')
				->innerJoin(Phpfox::getT('blog_category'), 'blog_category', 'blog_category.category_id = blog_category_data.category_id')
				->join(Phpfox::getT('blog_text'), 'blog_text', 'blog_text.blog_id = blog.blog_id')
				->join(Phpfox::getT('user'), 'u', 'blog.user_id = u.user_id')
				->where($aConds)
                ->group('blog.blog_id', true)
				->order($sSort)
				->limit($iPage, $sLimit, $iCnt)
				->execute('getSlaveRows');
		}

		return array($iCnt, $aItems);
	}
    
    /**
     * @todo Might not use anymore
     * Get blog search result
     * @param array $aConds
     * @param string $sSort
     *
     * @return array
     */
	public function getSearch($aConds, $sSort)
	{
		(($sPlugin = Phpfox_Plugin::get('blog.service_category_category_getsearch')) ? eval($sPlugin) : false);
		$aRows = $this->database()->select('blog.blog_id')
			->from(Phpfox::getT('blog'), 'blog')
			->join(Phpfox::getT('blog_text'), 'blog_text', 'blog_text.blog_id = blog.blog_id')
			->innerJoin(Phpfox::getT('blog_category_data'), 'blog_category_data', 'blog_category_data.blog_id = blog.blog_id')
			->innerJoin(Phpfox::getT('blog_category'), 'blog_category', 'blog_category.category_id = blog_category_data.category_id')
			->where($aConds)
			->order($sSort)
			->execute('getSlaveRows');
			
		$aSearchIds = array();
		foreach ($aRows as $aRow)
		{
			$aSearchIds[] = $aRow['blog_id'];
		}
		
		return $aSearchIds;
	}

    /**
     * Get category information for edit
     * @param int $iCategoryId
     *
     * @return bool|array
     */
    public function getForEdit($iCategoryId)
    {
        if (!$iCategoryId) {
            return false;
        }
        $aCategory = $this->database()->select('*')
            ->from(':blog_category')
            ->where('category_id=' . (int)$iCategoryId)
            ->execute('getSlaveRow');
        $aLanguages = Language_Service_Language::instance()->getAll();
        foreach ($aLanguages as $aLanguage) {
            $aCategory['name_' . $aLanguage['language_id']] = (Core\Lib::phrase()->isPhrase($aCategory['name'])) ? _p($aCategory['name'], [], $aLanguage['language_id']) : $aCategory['name'];;
        }
        return $aCategory;
    }

	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 * @return null
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('blog.service_category_category__call'))
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