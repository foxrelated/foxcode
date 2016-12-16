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
 * @version 		$Id: process.class.php 4786 2012-09-27 10:40:14Z Miguel_Espinoza $
 */
class Music_Service_Album_Process extends Phpfox_Service 
{
	private $_aPhotoSizes = array(50, 120, 200);
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('music_album');	
	}
	
	public function add($aVals)
	{
		if (!empty($_FILES['image']['name']))
		{
			$aImage = Phpfox_File::instance()->load('image', array(
					'jpg',
					'gif',
					'png'
				)
			);
				
			if ($aImage === false)
			{
				return false;
			}
		}		
		
		if (empty($aVals['privacy']))
		{
			$aVals['privacy'] = 0;
		}
		
		if (empty($aVals['privacy_comment']))
		{
			$aVals['privacy_comment'] = 0;
		}
        
        Ban_Service_Ban::instance()->checkAutomaticBan($aVals['name'] . ' ' . $aVals['text']);
		
		$iId = $this->database()->insert($this->_sTable, array(
				'view_id' => 0,
				'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
				'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
				'user_id' => Phpfox::getUserId(),
				'name' => $this->preParse()->clean($aVals['name'], 255),
				'year' => $aVals['year'],
				'time_stamp' => PHPFOX_TIME
			)
		);
		
		if (!$iId)
		{
			return false;
		}
		
		$this->database()->insert(Phpfox::getT('music_album_text'), array(
				'album_id' => $iId,
				'text' => (empty($aVals['text']) ? null : $this->preParse()->clean($aVals['text'])),
				'text_parsed' => (empty($aVals['text']) ? null : $this->preParse()->prepare($aVals['text']))
			)
		);
		
		if (isset($aImage))
		{
			$oImage = Phpfox_Image::instance();
			
			$sFileName = Phpfox_File::instance()->upload('image', Phpfox::getParam('music.dir_image'), $iId);
							
			$iFileSizes = filesize(Phpfox::getParam('music.dir_image') . sprintf($sFileName, ''));						
		
			foreach ($this->_aPhotoSizes as $iSize)
			{
				if(Phpfox::getParam('core.keep_non_square_images'))
				{
					$oImage->createThumbnail(Phpfox::getParam('music.dir_image') . sprintf($sFileName, ''), Phpfox::getParam('music.dir_image') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);
				}
				$oImage->createThumbnail(Phpfox::getParam('music.dir_image') . sprintf($sFileName, ''), Phpfox::getParam('music.dir_image') . sprintf($sFileName, '_' . $iSize . '_square'), $iSize, $iSize, false);
								
				$iFileSizes += filesize(Phpfox::getParam('music.dir_image') . sprintf($sFileName, '_' . $iSize));			
			}	
							
			$this->database()->update($this->_sTable, array('image_path' => $sFileName, 'server_id' => Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID')), 'album_id = ' . $iId);
				
			// Update user space usage
			User_Service_Space::instance()->update(Phpfox::getUserId(), 'music_image', $iFileSizes);
		}
		
		if ($aVals['privacy'] == '4')
		{
            Privacy_Service_Process::instance()->add('music_album', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
		}		
		
		return $iId;
	}
	
	/**
	 * @todo Security checks. Users perms.
	 *
	 * @param mixed $iId
	 * @param mixed $aVals
	 * @return mixed
	 */
	public function update($iId, $aVals)
	{
		$aAlbum = $this->database()->select('*')
			->from($this->_sTable)
			->where('album_id = ' . (int) $iId)
			->execute('getSlaveRow');
			
		if (!isset($aAlbum['album_id']))
		{
			return Phpfox_Error::set(_p('unable_to_find_the_album_you_want_to_edit'));
		}
        
        Ban_Service_Ban::instance()->checkAutomaticBan($aVals['name'] . ' ' . $aVals['text']);
		
		if (($aAlbum['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('music.can_edit_own_albums')) || Phpfox::getUserParam('music.can_edit_other_music_albums'))
		{		
			if (empty($aVals['privacy']))
			{
				$aVals['privacy'] = 0;
			}

			if (empty($aVals['privacy_comment']))
			{
				$aVals['privacy_comment'] = 0;
			}
			
			$this->database()->update($this->_sTable, array(
					'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
					'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
					'name' => $this->preParse()->clean($aVals['name'], 255),
					'year' => $aVals['year']
				), 'album_id = ' . $aAlbum['album_id']
			);
			
			$this->database()->update(Phpfox::getT('music_album_text'), array(
					'text' => (empty($aVals['text']) ? null : $this->preParse()->clean($aVals['text'])),
					'text_parsed' => (empty($aVals['text']) ? null : $this->preParse()->prepare($aVals['text']))
				), 'album_id = ' . $aAlbum['album_id']
			);	
			
			$aSongs = $this->database()->select('song_id, user_id')
				->from(Phpfox::getT('music_song'))
				->where('album_id = ' . (int) $aAlbum['album_id'])
				->execute('getSlaveRows');
				
			if (count($aSongs))
			{
				foreach ($aSongs as $aSong)
				{
					$this->database()->update(Phpfox::getT('music_song'), array(
							'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
							'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
						), 'song_id = ' . $aSong['song_id']
					);
					
					(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->update('music_song', $aSong['song_id'], $aVals['privacy'], $aVals['privacy_comment'], 0, $aSong['user_id']) : null);
					
					if (Phpfox::isModule('privacy'))
					{
						if ($aVals['privacy'] == '4')
						{
                            Privacy_Service_Process::instance()->update('music_song', $aSong['song_id'], (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
						}
						else 
						{
                            Privacy_Service_Process::instance()->delete('music_song', $aSong['song_id']);
						}						
					}
				}
			}
			
			if (Phpfox::isModule('privacy'))
			{
				if ($aVals['privacy'] == '4')
				{
                    Privacy_Service_Process::instance()->update('music_album', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
				}
				else 
				{
                    Privacy_Service_Process::instance()->delete('music_album', $iId);
				}				
			}
	
			if (!empty($_FILES['image']['name']))
			{
				$aImage = Phpfox_File::instance()->load('image', array(
						'jpg',
						'gif',
						'png'
					)
				);
				
				if ($aImage === false)
				{
					return false;
				}					
				
				$oImage = Phpfox_Image::instance();
			
				$sFileName = Phpfox_File::instance()->upload('image', Phpfox::getParam('music.dir_image'), $iId);
							
				$iFileSizes = filesize(Phpfox::getParam('music.dir_image') . sprintf($sFileName, ''));						
		
				foreach ($this->_aPhotoSizes as $iSize)
				{						
					$oImage->createThumbnail(Phpfox::getParam('music.dir_image') . sprintf($sFileName, ''), Phpfox::getParam('music.dir_image') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);								
					$oImage->createThumbnail(Phpfox::getParam('music.dir_image') . sprintf($sFileName, ''), Phpfox::getParam('music.dir_image') . sprintf($sFileName, '_' . $iSize . '_square'), $iSize, $iSize, false);
								
					$iFileSizes += filesize(Phpfox::getParam('music.dir_image') . sprintf($sFileName, '_' . $iSize));			
				}	
							
				$this->database()->update($this->_sTable, array('image_path' => $sFileName, 'server_id' => Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID')), 'album_id = ' . $iId);
				
				// Update user space usage
				User_Service_Space::instance()->update($aAlbum['user_id'], 'music_image', $iFileSizes);
			}		
			
			if (!empty($_FILES['mp3']['name']))
			{
				if (empty($aVals['title']))
				{
					return Phpfox_Error::set(_p('provide_a_title_for_this_track'));
				}
				
				if (!Music_Service_Process::instance()->upload($aVals, $aAlbum['album_id']))
				{
					return false;
				}
			}
			(($sPlugin = Phpfox_Plugin::get('music.service_album_process_update__1')) ? eval($sPlugin) : false);
			return true;
		}
		
		return Phpfox_Error::set(_p('unable_to_edit_this_album'));
	}
	
	public function deleteImage($iId, &$aAlbum = null)
	{
		$bSkip = true;
		if ($aAlbum === null)
		{
			$bSkip = false;
			$aAlbum = $this->database()->select('album_id, user_id, image_path, server_id')
				->from($this->_sTable)
				->where('album_id = ' . (int) $iId)
				->execute('getSlaveRow');
			
			if (!isset($aAlbum['album_id']))
			{
				return false;
			}
		}
		
		if (empty($aAlbum['image_path']))
		{
			return null;
		}
		
		if ($bSkip || (($aAlbum['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('music.can_edit_own_albums')) || Phpfox::getUserParam('music.can_edit_other_music_albums')))
		{
			$iFileSizes = 0;
			foreach (array_merge(array(''), $this->_aPhotoSizes) as $iSize)
			{
				$sImage = Phpfox::getParam('music.dir_image') . sprintf($aAlbum['image_path'], (empty($iSize) ? '' : '_' ) . $iSize);
				$sImageSquare = Phpfox::getParam('music.dir_image') . sprintf($aAlbum['image_path'], (empty($iSize) ? '' : '_' ) . $iSize . '_square');
			
				if (file_exists($sImageSquare))
				{
					$iFileSizes += filesize($sImage);
					
					Phpfox_File::instance()->unlink($sImage);
				}
				if(Phpfox::getParam('core.keep_non_square_images'))
				{
					if (file_exists($sImage))
					{
						$iFileSizes += filesize($sImage);
						
						Phpfox_File::instance()->unlink($sImage);
					}
				}
				
				// Update user space usage
				if(Phpfox::getParam('core.allow_cdn') && $aAlbum['server_id'] > 0)
				{
					$aFilesToDelete = array($sImageSquare, $sImage);
					foreach($aFilesToDelete as $sFilePath)
					{
						// Get the file size stored when the photo was uploaded
						$sTempUrl = Phpfox::getLib('cdn')->getUrl(str_replace(Phpfox::getParam('music.dir_image'), Phpfox::getParam('music.url_image'), $sFilePath));
						
						$aHeaders = get_headers($sTempUrl, true);
						if(preg_match('/200 OK/i', $aHeaders[0]))
						{
							$iFileSizes += (int) $aHeaders["Content-Length"];
						}
						
						Phpfox::getLib('cdn')->remove($sFilePath);
					}
				}
			}
			
			if ($iFileSizes > 0)
			{
				User_Service_Space::instance()->update($aAlbum['user_id'], 'music_image', $iFileSizes, '-');
			}		
			
			$this->database()->update($this->_sTable, array('image_path' => null, 'server_id' => 0), 'album_id = ' . $aAlbum['album_id']);	
			
			(($sPlugin = Phpfox_Plugin::get('music.service_album_process_deleteimage__1')) ? eval($sPlugin) : false);
			
			return true;
		}
		
		return Phpfox_Error::set(_p('not_allowed_to_edit_this_photo_album_art'));
	}
	
	public function delete($iId)
	{
		$aAlbum = $this->database()->select('*')
			->from($this->_sTable)
			->where('album_id = ' . (int) $iId)
			->execute('getSlaveRow');
		
		if (!isset($aAlbum['album_id']))
		{
			return Phpfox_Error::set(_p('album_you_are_trying_to_delete_cannot_be_found'));
		}
		
		if (($aAlbum['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('music.can_delete_own_music_album')) || Phpfox::getUserParam('music.can_delete_other_music_albums'))
		{
			$this->deleteImage($aAlbum['album_id'], $aAlbum);
			
			$aSongs = $this->database()->select('*')
				->from(Phpfox::getT('music_song'))
				->where('album_id = ' . $aAlbum['album_id'])
				->execute('getSlaveRows');
				
			foreach ($aSongs as $aSong)
			{
				Music_Service_Process::instance()->delete($aSong['song_id'], $aSong);
			}
			
			(Phpfox::isModule('feed') ? Feed_Service_Process::instance()->delete('comment_music_album', $iId) : null);
			(Phpfox::isModule('like') ? Like_Service_Process::instance()->delete('music_album',(int) $iId, 0, true) : null);
            (Phpfox::isModule('notification') ? Notification_Service_Process::instance()->deleteAllOfItem(['music_album_like', 'comment_music_album'],(int) $iId) : null);
			
			$this->database()->delete($this->_sTable, 'album_id = ' . $aAlbum['album_id']);
			$this->database()->delete(Phpfox::getT('music_album_text'), 'album_id = ' . $aAlbum['album_id']);
			if ($aAlbum['is_sponsor'] == 1)
			{
				$this->cache()->remove('music_album_sponsored');
			}

			if ($aAlbum['is_featured'] == 1)
			{
				$this->cache()->remove('music_album_featured');
			}

			(($sPlugin = Phpfox_Plugin::get('music.service_album_process_delete__1')) ? eval($sPlugin) : false);
			return true;
		}
		
		return Phpfox_Error::set(_p('not_allowed_to_delete_this_album'));
	}
	
	public function feature($iId, $iType)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('music.can_feature_music_albums', true);
		
		$this->database()->update($this->_sTable, array('is_featured' => ($iType ? '1' : '0')), 'album_id = ' . (int) $iId);
		
		$this->cache()->remove('music_album_featured');
		
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
		if ($sPlugin = Phpfox_Plugin::get('music.service_album_process__call'))
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