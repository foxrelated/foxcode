<?php
defined('PHPFOX') or exit('NO DICE!');

class Feed_Component_Block_Edit_User_Status extends Phpfox_Component
{
    public function process(){
        $iFeedId = $this->request()->get('id');
        $aFeedCallback = [];
        if ($module = $this->request()->get('module')) {
            $aFeedCallback = [
                'module' => $this->request()->get('module'),
                'table_prefix' => $this->request()->get('module') . '_',
                'item_id' => $this->request()->get('item_id')
            ];
        }

        $aFeed = Feed_Service_Feed::instance()->getUserStatusFeed($aFeedCallback, $iFeedId);
        if (!$aFeed) {
            return false;
        }
        //Check type_id is user_status
        if ($aFeed['type_id'] != "user_status") {
            return false;
        }
        //Check have permission to edit user status
        if (!((Phpfox::getUserParam('feed.can_edit_own_user_status') && $aFeed['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('feed.can_edit_other_user_status'))){
            return false;
        }
        $bLoadCheckIn = false;
        if ((!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE'))) && !defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getParam('feed.enable_check_in') && (Phpfox::getParam('core.ip_infodb_api_key') || Phpfox::getParam('core.google_api_key') ) ) {
            $bLoadCheckIn = true;
        }
        $this->template()->assign([
           'iFeedId' => $iFeedId,
           'bLoadCheckIn' => $bLoadCheckIn,
           'aForms' => $aFeed
        ]);
        return null;
    }
}