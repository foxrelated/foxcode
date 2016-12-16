<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Raymond_Benc
 * @package          Phpfox_Service
 * @version          $Id: category.class.php 5099 2013-01-07 19:01:38Z Raymond_Benc $
 */
class Pages_Service_Category_Category extends Phpfox_Pages_Category
{
    /**
     * @return Pages_Service_Facade
     */
    public function getFacade()
    {
        return Pages_Service_Facade::instance();
    }
}