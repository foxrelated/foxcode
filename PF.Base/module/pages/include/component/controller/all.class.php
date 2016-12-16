<?php

defined('PHPFOX') or exit('NO DICE!');

class Pages_Component_Controller_All extends Phpfox_Component
{

    public function process()
    {
        $aUser = $this->getParam('aUser');
        if (empty($aUser)) {
            $this->url()->send('pages');
        }
        list($iTotal, $aPages) = Pages_Service_Pages::instance()->getForProfile($aUser['user_id'], 0, false);
        if (!$iTotal) {
            return false;
        }
        $this->template()->assign([
                'aPagesList' => $aPages,
            ]);
        return null;
    }

    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('pages.component_block_profile_clean')) ? eval($sPlugin) : false);
    }
}