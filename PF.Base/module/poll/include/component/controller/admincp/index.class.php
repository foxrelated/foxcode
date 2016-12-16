<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * @author Neil <neil@phpfox.com>
 * Class Poll_Component_Controller_Admincp_Index
 */
class Poll_Component_Controller_Admincp_Index extends Phpfox_Component
{
    public function process()
    {
        $this->url()->send('admincp.setting.edit', ['module-id' => 'poll']);
    }
}