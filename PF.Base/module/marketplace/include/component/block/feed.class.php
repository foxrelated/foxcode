<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Marketplace_Component_Block_Feed
 */
class Marketplace_Component_Block_Feed extends Phpfox_Component {

	public function process() {
		$iFeedId = $this->getParam('this_feed_id');
		if ($iFeedId) {
			$aFeed = Feed_Service_Feed::instance()->getFeed($iFeedId);
			if (!$aFeed || ($aFeed['type_id'] != 'marketplace')) return false;
			$aRow =  Phpfox_Database::instance()->select('e.*')
				->from(Phpfox::getT('marketplace'), 'e')
				->where('e.listing_id = ' . (int) $aFeed['item_id'])
				->execute('getSlaveRow');

			if (!$aRow) return false;

			$aRow['is_in_feed'] = true;
			$aRow['url'] = Phpfox::permalink('marketplace', $aRow['listing_id'], $aRow['title']);

            $aRow['categories'] = Marketplace_Service_Category_Category::instance()->getCategoriesById($aRow['listing_id']);
			$this->template()->assign('aListing', $aRow);

		}

	}
}