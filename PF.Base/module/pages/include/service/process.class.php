<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Service
 * @version 		$Id: process.class.php 7230 2014-03-26 21:14:12Z Fern $
 */
class Pages_Service_Process extends Phpfox_Pages_Process
{
    /**
     * @return Pages_Service_Facade
     */
    public function getFacade()
    {
        return Pages_Service_Facade::instance();
    }
}
