<?php
namespace Apps\PHPfox_Groups\Service;

use Phpfox;
use Phpfox_Pages_Api;

/**
 * Class Api
 *
 * @package Apps\PHPfox_Groups\Service
 */
class Api extends Phpfox_Pages_Api
{
    public function getFacade()
    {
        return Phpfox::getService('groups.facade');
    }
}