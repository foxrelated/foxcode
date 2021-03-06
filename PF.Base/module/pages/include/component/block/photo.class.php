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
 * @version 		$Id: block.class.php 103 2009-01-27 11:32:36Z Raymond_Benc $
 */
class Pages_Component_Block_Photo extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (!defined('PHPFOX_IS_PAGES_VIEW'))
		{
			return false;
		}
		$aPage = $this->getParam('aPage');

        if (!isset($aPage['page_id']) || empty($aPage['page_id'])){
            return false;
        }

		$aCoverPhoto = ($aPage['cover_photo_id'] ? Photo_Service_Photo::instance()->getCoverPhoto($aPage['cover_photo_id']) : false);
		$iCoverPhotoPosition = $aPage['cover_photo_position'];

		$aPageMenus = Pages_Service_Pages::instance()->getMenu($aPage);

		$this->template()->assign([
			'aCoverPhoto' => $aCoverPhoto,
			'iConverPhotoPosition'=>$iCoverPhotoPosition,
			'aPageMenus' => $aPageMenus,
			'sCoverDefaultUrl' => flavor()->active->default_photo('pages_cover_default', true),
		]);
        return null;
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('pages.component_block_photo_clean')) ? eval($sPlugin) : false);
	}
}