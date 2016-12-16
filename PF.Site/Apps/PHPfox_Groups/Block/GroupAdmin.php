<?php

namespace Apps\PHPfox_Groups\Block;

use Core;
use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');
class GroupAdmin extends Phpfox_Component
{
    public function process()
    {
        if (!setting('pf_group_show_admins')) {
            return false;
        }

        $this->template()->assign([
                'sHeader'     => _p('Admins'),
                'aPageAdmins' => Core\Lib::appsGroup()->getPageAdmins(),
            ]
        );

        return 'block';
    }
}