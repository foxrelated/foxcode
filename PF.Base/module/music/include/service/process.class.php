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
 * @package  		Module_Music
 * @version 		$Id: process.class.php 6506 2013-08-26 08:42:59Z Miguel_Espinoza $
 */
class Music_Service_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('music_song');	
	}
	
	public function upload($aVals, $iAlbumId = 0)
	{
		if (!isset($_FILES['mp3']))
		{
			return Phpfox_Error::set(_p('select_an_mp3'));
		}
		
		$aSong = Phpfox_File::instance()->load('mp3', 'mp3', Phpfox::getUserParam('music.music_max_file_size'));

		if (function_exists('finfo_open'))
		{
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			if (!in_array(finfo_file($finfo, $_FILES['mp3']['tmp_name']), Music_Service_Music::instance()->getMimeTypes()))
			{
				return Phpfox_Error::set(_p('uploaded_file_is_not_valid'));
			}
			finfo_close($finfo);
		}

		if ($aSong === false)
		{
			return false;
		}
		
		if (empty($aVals['title']))
		{
			$aVals['title'] = $aSong['name'];
		}
		
		if (!isset($aVals['privacy']))
		{
			$aVals['privacy'] = 0;
		}
		
		if (!isset($aVals['privacy_comment']))
		{
			$aVals['privacy_comment'] = 0;
		}
		
		if ($iAlbumId < 1 && isset($aVals['album_id']))
		{
			$iAlbumId = (int)$aVals['album_id'];
		}
		
		
		if ($iAlbumId > 0)
		{
			$aAlbum = $this->database()->select('*')
				->from(Phpfox::getT('music_album'))
				->where('album_id = ' . (int) $iAlbumId)
				->execute('getSlaveRow');		
				
			$aVals['privacy'] = $aAlbum['privacy'];
			$aVals['privacy_comment'] = $aAlbum['privacy_comment'];
			
			if (!empty($aAlbum['module_id']))
			{
				$aVals['callback_module'] = $aAlbum['module_id'];
			}
			if (!empty($aAlbum['item_id']))
			{
				$aVals['callback_item_id'] = $aAlbum['item_id'];
			}
		}
		
		if (!empty($aVals['new_album_title']))
		{
			$iAlbumId = $this->database()->insert(Phpfox::getT('music_album'), array(
					'user_id' => Phpfox::getUserId(),
					'name' => $this->preParse()->clean($aVals['new_album_title']),
					'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
					'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
					'time_stamp' => PHPFOX_TIME,
					'module_id' => (isset($aVals['callback_module']) ? $aVals['callback_module'] : null),
					'item_id' => (isset($aVals['callback_item_id']) ? (int) $aVals['callback_item_id'] : '0')				
				)
			);
			
			$aAlbum = $this->database()->select('*')
				->from(Phpfox::getT('music_album'))
				->where('album_id = ' . (int) $iAlbumId)
				->execute('getSlaveRow');				
			
			$this->database()->insert(Phpfox::getT('music_album_text'), array(
					'album_id' => $iAlbumId
				)
			);			
		}
        
        Ban_Service_Ban::instance()->checkAutomaticBan($aVals['title']);
		
		$aInsert = array(
			'view_id' => (Phpfox::getUserParam('music.music_song_approval') ? '1' : '0'),
			'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
			'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),		
			'album_id' => $iAlbumId,
			'genre_id' => (isset($aVals['genre_id']) ? (int) $aVals['genre_id'] : '0'),
			'user_id' => Phpfox::getUserId(),
			'title' => Phpfox::getLib('parse.input')->clean($aVals['title'], 255),
			'description' => (isset($aVals['status_info']) ? Phpfox::getLib('parse.input')->clean($aVals['status_info'], 255) : null),
			'explicit' => ((isset($aVals['explicit']) && $aVals['explicit']) ? 1 : 0),
			'time_stamp' => PHPFOX_TIME,
			'module_id' => (isset($aVals['callback_module']) ? $aVals['callback_module'] : null),
			'item_id' => (isset($aVals['callback_item_id']) ? (int) $aVals['callback_item_id'] : '0')
		);
		
		$iId = $this->database()->insert($this->_sTable, $aInsert);
		
		if (!$iId)
		{
			return false;
		}
		
		$sFileName = Phpfox_File::instance()->upload('mp3', Phpfox::getParam('music.dir'), $iId);
		
		$sDuration = null;
		$aInsert['song_id'] = $iId;
		$aInsert['duration'] = $sDuration;
		$aInsert['song_path'] = $sFileName;
		$aInsert['full_name'] = $sFileName;
		$aInsert['is_featured'] = 0;
		$aInsert['user_name'] = Phpfox::getUserBy('user_name');
		// Return back error reporting
		Phpfox_Error::skip(false);		
		
		$this->database()->update($this->_sTable, array('song_path' => $sFileName, 'server_id' => Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID'), 'duration' => $sDuration), 'song_id = ' . (int) $iId);
		
		// Update user space usage
		if (!Phpfox::getUserParam('music.music_song_approval'))
		{
			User_Service_Space::instance()->update(Phpfox::getUserId(), 'music', filesize(Phpfox::getParam('music.dir') . sprintf($sFileName, '')));
		}
		
		if ($aVals['privacy'] == '4')
		{
            Privacy_Service_Process::instance()->add('music_song', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
		}		
		
		$aCallback = null;
		if (!empty($aVals['callback_module']) && Phpfox::hasCallback($aVals['callback_module'], 'uploadSong'))
		{
			$aCallback = Phpfox::callback($aVals['callback_module'] . '.uploadSong', $aVals['callback_item_id']);	
		}		

		if ($iAlbumId > 0)
		{			
			if (!Phpfox::getUserParam('music.music_song_approval'))
			{					
				$this->database()->updateCounter('music_album', 'total_track', 'album_id', $iAlbumId);
			
				(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->callback($aCallback)->add('music_song', $iId, $aAlbum['privacy'], (isset($aAlbum['privacy_comment']) ? (int) $aAlbum['privacy_comment'] : 0), (isset($aVals['callback_item_id']) ? (int) $aVals['callback_item_id'] : '0')) : null);
			}			
		}
		else 
		{
			if (!Phpfox::getUserParam('music.music_song_approval'))
			{	
				(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->callback($aCallback)->add('music_song', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0), (isset($aVals['callback_item_id']) ? (int) $aVals['callback_item_id'] : '0')) : null);
				//support add notification for parent module
				if (isset($aVals['callback_module']) && isset($aVals['callback_item_id']) && Phpfox::isModule('notification') && Phpfox::isModule($aVals['callback_module']) && Phpfox::hasCallback($aVals['callback_module'], 'addItemNotification'))
				{
					Phpfox::callback($aVals['callback_module'] . '.addItemNotification', ['page_id' => $aVals['callback_item_id'], 'item_perm' => 'music.view_browse_music', 'item_type' => 'music', 'item_id' => $iId, 'owner_id' => Phpfox::getUserId()]);
				}
			}
		}
		
		if (!Phpfox::getUserParam('music.music_song_approval'))
		{
			User_Service_Activity::instance()->update(Phpfox::getUserId(), 'music_song');
		}
		
        // plugin call
		if ($sPlugin = Phpfox_Plugin::get('music.service_process_upload__end')){eval($sPlugin);}
		
		return $aInsert;
	}
	
	public function delete($iId, &$aSong = null)
	{
		$bSkip = true;
		$mReturn = true;
		if ($aSong === null)
		{
			$bSkip = false;
			$aSong = $this->database()->select('song_id, album_id, module_id, item_id, user_id, song_path, is_sponsor, is_featured, server_id')
				->from($this->_sTable)
				->where('song_id = ' . (int) $iId)
				->execute('getSlaveRow');
			
			if (!isset($aSong['song_id']))
			{
				return false;
			}				
			
			if ($aSong['module_id'] == 'pages' && Pages_Service_Pages::instance()->isAdmin($aSong['item_id']))
			{
				$bSkip = true;
				$mReturn = Pages_Service_Pages::instance()->getUrl($aSong['item_id']) . 'music/';
			}
		}
		
		if ($bSkip || (($aSong['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('music.can_delete_own_track')) || Phpfox::getUserParam('music.can_delete_other_tracks')))
		{		
			// Update user space usage
			if(Phpfox::getParam('core.allow_cdn') && $aSong['server_id'] > 0)
			{
				// Get the file size stored when the photo was uploaded
				$sTempUrl = Phpfox::getLib('cdn')->getUrl(Phpfox::getParam('music.url') . sprintf($aSong['song_path'], ''));
				
				$aHeaders = get_headers($sTempUrl, true);
				if(preg_match('/200 OK/i', $aHeaders[0]))
				{
					User_Service_Space::instance()->update($aSong['user_id'], 'music', (int) $aHeaders["Content-Length"], '-');
				}
			}
			else
			{
				User_Service_Space::instance()->update($aSong['user_id'], 'music', filesize(Phpfox::getParam('music.dir') . sprintf($aSong['song_path'], '')), '-');
			}
			
			(($sPlugin = Phpfox_Plugin::get('music.service_process_delete__1')) ? eval($sPlugin) : false);
			
			Phpfox_File::instance()->unlink(Phpfox::getParam('music.dir') . sprintf($aSong['song_path'], ''));
			
			$this->database()->delete($this->_sTable, 'song_id = ' . $aSong['song_id']);
			if ($aSong['album_id'] > 0)
			{
				$this->database()->updateCounter('music_album', 'total_track', 'album_id', $aSong['album_id'], true);
			}
			(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->delete('music_song', $iId) : null);
			(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->delete('music_album', $iId) : null);
			(Phpfox::isModule('like') ? Like_Service_Process::instance()->delete('music_song',(int) $iId, 0, true) : null);
            (Phpfox::isModule('notification') ? Notification_Service_Process::instance()->deleteAllOfItem(['music_song_like', 'comment_music_song'],(int) $iId) : null);
			
			(($sPlugin = Phpfox_Plugin::get('music.service_process_delete__2')) ? eval($sPlugin) : false);
			
			User_Service_Activity::instance()->update($aSong['user_id'], 'music_song', '-');
		}

		if (isset($aSong['is_sponsor']) && $aSong['is_sponsor'] == 1)
		{
			$this->cache()->remove('music_song_sponsored');
		}

		if (isset($aSong['is_featured']) && $aSong['is_featured'] == 1)
		{
			$this->cache()->remove('music_song_featured');
		}
		
		return $mReturn;
	}
	
	public function update($iId, $aVals)
	{
		$aSong = $this->database()->select('song_id, user_id, album_id')
			->from($this->_sTable)
			->where('song_id = ' . (int) $iId)
			->execute('getSlaveRow');
		
		if (!isset($aSong['song_id']))
		{
			return false;	
		}		
		
		if ((isset($aVals['album_id']) && $aVals['album_id'] > 0) || $aSong['album_id'])
		{
			$aAlbum = $this->database()->select('*')
				->from(Phpfox::getT('music_album'))
				->where('album_id = ' . (int) (isset($aVals['album_id']) ? $aVals['album_id'] : $aSong['album_id']))
				->execute('getSlaveRow');
			
			if (isset($aAlbum['album_id']))
			{
				$aVals['album_id'] = $aAlbum['album_id'];
				$aVals['privacy'] = $aAlbum['privacy'];	
				$aVals['privacy_comment'] = $aAlbum['privacy_comment'];
			}
		}
		
		$aUpdate = array(					
			'album_id' => (isset($aVals['album_id']) ? (int) $aVals['album_id'] : 0),
			'genre_id' => (isset($aVals['genre_id']) ? (int) $aVals['genre_id'] : '0'),
			'title' => Phpfox::getLib('parse.input')->clean($aVals['title'], 255)
		);		
		
		if (empty($aVals['privacy']))
		{
			$aVals['privacy'] = 0;
		}
		if (empty($aVals['privacy_comment']))
		{
			$aVals['privacy_comment'] = 0;
		}		
		
		$aUpdate['privacy'] = (isset($aVals['privacy']) ? $aVals['privacy'] : '0');
		$aUpdate['privacy_comment'] = (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0');

		// Decrease the count for the old album
		$this->database()->updateCounter('music_album', 'total_track', 'album_id', $aSong['album_id'], true);		
		
		$this->database()->update($this->_sTable, $aUpdate, 'song_id = ' . (int) $iId);

		if (isset($aVals['album_id']))
		{
			// Decrease the count for the old album
			$this->database()->updateCounter('music_album', 'total_track', 'album_id', $aVals['album_id'], false);
		}
		
		if (Phpfox::isModule('privacy'))
		{
			if ($aVals['privacy'] == '4')
			{
                Privacy_Service_Process::instance()->update('music_song', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
			}
			else 
			{
                Privacy_Service_Process::instance()->delete('music_song', $iId);
			}				
		}

		(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->update('music_song', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0), 0, $aSong['user_id']) : null);

		(($sPlugin = Phpfox_Plugin::get('music.service_process_update__1')) ? eval($sPlugin) : false);
		
		return true;
	}
	
	public function play($iId)
	{	
		$aSong = $this->database()->select('song_id, album_id')
			->from($this->_sTable)
			->where('song_id = ' . (int) $iId)
			->execute('getSlaveRow');
		
		if (!isset($aSong['song_id']))
		{
			return false;	
		}
		
		$this->database()->updateCounter('music_song', 'total_play', 'song_id', $aSong['song_id']);
		
		if ($aSong['album_id'])
		{
			$this->database()->updateCounter('music_album', 'total_play', 'album_id', $aSong['album_id']);
		}
        return null;
	}
	
	public function addForProfile($iId, $iType)
	{
		Phpfox::isUser(true);
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('music_profile'))
			->where('user_id = ' . Phpfox::getUserId())
			->execute('getSlaveField');
			
		if ($iCnt >= Phpfox::getUserParam('music.total_song_on_profile'))
		{
			return Phpfox_Error::set(_p('you_have_reached_your_limit_max_songs_allowed_total', array('total' => Phpfox::getUserParam('music.total_song_on_profile'))));
		}
		
		$this->database()->delete(Phpfox::getT('music_profile'), 'song_id = ' . (int) $iId . ' AND user_id = ' . Phpfox::getUserId());
		
		if ($iType)
		{
			$this->database()->insert(Phpfox::getT('music_profile'), array(
					'song_id' => (int) $iId,
					'user_id' => Phpfox::getUserId()
				)
			);
			
			$this->database()->updateCounter('user_field', 'total_profile_song', 'user_id', Phpfox::getUserId());
		}	
		else 
		{
			$this->database()->updateCounter('user_field', 'total_profile_song', 'user_id', Phpfox::getUserId(), true);
		}
		
		return true;
	}
	
	public function feature($iId, $iType)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('music.can_feature_songs', true);
		
		$this->database()->update($this->_sTable, array('is_featured' => ($iType ? '1' : '0')), 'song_id = ' . (int) $iId);
		
		$this->cache()->remove('music_song_featured');
		
		return true;
	}	

	public function sponsorSong($iId, $iType)
	{
	    if (!Phpfox::getUserParam('music.can_sponsor_song') && !Phpfox::getUserParam('music.can_purchase_sponsor_song') && !defined('PHPFOX_API_CALLBACK'))
	    {
		return Phpfox_Error::set(_p('hack_attempt'));
	    }
	    $iType = (int)$iType;

	    if ($iType != 1 && $iType != 0)
	    {
		return false;
	    }
	    $this->database()->update($this->_sTable, array('is_sponsor' => $iType),
		    'song_id = ' . (int)$iId);

	    $this->cache()->remove('music_song_sponsored');
	    if ($sPlugin = Phpfox_Plugin::get('music.service_process_sponsorsong__end')){return eval($sPlugin);}
	    return true;
	}

	public function sponsorAlbum($iId, $iType)
	{
	    if (!Phpfox::getUserParam('music.can_sponsor_album') && !Phpfox::getUserParam('music.can_purchase_sponsor_album') && !defined('PHPFOX_API_CALLBACK'))
	    {
		return Phpfox_Error::set(_p('hack_attempt'));
	    }
	    $iType = (int)$iType;

	    if ($iType != 1 && $iType != 0)
	    {
		return false;
	    }
	    $this->database()->update(Phpfox::getT('music_album'), array('is_sponsor' => $iType),
		    'album_id = ' . (int)$iId);

	    $this->cache()->remove('music_album_sponsored');
	    if ($sPlugin = Phpfox_Plugin::get('music.service_process_sponsoralbum__end')){return eval($sPlugin);}
	    return true;
	}
	
	public function approve($iId)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('music.can_approve_songs', true);
		
		$aSong = $this->database()->select('v.*, ma.privacy AS album_privacy, ma.privacy_comment AS album_privacy_comment, ' . Phpfox::getUserField())
			->from($this->_sTable, 'v')
			->leftJoin(Phpfox::getT('music_album'), 'ma', 'ma.album_id = v.album_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->where('v.song_id = ' . (int) $iId)
			->execute('getSlaveRow');
			
		if (!isset($aSong['song_id']))
		{
			return Phpfox_Error::set(_p('unable_to_find_the_song_you_want_to_approve'));
		}
		
		$this->database()->update($this->_sTable, array('view_id' => '0', 'time_stamp' => PHPFOX_TIME), 'song_id = ' . $aSong['song_id']);
		
		if (Phpfox::isModule('notification'))
		{
			Notification_Service_Process::instance()->add('music_songapproved', $aSong['song_id'], $aSong['user_id']);
		}
		
		$bAddFeed = true;
		(($sPlugin = Phpfox_Plugin::get('music.service_process_approve__1')) ? eval($sPlugin) : false);
		
		// Send the user an email
		$sLink = Phpfox_Url::instance()->permalink('music', $aSong['song_id'], $aSong['title']);
		Phpfox::getLib('mail')->to($aSong['user_id'])
			->subject(array('music.your_song_title_has_been_approved_on_site_title', array('title' => $aSong['title'], 'site_title' => Phpfox::getParam('core.site_title'))))
			->message(array('music.your_song_title_has_been_approved_on_site_title_to_view_this_song', array('title' => $aSong['title'], 'site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)))
			->notification('music.song_is_approved')
			->send();				

		if ($aSong['album_id'])
		{			
			$this->database()->updateCounter('music_album', 'total_track', 'album_id', $aSong['album_id']);
			
			(Phpfox::isModule('feed') && $bAddFeed ? Feed_Service_Process::instance()->add('music_album', $aSong['song_id'], $aSong['album_privacy'], (isset($aSong['album_privacy_comment']) ? (int) $aSong['album_privacy_comment'] : 0), 0, $aSong['user_id']) : null);
		}
		else 
		{
			if ($aSong['module_id'] && $aSong['item_id'] && Phpfox::isModule($aSong['module_id']) && Phpfox::hasCallback($aSong['module_id'], 'getFeedDetails'))
			{
				(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->callback(Phpfox::callback($aSong['module_id'] . '.getFeedDetails', $aSong['item_id']))->add('music_song', $aSong['song_id'], $aSong['privacy'], (isset($aSong['privacy_comment']) ? (int) $aSong['privacy_comment'] : 0), $aSong['item_id'], $aSong['user_id']) : null);
			}
			else
			{
				(Phpfox::isModule('feed') && $bAddFeed ? Feed_Service_Process::instance()->add('music_song', $aSong['song_id'], $aSong['privacy'], (isset($aSong['privacy_comment']) ? (int) $aSong['privacy_comment'] : 0), 0, $aSong['user_id']) : null);
			}

			//support add notification for parent module
			if (Phpfox::isModule('notification') && $aSong['module_id'] && Phpfox::isModule($aSong['module_id']) && Phpfox::hasCallback($aSong['module_id'], 'addItemNotification'))
			{
				Phpfox::callback($aSong['module_id'] . '.addItemNotification', ['page_id' => $aSong['item_id'], 'item_perm' => 'music.view_browse_music', 'item_type' => 'music', 'item_id' => $iId, 'owner_id' => $aSong['user_id']]);
			}
		}		
			
		return true;	
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
		if ($sPlugin = Phpfox_Plugin::get('music.service_process__call'))
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