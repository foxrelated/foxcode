<?php

namespace Apps\PHPfox_Groups\Block;

use Core;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class GroupMembers extends Phpfox_Component
{
    public function process()
    {
        $aPage = $this->getParam('aPage');
        list($iTotalMembers, $aMembers) = Core\Lib::appsGroup()->getMembers($aPage['page_id'], 12);

        $this->template()->assign([
                'sHeader'  => '<a href="#" onclick="return $Core.box(\'like.browse\', 400, \'type_id=groups&amp;item_id=' . $aPage['page_id'] . '' . ($aPage['page_type'] != '1' ? '&amp;force_like=1' : '') . '&amp;block_title=' . _p('Members') . '\');">' . ( _p('Members')) . '<span>' . $iTotalMembers . '</span>' . '</a>',
                'aMembers' => $aMembers,
            ]
        );

        if (!PHPFOX_IS_AJAX || defined("PHPFOX_IN_DESIGN_MODE")) {
            return 'block';
        }
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_block_like_clean')) ? eval($sPlugin) : false);
    }
}