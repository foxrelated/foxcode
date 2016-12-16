<?php
defined('PHPFOX') or exit('NO DICE!');

class Admincp_Component_Controller_Product_Install extends Phpfox_Component {
    
	public function process() {
		$this->template()->setTitle(_p('installing'));
	}
}