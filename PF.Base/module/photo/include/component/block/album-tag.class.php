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
 * @version 		$Id: album-tag.class.php 4240 2012-06-08 11:29:40Z Raymond_Benc $
 */
class Photo_Component_Block_Album_Tag extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aAlbum = $this->getParam('aAlbum');
		$view = $this->getParam('view', 'block');
		$page = $this->request()->get('page', 1);
		$iLimit = ($view == 'all') ? 10 : 8;
		
		list($iCnt, $aUsers) = Photo_Service_Album_Album::instance()->inThisAlbum($aAlbum['album_id'], $iLimit, $page);
		
		if (!$iCnt)
		{
			return false;
		}

		if ($view == 'all') {
			define('TEST_PAGER', true);
			Phpfox_Pager::instance()->set(array('page' => $page, 'size' => $iLimit, 'count' => $iCnt, 'ajax' => 'photo.browseAlbumTags', 'popup' => true));
		}
		$this->template()->assign(array(
				'sHeader' => '<a href="#" onclick="return $Core.box(\'photo.browseAlbumTags\', 400, \'album_id=' . $aAlbum['album_id'] . '\');">' . _p('in_this_album') . '<span>' . $iCnt . '</span>' . '</a>',
				'aTaggedUsers' => $aUsers,
				'sView' => $view,
				'iPage' => $page
			)
		);
		
		return 'block';	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('photo.component_block_album_tag_clean')) ? eval($sPlugin) : false);
	}
}