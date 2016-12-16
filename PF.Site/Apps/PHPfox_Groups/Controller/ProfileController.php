<?php

namespace Apps\PHPfox_Groups\Controller;

use Phpfox;
use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');

class ProfileController extends Phpfox_Component
{
    public function process()
    {
        $this->setParam('bIsProfile', true);

        Phpfox::getComponent('groups.index', ['bNoTemplate' => true], 'controller');
    }
}