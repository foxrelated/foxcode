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
 * @version 		$Id: track.class.php 3553 2011-11-22 16:46:19Z Raymond_Benc $
 */
class Music_Component_Block_Track extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (!$this->getParam('inline_album')) {
			return false;
		}

		if ($this->getParam('album_user_id', null) === null)
		{
			return false;
		}
		
		$aSongs = Music_Service_Album_Album::instance()->getTracks($this->getParam('album_user_id'), $this->getParam('album_id'), $this->getParam('album_view_all', false));
		
		$this->template()->assign(array(
				'aTracks' => $aSongs,
				'bIsMusicPlayer' => ($this->getParam('is_player') ? true : false)
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
		(($sPlugin = Phpfox_Plugin::get('music.component_block_track_clean')) ? eval($sPlugin) : false);

		$this->clearParam('inline_album');
	}
}