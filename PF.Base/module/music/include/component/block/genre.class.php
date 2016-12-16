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
 * @version 		$Id: genre.class.php 1352 2009-12-22 19:33:07Z Raymond_Benc $
 */
class Music_Component_Block_Genre extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aUser = $this->getParam('aUser');		
		
		if (!Phpfox::getUserGroupParam((isset($aUser['user_group_id']) ? $aUser['user_group_id'] : Phpfox::getUserBy('user_group_id')), 'music.can_upload_music_public'))
		{			
			return false;
		}			
		
		$this->template()->assign(array(
				'iCustomGroupId' => Custom_Service_Group_Group::instance()->getId('music.custom_group_basics'),
				'aGenres' => Music_Service_Genre_Genre::instance()->getList(),
				'iGenerCount' => 3,
				'bIsGlobalEdit' => (isset($aUser['user_id']) ? true : false)
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
		(($sPlugin = Phpfox_Plugin::get('music.component_block_genre_clean')) ? eval($sPlugin) : false);
	}
}