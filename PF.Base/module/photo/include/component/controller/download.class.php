<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Controller used to download a users photo.
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Component
 * @version 		$Id: download.class.php 2610 2011-05-19 18:43:08Z Raymond_Benc $
 */
class Photo_Component_Controller_Download extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::getUserParam('photo.can_view_photos', true);
		
		// Check if we want to download a specific photo size
		$iDownloadSize = $this->request()->get('size');
		
		// Get photo array
		$aPhoto = $this->getParam('aPhoto');
        if ($aPhoto['user_id'] != Phpfox::getUserId()){
            // Make sure the user group can download this photo
            Phpfox::getUserParam('photo.can_download_user_photos', true);
        }

		if (!$aPhoto['allow_download'] && $aPhoto['user_id'] != Phpfox::getUserId())
		{
			return Phpfox_Error::display(_p('not_allowed_to_download_this_image'));
		}
		
		// Prepare the image path
		$sPath = Phpfox::getParam('photo.dir_photo') . sprintf($aPhoto['original_destination'], (is_numeric($iDownloadSize) ? '_' . $iDownloadSize : ''));
        //Make sure download file exist
		if (!file_exists($sPath)){
            $aSize = Phpfox::getParam('photo.photo_pic_sizes');
            rsort($aSize);
            foreach ($aSize as $size){
                $sPath = Phpfox::getParam('photo.dir_photo') . sprintf($aPhoto['original_destination'], (is_numeric($size) ? '_' . $size : ''));
                if (file_exists($sPath)){
                    break;
                }
            }
        }
		// Increment the download counter
        Photo_Service_Process::instance()->updateCounter($aPhoto['photo_id'], 'total_download');
		
		// Download the photo
		Phpfox_File::instance()->forceDownload($sPath, $aPhoto['file_name'], $aPhoto['mime_type'], $aPhoto['file_size'], $aPhoto['server_id']);
		
		// We are done, lets get out of here
		exit;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('photo.component_controller_download_clean')) ? eval($sPlugin) : false);
	}
}