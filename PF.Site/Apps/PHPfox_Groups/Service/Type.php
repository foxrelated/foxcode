<?php
namespace Apps\PHPfox_Groups\Service;

use Phpfox;
use Phpfox_Pages_Type;

/**
 * Class Groups
 *
 * @package Apps\PHPfox_Groups\Service
 */
class Type extends Phpfox_Pages_Type
{
    public function getFacade()
    {
        return Phpfox::getService('groups.facade');
    }
}