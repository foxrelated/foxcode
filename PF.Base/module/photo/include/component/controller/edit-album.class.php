<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Component
 * @version 		$Id: edit-album.class.php 4699 2012-09-20 10:30:04Z Raymond_Benc $
 */
class Photo_Component_Controller_Edit_Album extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		
		if (Phpfox::getUserBy('profile_page_id'))
		{
			Pages_Service_Pages::instance()->setIsInPage();
		}
		
		if (!($aAlbum = Photo_Service_Album_Album::instance()->getForEdit($this->request()->getInt('id'))))
		{
			return Phpfox_Error::display(_p('photo_album_not_found'));
		}
		
		if (($aVals = $this->request()->getArray('val')))
		{
			if ($this->request()->get('req3') == 'photo')
			{
				if (Photo_Service_Process::instance()->massProcess($aAlbum, $aVals))
				{
					$this->url()->send('photo.edit-album.photo', array('id' => $aAlbum['album_id']), _p('photo_s_successfully_updated'));
				}
			}
			else 
			{
				if (Photo_Service_Album_Process::instance()->update($aAlbum['album_id'], $aVals))
				{
					$this->url()->permalink('photo.album', $aAlbum['album_id'], $aAlbum['name'], true, _p('album_successfully_updated'));
				}
			}
		}
		
		$aMenus = array(
			'detail' => _p('album_info'),
			'photo' => _p('photos')
		);
		
		$this->template()->buildPageMenu('js_photo_block', 
			$aMenus,
			array(
				'link' => $this->url()->permalink('photo.album', $aAlbum['album_id'], $aAlbum['name']),
				'phrase' => _p('view_this_album_uppercase')
			)
		);	
		
		list(, $aPhotos) = Photo_Service_Photo::instance()->get('p.album_id = ' . (int) $aAlbum['album_id']);
		list(, $aAlbums) = Photo_Service_Album_Album::instance()->get('pa.user_id = ' . Phpfox::getUserId());
		
		$this->template()->setTitle(_p('editing_album') . ': ' . $aAlbum['name'])
			->setFullSite()
			->setBreadCrumb(_p('photo'), $this->url()->makeUrl('photo'))
			->setBreadCrumb(_p('editing_album') . ': ' . $aAlbum['name'], $this->url()->makeUrl('photo.edit-album', array('id' => $aAlbum['album_id'])), true)
			->setHeader(array(
					'edit.css' => 'module_photo',
					'photo.js' => 'module_photo'
				)
			)
			->assign(array(
					'aForms' => $aAlbum,
					'aPhotos' => $aPhotos,
					'aAlbums' => $aAlbums
				)
			);
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('photo.component_controller_edit_album_clean')) ? eval($sPlugin) : false);
	}
}