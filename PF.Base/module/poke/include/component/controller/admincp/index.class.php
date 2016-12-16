<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * @author Neil <neil@phpfox.com>
 * Class Poke_Component_Controller_Admincp_Index
 */
class Poke_Component_Controller_Admincp_Index extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $this->url()->send('admincp.setting.edit', ['module-id' => 'poke']);
    }
}