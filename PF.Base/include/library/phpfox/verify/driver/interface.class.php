<?php

defined('PHPFOX') or exit('NO DICE!');

interface Phpfox_Verify_Driver_Interface
{
    /**
     * @param $to
     * @param $msg
     * @return bool
     */
    public function sendSMS($to, $msg);
}