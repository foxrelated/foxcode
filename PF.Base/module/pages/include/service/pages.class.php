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
 * @version 		$Id: pages.class.php 7234 2014-03-27 14:40:29Z Fern $
 */
class Pages_Service_Pages extends Phpfox_Pages_Pages
{
    /**
     * @return Pages_Service_Facade
     */
    public function getFacade()
    {
        return Pages_Service_Facade::instance();
    }
}
