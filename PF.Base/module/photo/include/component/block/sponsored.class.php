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
 * @version 		$Id: sponsored.class.php 3214 2011-09-30 12:05:14Z Raymond_Benc $
 */
class Photo_Component_Block_Sponsored extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
		if (!Phpfox::isModule('ad'))
		{
			return false;
		}
    	
    	if (defined('PHPFOX_IS_GROUP_VIEW') || defined('PHPFOX_IS_PAGES_VIEW') || defined('PHPFOX_IS_USER_PROFILE'))
		{
		    return false;
		}
		
		$aSponsorPhoto = Photo_Service_Photo::instance()->getRandomSponsored();
		
		if (empty($aSponsorPhoto))
		{
		    return false;
		}
		
		Phpfox::getService('ad.process')->addSponsorViewsCount($aSponsorPhoto['sponsor_id'], 'photo');
		
		$aSponsorPhoto['details'] = array(
			_p('submitted') => Phpfox::getTime(Phpfox::getParam('photo.photo_image_details_time_stamp'), $aSponsorPhoto['time_stamp']),
			_p('file_size') => Phpfox_File::instance()->filesize($aSponsorPhoto['file_size']),
			_p('resolution') => $aSponsorPhoto['width'] . '×' . $aSponsorPhoto['height'],
			_p('views') => $aSponsorPhoto['total_view']
		);
		
		$this->template()->assign(array(
				'aSponsorPhoto' => $aSponsorPhoto,
				'sHeader' => _p('sponsored_photo'),
				'aFooter' => array(_p('encourage_sponsor') => $this->url()->makeUrl('photo', array('view' => 'my')))
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
	(($sPlugin = Phpfox_Plugin::get('photo.component_block_featured_clean')) ? eval($sPlugin) : false);
    }
}