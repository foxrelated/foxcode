<?php

namespace Apps\PHPfox_Groups\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
use Core;

defined('PHPFOX') or exit('NO DICE!');

class GroupProfile extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aUser = $this->getParam('aUser');

        $sExtraConds = (Phpfox::getUserParam('core.can_view_private_items') || $aUser['user_id'] == Phpfox::getUserId()) ? "" : " AND (p.reg_method <> 2)";

        list($iTotal, $aPages) = Core\Lib::appsGroup()->getForProfile($aUser['user_id'], 10, false, $sExtraConds);

        if (!$iTotal) {
            return false;
        }
        
        $this->template()->assign([
                'sHeader'    => '<a href="' . $this->url()->makeUrl($aUser['user_name'], 'groups/?view=all') . '" title="' . _p('Groups you created and joined') . '">' . _p('Joined groups') . '<span>' . $iTotal . '</span></a>',
                'aPagesList' => $aPages,
            ]
        );

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_block_profile_clean')) ? eval($sPlugin) : false);
    }
}