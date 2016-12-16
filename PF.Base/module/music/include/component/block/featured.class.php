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
 * @version 		$Id: featured.class.php 3542 2011-11-22 11:33:05Z Raymond_Benc $
 */
class Music_Component_Block_Featured extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aFeatured = Music_Service_Music::instance()->getFeaturedSongs();
		
		if (!is_array($aFeatured))
		{
			return false;
		}
		
		if (!count($aFeatured))
		{
			return false;
		}
		
		$this->template()->assign(array(
				'sHeader' => _p('featured_songs'),
				'aFeaturedSongs' => $aFeatured
			)
		);
		
		if (count($aFeatured) == 5)
		{
			$this->template()->assign(array(
					'aFooter' => array(
						_p('view_more') => $this->url()->makeUrl('music', array('view' => 'featured'))
					)
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
		(($sPlugin = Phpfox_Plugin::get('music.component_block_featured_clean')) ? eval($sPlugin) : false);
	}
}