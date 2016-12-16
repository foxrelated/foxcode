<?php

namespace Apps\PHPfox_Groups\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
use Core;

defined('PHPFOX') or exit('NO DICE!');

class AllController extends Phpfox_Component
{

    public function process()
    {
        $aUser = $this->getParam('aUser');
        if (empty($aUser)) {
            $this->url()->send('groups');
        }
        $sExtraConds = (Phpfox::getUserParam('core.can_view_private_items') || $aUser['user_id'] == Phpfox::getUserId()) ? "" : " AND (p.reg_method <> 2)";
        list($iTotal, $aGroups) = Core\Lib::appsGroup()->getForProfile($aUser['user_id'], 0, false, $sExtraConds);
        if (!$iTotal) {
            return false;
        }
        $this->template()->assign([
            'aGroupsList' => $aGroups,
        ]);

        return null;
    }

    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_block_profile_clean')) ? eval($sPlugin) : false);
    }
}