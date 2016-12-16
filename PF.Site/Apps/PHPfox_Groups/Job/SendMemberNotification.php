<?php
namespace Apps\PHPfox_Groups\Job;

use Core\Queue\JobAbstract;
use Notification_Service_Process;
use User_Service_Auth;
use Core;

/**
 * Class SendMemberNotification
 *
 * @package Apps\PHPfox_Groups\Job
 */
class  SendMemberNotification extends JobAbstract
{
    /**
     * @inheritdoc
     */
    public function perform()
    {
        $aParams = $this->getParams();
        if (isset($aParams['owner_id'])) {
            User_Service_Auth::instance()->getUserId();
        }
        $aGroupPerms = Core\Lib::appsGroup()->getPermsForPage($aParams['page_id']);
        $iPerm = isset($aGroupPerms[ $aParams['item_perm'] ]) ? $aGroupPerms[ $aParams['item_perm'] ] : 0;
        if ($iPerm == 2) {
            $aUsers = Core\Lib::appsGroup()->getPageAdmins($aParams['page_id']);
        } else {
            list($iCount, $aUsers) = Core\Lib::appsGroup()->getMembers($aParams['page_id']);
        }

        foreach ($aUsers as $aUser) {
            if (isset($aParams['owner_id']) && ($aUser['user_id'] == $aParams['owner_id'])) {
                continue;
            }
            Notification_Service_Process::instance()->add($aParams['item_type'] . '_newItem_groups', $aParams['item_id'], $aUser['user_id'], $aParams['owner_id']);
        }

        $this->delete();
    }
}