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
 * @package 		Phpfox_Component
 * @version 		$Id: public-album.class.php 1388 2010-01-11 20:17:18Z Raymond_Benc $
 */
class Photo_Component_Controller_Public_Album extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::getUserParam('photo.can_view_photos', true);
		
		$aCond = array();
		$aCond[] = 'AND pa.total_photo > 0';
		
		$iPage = $this->request()->getInt('page');
		$iPageSize = 8;
		
		list($iCnt, $aAlbums) = Photo_Service_Album_Album::instance()->get($aCond, 'pa.time_stamp DESC', $iPage, $iPageSize);
		Photo_Service_Album_Browse::instance()->processRows($aAlbums);
		
		Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));

		$this->template()
			->setBreadCrumb(_p('photos'), $this->url()->makeUrl('photo'))
			->setBreadCrumb(_p('albums'), $this->url()->makeUrl('photo.public-album'))
			->assign(array(
				'aAlbums' => $aAlbums
			)
		);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('photo.component_controller_public_album_clean')) ? eval($sPlugin) : false);
	}
}