<?php
namespace Apps\PHPfox_Groups\Service;

use Phpfox;
use Phpfox_Pages_Category;

/**
 * Class Category
 *
 * @package Apps\PHPfox_Groups\Service
 */
class Category extends Phpfox_Pages_Category
{
    public function getFacade()
    {
        return Phpfox::getService('groups.facade');
    }
}