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
 * @version 		$Id: blog.class.php 7059 2014-01-22 14:20:10Z Fern $
 */
class Blog_Service_Blog extends Phpfox_Service
{
    /**
     * @var array
     */
    private $_aSpecial = [
        'category',
        'tag'
    ];
    
    /**
	 * Class constructor
	 */	
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('blog');
		
		(($sPlugin = Phpfox_Plugin::get('blog.service_blog___construct')) ? eval($sPlugin) : false);
	}
    
    /**
     * @param string $sUrl
     *
     * @return bool
     */
	public function isValidUrl($sUrl)
	{
		return (in_array(Phpfox::getLib('parse.input')->cleanTitle($sUrl), $this->_aSpecial) ? true : Phpfox_Error::set(_p('invalid')));
	}
    
    /**
     * @param int $iUserId
     *
     * @return int
     */
	public function getDraftsCount($iUserId)
	{
		(($sPlugin = Phpfox_Plugin::get('blog.component_service_blog_getdraftscount__start')) ? eval($sPlugin) : false);
        
        $sCacheId = $this->cache()->set('blog_draft_count_' . (int) $iUserId);
        if (!$iBlogDraftsCount = $this->cache()->get($sCacheId, 1)) {
            $iBlogDraftsCount = $this->database()->select("COUNT(*)")
                ->from($this->_sTable, 'blog')
                ->where('user_id = ' . $iUserId . ' AND post_status = 2')
                ->execute('getSlaveField');
            $this->cache()->save($sCacheId, $iBlogDraftsCount);
        }
        return $iBlogDraftsCount;
	}
    
    /**
     * @todo check might not use anymore
     * @param string $sLimit
     *
     * @return array
     */
	public function getNewBlogs($sLimit)
	{
		(($sPlugin = Phpfox_Plugin::get('blog.component_service_blog_getnewblogs__start')) ? eval($sPlugin) : false);
		$aRows = $this->database()->getSlaveRows("
			SELECT b.blog_id, b.title, u.user_name
			FROM " . $this->_sTable . " AS b
				JOIN " . Phpfox::getT('user') . " AS u
					ON(b.user_id = u.user_id)
			LIMIT 0," . $sLimit . "
		");		
		(($sPlugin = Phpfox_Plugin::get('blog.component_service_blog_getnewblogs__end')) ? eval($sPlugin) : false);
		return $aRows;
	}
    
    /**
     * Get a blog detail for edit
     * @param int $iBlogId ID of a blog
     *
     * @return array detail of a blog
     */
	public function getBlogForEdit($iBlogId)
	{
		(($sPlugin = Phpfox_Plugin::get('blog.component_service_blog_getblogsforedit__start')) ? eval($sPlugin) : false);
		
        $sCacheId = $this->cache()->set('blog_detail_edit_' . (int) $iBlogId);
        if (!$aBlog = $this->cache()->get($sCacheId, 3)) {
            $aBlog = $this->database()->select("blog.*, blog_text.text AS text, u.user_name")
                ->from($this->_sTable, 'blog')
                ->join(Phpfox::getT('blog_text'), 'blog_text', 'blog_text.blog_id = blog.blog_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = blog.user_id')
                ->where('blog.blog_id = ' . (int) $iBlogId)
                ->execute('getSlaveRow');
            $this->cache()->save($sCacheId, $aBlog);
        }
        return $aBlog;
	}
    
    /**
     * Get detail of a blog for display
     * @param int $iBlogId ID of a blog
     *
     * @return array detail of a blog
     */
	public function getBlog($iBlogId)
	{
	    $sCacheId = $this->cache()->set('blog_detail_view_' . (int) $iBlogId);
        
        if (!$aRow = $this->cache()->get($sCacheId, 3)) {
            (($sPlugin = Phpfox_Plugin::get('blog.service_blog_getblog')) ? eval($sPlugin) : false);
    
            if (Phpfox::isModule('track')) {
                $this->database()->select("blog_track.item_id AS is_viewed, ")
                    ->leftJoin(Phpfox::getT('track'), 'blog_track', 'blog_track.item_id = blog.blog_id AND blog_track.user_id = ' . Phpfox::getUserBy('user_id') . ' AND type_id=\'blog\'');
            }
    
            if (Phpfox::isModule('friend')) {
                $this->database()->select('f.friend_id AS is_friend, ')
                    ->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = blog.user_id AND f.friend_user_id = " . Phpfox::getUserId());
            }
    
            if (Phpfox::isModule('like')) {
                $this->database()->select('l.like_id AS is_liked, ')
                    ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'blog\' AND l.item_id = blog.blog_id AND l.user_id = ' . Phpfox::getUserId());
            }
    
            $aRow = $this->database()
                ->select("blog.*, " . (Phpfox::getParam('core.allow_html') ? "blog_text.text_parsed" : "blog_text.text") . " AS text, " . Phpfox::getUserField())
                ->from($this->_sTable, 'blog')
                ->join(Phpfox::getT('blog_text'), 'blog_text', 'blog_text.blog_id = blog.blog_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = blog.user_id')
                ->where('blog.blog_id = ' . (int)$iBlogId)
                ->execute('getSlaveRow');
    
            (($sPlugin = Phpfox_Plugin::get('blog.component_service_blog_getblog__end')) ? eval($sPlugin) : false);
    
            if (!isset($aRow['is_friend'])) {
                $aRow['is_friend'] = 0;
            }
            $this->cache()->save($sCacheId, $aRow);
        }
		return $aRow;
	}
    
    /**
     * Check user can view a blog or not
     * @param int    $iBlogId
     * @param string $sUserPerm
     * @param string $sGlobalPerm
     *
     * @return bool
     */
	public function hasAccess($iBlogId, $sUserPerm, $sGlobalPerm)
	{
		(($sPlugin = Phpfox_Plugin::get('blog.service_blog_hasaccess_start')) ? eval($sPlugin) : false);
		
        $sCacheId = $this->cache()->set('blog_detail_access_' . (int) $iBlogId);
        if (!$aRow = $this->cache()->get($sCacheId)) {
            $aRow = $this->database()->select('u.user_id')
                ->from($this->_sTable, 'blog')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = blog.user_id')
                ->where('blog.blog_id = ' . (int) $iBlogId)
                ->execute('getSlaveRow');
            $this->cache()->save($sCacheId, $aRow);
        }
			
		(($sPlugin = Phpfox_Plugin::get('blog.service_blog_hasaccess_end')) ? eval($sPlugin) : false);
		
		if (!isset($aRow['user_id'])) {
			return false;
		}
		
		if ((Phpfox::getUserId() == $aRow['user_id'] && Phpfox::getUserParam('blog.' . $sUserPerm)) || Phpfox::getUserParam('blog.' . $sGlobalPerm)) {
			return $aRow['user_id'];
		}
		
		(($sPlugin = Phpfox_Plugin::get('blog.component_service_blog_getblog__end')) ? eval($sPlugin) : false);
        
		return false;
	}
    
    /**
     * @param string     $sTitle
     * @param bool $bCleanOnly
     *
     * @return string
     */
	public function prepareTitle($sTitle, $bCleanOnly = false)
	{
		(($sPlugin = Phpfox_Plugin::get('blog.component_service_blog_preparetitle__start')) ? eval($sPlugin) : false);
        
		return Phpfox::getLib('parse.input')->prepareTitle('blog', $sTitle, 'title_url', Phpfox::getUserId(), Phpfox::getT('blog'), null, $bCleanOnly);
	}
    
    /**
     * @param array       $aItems
     * @param null|string $sType
     */
	public function getExtra(&$aItems, $sType = null)
	{
		(($sPlugin = Phpfox_Plugin::get('blog.component_service_blog_getextra__start')) ? eval($sPlugin) : false);
		
		if (!is_array($aItems))
		{
			$aItems = array();
		}
		
		$aIds = array();
		foreach ($aItems as $iKey => $aValue)
		{
			$aIds[] = $aValue['blog_id'];
		}			

		$aCategories = Blog_Service_Category_Category::instance()->getCategoriesById(implode(', ', $aIds));

		if (Phpfox::isModule('tag'))
		{
			$aTags = Tag_Service_Tag::instance()->getTagsById('blog', implode(', ', $aIds));
		}

		foreach ($aItems as $iKey => $aValue)
		{
			if (isset($aCategories[$aValue['blog_id']]))
			{
				$sCategories = '';
				$aCacheCategory[$aValue['blog_id']] = array();
				foreach ($aCategories[$aValue['blog_id']] as $aCategory)
				{					
					if (isset($aCacheCategory[$aValue['blog_id']][$aCategory['category_id']]))
					{
						continue;
					}
					
					$aCacheCategory[$aValue['blog_id']][$aCategory['category_id']] = true;						

					if ($aCategory['user_id'] && $sType == 'user_profile')
					{
						$sCategories .= ', <a href="' . Phpfox_Url::instance()->permalink($aValue['user_name'] . '.blog.category',  $aCategory['category_id'], _p($aCategory['category_name'])) . '">' . _p($aCategory['category_name']) . '</a>';
					}
					else 
					{
						$sCategories .= ', <a href="' . Phpfox_Url::instance()->permalink('blog.category',  $aCategory['category_id'], Phpfox::getSoftPhrase($aCategory['category_name'])) . '">' . Phpfox::getSoftPhrase($aCategory['category_name']) . '</a>';
					}
				}
				$sCategories = trim(ltrim($sCategories, ','));

				$aItems[$iKey]['categories'] = $sCategories;

				$aItems[$iKey]['info'] = _p('posted_x_by_x_in_x', array('date' => Phpfox::getTime(Phpfox::getParam('blog.blog_time_stamp'), $aValue['time_stamp']), 'link' => Phpfox_Url::instance()->makeUrl($aValue['user_name']), 'user' => $aValue, 'categories' => $sCategories));
			}
			else 
			{				
				$aItems[$iKey]['info'] = _p('posted_x_by_x', [
				    'date' => Phpfox::getTime(Phpfox::getParam('blog.blog_time_stamp'), $aValue['time_stamp']),
                    'link' => Phpfox_Url::instance()->makeUrl($aValue['user_name']),
                    'user' => $aValue
                ]);
            }
			
			if (isset($aTags[$aValue['blog_id']]))
			{
				$aItems[$iKey]['tag_list'] = $aTags[$aValue['blog_id']];
			}
			
			$aItems[$iKey]['bookmark_url'] = Phpfox::permalink('blog', $aValue['blog_id'], $aValue['title']);
			
			$aItems[$iKey]['aFeed'] = array(			
				'feed_display' => 'mini',	
				'comment_type_id' => 'blog',
				'privacy' => $aValue['privacy'],
				'comment_privacy' => $aValue['privacy_comment'],
				'like_type_id' => 'blog',				
				'feed_is_liked' => (isset($aValue['is_liked']) ? $aValue['is_liked'] : false),
				'feed_is_friend' => (isset($aValue['is_friend']) ? $aValue['is_friend'] : false),
				'item_id' => $aValue['blog_id'],
				'user_id' => $aValue['user_id'],
				'total_comment' => $aValue['total_comment'],
				'feed_total_like' => $aValue['total_like'],
				'total_like' => $aValue['total_like'],
				'feed_link' => $aItems[$iKey]['bookmark_url'],
				'feed_title' => $aValue['title'],
				'time_stamp' => $aValue['time_stamp'],
				'type_id' => 'blog'
			);
		}						
		
		unset($aTags, $aCategories);
		
		(($sPlugin = Phpfox_Plugin::get('blog.component_service_blog_getextra__end')) ? eval($sPlugin) : false);
	}
    
    /**
     * @param int $iLimit
     *
     * @return array
     */
	public function getNew($iLimit = 3)
	{
	    $sCacheId = $this->cache()->set('blog_new_' . (int) $iLimit);
        if (!$aRows = $this->cache()->get($sCacheId)) {
            
            (($sPlugin = Phpfox_Plugin::get('blog.component_service_blog_getnew__start')) ? eval($sPlugin) : false);
    
            $aRows = $this->database()
                ->select('b.blog_id, b.time_stamp, b.title, b.title_url, ' . Phpfox::getUserField())
                ->from($this->_sTable, 'b')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
                ->where('b.is_approved = 1 AND b.privacy = 1 AND b.post_status = 1')
                ->limit($iLimit)
                ->order('b.time_stamp DESC')
                ->execute('getSlaveRows');
    
            foreach ($aRows as $iKey => $aRow) {
                $aRows[$iKey]['posted_on'] = _p('posted_on_post_time_by_user_link', [
                        'post_time' => Phpfox::getTime(Phpfox::getParam('blog.blog_time_stamp'), $aRow['time_stamp']),
                        'user'      => $aRow
                    ]);
            }
    
            (($sPlugin = Phpfox_Plugin::get('blog.component_service_blog_getnew__end')) ? eval($sPlugin) : false);
            
            $this->cache()->save($sCacheId, $aRows);
        }
        
        return $aRows;
	}
    
    /**
     * Get total blog marked as spam on site
     * @return int
     */
	public function getSpamTotal()
	{
		(($sPlugin = Phpfox_Plugin::get('blog.component_service_blog_getspamtotal__start')) ? eval($sPlugin) : false);
		
        $sCacheId = $this->cache()->set('blog_spam_total');
        if (!$iTotalSpam = $this->cache()->get($sCacheId)){
            $iTotalSpam = (int) $this->database()->select('COUNT(*)')
                ->from($this->_sTable)
                ->where('is_approved = 9')
                ->execute('getSlaveField');
            $this->cache()->save($sCacheId, $iTotalSpam);
        }
        return $iTotalSpam;
	}
    
    /**
     * Get total pending blog of site
     * @return int
     */
	public function getPendingTotal()
	{
		(($sPlugin = Phpfox_Plugin::get('blog.component_service_blog_getpendingtotal')) ? eval($sPlugin) : false);
        
        $sCacheId = $this->cache()->set('blog_pending_total');
        if (!$iTotalPending = $this->cache()->get($sCacheId, 3)){
            $iTotalPending =  (int) $this->database()->select('COUNT(*)')
                ->from($this->_sTable)
                ->where('is_approved = 0')
                ->execute('getSlaveField');
            $this->cache()->save($sCacheId, $iTotalPending);
        }
		
        return $iTotalPending;
	}
    
    /**
     * Get total blog draft of a user
     * @param int $iUserId
     *
     * @return int
     */
	public function getTotalDrafts($iUserId = 0)
	{
		(($sPlugin = Phpfox_Plugin::get('blog.component_service_blog_gettotaldrafts')) ? eval($sPlugin) : false);
        
        if (!$iUserId) {
            $iUserId = Phpfox::getUserId();
        }
        
        $sCacheId = $this->cache()->set('blog_draft_total_' . (int) $iUserId);
        if (!$iTotalDrafts = $this->cache()->get($sCacheId, 3)){
            $iTotalDrafts =  (int) $this->database()->select('COUNT(*)')
                ->from($this->_sTable)
                ->where('user_id = ' . (int) $iUserId . ' AND post_status = 2')
                ->execute('getSlaveField');
            $this->cache()->save($sCacheId, $iTotalDrafts);
        }
        return $iTotalDrafts;
	}
    
    /**
     * @todo Might not use anymore
     * @param array $aItem
     *
     * @return array
     */
	public function getInfoForAction($aItem)
	{
		$aRow = $this->database()->select('b.blog_id, b.title, b.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('blog'), 'b')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
			->where('b.blog_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');
		$aRow['link'] = Phpfox_Url::instance()->permalink('blog', $aRow['blog_id'], $aRow['title']);
		return $aRow;
	}

    /**
     * @description: check if current user can view a blog
     * @param      $iId
     * @param bool $bReturnItem
     *
     * @return array|bool
     */
	public function canViewItem($iId, $bReturnItem = false)
    {

        if (!Phpfox::getUserParam('blog.view_blogs'))
        {
            return false;
        }

        $aItem = $this->getBlog($iId);
        if (empty($aItem['blog_id']))
        {
            Phpfox_Error::set(_p('blog_not_found'));
            return false;
        }

        if (isset($aItem['module_id']) && !empty($aItem['item_id']) && !Phpfox::isModule($aItem['module_id']))
        {
            Phpfox_Error::set(_p('Cannot find the parent item.'));
            return false;
        }

        if (Phpfox::isUser() && User_Service_Block_Block::instance()->isBlocked(null, $aItem['user_id']))
        {
            Phpfox_Error::set(_p('Sorry, this content isn\'t available right now'));
            return false;
        }

        if (Phpfox::isModule('privacy'))
        {
            if (!Privacy_Service_Privacy::instance()->check('blog', $aItem['blog_id'], $aItem['user_id'], $aItem['privacy'], $aItem['is_friend'], true))
            {
                return false;
            }
        }

        if(isset($aItem['module_id']) && Phpfox::isModule($aItem['module_id']) && Phpfox::hasCallback($aItem['module_id'], 'checkPermission'))
        {
            if(!Phpfox::callback($aItem['module_id'] . '.checkPermission', $aItem['item_id'], 'blog.view_browse_blogs'))
            {
                Phpfox_Error::set(_p('unable_to_view_this_item_due_to_privacy_settings'));
                return false;
            }
        }

        if (!Phpfox::getUserParam('blog.can_approve_blogs'))
        {
            if ($aItem['is_approved'] != '1' && $aItem['user_id'] != Phpfox::getUserId())
            {
                Phpfox_Error::set(_p('blog_not_found'));
                return false;
            }
        }

        if ($aItem['post_status'] == 2 && Phpfox::getUserId() != $aItem['user_id'] && !Phpfox::getUserParam('blog.edit_user_blog'))
        {
            Phpfox_Error::set(_p('blog_not_found'));
            return false;
        }

        if ($bReturnItem)
        {
            $aCategories = Blog_Service_Category_Category::instance()->getCategoriesById($aItem['blog_id']);
            $aItem['categories'] = isset($aCategories[$aItem['blog_id']]) ? $aCategories[$aItem['blog_id']] : [];

            if (Phpfox::isModule('tag'))
            {
                $aTags = Tag_Service_Tag::instance()->getTagsById('blog', $aItem['blog_id']);
                $aItem['tag_list'] = '';
                if (isset($aTags[$aItem['blog_id']]))
                {
                    $aItem['tag_list'] = '';
                    foreach ($aTags[$aItem['blog_id']] as $aTag)
                    {
                        $aItem['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
                    }
                    $aItem['tag_list'] = trim(trim($aItem['tag_list'], ','));
                }
            }
        }

        return $bReturnItem ? $aItem : true;
    }
    
    /**
     * @param string $sMethod
     * @param array $aArguments
     *
     * @return null
     */
	public function __call($sMethod, $aArguments)
	{
		if ($sPlugin = Phpfox_Plugin::get('blog.service_blog__call'))
		{
			eval($sPlugin);
            return null;
		}
		
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
}