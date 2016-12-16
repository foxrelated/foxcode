<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Service
 */
class Forum_Service_Thread_Process extends Phpfox_Service 
{
    /**
     * @var bool
     */
	private $_bUpdateCounter = true;
    
    /**
     * @var mixed
     */
	private $_mUpdateView = null;
    
    /**
     * @var bool
     */
	private $_bPassed = false;
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('forum_thread');
	}
    
    /**
     * @param array      $aVals
     * @param bool|array $aCallback
     * @param array      $aExtra
     *
     * @return int
     */
	public function add($aVals, $aCallback = false, $aExtra = array())
	{
		static $iLoop = 0;
		//Plugin call
		if ($sPlugin = Phpfox_Plugin::get('forum.service_thread_process_add__start')){eval($sPlugin);}
        Ban_Service_Ban::instance()->checkAutomaticBan($aVals['title'] . ' ' . $aVals['text']);
		$aAccess = Forum_Service_Forum::instance()->getUserGroupAccess($aVals['forum_id'], Phpfox::getUserBy('user_group_id'));
        if ($aAccess['can_view_thread_content']['value'] != true) {
            return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
        }
		
		if ($this->_bPassed === false)
		{
			$this->_checkType($aVals, $aCallback);
            
            if (!Phpfox_Error::isPassed()) {
                return false;
            }
			
			if ($aCallback === false)
			{
				if (isset($aVals['type_id']) && $aVals['type_id'] == 'announcement' && (Phpfox::getUserParam('forum.can_post_announcement') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aVals['forum_id'], 'post_announcement')) && !empty($aVals['announcement_forum_id']))
				{
					$this->_bPassed = true;
					
					$aChildren = Forum_Service_Forum::instance()->id($aVals['announcement_forum_id'])->getChildren();
                    $iId = 0;
					foreach (array_merge(array($aVals['announcement_forum_id']), (is_array($aChildren) ? $aChildren : array())) as $iForumid)
					{					
						$aVals['forum_id'] = $iForumid;
						
						if ($iId = $this->add($aVals))
						{
							$this->database()->insert(Phpfox::getT('forum_announcement'), array('forum_id' => $iForumid, 'thread_id' => $iId));

							if (redis()->enabled()) {
								redis()->del('forum/announcement/' . $iForumid);
							}
						}
					}
						
					return $iId;
				}				
			}
		}	
		
		$iLoop++;
	
		$oParseInput = Phpfox::getLib('parse.input');		
		
		$bHasAttachments = (Phpfox::getUserParam('forum.can_add_forum_attachments') && Phpfox::isModule('attachment') && isset($aVals['attachment']) && !empty($aVals['attachment']));		
		
		$aInsert = array(
			'forum_id' => ($aCallback === false ? $aVals['forum_id'] : 0),
			'group_id' => ($aCallback === false ? 0 : (int) $aCallback['item']),
			'is_announcement' => (isset($aVals['is_announcement']) ? $aVals['is_announcement'] : 0),
			'is_closed' => ((isset($aVals['is_closed']) && (Phpfox::getUserParam('forum.can_close_a_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aVals['forum_id'], 'close_thread'))) ? $aVals['is_closed'] : 0),
			'user_id' => (isset($aExtra['user_id']) ? $aExtra['user_id'] : Phpfox::getUserId()),
			'title' => $oParseInput->clean($aVals['title'], 255),
			'title_url' => $oParseInput->prepareTitle('forum', $aVals['title'], 'title_url', null, $this->_sTable, null, false, false),
			'time_stamp' => (isset($aExtra['user_id']) ? $aExtra['time_stamp'] : PHPFOX_TIME),
			'time_update' => (isset($aExtra['time_update']) ? $aExtra['time_update'] : PHPFOX_TIME),
			'order_id' => (isset($aVals['order_id']) ? $aVals['order_id'] : 0)
		);		
		
		if ($this->_mUpdateView !== null)
		{
			$aInsert['total_view'] = $this->_mUpdateView;
		}
		
		if (Phpfox::getUserParam('forum.approve_forum_thread') && $aCallback === false)
		{
			$aInsert['view_id'] = '1';
			$bSkipFeedEntry = true;
		}
		
		if (!empty($aVals['poll_id']) && Phpfox::isModule('poll') && Phpfox::getUserParam('poll.can_create_poll'))
		{
			$aInsert['poll_id'] = (int) $aVals['poll_id'];	
		}
		
		$iId = $this->database()->insert($this->_sTable, $aInsert);
		
		if (!empty($aVals['poll_id']) && Phpfox::isModule('poll') && Phpfox::getUserParam('poll.can_create_poll'))
		{
			$this->database()->update(Phpfox::getT('poll'), array('item_id' => $iId), 'poll_id = ' . (int) $aVals['poll_id'] . ' AND user_id = ' . Phpfox::getUserId());
		}

		$post_data = array(
			'thread_id' => $iId,
			'user_id' => (isset($aExtra['user_id']) ? $aExtra['user_id'] : Phpfox::getUserId()),
			'title' => $oParseInput->clean($aVals['title'], 255),
			'total_attachment' => 0,
			'time_stamp' => (isset($aExtra['time_update']) ? $aExtra['time_update'] : PHPFOX_TIME)
		);
		$iPostId = $this->database()->insert(Phpfox::getT('forum_post'), $post_data);
		
		$this->database()->insert(Phpfox::getT('forum_post_text'), array(
				'post_id' => $iPostId,
				'text' => $oParseInput->clean($aVals['text']),
				'text_parsed' => $oParseInput->prepare($aVals['text'])
			)
		);
		
		$this->database()->update($this->_sTable, array('start_id' => $iPostId), 'thread_id = ' . $iId);
		
		if ($aCallback === false && !isset($bSkipFeedEntry))
		{
			foreach (Forum_Service_Forum::instance()->id($aVals['forum_id'])->getParents() as $iForumid)
			{
				$this->database()->update(Phpfox::getT('forum'), array('thread_id' => $iId, 'post_id' => 0, 'last_user_id' => (isset($aExtra['user_id']) ? $aExtra['user_id'] : Phpfox::getUserId())), 'forum_id = ' . $iForumid);
                
                Forum_Service_Process::instance()->updateCounter($iForumid, 'total_thread');
			}
		}
		
		if ($this->_bUpdateCounter)
		{
            User_Service_Field_Process::instance()->updateCounter(Phpfox::getUserId(), 'total_post');
		}
		
		// If we uploaded any attachments make sure we update the 'item_id'
		if ($bHasAttachments)
		{
            Attachment_Service_Process::instance()->updateItemId($aVals['attachment'], (isset($aExtra['user_id']) ? $aExtra['user_id'] : Phpfox::getUserId()), $iPostId);
		}

		if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && Phpfox::getUserParam('forum.can_add_tags_on_threads'))
		{
            Tag_Service_Process::instance()->add(($aCallback === false ? 'forum' : 'forum_group'), $iId, (isset($aExtra['user_id']) ? $aExtra['user_id'] : Phpfox::getUserId()), $aVals['text'], true);
		}
		else
		{
			if (Phpfox::getUserParam('forum.can_add_tags_on_threads') && isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list']))))
			{
                Tag_Service_Process::instance()->add(($aCallback === false ? 'forum' : 'forum_group'), $iId, (isset($aExtra['user_id']) ? $aExtra['user_id'] : Phpfox::getUserId()), $aVals['tag_list']);
			}
		}

		if ($iLoop === 1 && empty($aExtra) && !isset($bSkipFeedEntry) && !Forum_Service_Forum::instance()->isPrivateForum($aVals['forum_id']))
		{
			((Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY')) ? Feed_Service_Process::instance()->callback($aCallback)->add('forum', $iId, 0, 0, ($aCallback === null ? 0 : $aCallback['item'])) : null);

			if ($aCallback && Phpfox::isModule('notification') && Phpfox::isModule($aCallback['module']) && Phpfox::hasCallback($aCallback['module'], 'addItemNotification'))
			{
				Phpfox::callback($aCallback['module'] . '.addItemNotification', ['page_id' => $aCallback['item'], 'item_perm' => 'forum.who_can_view_browse_discussions', 'item_type' => 'forum', 'item_id' => $iId]);
			}
		}

		if (!isset($bSkipFeedEntry))
		{
			// Update user activity
			User_Service_Activity::instance()->update((isset($aExtra['user_id']) ? $aExtra['user_id'] : Phpfox::getUserId()), 'forum');
		}

		if (isset($aVals['is_subscribed']) && $aVals['is_subscribed'])
		{
            Forum_Service_Subscribe_Process::instance()->add($iId, (isset($aExtra['user_id']) ? $aExtra['user_id'] : Phpfox::getUserId()));
		}
        
        //Plugin call
        if ($sPlugin = Phpfox_Plugin::get('forum.service_thread_process_add__end')) {
            eval($sPlugin);
        }

		if (redis()->enabled()) {
			redis()->set('forum/thread/' . $iId, $aInsert);
			redis()->set('forum/post/' . $iPostId, $post_data);
			redis()->lpush('forum/recent/threads', $iId);
			redis()->ltrim('forum/recent/threads', 0, 20);
		}

		return $iId;
	}
    
    /**
     * @param int   $iId
     * @param int   $iUserId
     * @param array $aVals
     *
     * @return bool
     */
	public function update($iId, $iUserId, $aVals)
	{
		$oParseInput = Phpfox::getLib('parse.input');
        Ban_Service_Ban::instance()->checkAutomaticBan($aVals['title'] . ' ' . $aVals['text']);

		$this->_checkType($aVals);

		$bHasAttachments = (Phpfox::getUserParam('forum.can_add_forum_attachments') && Phpfox::isModule('attachment') && !empty($aVals['attachment']) && $iUserId == Phpfox::getUserId());

		// If we uploaded any attachments make sure we update the 'item_id'
		if ($bHasAttachments)
		{
            Attachment_Service_Process::instance()->updateItemId($aVals['attachment'], $iUserId, $aVals['post_id']);
		}

		$aUpdate = array(
			'is_closed' => ((isset($aVals['is_closed']) && Phpfox::getUserParam('forum.can_close_a_thread')) ? $aVals['is_closed'] : 0),
			'title' => $oParseInput->clean($aVals['title'], 255),
			'order_id' => (isset($aVals['order_id']) ? $aVals['order_id'] : 0)
		);

		if (!empty($aVals['poll_id']) && Phpfox::isModule('poll') && Phpfox::getUserParam('poll.can_create_poll'))
		{
			$aUpdate['poll_id'] = (int) $aVals['poll_id'];
		}

		$this->database()->update($this->_sTable, $aUpdate, 'thread_id = ' . (int) $iId);

		$this->database()->update(Phpfox::getT('forum_post'), array(
				'total_attachment' => (Phpfox::isModule('attachment') ? Attachment_Service_Attachment::instance()->getCountForItem($aVals['post_id'], 'forum') : 0),
				'title' => $oParseInput->clean($aVals['title'], 255),
				'update_time' => PHPFOX_TIME,
				'update_user' => substr(Phpfox::getUserBy('full_name'), 0, 100)
			), 'post_id = ' . (int) $aVals['post_id']
		);

		$this->database()->update(Phpfox::getT('forum_post_text'), array(
				'text' => $oParseInput->clean($aVals['text']),
				'text_parsed' => $oParseInput->prepare($aVals['text'])
			), 'post_id = ' . (int) $aVals['post_id']
		);

		if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && Phpfox::getUserParam('forum.can_add_tags_on_threads'))
		{
            Tag_Service_Process::instance()->update('forum', $iId, $iUserId, $aVals['text'], true);
		}
		else
		{
			if (Phpfox::getUserParam('forum.can_add_tags_on_threads') && Phpfox::isModule('tag') && isset($aVals['tag_list']) && !empty($aVals['tag_list']))
			{
                Tag_Service_Process::instance()->update('forum', $iId, $iUserId, $aVals['tag_list']);
			}
		}
		
		if (Phpfox::isModule('feed'))
		{
			$aThread = Forum_Service_Thread_Thread::instance()->getForEdit($iId);
			$aPosts = $this->database()->select('post_id')
				->from(Phpfox::getT('forum_post'))
				->where('thread_id = ' . (int) $iId)
				->execute('getSlaveRows');
			foreach ($aPosts as $aPost)
			{
				Feed_Service_Process::instance()->update('forum_post', $aPost['post_id'], serialize(array('post_id' => $aPost['post_id'], 'forum_id' => $aThread['forum_id'], 'forum_url' => $aThread['forum_url'], 'thread_url' => $aThread['title_url'], 'thread_title' => $aThread['title'])));
			}
		}

		Feed_Service_Process::instance()->update('forum', $iId);

		(($sPlugin = Phpfox_Plugin::get('forum.service_process_approve__1')) ? eval($sPlugin) : false);

		return true;
	}
    
    /**
     * @param int $iThread
     *
     * @return void
     */
	public function updateTrack($iThread)
	{
		$cache = cache('forum/track/thread/' . $iThread . '/' . Phpfox::getUserId());
		if (!$cache->exists()) {
			$this->database()->update(Phpfox::getT('forum_thread'), array('total_view' => array('= total_view +', 1)), 'thread_id = ' . (int)$iThread);
			$cache->set('set');
		}
	}
    
    /**
     * @param int $iThread
     * @param int $iForum
     *
     * @return bool
     */
	public function move($iThread, $iForum)
	{
		$aThread = $this->database()->select('ft.thread_id, ft.post_id, ft.user_id, ft.last_user_id, ft.forum_id, COUNT(fp.post_id) AS total_posts')
			->from($this->_sTable, 'ft')
			->leftJoin(Phpfox::getT('forum_post'), 'fp', 'fp.thread_id = ft.thread_id')
			->where('ft.thread_id = ' . (int) $iThread)
			->group('ft.forum_id, ft.thread_id')
			->execute('getSlaveRow');

		$iPosts = 0;
        if ($aThread['total_posts'] > 1) {
            $iPosts = ($aThread['total_posts'] - 1);
        }
        
        foreach (Forum_Service_Forum::instance()->id($aThread['forum_id'])->getParents() as $iForumId) {
            Forum_Service_Process::instance()->updateCounter($iForumId, 'total_thread', true);
        }
		
		foreach (Forum_Service_Forum::instance()->id($iForum)->getParents() as $iForumId)
		{
            Forum_Service_Process::instance()->updateCounter($iForumId, 'total_thread');
		}	
		
		if ($iPosts)
		{
			foreach (Forum_Service_Forum::instance()->id($aThread['forum_id'])->getParents() as $iForumId)
			{
                Forum_Service_Process::instance()->updateCounter($iForumId, 'total_post', true, $iPosts);
			}
			
			foreach (Forum_Service_Forum::instance()->id($iForum)->getParents() as $iForumId)
			{
                Forum_Service_Process::instance()->updateCounter($iForumId, 'total_post', false, $iPosts);
			}
		}
		
		$this->database()->update($this->_sTable, array(
				'forum_id' => $iForum
			), 'thread_id = ' . (int) $iThread
		);
		
		$aOldThread = $this->database()->select('thread_id, user_id, post_id, last_user_id')
			->from(Phpfox::getT('forum_thread'), 'ft')
			->where('ft.forum_id = ' . (int) $aThread['forum_id'])
			->order('ft.time_update DESC')
			->execute('getSlaveRow');

		foreach (Forum_Service_Forum::instance()->id($aThread['forum_id'])->getParents() as $iForumId)
		{
		    if (isset($aOldThread['thread_id']))
		    {
			    $this->database()->update(Phpfox::getT('forum'), array('thread_id' => $aOldThread['thread_id'], 'post_id' => $aOldThread['post_id'], 'last_user_id' => (empty($aOldThread['last_user_id']) ? $aOldThread['user_id'] : $aOldThread['last_user_id'])), 'forum_id = ' . $iForumId);
		    }
		    else
		    {
			    $this->database()->update(Phpfox::getT('forum'), array('thread_id' => 0, 'post_id' => 0, 'last_user_id' => 0), 'forum_id = ' . $iForumId);
		    }
		}

		foreach (Forum_Service_Forum::instance()->id($iForum)->getParents() as $iForumId)
		{
			$this->database()->update(Phpfox::getT('forum'), array('thread_id' => $aThread['thread_id'], 'post_id' => $aThread['post_id'], 'last_user_id' => (empty($aThread['last_user_id']) ? $aThread['user_id'] : $aThread['last_user_id'])), 'forum_id = ' . $iForumId);
		}
		
		return true;
	}
    
    /**
     * @param bool $bUpdate
     *
     * @return $this
     */
	public function counter($bUpdate)
	{
		$this->_bUpdateCounter = $bUpdate;
		return $this;
	}
    
    /**
     * @param int $iView
     *
     * @return $this
     */
	public function view($iView)
	{
		$this->_mUpdateView = $iView;
		return $this;
	}
    
    /**
     * @param int         $iThread
     * @param int         $iForum
     * @param null|string $sTitle
     *
     * @return bool
     */
	public function copy($iThread, $iForum, $sTitle = null)
	{
		$aThread = $this->database()->select('ft.*, fpt.text')
			->from($this->_sTable, 'ft')	
			->join(Phpfox::getT('forum_post_text'), 'fpt', 'fpt.post_id = ft.start_id')		
			->where('ft.thread_id = ' . (int) $iThread)			
			->execute('getSlaveRow');
        
        if ($sTitle !== null) {
            $aThread['title'] = $sTitle;
        }
		
		$aThread['forum_id'] = $iForum;
        
        if ((int)$aThread['order_id'] === 1) {
            $aThread['type_id'] = 'sticky';
        }
		
		$iNewThreadId = $this->counter(false)->view($aThread['total_view'])->add($aThread, false, $aThread);
		
		$aPosts = $this->database()->select('fp.*, fpt.text')
			->from(Phpfox::getT('forum_post'), 'fp')
			->join(Phpfox::getT('forum_post_text'), 'fpt', 'fpt.post_id = fp.post_id')
			->where('fp.thread_id = ' . (int) $iThread)
			->execute('getSlaveRows');
        
        foreach ($aPosts as $aPost) {
            $aPost['thread_id'] = $iNewThreadId;
            $aPost['forum_id'] = $iForum;
            
            if ($aThread['start_id'] == $aPost['post_id']) {
                continue;
            }
            
            Forum_Service_Post_Process::instance()->counter(false)->add($aPost, false, $aPost);
        }
		
		return true;
	}
    
    /**
     * @param int $iThread
     *
     * @return bool
     */
	public function delete($iThread)
	{
		$aThread = $this->database()->select('ft.forum_id, ft.group_id, ft.thread_id, ft.user_id')
			->from($this->_sTable, 'ft')
			->where('ft.thread_id = ' . (int) $iThread)
			->execute('getSlaveRow');
		
		$this->database()->delete($this->_sTable, 'thread_id = ' . $iThread);
		
		(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->delete('forum', $iThread) : null);
		
		$aLastThread = $this->database()->select('thread_id, post_id, user_id')
			->from(Phpfox::getT('forum_thread'))
			->where('forum_id = ' . $aThread['forum_id'])
			->order('time_update DESC')
			->execute('getSlaveRow');
				
		if (isset($aLastThread['thread_id']))
		{				
			$this->database()->update(Phpfox::getT('forum'), array('thread_id' => $aLastThread['thread_id'], 'post_id' => $aLastThread['post_id'], 'last_user_id' => $aLastThread['user_id']), 'thread_id = ' . $aThread['thread_id']);
		}
		else 
		{
			if ($aThread['forum_id'] > 0)
			{
				$this->database()->update(Phpfox::getT('forum'), array('thread_id' => 0, 'post_id' => 0, 'last_user_id' => 0), 'thread_id = ' . $aThread['forum_id']);	
			}			
		}		
		
		$aPosts = $this->database()->select('post_id, user_id')
			->from(Phpfox::getT('forum_post'))
			->where('thread_id = ' . $aThread['thread_id'])
			->execute('getSlaveRows');
			
		$iTotal = 0;
		foreach ($aPosts as $aPost)
		{
			$iTotal++;
			
			$this->database()->delete(Phpfox::getT('forum_post'), 'post_id = ' . $aPost['post_id']);
			$this->database()->delete(Phpfox::getT('forum_post_text'), 'post_id = ' . $aPost['post_id']);
            
            User_Service_Field_Process::instance()->updateCounter($aPost['user_id'], 'total_post', true);
			User_Service_Activity::instance()->update($aPost['user_id'], 'forum', '-');
			
			(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->delete('forum_post', $aPost['post_id']) : null);

			(Phpfox::isModule('like') ? Like_Service_Process::instance()->delete('forum_post',(int) $aPost['post_id'], 0, true) : null);

            (Phpfox::isModule('notification') ? Notification_Service_Process::instance()->deleteAllOfItem(['forum_post_like'],(int) $aPost['post_id']) : null);
		}

		// the first post of a thread isn't calculated
        $iTotal -= 1;
		
		if ($aThread['group_id'] == '0')
		{
			foreach (Forum_Service_Forum::instance()->id($aThread['forum_id'])->getParents() as $iForumId)
			{
                Forum_Service_Process::instance()->updateCounter($iForumId, 'total_thread', true);
                if ($iTotal > 0)
                {
                    Forum_Service_Process::instance()->updateCounter($iForumId, 'total_post', true, $iTotal);
                }
			}		
		}
		
		(($sPlugin = Phpfox_Plugin::get('forum.service_process_delete__1')) ? eval($sPlugin) : false);
		
		return true;
	}
    
    /**
     * @param int $iThread
     * @param int $iType
     *
     * @return bool
     */
	public function stick($iThread, $iType = 1)
	{
		$this->database()->update($this->_sTable, array('order_id' => $iType), 'thread_id = ' . (int) $iThread);		
		return true;
	}
	
	public function close($iThread, $iType = 1)
	{
		$this->database()->update($this->_sTable, array('is_closed' => $iType), 'thread_id = ' . (int) $iThread);		
		(($sPlugin = Phpfox_Plugin::get('forum.service_process_close__1')) ? eval($sPlugin) : false);
		return true;
	}
    
    /**
     * @todo This needs to work with user perms.
     *
     * @param int    $iThread
     * @param int    $iNewForumId
     * @param string $sUrl
     *
     * @return string
     */
	public function merge($iThread, $iNewForumId, $sUrl)
	{		
		if (!preg_match("/^" . str_replace('/', '\/', Phpfox_Url::instance()->getDomain()) . "(.*?)$/i", $sUrl, $aMatches))
		{
			return Phpfox_Error::set(_p('not_a_valid_forum_url_missing_forum_path'));
		}
		
		$aParams = Phpfox_Url::instance()->parseUrl($sUrl);

		if (!isset($aParams['req3']))
		{
			return Phpfox_Error::set(_p('not_a_valid_forum_url_missing_thread_id'));
		}		
		
		$aThread = $this->database()->select('ft.*')
			->from($this->_sTable, 'ft')
			->where('ft.thread_id = ' . (int) $iThread)
			->execute('getSlaveRow');
		
		if (!isset($aThread['thread_id']))
		{
			return Phpfox_Error::set(_p('not_a_valid_forum_url_thread_is_not_valid'));
		}
		
		$bIsGroup = false;
		if ((int) $aThread['group_id'] > 0)
		{
			$bIsGroup = true;
		}		
		
		$aOtherThread = $this->database()->select('ft.*')
			->from($this->_sTable, 'ft')
			->where('ft.group_id = ' . ($bIsGroup ? $aThread['group_id'] : '0') . ' AND ft.view_id = 0 AND ft.thread_id = ' . (int) $aParams['req3'])			
			->execute('getSlaveRow');
		
		if (!isset($aOtherThread['thread_id']))
		{
			return Phpfox_Error::set(_p('not_a_valid_forum_url_merging_thread_is_not_valid'));
		}
		
		if ($aThread['thread_id'] == $aOtherThread['thread_id'])
		{
			return Phpfox_Error::set(_p('you_cannot_merge_the_same_thread'));
		}
		
		if ($bIsGroup && $aThread['group_id'] != $aOtherThread['group_id'])
		{
			return Phpfox_Error::set(_p('you_cannot_merge_this_thread_as_it_belongs_to_another_group_forum'));
		}
		
		// Find the thread we plan to delete and merge with the thread we plan to keep
		$aNewThread = ($aThread['time_stamp'] > $aOtherThread['time_stamp'] ? $aThread : $aOtherThread);
		
		// Find the thread we plan to keep as the main thread
		$aOldThread = ($aThread['time_stamp'] < $aOtherThread['time_stamp'] ? $aThread : $aOtherThread);		
		
		// Delete the newer thread
		$this->database()->delete($this->_sTable, 'thread_id = ' . $aNewThread['thread_id']);
        		
		// Update the new posts with the merged thread id
		$this->database()->update(Phpfox::getT('forum_post'), array('thread_id' => $aOldThread['thread_id']), 'thread_id = ' . $aNewThread['thread_id']);
		
		$iTotalPosts = ($aNewThread['total_post'] + $aOldThread['total_post'] + 1);
		$this->database()->update($this->_sTable, array(
				'forum_id' => ($bIsGroup ? 0 : $iNewForumId),
				'total_post' => $iTotalPosts,
				'total_view' => ($aNewThread['total_view'] + $aOldThread['total_view']),
			), 'thread_id = ' . $aOldThread['thread_id']
		);
		
		if (!$bIsGroup)
		{
			foreach (Forum_Service_Forum::instance()->id($aNewThread['forum_id'])->getParents() as $iForumId)
			{
                Forum_Service_Process::instance()->updateCounter($iForumId, 'total_thread', true);
                Forum_Service_Process::instance()->updateCounter($iForumId, 'total_post', true, $aNewThread['total_post']);
			}
			
			foreach (Forum_Service_Forum::instance()->id($aOldThread['forum_id'])->getParents() as $iForumId)
			{
                Forum_Service_Process::instance()->updateCounter($iForumId, 'total_thread', true);
                Forum_Service_Process::instance()->updateCounter($iForumId, 'total_post', true, $aOldThread['total_post']);
			}
		
			foreach (Forum_Service_Forum::instance()->id($iNewForumId)->getParents() as $iForumId)
			{
                Forum_Service_Process::instance()->updateCounter($iForumId, 'total_thread');
                Forum_Service_Process::instance()->updateCounter($iForumId, 'total_post', false, $iTotalPosts);
	
				$this->database()->update(Phpfox::getT('forum'), array('thread_id' => $aOldThread['thread_id'], 'post_id' => 0), 'forum_id = ' . $iForumId);		
			}	
            
            // Update the last post from the parent forum
            Forum_Service_Process::instance()->updateLastPost($aNewThread['forum_id']);
            Forum_Service_Process::instance()->updateLastPost($aOldThread['forum_id']);
            
		}
		
		$aForum = Forum_Service_Forum::instance()
			->id($iNewForumId)
			->getForum();		
			
		return Phpfox_Url::instance()->makeUrl('forum', array($aForum['name_url'] . '-' . $aForum['forum_id'], $aOldThread['title_url']));
	}
    
    /**
     * @param int $iForumId
     * @param int $iGroupId
     *
     * @return bool
     */
	public function markRead($iForumId, $iGroupId = 0)
	{
        //Check can view this forum
        if (!Forum_Service_Thread_Thread::instance()->canViewForumId($iForumId)){
            return false;
        }
		list($iCnt, $aThreads) = Forum_Service_Thread_Thread::instance()->get(array('ft.forum_id = ' . (int) $iForumId .' AND ft.group_id = ' . (int) $iGroupId . ' AND ft.view_id = 0'));
        
        foreach ($aThreads as $aThread) {
            Forum_Service_Thread_Process::instance()->updateTrack($aThread['thread_id']);
        }
		
		return true;
	}
    
    /**
     * Sets or removes the sponsor status of a thread
     *
     * @param int $iThreadId
     * @param int $iType
     *
     * @return boolean
     */
	public function sponsor($iThreadId, $iType)
	{
	    if (!Phpfox::getUserParam('forum.can_sponsor_thread') && !Phpfox::getUserParam('forum.can_purchase_sponsor') && !defined('PHPFOX_API_CALLBACK'))
	    {
			return Phpfox_Error::set(_p('hack_attempt'));
	    }
	    $iType = (int)$iType;
	    $this->database()->update($this->_sTable,
		    array('order_id' => ($iType == 2 ? 2 : 0)), // 2 : sponsored; 1 : featured; 0 : normal
		    'thread_id = ' . (int)$iThreadId);
	    if ($sPlugin = Phpfox_Plugin::get('forum.service_thread_process_sponsor__end')){return eval($sPlugin);}
	    return true;
	}
    
    /**
     * @param int $iThreadId
     *
     * @return bool
     */
	public function approve($iThreadId)
	{
		$aThread = $this->database()->select('*')
			->from(Phpfox::getT('forum_thread'))
			->where('thread_id = ' . (int) $iThreadId)
			->execute('getSlaveRow');
        
        if (!isset($aThread['thread_id'])) {
            return false;
        }
		
		$this->database()->update(Phpfox::getT('forum_thread'), array('view_id' => '0'), 'thread_id = ' . (int) $iThreadId);
		
		foreach (Forum_Service_Forum::instance()->id($aThread['forum_id'])->getParents() as $iForumid)
		{
			$this->database()->update(Phpfox::getT('forum'), array('thread_id' => $iThreadId, 'post_id' => 0, 'last_user_id' => $aThread['user_id']), 'forum_id = ' . $iForumid);
            
            Forum_Service_Process::instance()->updateCounter($iForumid, 'total_thread');
		}
        
        User_Service_Field_Process::instance()->updateCounter($aThread['user_id'], 'total_post');
		
		((Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY')) ? Feed_Service_Process::instance()->add('forum_thread', $iThreadId, 0, 0, 0, $aThread['user_id']) : null);
		
		$sCurrentUrl = Phpfox::permalink('forum.thread', $aThread['thread_id'], $aThread['title']);
		
		// Update user activity
		User_Service_Activity::instance()->update($aThread['user_id'], 'forum');
		
		(($sPlugin = Phpfox_Plugin::get('forum.service_thread_process_approve__1')) ? eval($sPlugin) : false);
		
		if (Phpfox::isModule('notification'))
		{
			Notification_Service_Process::instance()->add('forum_thread_approved', $aThread['thread_id'], $aThread['user_id']);
		}
		
		Phpfox::getLib('mail')->to($aThread['user_id'])
			->subject(array('thread_approved_on_site_title', array('site_title' => Phpfox::getParam('core.site_title'))))
			->message(array('your_thread_title_on_site_title_has_been_approved', array('thread_title' => $aThread['title'], 'site_title' => Phpfox::getParam('core.site_title'), 'link' => $sCurrentUrl)))
			->send();
		
		return true;
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
        if ($sPlugin = Phpfox_Plugin::get('forum.service_thread_process__call')) {
            eval($sPlugin);
            return null;
        }
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
    
    /**
     * @param array $aVals
     * @param bool  $aCallback
     *
     * @return mixed
     */
	private function &_checkType(&$aVals, $aCallback = false)
	{
		unset($aVals['order_id'], $aVals['is_announcement']);	
		
		if (isset($aVals['type_id']))
		{
			switch ($aVals['type_id'])
			{
				case 'sticky':
					$bHasAccess = false;
					if ($aCallback !== false)
					{
						if (Phpfox::isModule('pages') && Pages_Service_Pages::instance()->isAdmin($aCallback['group_id']))
						{
							$bHasAccess = true;	
						}
					}
					else 
					{
						if (Phpfox::getUserParam('forum.can_stick_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aVals['forum_id'], 'post_sticky'))
						{
							$bHasAccess = true;
						}
					}					
					if ($bHasAccess)
					{
						$aVals['order_id'] = 1;
					}
					break;
				case 'announcement':
					$bHasAccess = false;
					if ($aCallback !== false)
					{
						if (Pages_Service_Pages::instance()->isAdmin($aCallback['group_id']))
						{
							$bHasAccess = true;	
						}
					}
					else 
					{
						if ((Phpfox::getUserParam('forum.can_post_announcement') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aVals['forum_id'], 'post_announcement')) && !empty($aVals['announcement_forum_id']))
						{
							$bHasAccess = true;
						}
					}
					
					if ($bHasAccess)
					{
						$aVals['is_announcement'] = 1;
					}
					else 
					{
						Phpfox_Error::set(_p('select_a_forum_this_announcement_will_belong_to'));
						
						return $aVals;
					}
					break;
				case 'sponsor':
					if (!Phpfox::getUserParam('forum.can_sponsor_thread'))
					{
					    Phpfox_Error::set(_p('you_are_not_allowed_to_mark_threads_as_sponsor'));
					    return $aVals;
					}
					$aVals['order_id'] = 2;
					break;

			}		
		}
		
		return $aVals;
	}
}