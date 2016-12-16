<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Forum
 */
class Forum_Service_Callback extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct() { }
    
    /**
     * @param int $iStartTime
     * @param int $iEndTime
     *
     * @return array
     */
	public function getSiteStatsForAdmin($iStartTime, $iEndTime)
	{
        $aCond = [];
        if ($iStartTime > 0) {
            $aCond[] = 'AND time_stamp >= \'' . $this->database()->escape($iStartTime) . '\'';
        }
        if ($iEndTime > 0) {
            $aCond[] = 'AND time_stamp <= \'' . $this->database()->escape($iEndTime) . '\'';
        }
		
		$iCnt = (int) $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('forum_thread'))
			->where($aCond)
			->execute('getSlaveField');
        
        $aCond = [];
        if ($iStartTime > 0) {
            $aCond[] = 'AND time_stamp >= \'' . $this->database()->escape($iStartTime) . '\'';
        }
        if ($iEndTime > 0) {
            $aCond[] = 'AND time_stamp <= \'' . $this->database()->escape($iEndTime) . '\'';
        }
        
        $iForumCnt = (int) $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('forum_post'))
			->where($aCond)
			->execute('getSlaveField');
        
        return [
            [
                'phrase' => 'forum.forum_threads',
                'total'  => $iCnt
            ],
            [
                'phrase' => 'forum.forum_posts',
                'total'  => $iForumCnt
            ]
        ];
    }
    
    /**
     * @param array $aParams
     *
     * @return bool|null
     */
    public function enableSponsor($aParams)
	{
	    if ($aParams['section'] == 'thread')
	    {
			return Forum_Service_Thread_Process::instance()->sponsor($aParams['item_id'], 2);
	    }
        return null;
	}
    
    /**
     * @param array $aParams
     *
     * @return string
     */
	public function getLink($aParams)
	{
	    $aItem = $this->database()->select('ft.thread_id, ft.title')
		    ->from(Phpfox::getT('forum_thread'),'ft')
		    ->where('ft.thread_id = ' . (int)$aParams['item_id'])
		    ->execute('getSlaveRow');
		
	    return Phpfox::permalink('forum.thread', $aItem['thread_id'], $aItem['title']);
	}
    
    /**
     * @return array
     */
	public function getAttachmentField()
	{
        return [
            'forum_post',
            'post_id'
        ];
    }
    
    /**
     * @return string
     */
    public function getTagLink()
	{		
		return Phpfox_Url::instance()->makeUrl('forum.tag');
	}
    
    /**
     * @return string
     */
	public function getTagLinkGroup()
	{		
		return Phpfox_Url::instance()->makeUrl('forum.tag', array('module' => 'group', 'item' => Phpfox_Request::instance()->get('req2')));
	}
    
    /**
     * @return string
     */
	public function getTagTypeGroup()
	{
		return 'forum';
	}
    
    /**
     * @return string
     */
	public function getTagType()
	{
		return 'forum';
	}
    
    /**
     * @return array
     */
	public function getTagCloud()
	{
        return [
            'link'     => 'forum',
            'category' => 'forum'
        ];
    }
    
    /**
     * @param array $aRow
     *
     * @return mixed
     */
    public function getNewsFeed($aRow)
	{
		if ($sPlugin = Phpfox_Plugin::get('forum.service_callback_getnewsfeed_start')){eval($sPlugin);}
		$oUrl = Phpfox_Url::instance();
		
		$aRow['text'] = _p('owner_full_name_added_a_new_thread', array(
				'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
				'owner_full_name' => $this->preParse()->clean($aRow['owner_full_name']),
				'title_link' => $aRow['link'],
				'title' => Feed_Service_Feed::instance()->shortenTitle($aRow['content'])
			)
		);		
		
		$aRow['icon'] = 'module/forum.png';
		$aRow['enable_like'] = true;
		
		return $aRow;
	}
    
    /**
     * @param int $iId
     *
     * @return bool|string
     */
	public function getFeedRedirect($iId)
	{
		$aThread = $this->database()->select('ft.thread_id, ft.forum_id, ft.group_id, ft.title_url, u.user_id, u.user_name, f.name_url AS forum_url')
			->from(Phpfox::getT('forum_thread'), 'ft')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ft.user_id')
			->leftJoin(Phpfox::getT('forum'), 'f', 'f.forum_id = ft.forum_id')
			->where('ft.thread_id = ' . (int) $iId)
			->execute('getSlaveRow');
        
        if (!isset($aThread['thread_id'])) {
            return false;
        }
        
        if ($aThread['group_id'] > 0) {
            return Phpfox_Url::instance()->makeUrl('group.forum', [
                $aThread['title_url'],
                'id' => $aThread['group_id']
            ]);
        } else {
            return Phpfox_Url::instance()->makeUrl('forum', [
                $aThread['forum_url'] . '-' . $aThread['forum_id'],
                $aThread['title_url']
            ]);
        }
    }
    
    /**
     * @param int $iId
     *
     * @return bool|string
     */
    public function getFeedRedirectPost($iId)
	{
		$aThread = $this->database()->select('fp.post_id, ft.thread_id, ft.title')
			->from(Phpfox::getT('forum_post'), 'fp')
			->leftJoin(Phpfox::getT('forum_thread'), 'ft', 'ft.thread_id = fp.thread_id')
			->leftJoin(Phpfox::getT('forum'), 'f', 'f.forum_id = ft.forum_id')
			->where('fp.post_id = ' . (int) $iId)
			->execute('getSlaveRow');
        
        if (!isset($aThread['post_id'])) {
            return false;
        }
		
		return Phpfox::permalink('forum.thread', $aThread['thread_id'], $aThread['title']) . 'view_' . $aThread['post_id'] . '/';
	}
    
    /**
     * @param int $iId video_id
     *
     * @return array in the format:
     * array(
     *    'title' => 'item title',            <-- required
     *  'link'  => 'makeUrl()'ed link',            <-- required
     *  'paypal_msg' => 'message for paypal'        <-- required
     *  'item_id' => int                <-- required
     *  'user_id'   => owner's user id            <-- required
     *    'error' => 'phrase if item doesnt exit'        <-- optional
     *    'extra' => 'description'            <-- optional
     *    'image' => 'path to an image',            <-- optional
     *    'image_dir' => 'photo.url_photo|...        <-- optional (required if image)
     * )
     */
	public function getToSponsorThreadInfo($iId)
	{
	    $aThread = $this->database()->select('fp.user_id, f.name, f.name_url, fpt.text_parsed as extra,
		fp.thread_id as item_id, ft.title, ft.title_url')
		    ->from(Phpfox::getT('forum'),'f')
		    ->join(Phpfox::getT('forum_thread'),'ft','ft.forum_id = f.forum_id')
		    ->join(Phpfox::getT('forum_post'),'fp', 'fp.thread_id = ft.thread_id')
		    ->join(Phpfox::getT('forum_post_text'),'fpt','fpt.post_id = fp.post_id')
		    ->where('fp.thread_id = ' . (int)$iId)
		    ->execute('getSlaveRow');
	    
	    if (empty($aThread))
	    {
			return array('error' => _p('sponsor_error_not_found'));
	    }
	    
	    $aThread['title'] = _p('sponsor_title',array('sThreadTitle' => $aThread['title']));
	    $aThread['paypal_msg'] = _p('sponsor_paypal_message', array('sThreadTitle' => $aThread['title']));
	    $aThread['link'] = Phpfox_Url::instance()->makeUrl('forum.'.$aThread['name_url'].'.'.$aThread['title_url']);
	    
	    return $aThread;
	}
    
    /**
     * @param int $iId
     *
     * @return bool|string
     */
	public function getReportRedirectPost($iId)
	{
		return $this->getFeedRedirectPost($iId);
	}
    
    /**
     * Action to take when user cancelled their account
     *
     * @param int $iUser
     *
     * @return boolean
     */
	public function onDeleteUser($iUser)
	{
		// get all the post id
		$aPosts = $this->database()
			->select('post_id')
			->from(Phpfox::getT('forum_post'))
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveRows');
		foreach ($aPosts as $aPost)
		{
            Forum_Service_Post_Process::instance()->delete($aPost['post_id']);
		}
		// Get all the thread id
		$aThreads = $this->database()
			->select('thread_id')
			->from(Phpfox::getT('forum_thread'))
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveRows');
		foreach ($aThreads as $aThread)
		{
            Forum_Service_Thread_Process::instance()->delete($aThread['thread_id']);
		}

		// Delete the moderators
		$iModerator = $this->database()
			->select('moderator_id')
			->from(Phpfox::getT('forum_moderator'))
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveField');
		if (isset($iModerator) && $iModerator > 0)
		{
			$this->database()->delete(Phpfox::getT('forum_moderator_access'), 'moderator_id = ' . $iModerator);
			$this->database()->delete(Phpfox::getT('forum_moderator'), 'user_id = ' . (int)$iUser);
		}

		// Delete the tracks
		$this->database()->delete(Phpfox::getT('track'), 'user_id = ' . (int)$iUser . ' AND type_id="forum_thread"');
		$this->database()->delete(Phpfox::getT('track'), 'user_id = ' . (int)$iUser . ' AND type_id="forum"');
		
		$aForums = $this->database()->select('forum_id')
			->from(Phpfox::getT('forum'))
			->execute('getSlaveRows');
		foreach ($aForums as $aForum)
		{
            Forum_Service_Process::instance()->updateLastPost($aForum['forum_id']);
		}
		
		// delete the cache moderators
		$this->cache()->remove('forum', 'substr');
		return true;
	}
    
    /**
     * @param string $sGroupUrl
     * @param int    $iGroupId
     *
     * @return array
     */
	public function groupMenu($sGroupUrl, $iGroupId)
	{
        return [
            _p('forum') => [
                'active' => 'forum',
                'url'    => Phpfox_Url::instance()->makeUrl('group', [$sGroupUrl, 'forum'])
            ]
        ];
    }
    
    /**
     * @return array
     */
    public function getGroupAccess()
	{
        return [
            _p('view_forum') => 'can_use_forum'
        ];
    }
    
    /**
     * @return array
     */
    public function getDashboardActivity()
	{
		$aUser = User_Service_User::instance()->get(Phpfox::getUserId(), true);
        
        return [
            _p('forum_posts') => $aUser['activity_forum']
        ];
    }
    
    /**
     * @param array $aRow
     *
     * @return array
     */
    public function getNotificationFeedSubscribed_Post($aRow)
	{
		return array(
			'message' => _p('full_name_replied_to_the_thread_title', array(
					'full_name' => $aRow['full_name'], 'title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...'),
					'user_link' => Phpfox_Url::instance()->makeUrl($aRow['user_name']),
					'link' => Phpfox_Url::instance()->makeUrl('forum.thread', array('predirect' => $aRow['item_id']))
				)
			),
			'link' => Phpfox_Url::instance()->makeUrl('forum.thread', array('predirect' => $aRow['item_id'])),
			'path' => 'core.url_user',
			'suffix' => '_50'
		);
	}
    
    /**
     * @param array $aRow
     *
     * @return array
     */
	public function getNotificationFeedSubscribed($aRow)
	{
		return array(
			'message' => _p('full_name_replied_to_the_thread_title', array(
					'full_name' => $aRow['full_name'], 'title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...'), 
					'user_link' => Phpfox_Url::instance()->makeUrl($aRow['user_name']),
					'link' => Phpfox_Url::instance()->makeUrl('forum.thread', array('redirect' => $aRow['item_id']))
				)
			),		
			'link' => Phpfox_Url::instance()->makeUrl('forum.thread', array('redirect' => $aRow['item_id'])),
			'path' => 'core.url_user',
			'suffix' => '_50'
		);				
	}
    
    /**
     * @return array
     */
	public function getNotificationSettings()
	{
        return [
            'forum.subscribe_new_post' => [
                'phrase'  => _p('forum_subscriptions'),
                'default' => 1
            ]
        ];
    }
    
    /**
     * @param array $aRequest
     *
     * @return array|string
     */
    public function legacyRedirect($aRequest)
	{
		if (isset($aRequest['req2']))
		{
			switch ($aRequest['req2'])
			{
				case 'topics':
					if (isset($aRequest['id']))
					{
						$aItem = Core_Service_Core::instance()->getLegacyUrl(array(
								'url_field' => 'name_url',
								'table' => 'forum',
								'field' => 'upgrade_item_id',
								'id' => $aRequest['id'],
								'user_id' => false,
								'select' => array(
									'forum_id'
								)				
							)
						);					
						
						if ($aItem !== false)
						{
							return array('forum', array($aItem['name_url'] . '-' . $aItem['forum_id']));
						}
					}					
					break;
				case 'posts':
					if (isset($aRequest['id']))
					{
						$this->database()->select('forum.name_url AS forum_name_url, forum.forum_id AS forum_id, ')->join(Phpfox::getT('forum'), 'forum', 'forum.forum_id = i.forum_id');
						
						$aItem = Core_Service_Core::instance()->getLegacyUrl(array(
								'url_field' => 'title_url',
								'table' => 'forum_thread',
								'field' => 'upgrade_item_id',
								'id' => $aRequest['id'],
								'user_id' => false								
							)
						);						
						
						if ($aItem !== false)
						{
							return array('forum', array($aItem['forum_name_url'] . '-' . $aItem['forum_id'], $aItem['title_url']));
						}
					}						
					break;
			}
		}				
		
		return 'forum';
	}
    
    /**
     * @return array
     */
	public function reparserList()
	{
        return [
            'name'       => _p('forum_post_text'),
            'table'      => 'forum_post_text',
            'original'   => 'text',
            'parsed'     => 'text_parsed',
            'item_field' => 'post_id'
        ];
    }
    
    /**
     * @param array $aRow
     *
     * @return mixed
     */
    public function getNewsFeedReply($aRow)
	{
		$oUrl = Phpfox_Url::instance();
		$aParts = unserialize($aRow['content']);
		
		$aRow['text'] = _p('full_name_replied_to_the_thread_title_with_link', array(
				'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
				'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
				'thread_link' => $aRow['link'],
				'title' => Feed_Service_Feed::instance()->shortenTitle($aParts['thread_title'])
			)
		);		
		
		$aRow['icon'] = 'module/forum.png';
		$aRow['enable_like'] = true;
		
		return $aRow;		
	}
    
    /**
     * @param int $iId
     *
     * @return bool|string
     */
	public function getFeedRedirectReply($iId)
	{
		return $this->getFeedRedirectPost($iId);
	}
    
    /**
     * @return array
     */
	public function getSiteStatsForAdmins()
	{
		$iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        
        return [
            'phrase' => _p('forum_posts'),
            'value'  => $this->database()
                ->select('COUNT(*)')
                ->from(Phpfox::getT('forum_post'))
                ->where('view_id = 0 AND time_stamp >= ' . $iToday)
                ->execute('getSlaveField')
        ];
    }
    
    /**
     * @param int $iId
     *
     * @return bool
     */
    public function deleteGroup($iId)
	{
        $aRows = $this->database()
            ->select('*')
            ->from(Phpfox::getT('forum_thread'))
            ->where('group_id = ' . (int)$iId)
            ->execute('getSlaveRows');
        
        foreach ($aRows as $aRow) {
            Forum_Service_Thread_Process::instance()->delete($aRow['thread_id']);
        }
        
        return true;
	}
    
    /**
     * @return array
     */
	public function updateCounterList()
	{
        $aList = [];
        
        $aList[] = [
            'name' => _p('forum_thread_post_count'),
            'id'   => 'forum-thread-post-count'
        ];
        
        $aList[] = [
            'name' => _p('forum_user_post_count'),
            'id'   => 'forum-user-post-count'
        ];
        
        $aList[] = [
            'name' => _p('update_forum_last_post'),
            'id'   => 'forum-last-post-info'
        ];
        
        (($sPlugin = Phpfox_Plugin::get('forum.service_callback_updatecounterlist')) ? eval($sPlugin) : false);
        
        return $aList;
    }
    
    /**
     * @param int $iId
     * @param int $iPage
     * @param int $iPageLimit
     *
     * @return array|int|string
     */
    public function updateCounter($iId, $iPage, $iPageLimit)
	{		
		if ($iId == 'forum-user-post-count')
		{
			$iCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('user'))
				->execute('getSlaveField');

            $this->database()->select('u.user_id, u.user_name, u.full_name, COUNT(fp.post_id) AS total_items')
                ->from(Phpfox::getT('user'), 'u')
                ->leftJoin(Phpfox::getT('forum_post'), 'fp', 'fp.user_id = u.user_id')
                ->limit($iPage, $iPageLimit, $iCnt)
                ->group('u.user_id')
                ->union();

			$aRows = $this->database()->select('u.user_id, u.user_name, u.full_name, u.total_items, uf.activity_points, uf.activity_total, uf.activity_forum')
				->unionFrom('u')
				->join(Phpfox::getT('user_activity'), 'uf', 'uf.user_id = u.user_id')
				->limit($iPage, $iPageLimit, $iCnt)
				->execute('getSlaveRows');

			foreach ($aRows as $aRow)
			{
				$this->database()->update(Phpfox::getT('user_activity'), array(
						'activity_points' => (($aRow['activity_points'] - ($aRow['activity_forum'] * Phpfox::getUserParam('forum.points_forum'))) + ($aRow['total_items'] * Phpfox::getUserParam('forum.points_forum'))),
						'activity_total' => (($aRow['activity_total'] - $aRow['activity_forum']) + $aRow['total_items']),
						'activity_forum' => $aRow['total_items']
					), 'user_id = ' . $aRow['user_id']
				);
				
				$this->database()->update(Phpfox::getT('user_field'), array('total_post' => $aRow['total_items']), 'user_id = ' . $aRow['user_id']);
			}			
			
			return $iCnt;
		}
		elseif ($iId == 'forum-thank')
		{
			if ((int) $iPage === 0)
			{
				$this->database()->update(Phpfox::getT('user_field'), array('total_thank' => 0, 'total_thanked' => 0), 'user_id > 0');
			}			
			
			$iCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('forum_thank'))
				->execute('getSlaveField');			
			
			$aRows = $this->database()->select('fp.user_id, ft.user_id AS thanked_user_id')
				->from(Phpfox::getT('forum_thank'), 'ft')
				->join(Phpfox::getT('forum_post'), 'fp', 'fp.post_id = ft.post_id')
				->limit($iPage, $iPageLimit, $iCnt)
				->execute('getSlaveRows');
			foreach ($aRows as $aRow)
			{
				$this->database()->updateCounter('user_field', 'total_thanked', 'user_id', $aRow['user_id']);
				$this->database()->updateCounter('user_field', 'total_thank', 'user_id', $aRow['thanked_user_id']);
			}
				
			return $iCnt;
		}		
		elseif ($iId == 'forum-last-post-info')
		{
		    $aForums = $this->database()->select('f.forum_id')
				->from(Phpfox::getT('forum'), 'f')
				->execute('getSlaveRows');			

		   foreach ($aForums as $aForum)
		   {
		   		$iChild = $this->_getChild($aForum['forum_id']);
				$aThread = $this->database()->select('thread_id, post_id, user_id, last_user_id')
					->from(Phpfox::getT('forum_thread'), 'ft')
					->where('ft.forum_id = ' . (int) $iChild)
					->order('ft.time_update DESC')
					->execute('getSlaveRow');
		   		foreach (Forum_Service_Forum::instance()->id($iChild)->getParents() as $iForumId)
		   		{
				    if (isset($aThread['thread_id']))
				    {
					    $this->database()->update(Phpfox::getT('forum'), array('thread_id' => $aThread['thread_id'], 'post_id' => $aThread['post_id'], 'last_user_id' => (empty($aThread['last_user_id']) ? $aThread['user_id'] : $aThread['last_user_id'])), 'forum_id = ' . $iForumId);
				    }
				    else
				    {
					    $this->database()->update(Phpfox::getT('forum'), array('thread_id' => 0, 'post_id' => 0, 'last_user_id' => 0), 'forum_id = ' . $iForumId);
				    }
		   		}	
		   }

		   return 0;
		}
		
		if ((int) $iPage === 0)
		{
			$this->database()->update(Phpfox::getT('forum'), array('total_post' => 0, 'total_thread' => 0), 'forum_id > 0');
			$this->database()->update(Phpfox::getT('forum_thread'), array('total_post' => 0), 'thread_id > 0');
		}
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('forum_thread'))
			->execute('getSlaveField');

        $this->database()->select('g.thread_id, g.forum_id, g.total_post, COUNT(gi.post_id) AS total_items')
            ->from(Phpfox::getT('forum_thread'), 'g')
            ->leftJoin(Phpfox::getT('forum_post'), 'gi', 'gi.thread_id = g.thread_id')
            ->group('g.thread_id')
            ->limit($iPage, $iPageLimit, $iCnt)
            ->union();

		$aRows = $this->database()->select('g.thread_id, g.forum_id, g.total_post, g.total_items, f.total_post AS forum_total_post, f.total_thread AS forum_total_thread')
			->unionFrom('g')
			->leftJoin(Phpfox::getT('forum'), 'f', 'f.forum_id = g.forum_id')
			->limit($iPage, $iPageLimit, $iCnt)
			->execute('getSlaveRows');
						
		foreach ($aRows as $aRow)
		{
			$iTotalPost = ($aRow['total_items'] > 1 ? ($aRow['total_items'] - 1) : 0);
			
			$this->database()->update(Phpfox::getT('forum_thread'), array('total_post' => ($iTotalPost + $aRow['total_post'])), 'thread_id = ' . (int) $aRow['thread_id']);
			if ($aRow['forum_id'] > 0)
			{
				foreach (Forum_Service_Forum::instance()->id($aRow['forum_id'])->getParents() as $iForumid)
				{
                    Forum_Service_Process::instance()->updateCounter($iForumid, 'total_thread');
                    Forum_Service_Process::instance()->updateCounter($iForumid, 'total_post', false, $iTotalPost);
				}				
			}
		}		
			
		return $iCnt;
	}
    
    /**
     * @param int $iId
     * @param int $iChildId
     *
     * @return bool|string
     */
	public function getFeedRedirectFeedLike($iId, $iChildId = 0)
	{
		return $this->getFeedRedirect($iChildId);
	}
    
    /**
     * @param array $aRow
     *
     * @return mixed
     */
	public function getNewsFeedFeedLike($aRow)
	{
		if ($aRow['owner_user_id'] == $aRow['viewer_user_id'])
		{
			$aRow['text'] = _p('a_href_user_link_full_name_a_likes_their_own_forum_a_href_link_thread_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox_Url::instance()->makeUrl($aRow['owner_user_name']),
					'gender' => User_Service_User::instance()->gender($aRow['owner_gender'], 1),
					'link' => $aRow['link']
				)
			);
		} else {
			$aRow['text'] = _p('a_href_user_link_full_name_a_likes_a_href_view_user_link_view_full_name_a_s_forum_a_href_link_thread_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox_Url::instance()->makeUrl($aRow['owner_user_name']),
					'view_full_name' => Phpfox::getLib('parse.output')->clean($aRow['viewer_full_name']),
					'view_user_link' => Phpfox_Url::instance()->makeUrl($aRow['viewer_user_name']),
					'link' => $aRow['link']			
				)
			);
		}
		
		$aRow['icon'] = 'misc/thumb_up.png';

		return $aRow;				
	}
    
    /**
     * @param array $aRow
     *
     * @return array
     */
	public function getNotificationFeedNotifyLike($aRow)
	{		
		return array(
			'message' => _p('a_href_user_link_full_name_a_likes_your_forum_a_href_link_thread_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
					'user_link' => Phpfox_Url::instance()->makeUrl($aRow['user_name']),
					'link' => Phpfox_Url::instance()->makeUrl('forum.thread', array('redirect' => $aRow['item_id']))
				)
			),
			'link' => Phpfox_Url::instance()->makeUrl('forum.thread', array('redirect' => $aRow['item_id']))
		);				
	}
    
    /**
     * @param int $iItemId
     *
     * @return string
     */
	public function sendLikeEmail($iItemId)
	{
		return _p('a_href_user_link_full_name_a_likes_your_forum_a_href_link_thread_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean(Phpfox::getUserBy('full_name')),
					'user_link' => Phpfox_Url::instance()->makeUrl(Phpfox::getUserBy('user_name')),
					'link' => Phpfox_Url::instance()->makeUrl('forum.thread', array('redirect' => $iItemId))
				)
			);
	}
    
    /**
     * @param int $iItemId
     *
     * @return string
     */
	public function sendLikeEmailReply($iItemId)
	{
		return _p('a_href_user_link_full_name_a_likes_your_forum_a_href_link_post_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean(Phpfox::getUserBy('full_name')),
					'user_link' => Phpfox_Url::instance()->makeUrl(Phpfox::getUserBy('user_name')),
					'link' => Phpfox_Url::instance()->makeUrl('forum.thread', array('predirect' => $iItemId))
				)
			);
	}
    
    /**
     * @param array $aRow
     *
     * @return array
     */
	public function getNotificationFeedReply_NotifyLike($aRow)
	{
		return array(
			'message' => _p('a_href_user_link_full_name_a_likes_your_forum_a_href_link_post_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
					'user_link' => Phpfox_Url::instance()->makeUrl($aRow['user_name']),
					'link' => Phpfox_Url::instance()->makeUrl('forum.thread', array('predirect' => $aRow['item_id']))
				)
			),
			'link' => Phpfox_Url::instance()->makeUrl('forum.thread', array('predirect' => $aRow['item_id']))
		);	
	}
    
    /**
     * @param array $aRow
     *
     * @return mixed
     */
	public function getNewsFeedReply_FeedLike($aRow)
	{
		if ($aRow['owner_user_id'] == $aRow['viewer_user_id'])
		{
			$aRow['text'] = _p('a_href_user_link_full_name_a_likes_their_own_forum_a_href_link_reply_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox_Url::instance()->makeUrl($aRow['owner_user_name']),
					'gender' => User_Service_User::instance()->gender($aRow['owner_gender'], 1),
					'link' => $aRow['link']
				)
			);
		}  else {
			$aRow['text'] = _p('a_href_user_link_full_name_a_likes_a_href_view_user_link_view_full_name_a_s_forum_a_href_link_reply_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox_Url::instance()->makeUrl($aRow['owner_user_name']),
					'view_full_name' => Phpfox::getLib('parse.output')->clean($aRow['viewer_full_name']),
					'view_user_link' => Phpfox_Url::instance()->makeUrl($aRow['viewer_user_name']),
					'link' => $aRow['link']			
				)
			);
		}
		
		$aRow['icon'] = 'misc/thumb_up.png';

		return $aRow;		
	}
    
    /**
     * @param int $iId
     * @param int $iChildId
     *
     * @return bool|string
     */
	public function getFeedRedirectReply_FeedLike($iId, $iChildId)
	{
		Notification_Service_Process::instance()->delete('forum_post_notifyLike', $iChildId, Phpfox::getUserId());
		
		return $this->getFeedRedirectReply($iChildId);
	}
    
    /**
     * @return array
     */
	public function getActivityPointField()
	{
        return [
            _p('forum_posts') => 'activity_forum'
        ];
    }
    
    /**
     * @return array
     */
    public function pendingApproval()
	{
		$aPending[] = array(
			'phrase' => _p('forum_threads'),
			'value' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('forum_thread'))->where('view_id = 1')->execute('getSlaveField'),
			'link' => Phpfox_Url::instance()->makeUrl('forum.search', array('view' => 'pending-thread'))
		);
		
		$aPending[] = array(
			'phrase' => _p('forum_posts'),
			'value' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('forum_post'))->where('view_id = 1')->execute('getSlaveField'),
			'link' => Phpfox_Url::instance()->makeUrl('forum.search', array('view' => 'pending-post'))
		);		
		
		return $aPending;
	}
    
    /**
     * @return array
     */
	public function getSqlTitleField()
	{
        return [
            [
                'table' => 'forum',
                'field' => 'name'
            ],
            [
                'table'     => 'forum_thread',
                'field'     => 'title',
                'has_index' => 'title'
            ],
            [
                'table' => 'forum_post',
                'field' => 'title'
            ]
        ];
    }
    
    /**
     * This like is for first posts in a thread, not for replies
     *
     * @param int  $iItemId
     * @param bool $bDoNotSendEmail
     *
     * @return bool
     */
	public function addLike($iItemId, $bDoNotSendEmail = false)
	{
		return $this->addLikePost($iItemId, $bDoNotSendEmail);
	}
    
    /**
     * @param int  $iItemId
     * @param bool $bDoNotSendEmail
     *
     * @return bool
     */
	public function addLikeReply($iItemId, $bDoNotSendEmail = false)
	{
		return $this->addLikePost($iItemId, $bDoNotSendEmail);
	}
    
    /**
     * @param int $iItemId
     * @param bool $bDoNotSendEmail
     *
     * @return bool
     */
	public function addLikePost($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('fp.post_id, ft.thread_id, ft.title, fp.user_id')
			->from(Phpfox::getT('forum_post'), 'fp')
			->join(Phpfox::getT('forum_thread'), 'ft', 'ft.thread_id = fp.thread_id')
			->where('fp.post_id = ' . (int) $iItemId)
			->execute('getSlaveRow');
        
        if (!isset($aRow['post_id'])) {
            return false;
        }
		
		$this->database()->updateCount('like', 'type_id = \'forum_post\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'forum_post', 'post_id = ' . (int) $iItemId);	
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::permalink('forum.thread', $aRow['thread_id'], $aRow['title'], false, null, array('view' => $aRow['post_id']));
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(array('forum.full_name_liked_one_of_your_forum_posts', array('full_name' => Phpfox::getUserBy('full_name'))))
				->message(array('forum.full_name_liked_your_one_of_your_forum_posts_in', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])))
				->notification('like.new_like')
				->send();
					
			Notification_Service_Process::instance()->add('forum_post_like', $aRow['post_id'], $aRow['user_id']);
		}
		return true;
	}
    
    /**
     * This like is for first posts in a thread, not for replies
     *
     * @param int  $iItemId
     * @param bool $bDoNotSendEmail
     *
     * @return void|null
     */
	public function deleteLike($iItemId, $bDoNotSendEmail = false)
	{
		return $this->deleteLikePost($iItemId);
	}
    
    /**
     * @param int $iItemId
     */
	public function deleteLikeReply($iItemId)
	{
		return $this->deleteLikePost($iItemId);
	}
    
    /**
     * @param int $iItemId
     *
     * @return void
     */
	public function deleteLikePost($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'forum_post\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'forum_post', 'post_id = ' . (int) $iItemId);
	}
    
    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
	public function getNotificationPost_Like($aNotification)
	{
		$aRow = $this->database()->select('fp.post_id, ft.thread_id, ft.title, fp.user_id, u.full_name, u.gender')
			->from(Phpfox::getT('forum_post'), 'fp')
			->join(Phpfox::getT('forum_thread'), 'ft', 'ft.thread_id = fp.thread_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fp.user_id')
			->where('fp.post_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');			
		
		if (!isset($aRow['user_id']))
		{
		    return false;
		}
		
		$sUsers = Notification_Service_Notification::instance()->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('users_liked_gender_own_forum_post_in_the_thread_title', array('users' => $sUsers, 'gender' => User_Service_User::instance()->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('users_liked_your_forum_post_in_the_thread_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('users_liked_span_class_drop_data_user_row_full_name_s_span_forum_post_in_the_thread_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
        
        return [
            'link'    => Phpfox_Url::instance()
                ->permalink('forum.thread', $aRow['thread_id'], $aRow['title'], false, null, ['view' => $aRow['post_id']]),
            'message' => $sPhrase,
            'icon'    => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        ];
    }
    
    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
	public function getNotificationSubscribed_Post($aNotification)
	{
		$aRow = $this->database()->select('fp.post_id, ft.thread_id, ft.title, fp.user_id')
			->from(Phpfox::getT('forum_post'), 'fp')
			->join(Phpfox::getT('forum_thread'), 'ft', 'ft.thread_id = fp.thread_id')
			->where('fp.post_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');			
		
		if (!isset($aRow['post_id']))
		{
			return false;
		}
			
		$sPhrase = _p('users_replied_to_the_thread_title', array('users' => Notification_Service_Notification::instance()->getUsers($aNotification), 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
			
		return array(
			'link' => Phpfox_Url::instance()->permalink('forum.thread', $aRow['thread_id'], $aRow['title'], false, null, array('view' => $aRow['post_id'])),
			'message' => $sPhrase,
			'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
		);	
	}
    
    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
	public function getNotificationThread_Approved($aNotification)
	{
		$aRow = $this->database()->select('ft.thread_id, ft.title, ft.user_id')
			->from(Phpfox::getT('forum_thread'), 'ft')
			->where('ft.thread_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
        
        if (!isset($aRow['thread_id'])) {
            return false;
        }
			
		$sPhrase = _p('your_thread_has_been_approved', array('thread_title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], 20,'...')));
        
        return [
            'link'    => Phpfox_Url::instance()
                ->permalink('forum.thread', $aRow['thread_id'], $aRow['title'], false, null),
            'message' => $sPhrase,
            'icon'    => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        ];
    }
    
    /**
     * @return void
     */
	public function canShareItemOnFeedReply(){}
    
    /**
     * @return void
     */
	public function canShareItemOnFeed(){}
    
    /**
     * @param array      $aItem
     * @param null|array $aCallback
     * @param bool       $bIsChildItem
     *
     * @return array|bool
     */
	public function getActivityFeedPost($aItem, $aCallback = null, $bIsChildItem = false)
	{
		return $this->getActivityFeedReply($aItem, $aCallback, $bIsChildItem);
	}
    
    /**
     * @param array $aRow
     *
     * @return bool|array
     */
	public function getActivityFeedCustomChecksPost($aRow)
	{
		if ((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null, 'forum.view_browse_forum'))
			|| (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['custom_data_cache']['group_id'] > 0 && !Pages_Service_Pages::instance()->hasPerm($aRow['custom_data_cache']['group_id'], 'forum.view_browse_forum'))
		)
		{
			return false;
		}

		return $aRow;
	}
    
    /**
     * @param array $aRow
     *
     * @return bool|array
     */
	public function getActivityFeedCustomChecksReply($aRow)
	{
		if ((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null, 'forum.view_browse_forum'))
			|| (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['custom_data_cache']['group_id'] > 0 && !Pages_Service_Pages::instance()->hasPerm($aRow['custom_data_cache']['group_id'], 'forum.view_browse_forum'))
		)
		{
			return false;
		}

		return $aRow;
	}
    
    /**
     * @param array      $aItem
     * @param null|array $aCallback
     * @param bool       $bIsChildItem
     *
     * @return array|bool
     */
	public function getActivityFeedReply($aItem, $aCallback = null, $bIsChildItem = false)
	{
		if (Phpfox::isUser() && Phpfox::isModule('like'))
		{
			$this->database()->select('l.like_id AS is_liked, ')
					->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'forum_post\' AND l.item_id = fp.post_id AND l.user_id = ' . Phpfox::getUserId());
		}
		
		if ($bIsChildItem)
		{
			$this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = fp.user_id');
		}		
		
		$aRow = $this->database()->select('fp.post_id, ft.thread_id, ft.group_id, ft.title, fp.user_id AS post_user_id, fp.total_like, fpt.text_parsed AS text, fp.time_stamp, fpt.text AS normal_text, ' . Phpfox::getUserField())
			->from(Phpfox::getT('forum_post'), 'fp')			
			->join(Phpfox::getT('forum_thread'), 'ft', 'ft.thread_id = fp.thread_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ft.user_id')
			->join(Phpfox::getT('forum_post_text'), 'fpt', 'fpt.post_id = fp.post_id')
			->where('fp.post_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');	
		
		if (!isset($aRow['post_id']))
		{
			return false;
		}
		
		if ($bIsChildItem)
		{
			$aItem = $aRow;
		}		
		$sPageType = Phpfox::getPagesType($aRow['group_id']);
        $bHasPerm = false;
        if ($sPageType == 'pages') {
            $bHasPerm = Pages_Service_Pages::instance()->hasPerm($aRow['group_id'], 'forum.view_browse_forum');
        } elseif ($sPageType == 'groups') {
            $bHasPerm = Phpfox::getService('groups')->hasPerm($aRow['group_id'], 'forum.view_browse_forum');
        }
		if (defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null, 'forum.view_browse_forum')
			|| (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['group_id'] > 0 && !$bHasPerm)
			)
		{
			return false;
		}			
		
		$sLink = Phpfox::permalink('forum.thread', $aRow['thread_id'], $aRow['title'], false, null, array('view' => $aRow['post_id']));
		if ($aRow['user_id'] == $aRow['post_user_id'])
		{
			$sPhrase = _p('replied_on_gender_thread_a_href_link_title_a', array('gender' => User_Service_User::instance()->gender($aRow['gender'], 1), 'link' => $sLink, 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : 50), '...')));
		}
		else 
		{
			$sPhrase = _p('replied_on_a_href_user_name_full_name_a_s_thread_a_href_link_title_a', array('user_name' => Phpfox_Url::instance()->makeUrl($aRow['user_name']), 'full_name' => $aRow['full_name'], 'link' => $sLink, 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
		}

		if (preg_match('/\[quote(.*)\]/i', $aRow['normal_text']))
		{
			$aRow['text'] = $aRow['normal_text'];
			$aRow['text'] = str_replace(array('&lt;p&gt;', '&lt;/p&gt;'), array('', ''), $aRow['text']);
			$aRow['text'] = Phpfox::getLib('parse.bbcode')->stripCode($aRow['text'], 'quote');
			$aRow['text'] = Phpfox::getLib('parse.input')->prepare($aRow['text']);						
			$aRow['text'] = trim($aRow['text'], '<br />');
		}	
		
		$aReturn = array(
			'feed_title' => '',
			'feed_info' => $sPhrase,
			'feed_link' => $sLink,
			'feed_content' => $aRow['text'],
			'feed_total_like' => $aRow['total_like'],
			'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/forum.png', 'return_url' => true)),
			'time_stamp' => $aRow['time_stamp'],			
			'enable_like' => true,			
			'like_type_id' => 'forum_post',
			'custom_data_cache' => $aRow
		);
		
		if ($bIsChildItem)
		{
			$aReturn = array_merge($aReturn, $aItem);
		}		
		return $aReturn;
	}
    
    /**
     * @param array $aRow
     *
     * @return bool|array
     */
	public function getActivityFeedCustomChecks($aRow)
	{
		if ((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null, 'forum.view_browse_forum'))
			|| (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['custom_data_cache']['group_id'] > 0 && !Pages_Service_Pages::instance()->hasPerm($aRow['custom_data_cache']['group_id'], 'forum.view_browse_forum'))
		)
		{
			return false;
		}

		return $aRow;
	}
    
    /**
     * @param array      $aItem
     * @param null|array $aCallBack
     * @param bool       $bIsChildItem
     *
     * @return array
     */
	public function getActivityFeed($aItem, $aCallBack = null, $bIsChildItem = false)
	{
		$this->database()->select('ft.thread_id, ft.title, ft.user_id, ft.group_id, fp.total_like, fpt.text_parsed AS text, ft.time_stamp, fp.post_id')
			->from(Phpfox::getT('forum_thread'), 'ft')
			->join(Phpfox::getT('forum_post'), 'fp', 'fp.post_id = ft.start_id')
			->join(Phpfox::getT('forum_post_text'), 'fpt', 'fpt.post_id = fp.post_id')
			->where('ft.thread_id = ' . (int) $aItem['item_id']);

		if(Phpfox::isModule('like')) {
			$this->database()->select(', l.type_id as like_type_id, l.like_id AS is_liked')
					->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'forum_post\' AND l.item_id = fp.post_id AND l.user_id = ' . Phpfox::getUserId());
		}

		if ($bIsChildItem) {
			$this->database()->select(',' . Phpfox::getUserField('u2'))->join(Phpfox::getT('user'), 'u2', 'u2.user_id = ft.user_id');
		}

		$aRow = $this->database()->execute('getSlaveRow');

		if (!isset($aRow['thread_id']))
		{
			return false;
		}

		$sGroupType = ($aRow['group_id'] > 0) ? Phpfox::getLib('pages.facade')->getPageItemType($aRow['group_id']) : '';
		if (((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null, 'forum.view_browse_forum') == false)
			|| (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['group_id'] > 0 && $sGroupType == 'pages' && Phpfox::isModule('pages') && !Pages_Service_Pages::instance()->hasPerm($aRow['group_id'], 'forum.view_browse_forum')))
			|| ($sGroupType && Phpfox::isModule($sGroupType) && Phpfox::hasCallback($sGroupType, 'canShareOnMainFeed') && !Phpfox::callback($sGroupType . '.canShareOnMainFeed', $aRow['group_id'], 'forum.view_browse_forum', $bIsChildItem))
		)
		{
			return false;
		}		
		if (!isset($aRow['title'])){
            $aRow['title'] = '';
        }
		$sLink = Phpfox::permalink('forum.thread', $aRow['thread_id'], $aRow['title'], false, null);
		$aRow['feed_info'] = _p('posted_a_thread');
		$aRow['feed_icon'] = Phpfox::getLib('image.helper')->display(array('theme' => 'module/forum.png', 'return_url' => true));

		if ($bIsChildItem)
		{
			$aItem = $aRow;
		}
		
		$aOut = array(
			'feed_title' => $aRow['title'],
			'feed_info' => $aRow['feed_info'],
			'feed_link' => $sLink,
			'feed_content' => $aRow['text'],
			'feed_total_like' => $aRow['total_like'],
			'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
			'feed_icon' => $aRow['feed_icon'],
			'time_stamp' => $aRow['time_stamp'],			
			'enable_like' => true,			
			'like_type_id' => (!empty($aRow['like_type_id']) ? $aRow['like_type_id'] : 'forum_post'),//'forum_post',
			'like_item_id' => $aRow['post_id'],
			'custom_data_cache' => $aRow
		);
		if (isset($aItem['type_id']) && $aItem['type_id'] == 'forum')
		{
			$aOut['like_type_id'] = 'forum_post';
		}
        if ($bIsChildItem){
            $aOut = array_merge($aOut, $aItem);
        }
		if (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['group_id'] && $sGroupType) {
			$aPage = $this->database()->select('p.*, pu.vanity_url, ' .Phpfox::getUserField('u', 'parent_'))
				->from(':pages', 'p')
				->join(':user', 'u', 'p.page_id=u.profile_page_id')
				->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
				->where('p.page_id=' . (int) $aRow['group_id'])
				->execute('getSlaveRow');
			$aOut['parent_user_name'] = Phpfox::getService($sGroupType)->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
            $aReturn['feed_table_prefix'] = 'pages_';
			if ($aRow['user_id'] != $aPage['parent_user_id']){
				$aOut['parent_user'] = User_Service_User::instance()->getUserFields(true, $aPage, 'parent_');
				unset($aOut['feed_info']);
			}
		}

		(($sPlugin = Phpfox_Plugin::get('forum.component_service_callback_getactivityfeed__1')) ? eval($sPlugin) : false);
		
		return $aOut;
	}
    
    /**
     * @param string $sSearch
     *
     * @return void
     */
	public function globalUnionSearch($sSearch)
	{
		$this->database()->select('item.thread_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'forum\' AS item_type_id, \'\' AS item_photo, 0 AS item_photo_server')
			->from(Phpfox::getT('forum_thread'), 'item')
			->where('item.view_id = 0 AND ' . $this->database()->searchKeywords('item.title', $sSearch) . ' AND item.group_id=0')
			->union();
	}
    
    /**
     * @param array $aRow
     *
     * @return array
     */
	public function getSearchInfo($aRow)
	{
		$aInfo = array();
		$aInfo['item_link'] = Phpfox_Url::instance()->permalink('forum.thread', $aRow['item_id'], $aRow['item_title']);
		$aInfo['item_name'] = _p('forum_thread');
		
		return $aInfo;
	}
    
    /**
     * @return array
     */
	public function getSearchTitleInfo()
	{
        return [
            'name' => _p('forum_threads')
        ];
    }
    
    /**
     * @param array $aPage
     *
     * @return array|null
     */
	public function getPageMenu($aPage)
	{
		if (!Pages_Service_Pages::instance()->hasPerm($aPage['page_id'], 'forum.view_browse_forum'))
		{
			return null;
		}		
		
		$aMenus[] = array(
			'phrase' => _p('discussions'),
			'url' => Pages_Service_Pages::instance()->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'forum/',
			'icon' => 'module/forum.png',
			'landing' => 'forum'
		);
		
		return $aMenus;
	}
    
    /**
     * @param array $aPage
     *
     * @return array|null
     */
	public function getGroupMenu($aPage)
	{
		if (!Core\Lib::appsGroup()->hasPerm($aPage['page_id'], 'forum.view_browse_forum'))
		{
			return null;
		}

		$aMenus[] = array(
			'phrase' => _p('Discussions'),
			'url' => Core\Lib::appsGroup()->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'forum/',
			'icon' => 'module/forum.png',
			'landing' => 'forum'
		);

		return $aMenus;
	}
    
    /**
     * @param array $aPage
     *
     * @return array|null
     */
	public function getPageSubMenu($aPage)
	{
        if (!Pages_Service_Pages::instance()->hasPerm($aPage['page_id'], 'forum.share_forum')) {
            return null;
        }
        
        return [
            [
                'phrase' => _p('post_a_new_thread'),
                'url'    => Phpfox_Url::instance()->makeUrl('forum.post.thread', [
                    'module' => 'pages',
                    'item'   => $aPage['page_id']
                ])
            ]
        ];
    }
    
    /**
     * @param array $aPage
     *
     * @return array|null
     */
	public function getGroupSubMenu($aPage)
	{
        if (!Core\Lib::appsGroup()->hasPerm($aPage['page_id'], 'forum.share_forum')) {
            return null;
        }
        
        return [
            [
                'phrase' => _p('post_a_new_thread'),
                'url'    => Phpfox_Url::instance()->makeUrl('forum.post.thread', [
                    'module' => 'groups',
                    'item'   => $aPage['page_id']
                ])
            ]
        ];
    }
    
    /**
     * @return array
     */
	public function getPagePerms()
	{
		$aPerms = array();
		
		$aPerms['forum.share_forum'] = _p('who_can_start_a_discussion');
		$aPerms['forum.reply_forum'] = _p('Who can reply a discussion');
		$aPerms['forum.view_browse_forum'] = _p('who_can_view_browse_discussions');
		
		return $aPerms;
	}
    
    /**
     * @return array
     */
	public function getGroupPerms()
	{
        $aPerms = [
            'forum.share_forum' => _p('who_can_start_a_discussion'),
            'forum.reply_forum' => _p('Who can reply a discussion')
        ];
        return $aPerms;
	}
    
    /**
     * @param int $iPage
     *
     * @return bool
     */
	public function canViewPageSection($iPage)
	{
		if (!Pages_Service_Pages::instance()->hasPerm($iPage, 'forum.view_browse_forum'))
		{
			return false;
		}
		
		return true;
	}
    
    /**
     * @description This function filters out thread ids from search results.
     *
     * @param array $aResults from search->query it has thread_ids and we need to find their forum
     *
     * @return array ids of the threads that are not allowed to be shown
     */
	public function filterSearchResults($aResults)
	{
		$sInts = implode(',', $aResults);
		preg_match('/([0-9,]*)/', $sInts, $aMatches);
        
        if (!isset($aMatches[1]) || empty($aMatches[1])) {
            return [];
        }
        
        $aRows = $this->database()
			->select('ft.thread_id as item_id, "forum" as item_type_id')
			->from(Phpfox::getT('forum_access'), 'fa')
			->join(Phpfox::getT('forum_thread'), 'ft', 'ft.forum_id = fa.forum_id')
			->where('ft.thread_id IN (' . $aMatches[1] .') AND fa.user_group_id = ' . Phpfox::getUserBy('user_group_id') .' AND fa.var_value = 0 AND (fa.var_name = "can_view_forum" OR fa.var_name = "can_view_thread_content")' )
			->execute('getSlaveRows');

		return $aRows;
	}
    
    /**
     * @param array $aVals
     *
     * @return bool|null
     */
	public function onUserUpdate($aVals)
	{
		if (!isset($aVals['full_name']) || empty($aVals['full_name']) || !isset($aVals['prev_full_name']) || empty($aVals['prev_full_name']))
		{
			return false;
		}
		$this->database()->update(Phpfox::getT('forum_post'), array(
			'update_user' => $aVals['full_name']
			), 
			'user_id = ' . Phpfox::getUserId() . ' AND update_user = "'. $aVals['prev_full_name']. '"');
        return null;
	}
    
    /**
     * @param int  $iItemId
     * @param bool $bReturnTittle
     *
     * @return array|string
     */
    public function getItemLink($iItemId, $bReturnTittle = false){
        $aThread = $this->database()->select('ft.*')
            ->from(':forum_thread', 'ft')
            ->join(':forum_post', 'fp', 'fp.thread_id=ft.thread_id AND fp.post_id=' . (int) $iItemId)
            ->where('true')
            ->execute('getSlaveRow');
        $sUrl = Phpfox_Url::instance()->permalink('forum.thread', $aThread['thread_id'], $aThread['title_url']);
        if ($bReturnTittle){
            return [
                'title' => $aThread['title'],
                'url' => $sUrl
            ];
        } else {
            return $sUrl;
        }
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
        if ($sPlugin = Phpfox_Plugin::get('forum.service_callback__call')) {
            eval($sPlugin);
            return null;
        }
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
    
    /**
     * @param int $iForum
     *
     * @return mixed
     */
	private function _getChild($iForum)
	{
		$aForum = $this->database()->select('f.forum_id')
			->from(Phpfox::getT('forum'), 'f')				
			->where('parent_id = ' . (int) $iForum)
			->execute('getSlaveRow');		
				
		return (isset($aForum['forum_id']) ? $this->_getChild($aForum['forum_id']) : $iForum);			
	}
    
    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
	public function getNotificationNewItem_Groups($aNotification)
	{
		if (!Phpfox::isModule('groups')) return false;
		$aItem = Forum_Service_Thread_Thread::instance()->getForEdit($aNotification['item_id']);
		if (empty($aItem) || empty($aItem['group_id']))
		{
			return false;
		}

		$aRow = Core\Lib::appsGroup()->getPage($aItem['group_id']);

		if (!isset($aRow['page_id']))
		{
			return false;
		}

		$sPhrase = _p('{{ users }} add a new discussion in the group "{{ title }}"', array('users' => Notification_Service_Notification::instance()->getUsers($aNotification), 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
        
        return [
            'link'    => Phpfox_Url::instance()->permalink('forum.thread', $aItem['thread_id'], $aItem['title']),
            'message' => $sPhrase,
            'icon'    => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'forum')
        ];
    }
    
    /**
     * @return bool
     */
    public function ignoreDeleteLikesAndTagsWithFeed()
    {
        return true;
    }
}