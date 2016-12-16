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
 * @version 		$Id: type.class.php 2818 2011-08-09 12:01:57Z Raymond_Benc $
 */
class Pages_Service_Type_Type extends Phpfox_Pages_Type
{
    /**
     * @return Pages_Service_Facade
     */
    public function getFacade()
    {
        return Pages_Service_Facade::instance();
    }
}