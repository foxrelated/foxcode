<?php
namespace Apps\PHPfox_Groups\Service;

use Phpfox;
use Phpfox_Pages_Process;

/**
 * Class Browse
 *
 * @package Apps\PHPfox_Groups\Service
 */
class Process extends Phpfox_Pages_Process
{
    public function getFacade()
    {
        return Phpfox::getService('groups.facade');
    }
}