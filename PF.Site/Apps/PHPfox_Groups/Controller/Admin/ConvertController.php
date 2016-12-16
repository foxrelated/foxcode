<?php

namespace Apps\PHPfox_Groups\Controller\Admin;

use Phpfox;
use Admincp_Component_Controller_App_Index;
use Phpfox_Queue;
use Core;

defined('PHPFOX') or exit('NO DICE!');

class ConvertController extends Admincp_Component_Controller_App_Index
{
    public function process() {
        parent::process();
        $iNumberGroups = Core\Lib::appsGroup()->getCountConvertibleGroups();
        $iConvert = $this->request()->getInt('convert');
        if ($iNumberGroups && $iConvert) {
            $iCron = $this->request()->getInt('cron');
            //Map old groups to new groups
            if ($iCron){
                Phpfox_Queue::instance()->addJob('groups_convert_old_group', []);
            } else {
                (new \Apps\PHPfox_Groups\Job\ConvertOldGroups(0, 0, '',[]))
                    ->perform();
            }
            Phpfox::addMessage(_p('Your job is running. You will receive notice when it done'));
            storage()->set('phpfox_job_queue_convert_group_run', Phpfox::getUserId());
            $this->url()->send('admincp.app', [
                'id' => 'PHPfox_Groups'
            ]);
        }
        $store_data = storage()->get('phpfox_job_queue_convert_group_run');
        $iConvertedUserId = isset($store_data->value) ? $store_data->value : 0;
        $this->template()
            ->assign([
            'iNumberGroups' => $iNumberGroups,
            'iConvertedUserId' => $iConvertedUserId
        ]);
    }
}