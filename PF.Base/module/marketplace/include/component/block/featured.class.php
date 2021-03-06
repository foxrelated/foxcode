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
 * @version 		$Id: featured.class.php 3588 2011-11-28 08:28:21Z Raymond_Benc $
 */
class Marketplace_Component_Block_Featured extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aFeatured = Marketplace_Service_Marketplace::instance()->getFeatured();
		
		if (!count($aFeatured))
		{
			return false;
		}
		
		$this->template()->assign(array(
				'sHeader' => _p('featured_listings'),
				'aFeatured' => $aFeatured
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
		(($sPlugin = Phpfox_Plugin::get('marketplace.component_block_featured_clean')) ? eval($sPlugin) : false);
	}
}