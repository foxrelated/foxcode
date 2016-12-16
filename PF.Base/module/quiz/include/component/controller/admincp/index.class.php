<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * @author Neil <neil@phpfox.com>
 * Class Quiz_Component_Controller_Admincp_Index
 */
class Quiz_Component_Controller_Admincp_Index extends Phpfox_Component
{
    public function process()
    {
        $this->url()->send('admincp.setting.edit', ['module-id' => 'quiz']);
    }
}