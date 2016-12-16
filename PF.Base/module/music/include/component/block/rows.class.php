<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Music_Component_Block_Rows
 */
class Music_Component_Block_Rows extends Phpfox_Component {

	public function process() {
		if ($this_feed_id = $this->getParam('this_feed_id')) {
			$custom = $this->getParam('custom_param_' . $this_feed_id);
			$this->template()->assign([
				'aSong' => $custom
			]);
		}
	}
}