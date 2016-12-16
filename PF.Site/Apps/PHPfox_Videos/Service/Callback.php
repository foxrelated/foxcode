<?php

namespace Apps\Phpfox_Videos\Service;

use Phpfox_Service;
use User_Service_User;
use Pages_Service_Pages;
use Core;
use Notification_Service_Notification;

class Callback extends Phpfox_Service
{

    public function canShareItemOnFeed()
    {
    }

    public function getActivityFeed($aItem, $aCallback = null, $bIsChildItem = false)
    {
        \Feed_Service_Feed::instance()->callback([]);
        $aReturn = \Feed_Service_Feed::instance()->get(['id' => $aItem['feed_id'], 'bIsChildren' => $bIsChildItem]);
        if (empty($aReturn)) return false;
        $aReturn = $aReturn[0];
        $aItem = null;
        if ($bIsChildItem) {
            $aUserInfo = User_Service_User::instance()->getUserFields(true, $aItem, null, $aReturn['user_id']);
            array_merge($aReturn, $aUserInfo);
        }
        return $aReturn;
    }

    public function getActivityFeedPages($aItem, $aCallback = null, $bIsChildItem = false)
    {
        $aCallback = storage()->get('feed_callback_' . $aItem['feed_id']);
        if (!$aCallback) return false;
        if ($aCallback->id && $aCallback->value) {
            \Feed_Service_Feed::instance()->callback((array) $aCallback->value);
        }
        $aReturn = \Feed_Service_Feed::instance()->get(['id' => $aItem['feed_id']]);
        if (empty($aReturn)) return false;
        $aReturn = $aReturn[0];
        if ($aCallback && $aCallback->value && $aCallback->value->table_prefix == 'pages_') {
            $aPage = $this->database()->select('p.*, pu.vanity_url, ' . \Phpfox::getUserField('u', 'parent_'))
                ->from(':pages', 'p')
                ->join(':user', 'u', 'p.page_id=u.profile_page_id')
                ->leftJoin(\Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
                ->where('p.page_id=' . (int) $aReturn['parent_user_id'])
                ->execute('getRow');

            $aReturn['feed_link'] = url('v/play/p-' . $aItem['feed_id']);
            $aReturn['parent_user_name'] = \Phpfox::getService($aCallback->value->module)->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
            if ($aReturn['user_id'] != $aPage['parent_user_id']){
                $aReturn['parent_user'] = User_Service_User::instance()->getUserFields(true, $aPage, 'parent_');
                unset($aReturn['feed_info']);
            }
        }
        return $aReturn;
    }

    public function getPagePerms()
    {
        $aPerms = [];

        $aPerms['pf_video.share_videos'] = _p('Who can share videos?');
        $aPerms['pf_video.view_browse_videos'] = _p('Who can view videos?');

        return $aPerms;
    }

    public function getGroupPerms()
    {
        $aPerms = [
            'pf_video.share_videos' => _p('Who can share videos?')
        ];

        return $aPerms;
    }

    public function canViewPageSection($iPage)
    {
        if (!Pages_Service_Pages::instance()->hasPerm($iPage, 'pf_video.view_browse_videos')) {
            return false;
        }

        return true;
    }

    public function canViewGroupSection($iPage)
    {
        if (!Core\Lib::appsGroup()->hasPerm($iPage, 'pf_video.view_browse_videos')) {
            return false;
        }

        return true;
    }

    public function getNotificationNewItem_Groups($aNotification)
    {
        if (!\Phpfox::isModule('groups')) return false;
        $aItem = (new \Api\Feed())->get($aNotification['item_id'], 'PHPfox_Videos', true);
        if (empty($aItem) || !($aItem->module_id) || $aItem->module_id != 'groups')
        {
            return false;
        }

        $aRow = Core\Lib::appsGroup()->getPage($aItem->module_item_id);

        if (!isset($aRow['page_id']))
        {
            return false;
        }

        $sPhrase = _p('{{ users }} add a new video in the group "{{ title }}"', array('users' => Notification_Service_Notification::instance()->getUsers($aNotification), 'title' => \Phpfox::getLib('parse.output')->shorten($aRow['title'], \Phpfox::getParam('notification.total_notification_title_length'), '...')));

        return array(
            'link' => url('/v/play/p-' . $aNotification['item_id']),
            'message' => $sPhrase,
            'icon' => \Phpfox_Template::instance()->getStyle('image', 'activity.png', 'video')
        );
    }

    public function getSearchTitleInfo(){
        return array(
            'name' => _p('Videos')
        );
    }
}