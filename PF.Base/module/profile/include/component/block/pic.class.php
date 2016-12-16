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
 * @package  		Module_Profile
 * @version 		$Id: pic.class.php 7305 2014-05-07 19:35:55Z Fern $
 */
class Profile_Component_Block_Pic extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{

		if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PAGE_TIME_LINE'))
		{
			return false;
		}
		
		(($sPlugin = Phpfox_Plugin::get('profile.component_block_pic_start')) ? eval($sPlugin) : false);
		
		if (isset($bHideThisBlock))
		{
			return false;
		}		
		
		$aUser = $this->getParam('aUser');

		if ($aUser === null)
		{
			$aUser = $this->getParam('aPage');
			$aUser['user_image'] = $aUser['image_path'];
			foreach ($aUser as $sKey => $sValue)
			{
				if (strpos($sKey, 'owner_') !== false && $sKey != 'owner_user_image')
				{
					$aUser[str_replace('owner_', '', $sKey)] = $sValue;
				}
			}
		}
		
		if(defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getParam('core.allow_cdn'))
		{
			$aUser['user_id'] = $aUser['page_user_id'];
			$aUser['server_id'] = $aUser['image_server_id'];
			$aUser['full_name'] = $aUser['title'];
			$aUser['user_name'] = !empty($aUser['vanity_url']) ? $aUser['vanity_url'] : $aUser['title'];
			$aUser['user_group_id'] = 2;
			
			$this->template()->assign(array(
					'aUser' => $aUser
				)
			);
		}

		$aUserInfo = array(
			'title' => $aUser['full_name'],
			'path' => 'core.url_user',
			'file' => $aUser['user_image'],
			'suffix' => '_200_square',
			'max_width' => 200,
			'no_default' => (Phpfox::getUserId() == $aUser['user_id'] ? false : true),
			'thickbox' => true,
			'class' => 'profile_user_image',
			'no_link' => true
		);

		(($sPlugin = Phpfox_Plugin::get('profile.component_block_pic_process')) ? eval($sPlugin) : false);
		
		$sImage = Phpfox::getLib('image.helper')->display(array_merge(array('user' => User_Service_User::instance()->getUserFields(true, $aUser)), $aUserInfo));

		$this->template()->assign(array(
				'sProfileImage' => $sImage
			)
		);

		$bCanSendPoke = Phpfox::isModule('poke') && Poke_Service_Poke::instance()->canSendPoke($aUser['user_id']);
		$aCoverPhoto = Photo_Service_Photo::instance()->getCoverPhoto($aUser['cover_photo']);
		$this->template()->assign(array(
				'bCanPoke' => $bCanSendPoke,
				'aCoverPhoto' => $aCoverPhoto,
				'aUser' => $aUser,
                'iConverPhotoPosition' => $aUser['cover_photo_top'],
				'sCoverDefaultUrl' => flavor()->active->default_photo('user_cover_default', true),
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
		(($sPlugin = Phpfox_Plugin::get('profile.component_block_pic_clean')) ? eval($sPlugin) : false);
	}
}