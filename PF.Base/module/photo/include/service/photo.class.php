<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Gets photo details from the database.
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: photo.class.php 7059 2014-01-22 14:20:10Z Fern $
 */
class Photo_Service_Photo extends Phpfox_Service 
{
	private $_bIsTagSearch = false;
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('photo');	
	}
	
	public function getCoverPhoto($iPhotoId)
	{
		$aRow = $this->database()->select('*')
			->from(Phpfox::getT('photo'))
			->where('photo_id = ' . (int) $iPhotoId)
			->execute('getSlaveRow');
		
		return $aRow;
	}
	
	public function isTagSearch($bIsTagSearch = false)
	{
		$this->_bIsTagSearch = $bIsTagSearch;		
		
		return $this;
	}		

	/**
	 * Get all photos based on filters we passed via the params.
	 *
	 * @param array $mConditions SQL Conditions
	 * @param string $sOrder SQL Ordering
	 * @param mixed $iPage Current page we are on
	 * @param mixed $iPageSize Define how many photos we can display at one time
	 * @param array $aCallback
	 * 
	 * @return array Return an array of the total photo count and the photos
	 */
	public function get($mConditions = array(), $sOrder = 'p.time_stamp DESC', $iPage = '', $iPageSize = '', $aCallback = null)
	{		
		$aPhotos = array();
		if ($this->_bIsTagSearch !== false)
		{
			$this->database()->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = p.photo_id AND tag.category_id = 'photo'");
		}		
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('photo'), 'p')
			->where($mConditions)
			->execute('getSlaveField');

		if ($iCnt)
		{
			if ($this->_bIsTagSearch !== false)
			{
				$this->database()->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = p.photo_id AND tag.category_id = 'photo'");
			}			
			
			if (Phpfox::isModule('like'))
			{
				$this->database()->select('l.like_id as is_liked, ')
					->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = "photo" AND l.item_id = p.photo_id AND l.user_id = ' . Phpfox::getUserId() . '');				
			}			
			
			$aPhotos = $this->database()->select(Phpfox::getUserField() . ', p.*, pa.name AS album_url, pi.*')
				->from(Phpfox::getT('photo'), 'p')
				->leftJoin(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
				->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
				->where($mConditions)
				->order($sOrder)
				->limit($iPage, $iPageSize, $iCnt)
				->execute('getSlaveRows');
				
			foreach ($aPhotos as $iKey => $aPhoto)
			{				
				$sCategoryList = '';
				$aCategories = (array) $this->database()->select('category_id')
					->from(Phpfox::getT('photo_category_data'))
					->where('photo_id = ' . (int) $aPhoto['photo_id'])
					->execute('getSlaveRows');
					
				foreach ($aCategories as $aCategory)
				{
					$sCategoryList .= $aCategory['category_id'] . ',';
				}
				
				$aPhotos[$iKey]['link'] = Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);
				$aPhotos[$iKey]['category_list'] = rtrim($sCategoryList, ',');
				$aPhotos[$iKey]['destination'] = $this->getPhotoUrl($aPhoto);
			}
		}		
		
		return array($iCnt, $aPhotos);
	}
	
	public function getForEdit($iId)
	{
		$aPhoto = $this->database()->select('p.*, pi.*, pa.name AS album_url, pa.name AS album_title, ' . Phpfox::getUserField())
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->leftJoin(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
			->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')			
			->where('p.photo_id = ' . (int) $iId)
			->execute('getSlaveRow');		
			
		$aPhoto['categories'] = Photo_Service_Category_Category::instance()->getCategoriesById($aPhoto['photo_id']);

		if (Phpfox::isModule('tag'))
		{
			$aTags = Tag_Service_Tag::instance()->getTagsById('photo', $aPhoto['photo_id']);
			if (isset($aTags[$aPhoto['photo_id']]))
			{
				$aPhoto['tag_list'] = '';					
				foreach ($aTags[$aPhoto['photo_id']] as $aTag)
				{
					$aPhoto['tag_list'] .= ' ' . $aTag['tag_text'] . ',';	
				}
				$aPhoto['tag_list'] = trim(trim($aPhoto['tag_list'], ','));
			}			
		}		
		
		$sCategoryList = '';
		$aCategories = (array) $this->database()->select('category_id')
			->from(Phpfox::getT('photo_category_data'))
			->where('photo_id = ' . (int) $aPhoto['photo_id'])
			->execute('getSlaveRows');
					
		foreach ($aCategories as $aCategory)
		{
			$sCategoryList .= $aCategory['category_id'] . ',';
		}
			
		$aPhoto['category_list'] = rtrim($sCategoryList, ',');		

		if (!empty($aPhoto['description']))
		{
			$aPhoto['description'] = str_replace('<br />', "\n", $aPhoto['description']);
		}
			
		return $aPhoto;
	}
	
	public function getForProcess($iId)
	{
		return $this->database()->select('user_id, photo_id, server_id, title, album_id, group_id, destination, privacy, privacy_comment')
			->from($this->_sTable)
			->where('photo_id = ' . (int) $iId . ' AND user_id = ' . Phpfox::getUserId())
			->execute('getSlaveRow');
	}
	
	public function getApprovalPhotosCount()
	{
		return $this->database()->select('COUNT(*)')
			->from($this->_sTable)
			->where('view_id = 1')
			->execute('getSlaveField');
	}
	
	public function getPhotoByDestination($sName)
	{
		$aPhoto = $this->database()->select('p.*, pi.*, pa.name AS album_title, ' . Phpfox::getUserField()) 
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->leftJoin(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
			->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')		
			->where('p.destination = \'' . $this->database()->escape($sName) . '\'')	
			->execute('getSlaveRow');		
			
		if (!isset($aPhoto['photo_id']))
		{
			return false;
		}
		
		return $aPhoto;
	}
	
	public function getPhoto($sId, $iUserId = 0, $aCallback = null)
	{
		if (Phpfox::isModule('like'))
		{
			$this->database()->select('lik.like_id AS is_liked, ')
					->leftJoin(Phpfox::getT('like'), 'lik', 'lik.type_id = \'photo\' AND lik.item_id = p.photo_id AND lik.user_id = ' . Phpfox::getUserId());
		}	
		
		$this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = p.user_id AND f.friend_user_id = " . Phpfox::getUserId());		
		
		$this->database()->where('p.photo_id = ' . (int) $sId);
		
		$aPhoto = $this->database()->select('' . Phpfox::getUserField() . ', p.*, pi.*, pa.name AS album_url, pa.name AS album_title, pa.profile_id AS album_profile_id, pt.item_id AS is_viewed')
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->leftJoin(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
			->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
			->leftJoin(Phpfox::getT('track'), 'pt', 'pt.item_id = p.photo_id AND pt.user_id = ' . Phpfox::getUserId() . ' AND pt.type_id=\'photo\'')
			->execute('getSlaveRow');
			
		if (!isset($aPhoto['photo_id']))
		{
			return false;
		}
			
		if (!Phpfox::isModule('like'))
		{
			$aPhoto['is_liked'] = false;
		}
		if (Phpfox::isModule('tag'))
		{
			$aTags = Tag_Service_Tag::instance()->getTagsById('photo', $aPhoto['photo_id']);
			if (isset($aTags[$aPhoto['photo_id']]))
			{
				$aPhoto['tag_list'] = $aTags[$aPhoto['photo_id']];
			}
		}
		
		$aPhoto['categories'] = Photo_Service_Category_Category::instance()->getCategoriesById($aPhoto['photo_id']);
		$aPhoto['category_list'] = Photo_Service_Category_Category::instance()->getCategoryIds($aPhoto['photo_id']);
		
		if (empty($aPhoto['album_id']))
		{
			$aPhoto['album_url'] = 'view';
		}
		
		$aPhoto['original_destination'] = $aPhoto['destination'];
		$aPhoto['destination'] = $this->getPhotoUrl($aPhoto);
		
		if ($aPhoto['album_id'] > 0)
		{
			if ($aPhoto['album_profile_id'] > 0)
			{
				$aPhoto['album_title'] = _p('profile_pictures');
				$aPhoto['album_url'] = Phpfox::permalink('photo.album.profile', $aPhoto['user_id'], $aPhoto['user_name']);
			}
			else
			{
				$aPhoto['album_url'] = Phpfox::permalink('photo.album', $aPhoto['album_id'], $aPhoto['album_title']);
			}
		}

		return $aPhoto;
	}
 
	/**
	 * We get and return the latest images we uploaded. The reason we run
	 * this check is so we only return images that belong to the user that is loggeed in
	 * and not someone else images.
	 *
	 * @param int $iUserId User ID of the user the images belong to.
	 * @param array $aIds Array of photo IDS
	 * 
	 * @return array Array of user images.
	 */
	public function getNewImages($iUserId, $aIds)
	{
		// We run an INT check just in case someone is trying to be funny.
		$sIds = '';
		foreach ($aIds as $iKey => $sId)
		{
			if (!is_numeric($sId))
			{
				continue;
			}
			$sIds .= $sId . ',';
		}
		$sIds = rtrim($sIds, ',');		
		
		// Lets the new images and return them.
		return $this->database()->select('p.photo_id, p.album_id, p.destination, p.server_id, p.view_id, pa.privacy, p.title')
					->from($this->_sTable, 'p')
					->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
					->where('p.photo_id IN(' . $sIds . ') AND p.user_id = ' . (int) $iUserId)
					->order('p.photo_id DESC')
					->execute('getSlaveRows');
	}
	
	public function getRandomSponsored()
	{
	    $sCacheId = $this->cache()->set('photo_sponsored');
	    if (!($aPhotos = $this->cache()->get($sCacheId)))
		{
			$aPhotos = $this->database()->select('s.*, pi.width, pi.height, u.user_name, p.total_view, p.time_stamp, pi.file_size, p.photo_id, p.destination, p.server_id, p.title, p.album_id, p.total_view')
				->from($this->_sTable, 'p')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
				->join(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
				->join(Phpfox::getT('ad_sponsor'),'s','s.item_id = p.photo_id')
				->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
				->where('p.view_id = 0 AND p.group_id = 0 AND p.privacy = 0 AND p.is_sponsor = 1 AND s.module_id = \'photo\'')
				->execute('getSlaveRows');
			
			$this->cache()->save($sCacheId, $aPhotos);
		}

		if (Phpfox::isModule('ad'))
		{
			$aPhotos = Ad_Service_Ad::instance()->filterSponsor($aPhotos);
		}
		
		if ($aPhotos === true || (is_array($aPhotos) && !count($aPhotos)))
		{
			return false;
		}

		// Randomize to get a photo		
		return $aPhotos[mt_rand(0, (count($aPhotos) - 1))];
	}

	/**
	 * @param $iPhotoId
	 * @return bool
	 */
	public function isSponsoredInFeed($iPhotoId){
		if (!Phpfox::isModule('ad') || !Phpfox::isModule('feed')){
			return false;
		}
		//Get Feed ID of Photo
		$iFeedId = $this->database()->select('feed_id')
			->from(':feed')
			->where('type_id="photo" AND item_id=' . (int) $iPhotoId)
			->execute('getSlaveField');
		if (!$iFeedId){
			return false;
		}
		$iCnt = $this->database()->select('DISTINCT item_id')
			->from(Phpfox::getT('ad_sponsor'))
			->where('module_id = "feed" AND item_id=' .(int) $iFeedId)
			->execute('getSlaveField');
		return ($iCnt) ? false : true;
	}
	
	public function getNew($iLimit = 3)
	{
		$aPhotos = $this->database()->select('p.destination, p.server_id, p.title, p.photo_id, p.mature, p.album_id, ' . Phpfox::getUserField())
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
			->where('p.view_id = 0 AND p.group_id = 0 AND p.privacy = 0')
			->order('p.time_stamp DESC')	
			->limit($iLimit)
			->execute('getSlaveRows');
			
		foreach ($aPhotos as $iKey => $aPhoto)
		{				
			$aPhotos[$iKey]['link'] = Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);
		}		
		
		return $aPhotos;	
	}
	
	/* This function is used in the converting controller to get the image that needs to have its thumbnails created.*/
	public function getForConverting($iUserId, $iLimit = 1)
	{
		$aPhoto = $this->database()->select('p.photo_id, p.destination, p.server_id, p.title, p.mature, p.album_id, ' . Phpfox::getUserField())
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
			->where('p.view_id = 0 AND p.user_id = ' . (int)$iUserId)
			->order('p.time_stamp DESC')	
			->limit($iLimit)
			->execute('getSlaveRows');
		return $aPhoto;
	}
	
	public function getForProfile($iUserId, $iLimit = 3)
	{
		$aPhotos = $this->database()->select(Phpfox::getUserField() . ',p.*, p.destination, p.server_id, p.title, p.mature, p.album_id, pa.name AS album_name')
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
			->where('p.view_id = 0 AND p.group_id = 0 AND p.privacy = 0 AND p.user_id = ' . (int) $iUserId)
			->order('p.time_stamp DESC')	
			->limit($iLimit)
			->execute('getSlaveRows');
		foreach ($aPhotos as $iKey => $aPhoto)
		{
			$aPhotos[$iKey]['link'] = Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);
		}		
		
		return $aPhotos;	
	}	
	
	public function getForGroup($iGroupId, $sGroupUrl)
	{
		$aPhotos = $this->database()->select('p.destination, p.server_id, p.title, p.mature, p.album_id, ' . Phpfox::getUserField())
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
			->where('p.view_id = 0 AND p.group_id = ' . $iGroupId . ' AND p.privacy = 0')
			->order('p.time_stamp DESC')	
			->limit(3)
			->execute('getSlaveRows');
			
		foreach ($aPhotos as $iKey => $aPhoto)
		{				
			$aPhotos[$iKey]['link'] = Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);
		}		
		
		return $aPhotos;	
	}	
	
	/**
	 * Return the featured time stamp in milliseconds
	 *
	 * @return int Time stamp in milliseconds
	 */
	public function getFeaturedRefreshTime()
	{
		// Get the refresh setting
		$sTime = Phpfox::getUserParam('photo.refresh_featured_photo');
		
		// Match the minutes or seconds
		preg_match("/(.*?)(min|sec)$/i", $sTime, $aMatches);
		
		// Make sure we have a match
		if (isset($aMatches[1]) && isset($aMatches[2]))
		{
			// Trim the matched time stamp
			$aMatches[2] = trim($aMatches[2]);
			
			// If we want to work with minutes
			if ($aMatches[2] == 'min')
			{
				// Convert to milliseconds
				return (int) ($aMatches[1] * 60000);
			}
			// If we want to work with seconds
			elseif ($aMatches[2] == 'sec')
			{
				// Convert to milliseconds
				return (int) ($aMatches[1] * 1000);	
			}
		}
		
		// Return the default value (60 seconds)
		return 60000;
	}
	
	/**
	 * Get the next photo based on the current photo and album we are viewing.
	 *
	 * @param int $iPhotoId ID of the current photo we are viewing
	 * @param string $sType
	 * @param int $iItemId
	 * @param array $aCallback
	 * @param int $iUserId
	 * @return array Array of the next photo
	 */
	public function getPreviousPhotos($iPhotoId, $sType = null, $iItemId = null, $aCallback = null, $iUserId = 0)
	{		
		(($sPlugin = Phpfox_Plugin::get('photo.service_album_album_getnextphoto')) ? eval($sPlugin) : false);
		
		$sView = 'p.view_id = 0';
		if ($iUserId == Phpfox::getUserId() || Phpfox::getUserParam('photo.can_approve_photos'))
		{
			$sView = 'p.view_id IN(0,1)';
		}		
		
		$aCond = array();
		if ($sType !== null)
		{
			if ($sType == 'album')
			{
				$aCond[] = 'p.photo_id > ' . (int) $iPhotoId . ' AND p.album_id = ' . (int) $iItemId . ' AND p.group_id = 0 AND ' . $sView;
			}
			elseif ($sType == 'group')
			{
				$aCond[] = 'p.photo_id > ' . (int) $iPhotoId . ' AND p.group_id = ' . (int) $iItemId . ' AND ' . $sView;
			}
		}	
		
		$aPhoto = $this->database()->select(Phpfox::getUserField() . ', p.photo_id,  p.destination, p.server_id, p.title, p.mature, p.album_id')
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
			->where($aCond)
			->order('p.photo_id ASC')
			->execute('getSlaveRow');	
			
		if (!isset($aPhoto['photo_id']))
		{
			return false;
		}
			
		$aPhoto['link'] = Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);
			
		return $aPhoto;
	}
	
	/**
	 * Get the previous photo based on the current photo and album we are viewing.
	 *
	 * @param int $iPhotoId ID of the current photo we are viewing
	 * @param string $sType
	 * @param int $iItemId
	 * @param array $aCallback
	 * @param int $iUserId
	 * @return array|bool Array of the previous photo
	 */	
	public function getNextPhotos($iPhotoId, $sType = null, $iItemId = null, $aCallback = null, $iUserId = 0)
	{		
		(($sPlugin = Phpfox_Plugin::get('photo.service_album_album_getpreviousphoto')) ? eval($sPlugin) : false);
		
		$sView = 'p.view_id = 0';
		if ($iUserId == Phpfox::getUserId() || Phpfox::getUserParam('photo.can_approve_photos'))
		{
			$sView = 'p.view_id IN(0,1)';
		}				
		
		$aCond = array();
		if ($sType !== null)
		{
			if ($sType == 'album')
			{
				$aCond[] = 'p.photo_id < ' . (int) $iPhotoId . ' AND p.album_id = ' . (int) $iItemId . ' AND p.group_id = 0 AND ' . $sView;
			}
			elseif ($sType == 'group')
			{
				$aCond[] = 'p.photo_id < ' . (int) $iPhotoId . ' AND p.group_id = ' . (int) $iItemId . ' AND ' . $sView;
			}			
		}
		
		$aPhoto = $this->database()->select(Phpfox::getUserField() . ', p.photo_id, p.destination, p.server_id, p.title, p.mature, p.album_id')
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
			->where($aCond)
			->order('p.photo_id DESC')
			->execute('getSlaveRow');
			
		if (!isset($aPhoto['photo_id']))
		{
			return false;
		}			
			
		$aPhoto['link'] = Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);
			
		return $aPhoto;
	}	
	
	public function getPhotoStream($iPhotoId, $iAlbumId = 0, $aCallback = null, $iUserId = 0, $iCategory = null, $iOwnerId = 0)
	{
		if (isset($aCallback['module_id']))
		{
			$sQuery = ' AND photo.module_id = \'' . $this->database()->escape($aCallback['module_id']) . '\' AND photo.group_id = ' . (int) $aCallback['item_id'];
		}
		else
		{
			$sQuery = ' AND photo.group_id = 0 AND photo.type_id = 0 ';
		}
		
		if ($iAlbumId > 0)
		{
			$sQuery = ' AND photo.album_id = ' . (int) $iAlbumId;	
		}
		if ($iUserId > 0)
		{
			$sQuery .= ' AND photo.user_id = ' . (int) $iUserId;
		}
		
        $bIsProfilePhotoAlbum = false;
        if ($iAlbumId > 0 && ($aAlbum = $this->database()->select('user_id, profile_id')->from(Phpfox::getT('photo_album'))->where('album_id = ' . (int)$iAlbumId)->execute('getSlaveRow') ) && $aAlbum['user_id'] == $aAlbum['profile_id'] )
        {
            $bIsProfilePhotoAlbum = true;
        }
		if (!Phpfox::getParam('photo.display_profile_photo_within_gallery') && !$bIsProfilePhotoAlbum)
		{
			$sQuery .= ' AND photo.is_profile_photo IN (0)';
		}
        
		// Check permissions
		if ($iAlbumId > 0 && $iOwnerId > 0 && Phpfox::getUserId() == $iOwnerId)
		{
			
		}
		elseif (!Phpfox::isAdmin())
		{
			
			/*
				4 => Custom
				3 => Only Me
				2 => Friends of Friends
				1 => Friends	
				0 => Everyone
			*/
			$sQuery .= (empty($sQuery) ? '' : ' AND ') . '(';
			$sQuery .= '(photo.privacy = 0)';
			if (Phpfox::getParam('core.section_privacy_item_browsing'))
			{	
				$sQuery .= ' OR ';
				// 3 - "Only me" privacy
				$sQuery .= ' (photo.privacy = 3 AND photo.user_id = ' . Phpfox::getUserId() . ') '; 				
				
                // Can view Pending-Approval photos
                if (Phpfox::getUserParam('photo.can_approve_photos') == false)
                {
                    $sQuery .= ' AND photo.view_id = 0';
                }
				$iCnt = 0;
				$aFriends = array();
				if (Phpfox::isModule('friend'))
				{
					list($iCnt, $aFriends) = Friend_Service_Friend::instance()->get(array('AND friend.user_id = ' . (int) Phpfox::getUserId()), '', '', false);
				}
				if ($iCnt > 0)
				{
					// 1 - Friends
					$sFriendsIn = '(';
					foreach ($aFriends as $aFriend)
					{
						$sFriendsIn .= $aFriend['friend_user_id'] .',';
					}
					$sFriendsIn = rtrim($sFriendsIn, ',') .')';
					
					$sQuery .= ' OR (photo.privacy = 1 AND photo.user_id IN ' . $sFriendsIn .')';
					
					if (Phpfox::isModule('friend')){
                        // 2 - Friends of Friends
                        $aFriendsOfFriends = Friend_Service_Friend::instance()->getFriendsOfFriends($sFriendsIn);
                        if (!empty($aFriendsOfFriends))
                        {
                            $sIn = implode(',', $aFriendsOfFriends);
                            $sQuery .= ' OR (photo.privacy = 2 AND photo.user_id IN (' . $sIn . '))';
                        }
                        $aInList = Friend_Service_List_List::instance()->getUsersInAnyList();
                        if (!empty($aInList)) {
                            $sIn = implode(',', $aInList);
                            $sQuery .= ' OR (photo.privacy = 4 AND photo.user_id IN (' . $sIn . '))';
                        }
                    }
				}
				else
				{
					$sQuery .= ') AND (photo.photo_id = 0';
				}				
			}
			else
			{
				$sQuery .= ' AND photo.privacy = 0 AND photo.view_id = 0';
			}
			$sQuery .= ')';
		}		
		
		list($iPreviousCnt, $aPrevious) = $this->_getPhoto('AND photo.photo_id > ' . (int) $iPhotoId . $sQuery, 'ASC', (empty($sQuery) ? false : true), $iCategory);
		list($iNextCount, $aNext) = $this->_getPhoto('AND photo.photo_id < ' . (int) $iPhotoId . $sQuery, 'DESC', (empty($sQuery) ? false : true), $iCategory);				

		return array(
			'total' => ($iNextCount + $iPreviousCnt + 1),
			'current' => ($iPreviousCnt + 1),			
			'previous' => $aPrevious,
			'next' => $aNext
		);
	}
		
	public function getPendingTotal()
	{
		return (int) $this->database()->select('COUNT(*)')
			->from($this->_sTable)
			->where('view_id = 1')
			->execute('getSlaveField');		
	}

	public function getPhotoUrl($aPhoto)
	{
		$sUrl = $aPhoto['destination'];
		if (Phpfox::getParam('photo.rename_uploaded_photo_names'))
		{			
			if (preg_match('/(.*)\/(.*)\%s\.(.*)/i', $aPhoto['destination'], $aMatches) && isset($aMatches[2]) && (int) strlen($aMatches[2]) == 32)
			{
				$sUrl = '[PHPFOX_CUSTOM_URL]' . $aMatches[1] . '/' . $aMatches[2] . '-' . Phpfox::getLib('parse.input')->cleanFileName($aPhoto['title']) . '%s.' . $aMatches[3];
			}
		}

		return $sUrl;
	}
	
	public function getFeatured()
	{
		static $aFeatured = null;
		static $iTotal = null;
		
		if ($aFeatured !== null)
		{
			return array($iTotal, $aFeatured);
		}
		
		$aFeatured = array();
		$sCacheId = $this->cache()->set('photo_featured');		
		if (!($aRows = $this->cache()->get($sCacheId)))
		{
			$aRows = $this->database()->select('v.*, ' . Phpfox::getUserField())
				->from(Phpfox::getT('photo'), 'v')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
				->where('v.is_featured = 1 AND v.group_id = 0 AND v.type_id = 0 AND v.privacy = 0')
				->execute('getSlaveRows');
			
			$this->cache()->save($sCacheId, $aRows);
		}
		
		$iTotal = 0;
		if (is_array($aRows) && count($aRows))
		{
			$iTotal = count($aRows);
			shuffle($aRows);
			foreach ($aRows as $iKey => $aRow)
			{
				$aFeatured[] = $aRow;
			}
		}

		return array($iTotal, $aFeatured);
	}	
	
	public function buildMenu()
	{
		$aFilterMenu = array();
		if (!defined('PHPFOX_IS_USER_PROFILE'))
		{
			if (Phpfox::getParam('photo.in_main_photo_section_show') == 'albums' && Phpfox::getUserParam('photo.can_view_photo_albums'))
			{
			    $aFilterMenu[_p('all_albums')] = '';
			    $aFilterMenu[_p('my_albums')] = 'photo.albums.view_myalbums';
			    $aFilterMenu[] = true;
			}
			
			if (Phpfox::getParam('core.friends_only_community') ||  !Phpfox::isModule('friend'))
			{				
				$aFilterMenu[_p('all_photos')] = 'photos';
				$aFilterMenu[_p('my_photos')] = 'my';
			}
			else 
			{
				if (Phpfox::getParam('photo.in_main_photo_section_show') == 'albums')
				{
				    $aFilterMenu[_p('all_photos')] = 'photo.view_photos';
				}
				else
				{
				    $aFilterMenu[_p('all_photos')] = '';
				}
				
				$aFilterMenu[_p('my_photos')] = 'my';
				$aFilterMenu[_p('friends_photos')] = 'friend';
			}				
			
			list($iTotalFeatured, $aFeatured) = Photo_Service_Photo::instance()->getFeatured();
			if ($iTotalFeatured)
			{
				$aFilterMenu[_p('featured_photos') . '<span class="pending">' . $iTotalFeatured . '</span>'] = 'featured';
			}			
				
			if (Phpfox::getUserParam('photo.can_approve_photos'))
			{
				$iPendingTotal = Photo_Service_Photo::instance()->getPendingTotal();
				
				if ($iPendingTotal)
				{
					$aFilterMenu[_p('pending_photos') . (Phpfox::getUserParam('photo.can_approve_photos') ? '<span class="pending">' . $iPendingTotal . '</span>' : 0)] = 'pending';
				}
			}
			
			if (Phpfox::getParam('photo.in_main_photo_section_show') != 'albums' &&Phpfox::getUserParam('photo.can_view_photo_albums'))
			{
				$aFilterMenu[] = true;
				$aFilterMenu[_p('all_albums')] = 'photo.albums';
				$aFilterMenu[_p('my_albums')] = 'photo.albums.view_myalbums';
			}
		}
		
		Phpfox_Template::instance()->buildSectionMenu('photo', $aFilterMenu);
	}
	
	public function getInfoForAction($aItem)
	{
		if (is_numeric($aItem))
		{
			$aItem = array('item_id' => $aItem);
		}
		$aRow = $this->database()->select('p.photo_id, p.title, p.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('photo'), 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.photo_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');
			
		if (empty($aRow))
		{
			d($aRow);
			d($aItem);
		}
		
		$aRow['link'] = Phpfox_Url::instance()->permalink('photo', $aRow['photo_id'], $aRow['title']);
		return $aRow;
	}

	public function getFeedPhotos($iFeedId, $iLimit = null, $sFeedTablePrefix = '')
	{
		$aFeed = Feed_Service_Feed::instance()->getFeed($iFeedId, $sFeedTablePrefix);
		if (!$aFeed)
		{
			return [];
		}
		if ($iLimit)
		{
			$aPhotos = $this->database()
				->select('p.photo_id, p.album_id, p.user_id, p.title, p.server_id, p.destination, p.mature')
				->from(Phpfox::getT('photo'), 'p')
				->leftJoin(Phpfox::getT('photo_feed'), 'pfeed', 'p.photo_id = pfeed.photo_id')
				->where('(pfeed.feed_id = ' . $iFeedId . ' AND  pfeed.feed_table = \'' . $sFeedTablePrefix . 'feed\') OR p.photo_id = ' . $aFeed['item_id'])
				->limit($iLimit)
				->order('pfeed.feed_id ASC, p.time_stamp DESC')
				->execute('getSlaveRows');
		}
		else {
			$aPhotos = $this->database()
				->select('p.photo_id, p.album_id, p.user_id, p.title, p.server_id, p.destination, p.mature')
				->from(Phpfox::getT('photo'), 'p')
				->leftJoin(Phpfox::getT('photo_feed'), 'pfeed', 'p.photo_id = pfeed.photo_id')
				->where('(pfeed.feed_id = ' . $iFeedId . ' AND  pfeed.feed_table = \'' . $sFeedTablePrefix . 'feed\') OR p.photo_id = ' . $aFeed['item_id'])
				->order('pfeed.feed_id ASC, p.time_stamp DESC')
				->execute('getSlaveRows');
		}
		foreach ($aPhotos as $key => $aPhoto)
		{
			$aPhotos[$key]['html'] = Phpfox::getLib('image.helper')->display([
					'server_id' => $aPhoto['server_id'],
					'path' => 'photo.url_photo',
					'file' => Photo_Service_Photo::instance()->getPhotoUrl($aPhoto),
					'suffix' => '_500',
					'userid' => $aPhoto['user_id']
				]
			);
			$aPhotos[$key]['link'] = Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);
		}
		return $aPhotos;
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
		if ($sPlugin = Phpfox_Plugin::get('photo.service_photo__call'))
		{
			eval($sPlugin);
            return null;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
	
	private function _getPhoto($sCondition, $sOrder, $bNoPrivacy = false, $iCategory = null)
	{		
		if ($bNoPrivacy === true)
		{
			$iCategoryChecked = null;
			if ($iCategory !== null)
			{
				$iCategoryChecked = (int)$iCategory;
			}
			else if (Phpfox::getCookie('photo_category'))
			{
				$iCategoryChecked = Phpfox::getCookie('photo_category');
			}
			else if ( (isset($_SESSION['photo_category']) && $_SESSION['photo_category'] != '') )
			{
				$iCategoryChecked = $_SESSION['photo_category'];
			}
			
			if ( $iCategoryChecked !== null )
			{
				$this->database()->join(Phpfox::getT('photo_category_data'), 'pcd', 'pcd.photo_id = photo.photo_id AND pcd.category_id = ' . ((int)$iCategoryChecked));
			}
			$iPreviousCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('photo'), 'photo')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = photo.user_id')
				->where(array($sCondition))
				->execute('getSlaveField');

			if ($iCategoryChecked !== null)
			{				
				$this->database()->select('pcd.category_id,')->join(Phpfox::getT('photo_category_data'), 'pcd', 'pcd.photo_id = photo.photo_id AND pcd.category_id = ' . (int)$iCategoryChecked);
			}
			$aPrevious = (array) $this->database()->select('photo.*')
				->from(Phpfox::getT('photo'), 'photo')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = photo.user_id')
				->where(array($sCondition))
				->order('photo.photo_id ' . $sOrder)
				->execute('getSlaveRow');

			if (!empty($aPrevious['photo_id']))
			{
				$aPrevious['link'] = Phpfox_Url::instance()->permalink('photo', $aPrevious['photo_id'], $aPrevious['title']) . ($iCategoryChecked !== null ? 'category_' . $iCategoryChecked : '');
			}
			
			return array($iPreviousCnt, $aPrevious);	
		}
		
		$aBrowseParams = array(
			'module_id' => 'photo',
			'alias' => 'photo',
			'field' => 'photo_id',
			'table' => Phpfox::getT('photo'),
			'hide_view' => array('pending', 'my')
		);				
		
		$this->search()->set(array(
				'type' => 'photo',
				'filters' => array(
					'display' => array('type' => 'option', 'default' => '1'),
					'sort' => array('type' => 'option', 'default' => 'photo_id'),
					'sort_by' => array('type' => 'option', 'default' => $sOrder)
				)
			)
		);

		$this->search()->setCondition($sCondition);	
		$this->search()->setCondition('AND photo.view_id = 0 AND photo.group_id = 0 AND photo.type_id = 0 AND photo.privacy IN(%PRIVACY%)');

		$this->search()->browse()->params($aBrowseParams)->execute();
		$iPreviousCnt = $this->search()->browse()->getCount();
		$aPreviousRows = $this->search()->browse()->getRows();	
		
		$this->search()->browse()->reset();
		
		$aPrevious = array();
		if (isset($aPreviousRows[0]))
		{
			$aPrevious = $aPreviousRows[0];	
		}		
		
		return array($iPreviousCnt, $aPrevious);
	}

    public function cropMaxWidth($sDes)
    {
        $oImage = Phpfox_Image::instance();
        list($width, $height, $type, $attr) = @getimagesize($sDes);
        if ($width == 0 || $height == 0){
            return null;
        }
        $iWidth = (int)Phpfox::getUserParam('photo.maximum_image_width_keeps_in_server');
        if ($iWidth < $width) {
            $bIsCropped = $oImage->createThumbnail($sDes, $sDes, $iWidth, $height, true, ((Phpfox::getParam('photo.enabled_watermark_on_photos') && Phpfox::getParam('core.watermark_option') != 'none') ? (Phpfox::getParam('core.watermark_option') == 'image' ? 'force_skip' : true) : false));
            if ($bIsCropped !== false && Phpfox::getParam('photo.enabled_watermark_on_photos')) {
                $oImage->addMark($sDes);
            }
        }
    }

    /**
     * @description: check permission to view a photo
     * @param int $iId
     * @param bool $bReturnItem
     *
     * @return array|bool|int|string
     */
    public function canViewItem($iId, $bReturnItem = false)
    {
        if (!Phpfox::getUserParam('photo.can_view_photos'))
        {
            Phpfox_Error::set(_p('You don\'t have permission to {{ action }} {{ items }}.', ['action' => _p('view__l'), 'items' => _p('photos__l')]));
            return false;
        }

        $aPhoto = Photo_Service_Photo::instance()->getPhoto($iId);

        // No photo founds lets get out of here
        if (!isset($aPhoto['photo_id']))
        {
            Phpfox_Error::set(_p('This {{ item }} cannot be found.', ['item' => _p('photo__l')]));
            return false;
        }

        if (Phpfox::isUser() && User_Service_Block_Block::instance()->isBlocked(null, $aPhoto['user_id']))
        {
            Phpfox_Error::set(_p('Sorry, this content isn\'t available right now'));
            return false;
        }

        if (Phpfox::isModule('privacy'))
        {
            if (!Privacy_Service_Privacy::instance()->check('photo', $aPhoto['photo_id'], $aPhoto['user_id'], $aPhoto['privacy'], $aPhoto['is_friend'], true))
            {
                return false;
            }
        }

        if ($aPhoto['mature'] != 0)
        {
            if (Phpfox::getUserId())
            {
                if ($aPhoto['user_id'] != Phpfox::getUserId())
                {
                    if ($aPhoto['mature'] == 1 && Phpfox::getUserParam(array(
                                'photo.photo_mature_age_limit' => array(
                                    '>',
                                    (int) Phpfox::getUserBy('age')
                                )
                            )
                        )
                    )
                    {
                        // warning check cookie
                    }
                    elseif ($aPhoto['mature'] == 2 && Phpfox::getUserParam(array(
                                'photo.photo_mature_age_limit' => array(
                                    '>',
                                    (int) Phpfox::getUserBy('age')
                                )
                            )
                        )
                    )
                    {
                        return Phpfox_Error::display(_p('sorry_this_photo_can_only_be_viewed_by_those_older_then_the_age_of_limit', array('limit' => Phpfox::getUserParam('photo.photo_mature_age_limit'))));
                    }
                }
            }
            else
            {
                Phpfox_Error::set(_p('You don\'t have permission to {{ action }} this {{ item }}.', ['action' => _p('view__l'), 'item' => _p('photo__l')]));
                return false;
            }
        }
        if (!empty($aPhoto['module_id']) && $aPhoto['module_id'] != 'photo')
        {
            if ($aCallback = Phpfox::callback($aPhoto['module_id'] . '.getPhotoDetails', $aPhoto))
            {
                if(Phpfox::isModule($aPhoto['module_id']) && Phpfox::hasCallback($aPhoto['module_id'], 'checkPermission'))
                {
                    if(!Phpfox::callback($aPhoto['module_id'] . '.checkPermission', $aCallback['item_id'], 'photo.view_browse_photos'))
                    {
                        Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
                        return false;
                    }
                }
            }
        }

        if (!$bReturnItem)
        {
            return true;
        }

        $aPhoto['bookmark_url'] = Phpfox_Url::instance()->permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);
        $aPhoto['photo_url'] = Phpfox::getLib('image.helper')->display(array(
            'server_id' => $aPhoto['server_id'],
            'path' => 'photo.url_photo',
            'file' => $aPhoto['destination'],
            'suffix' => '_1024',
            'return_url' => true
        ));

        return $aPhoto;
    }
}