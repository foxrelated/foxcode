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
 * @version 		$Id: my.class.php 3444 2011-11-03 12:56:50Z Raymond_Benc $
 */
class Marketplace_Component_Block_My extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aListing = $this->getParam('aListing');
		
		list($iCnt, $aListings) = Marketplace_Service_Marketplace::instance()->getUserListings($aListing['listing_id'], $aListing['user_id']);
        
        if (!$iCnt) {
            return false;
        }
		
		$this->template()->assign(array(
				'sHeader' => _p('more_from_seller'),
				'aMyListings' => $aListings
			)
		);
		
		if ($iCnt > Phpfox::getParam('marketplace.total_listing_more_from'))
		{
			$this->template()->assign(array(
					'aFooter' => array(
						_p('view_more') => $this->url()->makeUrl($aListing['user_name'], array('marketplace'))
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
		(($sPlugin = Phpfox_Plugin::get('marketplace.component_block_my_clean')) ? eval($sPlugin) : false);
	}
}