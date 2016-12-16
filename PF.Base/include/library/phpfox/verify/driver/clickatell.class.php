<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * get abstract class
 */
Phpfox::getLibClass('phpfox.verify.driver.abstract');


class Phpfox_Verify_Driver_Clickatell extends Phpfox_Verify_Driver_Abstract
{
    /**
     *
     */
    const BASE_URL = 'http://api.clickatell.com/';


    /**
     * @link https://www.twilio.com
     *
     * <code>
     * Phpfox::getLib('phpfox.verify')->sendSMS('+841637514924', 'test message from server');
     * </code>
     * @param $to
     * @param $msg
     * @return bool
     */
    public function sendSMS($to, $msg)
    {

        $username = Phpfox::getParam('core.clickatell_username');
        $password = Phpfox::getParam('core.clickatell_password');
        $appId = Phpfox::getParam('core.clickatell_app_id');

        $endpointUrl = self::BASE_URL . 'http/sendmsg';

        $postFields = http_build_query([
            'api_id' => $appId,
            'to' => trim($to, '+'),
            'user' => $username,
            'password' => $password,
            'text' => $msg,
        ]);

        $ch = curl_init($endpointUrl);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $response = curl_exec($ch);

        if (empty($response) || curl_error($ch)) {
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        return true;
    }
}