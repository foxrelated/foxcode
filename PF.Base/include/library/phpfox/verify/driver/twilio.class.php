<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * get abstract class
 */
Phpfox::getLibClass('phpfox.verify.driver.abstract');


class Phpfox_Verify_Driver_Twilio extends Phpfox_Verify_Driver_Abstract
{

    /**
     *
     */
    const BASE_URL = 'https://api.twilio.com/2010-04-01/';


    /**
     * @link https://www.twilio.com
     *
     * @param $to
     * @param $msg
     * @return bool
     */
    public function sendSMS($to, $msg)
    {

        $accountId = Phpfox::getParam('twilio_account_id');
        $authToken = Phpfox::getParam('twilio_auth_token');
        $from = Phpfox::getParam('core.twilio_phone_number');
        $userpwd = sprintf('%s:%s', $accountId, $authToken);

        $endpointUrl = self::BASE_URL . 'Accounts/' . $accountId . '/Messages.json';
        $postFields = http_build_query([
            'To' => '+' . trim($to, '+'),
            'From' => '+' . trim($from, '+'),
            'Body' => $msg,
        ]);

        $ch = curl_init($endpointUrl);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_USERPWD, $userpwd);

        $response = curl_exec($ch);

        if (empty($response) || curl_error($ch)) {
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        return true;
    }
}