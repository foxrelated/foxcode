<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Feed
 */
class Feed_Service_Process extends Phpfox_Service 
{
    /**
     * @var bool
     */
	private $_bAllowGuest = false;
    
    /**
     * @var int
     */
	private $_iLastId = 0;
    
    /**
     * @var array
     */
    private $_aCallback = [];
    
    /**
     * @var bool
     */
    private $_bIsCallback = false;
    
    /**
     * @var bool
     */
	private $_bIsNewLoop = false;
    
    /**
     * @var
     */
    private $_content;
    
    /**
     * @var int
     */
    private $_iNewLoopFeedId = 0;
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('feed');
	}
    
    /**
     * @param string $sType
     * @param int    $iItemId
     *
     * @return void
     */
	public function clearCache($sType, $iItemId)
	{
        if (!Phpfox::getParam('feed.cache_each_feed_entry')) {
            return;
        }
				
		$iParentId = Phpfox_Request::instance()->getInt('parent_id');
		if ($sType == 'feed_mini' && !empty($iParentId))
		{
			$sTable = '';
			if (Phpfox_Request::instance()->get('pmodule') == 'event')
			{
				$sTable = 'event_';
			}
			elseif (Phpfox_Request::instance()->get('pmodule') == 'pages')
			{
				$sTable = 'pages_';
			}
			
			$aFeed = $this->database()->select('*')
				->from(Phpfox::getT($sTable . 'feed'))
				->where('feed_id = ' . (int) $iParentId)
				->execute('getSlaveRow');		
			if (isset($aFeed['feed_id']))
			{
				$sType = $aFeed['type_id'];
				$iItemId = $aFeed['item_id'];				
			}
		}		
		elseif ($sType == 'forum_post')
		{
			$this->cache()->remove(array('feeds', 'forum_' . $iItemId));
		}
        else if ($sType == 'feed')
        {
            $aVal = Phpfox_Request::instance()->getArray('val');
            
            if (isset($aVal['is_via_feed']) && $aVal['is_via_feed'] > 0 )
            {
                $iItemId = $this->database()->select('item_id')
                    ->from(Phpfox::getT('feed'))
                    ->where('feed_id = ' . (int)$aVal['is_via_feed'])
                    ->execute('getSlaveField');
                
                $sType .= '_comment';
            }
        }
        else if ($sType == 'pages')
        {
            $aVal = Phpfox_Request::instance()->getArray('val');
            
            if (isset($aVal['is_via_feed']) && $aVal['is_via_feed'] > 0)
            {
                $aRow = $this->database()->select('type_id, item_id')
                        ->from(Phpfox::getT('pages_feed'))
                        ->where('feed_id = ' . (int)$aVal['is_via_feed'])
                        ->execute('getSlaveRow');
                
                if (!empty($aRow) && isset($aRow['item_id']) && $aRow['item_id'] > 0)
                {
                    $sType = $aRow['type_id'];
                    $iItemId = $aRow['item_id'];
                }
            }
        }
        
		$this->cache()->remove(array('feeds', $sType . '_' . $iItemId));
	}
    
    /**
     * @param array $aCallback
     *
     * @return $this
     */
	public function callback($aCallback)
	{
        if (isset($aCallback['module'])) {
            $this->_bIsCallback = true;
            $this->_aCallback = $aCallback;
        }
        
        return $this;
	}
    
    /**
     * @return $this
     */
	public function allowGuest()
	{
		$this->_bAllowGuest = true;
		return $this;
	}
    
    /**
     * @param string      $sType
     * @param int         $iItemId
     * @param int         $iPrivacy
     * @param int         $iPrivacyComment
     * @param int         $iParentUserId
     * @param null|int    $iOwnerUserId
     * @param bool|int    $bIsTag
     * @param int         $iParentFeedId
     * @param null|string $sParentModuleName
     *
     * @return int
     */
	public function add($sType, $iItemId = 0, $iPrivacy = 0, $iPrivacyComment = 0, $iParentUserId = 0, $iOwnerUserId = null, $bIsTag = 0, $iParentFeedId = 0, $sParentModuleName = null)
	{
        if (defined('NO_TWO_FEEDS_THIS_ACTION')){
            if (defined('NO_TWO_FEEDS_THIS_ACTION_RAN')){
                return true;
            } else {
                define('NO_TWO_FEEDS_THIS_ACTION_RAN', true);
            }
        }
		$isApp = false;
		$content = null;
		if (is_array($sType)) {
			$app = $sType;
			$sType = $app['type_id'];
			$isApp = true;
			$content = $app['content'];
			if (isset($app['privacy'])) {
				$iPrivacy = $app['privacy'];
			}

			if (isset($app['parent_user_id'])) {
				$iParentUserId = $app['item_id'];
			}
		}
        if (!empty($this->_content)){
            $content = $this->_content;
        }
		//Plugin call
		if (($sPlugin = Phpfox_Plugin::get('feed.service_process_add__start')))
		{
			eval($sPlugin);
		}

		if (!defined('PHPFOX_FEED_NO_CHECK')) {
			if (!$isApp && ((!Phpfox::isUser() && $this->_bAllowGuest === false) || (defined('PHPFOX_SKIP_FEED') && PHPFOX_SKIP_FEED)))
			{
				return false;
			}
		}
		
		if ($iParentUserId === null)
        {
			$iParentUserId = 0;
		}

		$iNewTimeStamp = PHPFOX_TIME;
		$iNewTimeStampCheck = Phpfox::getLib('date')->mktime(0, 0, 0, date('n', PHPFOX_TIME), date('j', PHPFOX_TIME), date('Y', PHPFOX_TIME));
		$aParentModuleName = explode('_', $sParentModuleName);
        $post_user_id = (defined('FEED_FORCE_USER_ID') ? FEED_FORCE_USER_ID : ($iOwnerUserId === null ? Phpfox::getUserId() : (int) $iOwnerUserId));
		$aInsert = array(
			'privacy' => (int) $iPrivacy,
			'privacy_comment' => (int) $iPrivacyComment,
			'type_id' => $sType,
			'user_id' => $post_user_id,
			'parent_user_id' => $iParentUserId,
			'item_id' => $iItemId,
			'time_stamp' => $iNewTimeStamp,
			'parent_feed_id' => (int) $iParentFeedId,
			'parent_module_id' => ((Phpfox::isModule($aParentModuleName[0]) || Phpfox::isApps($sParentModuleName)) ? $this->database()->escape($sParentModuleName) : null),
			'time_update' => $iNewTimeStamp,
			'content' => $content
		);

		if ($this->_bIsCallback && !isset($this->_aCallback['has_content'])) {
			unset($aInsert['content']);
		}

		if (!defined('PHPFOX_INSTALLER') && !$this->_bIsCallback && !Phpfox::getParam('feed.add_feed_for_comments') && preg_match('/^(.*)_comment$/i', $sType))
		{
			$aInsert['feed_reference'] = true;
		}		
		
		if (empty($aInsert['parent_module_id']))
		{
			unset($aInsert['parent_module_id']);
		}
		if (defined('PHPFOX_APP_ID'))
		{
			$aInsert['app_id'] = PHPFOX_APP_ID;
		}
		
		//Plugin call
		if (($sPlugin = Phpfox_Plugin::get('feed.service_process_add__end')))
		{
			eval($sPlugin);
		}

		if ($this->_bIsNewLoop)
		{
			$aInsert['feed_reference'] = (int)$bIsTag;
			$this->_iNewLoopFeedId = $this->database()->insert(Phpfox::getT('feed'), $aInsert);
		}
		else
		{
			$this->_iLastId = $this->database()->insert(Phpfox::getT(($this->_bIsCallback ? $this->_aCallback['table_prefix'] : '') . 'feed'), $aInsert);
            if (redis()->enabled()) {
                $add = function($user_id, $feed_id) {
                    redis()->lpush('feed_stream_' . $user_id, $feed_id);
                    redis()->ltrim('feed_stream_' . $user_id, 0, 49);
                };

                $add($post_user_id, $this->_iLastId);
                if ($iPrivacy != '3' && $iPrivacy != '4') {
                    redis()->lpush('public_feeds', $this->_iLastId);
                    redis()->ltrim('public_feeds', 0, 199);

                    $friends = db()->select('u.user_id')
                        ->from(':friend', 'f')
                        ->join(':user', 'u', 'u.user_id = f.friend_user_id')
                        ->where('f.user_id = ' . $post_user_id)
                        ->order('u.last_login DESC')
                        ->limit(50)
                        ->all();

                    foreach ($friends as $friend) {
                        $add($friend['user_id'], $this->_iLastId);
                    }
                }
            }
			if ($this->_bIsCallback) {
				storage()->set('feed_callback_' . $this->_iLastId, $this->_aCallback);
			}
            //Loop Feed for main of pages/groups items
            if ($this->_bIsCallback && ($this->_aCallback['module'] == 'pages' || (isset($this->_aCallback['add_to_main_feed']) && $this->_aCallback['add_to_main_feed'])) && !$this->_bIsNewLoop  && $iParentUserId > 0)
            {
                $aUser = $this->database()->select('u.user_id, p.view_id')
                    ->from(Phpfox::getT('user'), 'u')
                    ->join(Phpfox::getT('pages'), 'p', 'p.page_id = u.profile_page_id')
                    ->where('u.profile_page_id = ' . (int) $iParentUserId)
                    ->execute('getSlaveRow');

				if (!$iParentFeedId && defined('PHPFOX_PAGES_IS_PARENT_FEED')) {
					$iParentFeedId = $this->_iLastId;
				}

                if (!$aUser['view_id'])
                {
                    $this->_content = $content;
                    if (isset($aUser['user_id']) && Phpfox::getUserId() == $aUser['user_id'])
                    {
                        $this->_bIsNewLoop = true;
                        $this->_bIsCallback  = false;
                        $this->_aCallback = array();
                        $iNewLoopId = $this->add($sType, $iItemId, $iPrivacy, $iPrivacyComment, 0, null, 0, $iParentFeedId);
                    }
                    else
                    {
                        $this->_bIsNewLoop = true;
                        $this->_bIsCallback  = false;
                        $this->_aCallback = array();
                        $iNewLoopId = $this->add($sType, $iItemId, $iPrivacy, $iPrivacyComment, 0, $iOwnerUserId === null ? Phpfox::getUserId() : $iOwnerUserId, 0, $iParentFeedId);
                    }
                    $this->_content = '';
                    defined('PHPFOX_NEW_FEED_LOOP_ID') || define('PHPFOX_NEW_FEED_LOOP_ID', $this->_iNewLoopFeedId);
                }
            }
            //End loop feed
		}


		if ($sPlugin = Phpfox_Plugin::get('feed.service_process_add__end2'))
		{
			eval($sPlugin);
		}
		
		return $this->_iLastId;
	}
    
    /**
     * @param string $sType
     * @param int    $iItemId
     * @param int    $iPrivacy
     * @param int    $iPrivacyComment
     *
     * @return bool
     */
	public function update($sType, $iItemId, $iPrivacy = 0, $iPrivacyComment = 0)
	{
		$feed = $this->database()->select('*')
			->from(':feed')
			->where(['type_id' => $sType, 'item_id' => $iItemId])
			->executeRow();

		$this->database()->update($this->_sTable, array(
				'privacy' => (int) $iPrivacy,
				'privacy_comment' => (int) $iPrivacyComment,
			), 'type_id = \'' . $this->database()->escape($sType) . '\' AND item_id = ' . (int) $iItemId
		);

		if (redis()->enabled() && isset($feed['feed_id'])) {
			redis()->del('feed/' . $feed['feed_id']);
			redis()->del('feed_focus_' . $feed['feed_id']);
		}
		
		return true;
	}
    
    /**
     * Deletes an entry from the feeds
     *
     * @param string   $sType module as defined in: type_id
     * @param integer  $iId   numeric as defined in item_id
     * @param int|bool $iUser
     *
     * @return void
     */
	public function delete($sType, $iId, $iUser = false)
	{		
		$aFeeds = $this->database()->select('feed_id, user_id')
			->from(Phpfox::getT(($this->_bIsCallback ? $this->_aCallback['table_prefix'] : '') . 'feed'))
			->where('type_id = \'' . $sType . '\' AND item_id = ' . (int) $iId . ($iUser != false ? ' AND user_id = ' . (int)$iUser : ''))
			->execute('getSlaveRows');
			
		foreach ($aFeeds as $aFeed)
		{			
			if ($iUser != false)
			{
				$this->database()->delete(Phpfox::getT('feed'), 'feed_id = ' . $aFeed['feed_id']);
			}
		}
		if ($iUser == false)
		{
			$this->database()->delete(Phpfox::getT('feed'), 'type_id = \'' . $sType . '\' AND item_id = ' . (int) $iId);
		}
		if ($sPlugin = Phpfox_Plugin::get('feed.service_process_delete__end'))
		{
			eval($sPlugin);
		}
	}
    
    /**
     * @param string $sType
     * @param int    $iId
     *
     * @return void
     */
	public function deleteChild($sType, $iId)
	{		
		$this->database()->delete(Phpfox::getT('feed'), 'type_id = \'' . $sType . '\' AND child_item_id = ' . (int) $iId);
	}
    
    /**
     * @param int         $iId
     * @param null|string $sModule
     * @param int         $iItem
     *
     * @return bool
     */
	public function deleteFeed($iId, $sModule = null, $iItem = 0)
	{
		$aCallback = null;
		if (!empty($sModule))
		{
			if (Phpfox::hasCallback($sModule, 'getFeedDetails'))
			{
				$aCallback = Phpfox::callback($sModule . '.getFeedDetails', $iItem);
			}
		}
				
		$aFeed = Feed_Service_Feed::instance()->callback($aCallback)->getFeed($iId);
        $sType = '';

        if (!$aFeed && ($cache = storage()->get('feed_callback_' . $iId)))
        {
            if (in_array($cache->value->module, ['pages', 'groups']))
            {
                $aFeed = Feed_Service_Feed::instance()->callback($aCallback)->getFeed($iId, 'pages_');
                $sType = 'v_pages';
            }
        }

        if (!isset($aFeed['feed_id']))
        {
            return false;
        }

        if (empty($sType))
        {
            $sType = $aFeed['type_id'];
        }

        $iItemId = $aFeed['item_id'];
        if (!$iItemId)
        {
            $iItemId = $aFeed['feed_id'];
        }

        //Delete all shared items from this item
        $aSharedItems = $this->database()->select('feed_id')
            ->from(':feed')
            ->where('parent_module_id="' . $sType . '" AND parent_feed_id =' . (int) $iItemId)
            ->execute('getSlaveRows');

        if (is_array($aSharedItems) && count($aSharedItems)){
            foreach ($aSharedItems as $aSharedItem){
                if (isset($aSharedItem['feed_id'])){
                    $this->deleteFeed($aSharedItem['feed_id']);
                }
            }
        }
		
		if($aFeed['type_id'] == 'photo')
		{
			Phpfox::callback($aFeed['type_id'] . '.deleteFeedItem', $aFeed['item_id']);
		}
		
		if ($sPlugin = Phpfox_Plugin::get('feed.service_process_deletefeed'))
		{
			eval($sPlugin);
		}		
		
		$bCanDelete = false;
		if (Phpfox::getUserParam('feed.can_delete_own_feed') && ($aFeed['user_id'] == Phpfox::getUserId()))
		{
			$bCanDelete = true;
		}

		if (defined('PHPFOX_FEED_CAN_DELETE'))
		{
			$bCanDelete = true;
		}
		
		if (Phpfox::getUserParam('feed.can_delete_other_feeds'))
		{
			$bCanDelete = true;
		}		

		if ($bCanDelete === true)
		{		
			
			if (isset($aCallback['table_prefix']))
			{
				$this->database()->delete(Phpfox::getT($aCallback['table_prefix']  . 'feed'), 'feed_id = ' . (int) $iId);				
			}

			if ($aFeed['type_id'] == 'feed_comment')
			{
				$aCore = Phpfox_Request::instance()->getArray('core');
				if (isset($aCore['is_user_profile']) && $aCore['profile_user_id'] != Phpfox::getUserId())
				{

					$this->database()->delete(Phpfox::getT('feed'), 'user_id = ' . $aFeed['user_id'] .' AND time_stamp = ' . $aFeed['time_stamp'] . ' AND parent_user_id = ' . $aCore['profile_user_id']);
				}
				elseif (isset($aCore['is_user_profile']) && $aCore['profile_user_id'] == Phpfox::getUserId())
				{
					$this->database()->delete(Phpfox::getT('feed'), 'feed_id = ' . (int) $aFeed['feed_id']);
				}
				$this->database()->delete(Phpfox::getT('feed'), 'user_id = ' . $aFeed['user_id'] .' AND time_stamp = ' . $aFeed['time_stamp'] . ' AND parent_user_id = ' . Phpfox::getUserId());
			}
			else
			{
				$this->database()->delete(Phpfox::getT('feed'), 'user_id = ' . $aFeed['user_id'] .' AND time_stamp = ' . $aFeed['time_stamp']);
			}
            if (!(Phpfox::hasCallback($aFeed['type_id'], 'ignoreDeleteLikesAndTagsWithFeed') && Phpfox::callback($aFeed['type_id'] . '.ignoreDeleteLikesAndTagsWithFeed')))
            {
                // Delete likes that belonged to this feed
                $this->database()->delete(Phpfox::getT('like'), 'type_id = "'. $aFeed['type_id'] .'" AND item_id = ' . $aFeed['item_id']);

                // Delete tags that belonged to this feed
                $this->database()->delete(Phpfox::getT('tag'), 'category_id = "'. $aFeed['type_id'] .'" AND item_id = ' . $aFeed['item_id']);
            }

			if (!empty($sModule))
			{
				if (Phpfox::hasCallback($sModule, 'deleteFeedItem'))
				{
					Phpfox::callback($sModule . '.deleteFeedItem', $iItem);
				}
			}
			
			return true;
		}
		
		return false;
	}
    
    /**
     * @param array $aVals
     *
     * @return bool|int
     */
	public function addComment($aVals)
	{		
		if (empty($aVals['privacy_comment']))
		{
			$aVals['privacy_comment'] = 0;
		}
		
		if (empty($aVals['privacy']))
		{
			$aVals['privacy'] = 0;
		}
		
		if (empty($aVals['parent_user_id']))
		{
			$aVals['parent_user_id'] = 0;
		}
		
		if (!Ban_Service_Ban::instance()->checkAutomaticBan($aVals['user_status']))
		{
			return false;
		}		
		        
		$sStatus = $this->preParse()->prepare($aVals['user_status']);
		
		$iStatusId = $this->database()->insert(Phpfox::getT(($this->_bIsCallback ? $this->_aCallback['table_prefix'] : '') . 'feed_comment'), array(
				'user_id' => (int) Phpfox::getUserId(),
				'parent_user_id' => (int) $aVals['parent_user_id'],
				'privacy' => $aVals['privacy'],
				'privacy_comment' => $aVals['privacy_comment'],
				'content' => $sStatus,
				'time_stamp' => PHPFOX_TIME
			)
		);

		if (!defined('PHPFOX_NEW_USER_STATUS_ID'))
		{
			define('PHPFOX_NEW_USER_STATUS_ID', $iStatusId);
		}
		
		if ($this->_bIsCallback)
		{
			if ($sPlugin = Phpfox_Plugin::get('feed.service_process_addcomment__1'))
			{
				eval($sPlugin);
			}
			$sLink = $this->_aCallback['link'] . 'comment-id_' . $iStatusId . '/';
	
			if (!empty($this->_aCallback['notification']) && !Phpfox::getUserBy('profile_page_id'))
			{
				Phpfox::getLib('mail')->to($this->_aCallback['email_user_id'])
					->translated(isset($this->_aCallback['mail_translated']) ? $this->_aCallback['mail_translated'] : false)
					->subject($this->_aCallback['subject'])
					->message(sprintf($this->_aCallback['message'], $sLink))
                    ->notification( ($this->_aCallback['notification'] == 'pages_comment' ? 'comment.add_new_comment' : $this->_aCallback['notification']))
					->send();

				if (Phpfox::isModule('notification'))
				{
					Notification_Service_Process::instance()->add($this->_aCallback['notification'], $iStatusId, $this->_aCallback['email_user_id']);
				}
			}
			
			if (isset($this->_aCallback['add_tag']) && $this->_aCallback['add_tag'])
			{
				if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support'))
				{
                    Tag_Service_Process::instance()->add($this->_aCallback['feed_id'], $iStatusId, Phpfox::getUserId(), $aVals['user_status'], true);
				}
			}
			
			return Feed_Service_Process::instance()->add($this->_aCallback['feed_id'], $iStatusId, $aVals['privacy'], $aVals['privacy_comment'], (int) $aVals['parent_user_id']);
		}
		
		$aUser = $this->database()->select('user_name')
			->from(Phpfox::getT('user'))
			->where('user_id = ' . (int) $aVals['parent_user_id'])
			->execute('getSlaveRow');
		
		$sLink = Phpfox_Url::instance()->makeUrl($aUser['user_name'], array('comment-id' => $iStatusId));

        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support'))
        {
            Tag_Service_Process::instance()->add((isset($aVals['feed_type']) ? $aVals['feed_type'] : 'feed_comment'), $iStatusId, Phpfox::getUserId(), $aVals['user_status'], true);
        }

		/* When a user is tagged it needs to add a special feed */
		if (!isset($aVals['feed_reference']) || empty($aVals['feed_reference']))
		{
			Phpfox::getLib('mail')->to($aVals['parent_user_id'])
			->subject(array('feed.full_name_wrote_a_comment_on_your_wall', array('full_name' => Phpfox::getUserBy('full_name'))))
			->message(array('feed.full_name_wrote_a_comment_on_your_wall_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink)))
			->notification('comment.add_new_comment')
			->send();
			
			if (Phpfox::isModule('notification'))
			{
				Notification_Service_Process::instance()->add('feed_comment_profile', $iStatusId, $aVals['parent_user_id']);
			}
			if (isset($aVals['feed_type']))
			{
				return Feed_Service_Process::instance()->add($aVals['feed_type'], $iStatusId, $aVals['privacy'], $aVals['privacy_comment'], (int) $aVals['parent_user_id']);
			}
		}
		else
		{ // This is a special feed
			// Send mail
			return Feed_Service_Process::instance()->add('feed_comment', $iStatusId, $aVals['privacy'], $aVals['privacy_comment'], (int) $aVals['parent_user_id'], null,$aVals['feed_reference']);
		}
		return Feed_Service_Process::instance()->add('feed_comment', $iStatusId, $aVals['privacy'], $aVals['privacy_comment'], (int) $aVals['parent_user_id'], null, 0, (isset($aVals['parent_feed_id']) ? $aVals['parent_feed_id'] : 0), (isset($aVals['parent_module_id']) ? $aVals['parent_module_id'] : null));
	}
	
	public function getLastId()
	{
		return (int) $this->_iLastId;
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
		if ($sPlugin = Phpfox_Plugin::get('feed.service_process__call'))
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