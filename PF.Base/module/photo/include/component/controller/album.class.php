<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Controller used to view photo albums on a users profile.
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: album.class.php 7275 2014-04-21 14:44:05Z Fern $
 */
class Photo_Component_Controller_Album extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		(($sPlugin = Phpfox_Plugin::get('photo.component_controller_album__1')) ? eval($sPlugin) : false);
		
		Phpfox::getUserParam('photo.can_view_photo_albums', true);
		Phpfox::getUserParam('photo.can_view_photos', true);
		
		if (Phpfox::isUser() && Phpfox::isModule('notification'))
		{
			Notification_Service_Process::instance()->delete('comment_photo_album', $this->request()->getInt('req3'), Phpfox::getUserId());
			Notification_Service_Process::instance()->delete('photo_album_like', $this->request()->getInt('req3'), Phpfox::getUserId());
		}		
		
		(($sPlugin = Phpfox_Plugin::get('photo.component_controller_album_process_start')) ? eval($sPlugin) : false);		
		
		$bIsProfilePictureAlbum = false;
		$bIsCoverPhotoAlbum = false;
		if ($this->request()->get('req3') == 'profile')
		{
			$bIsProfilePictureAlbum = true;
			$aAlbum = Photo_Service_Album_Album::instance()->getForProfileView($this->request()->getInt('req4'));
		} elseif ($this->request()->get('req3') == 'cover'){
            $bIsCoverPhotoAlbum = true;
            $aAlbum = Photo_Service_Album_Album::instance()->getForCoverView($this->request()->getInt('req4'));
        } else {
			// Get the current album we are trying to view
			$aAlbum = Photo_Service_Album_Album::instance()->getForView($this->request()->getInt('req3'));
			if ($aAlbum['profile_id'] > 0)
			{
				$bIsProfilePictureAlbum = true;
			}
		}
		// Make sure this is a valid album
		if ($aAlbum === false)
		{
			return Phpfox_Error::display(_p('invalid_photo_album'));
		}
		
		if ($bIsProfilePictureAlbum) {
			$aAlbum['name'] = _p('profile_pictures');
		} elseif ($bIsCoverPhotoAlbum){
            $aAlbum['name'] = _p('cover_photo');
        }

        if (Phpfox::isUser() && User_Service_Block_Block::instance()->isBlocked(null, $aAlbum['user_id']))
        {
            return Phpfox_Module::instance()->setController('error.invalid');
        }

		$aCallback = null;
		if (!empty($aAlbum['module_id']))
		{			
			if ($aCallback = Phpfox::callback($aAlbum['module_id'] . '.getPhotoDetails', $aAlbum))
			{
				$this->template()->setBreadCrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
				$this->template()->setBreadCrumb($aCallback['title'], $aCallback['url_home']);
			}
		}		
		
		if (Phpfox::isModule('privacy'))
		{
			Privacy_Service_Privacy::instance()->check('photo_album', $aAlbum['album_id'], $aAlbum['user_id'], $aAlbum['privacy'], $aAlbum['is_friend']);
		}
		
		(($sPlugin = Phpfox_Plugin::get('photo.component_controller_album_process_album')) ? eval($sPlugin) : false);
		
		// Store the album details so we can use it in a block later on
		$this->setParam('aAlbum', $aAlbum);

		// Setup the page data
		$iPage = $this->request()->getInt('page');
		$iPageSize = Phpfox::getUserParam('photo.total_photo_display_profile');

		// Create the SQL condition array
		$aConditions = array();
		$aConditions[] = 'p.album_id = ' . $aAlbum['album_id'] . '';
		
		(($sPlugin = Phpfox_Plugin::get('photo.component_controller_album_process_conditions')) ? eval($sPlugin) : false);
		
		// Get the photos based on the conditions
		list($iCnt, $aPhotos) = Photo_Service_Photo::instance()->get($aConditions, 'p.photo_id DESC', $iPage, $iPageSize);
		
		// Set the pager for the photos
		Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));

		foreach ($aPhotos as $aPhoto)
		{
			$this->template()->setMeta('keywords', $this->template()->getKeywords($aPhoto['title']));		
			if ($aPhoto['is_cover'])
			{				
				$this->template()->setMeta('og:image', Phpfox::getLib('image.helper')->display(array(
							'server_id' => $aPhoto['server_id'],
							'path' => 'photo.url_photo',
							'file' => $aPhoto['destination'],
							'suffix' => '_240',
							'return_url' => true
						)
					)
				);
			}
		}	
		
		if (Phpfox::getUserBy('profile_page_id'))
		{
			Pages_Service_Pages::instance()->setIsInPage();
		}
        $aParamFeed = [
            'comment_type_id' => 'photo_album',
            'privacy' => $aAlbum['privacy'],
            'comment_privacy' => $aAlbum['privacy_comment'],
            'like_type_id' => 'photo_album',
            'feed_is_liked' => $aAlbum['is_liked'],
            'feed_is_friend' => $aAlbum['is_friend'],
            'item_id' => $aAlbum['album_id'],
            'user_id' => $aAlbum['user_id'],
            'total_comment' => $aAlbum['total_comment'],
            'total_like' => $aAlbum['total_like'],
            'feed_link' => $this->url()->permalink('photo.album', $aAlbum['album_id'], $aAlbum['name']),
            'feed_title' => $aAlbum['name'],
            'feed_display' => 'view',
            'feed_total_like' => $aAlbum['total_like'],
            'report_module' => 'photo_album',
            'report_phrase' => _p('report_this_photo_album')
        ];
        //Disable like and comment if non-friend view profile|cover album
        if ($aAlbum['profile_id'] || $aAlbum['cover_id']){
            if (!User_Service_Privacy_Privacy::instance()->hasAccess($aAlbum['user_id'],'feed.share_on_wall')){
                unset($aParamFeed['comment_type_id']);
                $aParamFeed['disable_like_function'] = true;
            }
        }
		$this->setParam('aFeed', $aParamFeed);

		// Assign the template vars
		$this->template()->setTitle($aAlbum['name'])
				->setBreadCrumb(_p('photos'), ($aCallback === null ? $this->url()->makeUrl('photo') : $this->url()->makeUrl($aCallback['url_home_photo'])))
				->setBreadCrumb($aAlbum['name'], $this->url()->permalink('photo.album', $aAlbum['album_id'], $aAlbum['name']), true)
				->setMeta('description', (empty($aAlbum['description']) ? $aAlbum['name'] : $aAlbum['description']))
				->setMeta('keywords', $this->template()->getKeywords($aAlbum['name']))
				->setMeta('keywords', Phpfox::getParam('photo.photo_meta_keywords'))
				->setPhrase(array(
						'updating_album',
						'none_of_your_files_were_uploaded_please_make_sure_you_upload_either_a_jpg_gif_or_png_file'
					)
				)
				->setEditor()
				->setHeader('cache', array(
					'jquery/plugin/jquery.mosaicflow.min.js' => 'static_script'
				)
			)
			->assign(array(
				'aPhotos' => $aPhotos,
				'aForms' => $aAlbum,
				'aAlbum' => $aAlbum,
				'aCallback' => null,
				'bIsInAlbumMode' => true,
				'iForceAlbumId' => $aAlbum['album_id'],
				'iPhotosPerRow' => 5
			)
		);
		
		$this->setParam('global_moderation', array(
				'name' => 'photo',
				'ajax' => 'photo.moderation',
				'menu' => array(
					array(
						'phrase' => _p('delete'),
						'action' => 'delete'
					),
					array(
						'phrase' => _p('approve'),
						'action' => 'approve'
					)					
				)
			)
		);		
		
		(($sPlugin = Phpfox_Plugin::get('photo.component_controller_album_process_end')) ? eval($sPlugin) : false);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('photo.component_controller_album_clean')) ? eval($sPlugin) : false);
	}
}