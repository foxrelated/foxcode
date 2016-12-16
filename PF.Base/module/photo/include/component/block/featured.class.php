<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Used to display a featured image and is setup to refresh X number of milliseconds.
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: featured.class.php 3469 2011-11-07 16:51:48Z Raymond_Benc $
 */
class Photo_Component_Block_Featured extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (defined('PHPFOX_IS_GROUP_VIEW') || defined('PHPFOX_IS_PAGES_VIEW') || defined('PHPFOX_IS_USER_PROFILE'))
		{
			return false;
		}
		
		// Get the featured random image
		list($iTotalImages, $aFeatured) = Photo_Service_Photo::instance()->getFeatured();
				
		// If not images were featured lets get out of here
		if (!count($aFeatured))
		{
			return false;
		}
		
		$aFeaturedImage = $aFeatured[rand(0, (count($iTotalImages) - 1))];
		
		// If this is not AJAX lets display the block header, footer etc...
		if (!PHPFOX_IS_AJAX)
		{
			$this->template()->assign(array(
					'sHeader' => _p('featured_photo'),
					'sBlockJsId' => 'featured_photo'
					
				)
			);	
		}
		
		// Assign template vars
		$this->template()->assign(array(				
				'aFeaturedImage' => $aFeaturedImage,
				'iRefreshTime' => Photo_Service_Photo::instance()->getFeaturedRefreshTime()
			)
		);	
		
		if (Phpfox::getUserParam('photo.can_feature_photo'))
		{
			$this->template()->assign(array(
					'aFooter' => array(_p('unfeature') => $this->url()->makeUrl('photo', array('unfeature' => $aFeaturedImage['photo_id'])))
				)
			);
		}
		
		return 'block';
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('photo.component_block_featured_clean')) ? eval($sPlugin) : false);
	}
}