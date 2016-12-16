<?php

defined('PHPFOX') or exit('NO DICE!');

class Pages_Component_Block_Cropme extends Phpfox_Component
{
    public function process() {
        $iPage = $this->request()->get('id');
        $aPage = Pages_Service_Pages::instance()->getForEdit($iPage);
        $this->template()->assign([
           'aPageCropMe' => $aPage
        ]);
    }
}