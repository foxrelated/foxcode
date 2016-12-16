<?php
namespace Apps\PHPfox_Groups\Service;

use Phpfox;
use Phpfox_Pages_Pages;
use Phpfox_Url;
use Core;

/**
 * Class Groups
 *
 * @package Apps\PHPfox_Groups\Service
 */
class Groups extends Phpfox_Pages_Pages
{
    public function getFacade()
    {
        return Phpfox::getService('groups.facade');
    }

    public function getUrl($iPageId, $sTitle = null, $sVanityUrl = null, $bIsGroup = false)
    {
        if ($sTitle === null && $sVanityUrl === null)
        {
            $aPage = $this->getPage($iPageId);
            $sTitle = $aPage['title'];
            $sVanityUrl = $aPage['vanity_url'];
        }

        if (!empty($sVanityUrl))
        {
            return Phpfox_Url::instance()->makeUrl($sVanityUrl);
        }

        return Phpfox_Url::instance()->makeUrl('groups', $iPageId);
    }
    /**
     * @param int|array $iPage
     * @param string $sPerm
     *
     * @return bool
     */
    public function hasPerm($iPage, $sPerm){
        if (Phpfox::isAdmin()){
            return true;
        }
        if (defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getUserParam('core.can_view_private_items')) {
            return true;
        }

        if (defined('PHPFOX_POSTING_AS_PAGE')) {
            return true;
        }


        if (is_array($iPage) && isset($iPage['page_id']))
        {
            $aPage = $iPage;
        } else {
            $aPage = $this->getPage($iPage);
        }
        $aPerms = Core\Lib::appsGroup()->getPermsForPage($aPage['page_id']);
        if (isset($aPerms[$sPerm])) {
            switch ((int) $aPerms[$sPerm]) {
                case 1:
                    if (!$this->isMember($aPage['page_id'])) {
                        return false;
                    }
                    break;
                case 2:
                    if (!$this->isAdmin($aPage['page_id'])) {
                        return false;
                    }
                    break;
            }
        }
        //If don't set in Permission list, Use groups permission
        if ($aPage['reg_method'] == 0 || $this->isMember($aPage['page_id'])){
            return true;
        } else {
            return false;
        }
    }

    public function getCountConvertibleGroups() {
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(':pages', 'p')
            ->join(':pages_category', 'pc', 'p.category_id=pc.category_id')
            ->where('pc.page_type=1')
            ->execute('getSlaveField');
        return $iCnt;
    }
    public function convertOldGroups(){
        //each time run in 300 seconds or 1000 groups
        $start = time();

        //Map old groups Category to new
        $aCategories = $this->database()->select('*')
            ->from(':pages_category')
            ->where('page_type=1')
            ->execute('getRows');
        foreach ($aCategories as $aCategory){
            $aTypeInsert = [
              'is_active' =>  1,
              'item_type' => 1,//1 mean groups
              'name' => $aCategory['name'],
              'time_stamp' => PHPFOX_TIME,
              'ordering' => $aCategory['ordering'],
            ];
            $this->database()->insert(':pages_type', $aTypeInsert);
        }
        //Get 1000 old groups
        $aOldGroups = $this->database()->select('p.page_id, p.category_id')
            ->from(':pages', 'p')
            ->join(':pages_category', 'pc', 'p.category_id=pc.category_id')
            ->where('pc.page_type=1')
            ->limit(1000)
            ->execute('getSlaveRows');
        $group_type_id = $this->database()->select('type_id')
            ->from(':pages_type')
            ->where('item_type=1')
            ->execute('getSlaveField');
        foreach ($aOldGroups as $aGroup){
            //Get new groups type
            $new_groups_type_id = $this->database()->select('pt.type_id')
                ->from(':pages_type', 'pt')
                ->join(':pages_category', 'pc', 'pc.name=pt.name')
                ->where('pc.category_id=' . (int) $aGroup['category_id'] .' AND pt.item_type=1')
                ->execute('getSlaveField');
            $group_type_id = ($new_groups_type_id > 0) ? $new_groups_type_id : $group_type_id;
            $this->database()->update(':pages', [
                'type_id' => $group_type_id,
                'category_id' => 0,//We do not have default groups category
                'item_type' => 1
            ], 'page_id=' . (int) $aGroup['page_id']);
            //Update blog data
            $this->database()->update(':blog', [
                'module_id' => 'groups'
            ], 'item_id=' . (int) $aGroup['page_id']);

            //Update event data
            $this->database()->update(':event', [
                'module_id' => 'groups'
            ], 'item_id=' . (int) $aGroup['page_id']);

            //Forum: do nothing

            //Update music album
            $this->database()->update(':music_album', [
                'module_id' => 'groups'
            ], 'item_id=' . (int) $aGroup['page_id']);
            //Update music song
            $this->database()->update(':music_song', [
                'module_id' => 'groups'
            ], 'item_id=' . (int) $aGroup['page_id']);

            //Update photo
            $this->database()->update(':photo', [
                'module_id' => 'groups'
            ], 'group_id=' . (int) $aGroup['page_id']);
            //Update photo album
            $this->database()->update(':photo_album', [
                'module_id' => 'groups'
            ], 'group_id=' . (int) $aGroup['page_id']);

            //Update groups comment
            $this->database()->update(':pages_feed', [
                'type_id' => 'groups_comment'
            ], 'type_id="pages_comment" AND parent_user_id=' . (int) $aGroup['page_id']);

            //Update comments on groups
            $this->database()->update(':comment', [
                'type_id' => 'groups'
            ], 'type_id="pages" AND item_id=' . (int) $aGroup['page_id']);

            //Update likes on groups
            db()->update(Phpfox::getT('like'), ['type_id' => 'REPLACE(type_id, \'pages\', \'groups\')'], 'type_id LIKE \'pages%\' AND item_id=' . (int) $aGroup['page_id'], false);
            //Video not yet integrate with pages on 4.2.2

            //Update link data
            $this->database()->update(':link', [
                'module_id' => 'groups'
            ], 'item_id=' . (int) $aGroup['page_id']);

            //Update Home Feed
            db()->update(Phpfox::getT('feed'), ['type_id' => 'REPLACE(type_id, \'pages\', \'groups\')'], 'type_id LIKE \'pages%\' AND item_id=' . (int) $aGroup['page_id'], false);

            //Update Notification
            db()->update(Phpfox::getT('notification'), ['type_id' => 'REPLACE(type_id, \'pages\', \'groups\')'], 'type_id LIKE \'pages%\' AND item_id=' . (int) $aGroup['page_id'], false);
            //----------------------//
            //End process convert
            $end = time();
            if (($end - $start) >= 300){
                break;
            }
        }
    }
}