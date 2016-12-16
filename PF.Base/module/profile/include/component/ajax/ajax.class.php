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
 * @package 		Phpfox_Ajax
 * @version 		$Id: ajax.class.php 4622 2012-09-12 07:18:24Z Miguel_Espinoza $
 */
class Profile_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function logo()
	{
		$this->setTitle(_p('cover_photo'));
		$aParams = array(
			'page_id' => $this->get('page_id'),
			'groups_id' => $this->get('groups_id')
		);
		
		Phpfox::getBlock('profile.cover', $aParams);
	}
}