<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        Raymond Benc
 * @package        Phpfox_Service
 * @version        $Id: marketplace.class.php 7059 2014-01-22 14:20:10Z Fern $
 */
class Marketplace_Service_Marketplace extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('marketplace');
    }

    public function getListing($iId)
    {
        (($sPlugin = Phpfox_Plugin::get('marketplace.service_marketplace_getlisting')) ? eval($sPlugin) : false);

        if (Phpfox::isModule('like')) {
            $this->database()->select('lik.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'lik', 'lik.type_id = \'marketplace\' AND lik.item_id = l.listing_id AND lik.user_id = ' . Phpfox::getUserId());
        }

        $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = l.user_id AND f.friend_user_id = " . Phpfox::getUserId());

        $aListing = $this->database()->select(Phpfox::getUserField() . ', l.*, ml.invite_id, ml.visited_id, uf.total_score, uf.total_rating, ua.activity_points, ' . (Phpfox::getParam('core.allow_html') ? 'mt.description_parsed' : 'mt.description') . ' AS description')
            ->from($this->_sTable, 'l')
            ->join(Phpfox::getT('marketplace_text'), 'mt', 'mt.listing_id = l.listing_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->join(Phpfox::getT('user_field'), 'uf', 'uf.user_id = l.user_id')
            ->join(Phpfox::getT('user_activity'), 'ua', 'ua.user_id = l.user_id')
            ->leftJoin(Phpfox::getT('marketplace_invite'), 'ml', 'ml.listing_id = l.listing_id AND ml.invited_user_id = ' . Phpfox::getUserId())
            ->where('l.listing_id = ' . (int)$iId)
            ->execute('getSlaveRow');
        if (!isset($aListing['listing_id'])) {
            return false;
        }
        if (!Phpfox::isModule('like')) {
            $aListing['is_liked'] = false;
        }
        if ($aListing['view_id'] == '1') {
            if ($aListing['user_id'] == Phpfox::getUserId() || Phpfox::getUserParam('marketplace.can_approve_listings')) {

            } else {
                return false;
            }
        }

        $aListing['categories'] = Marketplace_Service_Category_Category::instance()->getCategoriesById($aListing['listing_id']);
        $aListing['bookmark_url'] = Phpfox_Url::instance()->permalink('marketplace', $aListing['listing_id'], $aListing['title']);

        return $aListing;
    }

    public function getForEdit($iId, $bForce = false)
    {
        (($sPlugin = Phpfox_Plugin::get('marketplace.service_marketplace_getforedit')) ? eval($sPlugin) : false);

        $aListing = $this->database()->select('l.*, description')
            ->from($this->_sTable, 'l')
            ->join(Phpfox::getT('marketplace_text'), 'mt', 'mt.listing_id = l.listing_id')
            ->where('l.listing_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if ((($aListing['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('marketplace.can_edit_own_listing')) || Phpfox::getUserParam('marketplace.can_edit_other_listing')) || ($bForce === true)) {
            $aListing['categories'] = Marketplace_Service_Category_Category::instance()->getCategoryIds($aListing['listing_id']);

            return $aListing;
        }

        return false;
    }

    public function getInvoice($iId)
    {
        $aInvoice = $this->database()->select('mi.*, m.title, m.user_id AS marketplace_user_id, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('marketplace_invoice'), 'mi')
            ->join(Phpfox::getT('marketplace'), 'm', 'm.listing_id = mi.listing_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = mi.user_id')
            ->where('mi.invoice_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        return (isset($aInvoice['invoice_id']) ? $aInvoice : false);
    }

    public function getInvoices($aCond, $bGroupUser = false)
    {
        if ($bGroupUser) {
            $this->database()->group('mi.user_id');
        }

        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('marketplace_invoice'), 'mi')
            ->where($aCond)
            ->execute('getSlaveField');

        if ($bGroupUser) {
            $this->database()->group('mi.user_id');
        }

        $aRows = $this->database()->select('mi.*, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('marketplace_invoice'), 'mi')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = mi.user_id')
            ->where($aCond)
            ->execute('getSlaveRows');

        foreach ($aRows as $iKey => $aRow) {
            switch ($aRow['status']) {
                case 'completed':
                    $aRows[$iKey]['status_phrase'] = _p('paid');
                    break;
                case 'cancel':
                    $aRows[$iKey]['status_phrase'] = _p('cancelled');
                    break;
                case 'pending':
                    $aRows[$iKey]['status_phrase'] = _p('pending_payment');
                    break;
                default:
                    $aRows[$iKey]['status_phrase'] = _p('pending_payment');
                    break;
            }
        }

        return array($iCnt, $aRows);
    }

    public function getForProfileBlock($iUserId, $iLimit = 5)
    {
        (($sPlugin = Phpfox_Plugin::get('marketplace.service_marketplace_getforprofileblock')) ? eval($sPlugin) : false);

        return $this->database()->select('m.*')
            ->from($this->_sTable, 'm')
            ->where('m.view_id = 0 AND m.group_id = 0 AND m.user_id = ' . (int)$iUserId)
            ->limit($iLimit)
            ->order('m.time_stamp DESC')
            ->execute('getSlaveRows');
    }

    public function getImages($iId, $iLimit = null, $exclude_image_path = null)
    {
        $select = $this->database()->select('image_id, image_path, server_id')
            ->from(Phpfox::getT('marketplace_image'))
            ->order('ordering ASC')
            ->limit($iLimit);

        if (!empty($exclude_image_path)) {
            $exclude_image_path =  $this->database()->escape($exclude_image_path);
            $select->where('listing_id = ' . (int)$iId . " AND image_path <> '$exclude_image_path'");
        }else{
            $select->where('listing_id = ' . (int)$iId);
        }

        return $select->execute('getSlaveRows');
    }

    /**
     * @param  int $iId
     *
     * @return int
     */
    public function countImages($iId)
    {
        return $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('marketplace_image'))
            ->where('listing_id = ' . (int)$iId)
            ->order('ordering ASC')
            ->execute('getSlaveField');
    }

    public function getSponsorListings()
    {
        $sCacheId = $this->cache()->set('marketplace_sponsored');

        if (!($aListing = $this->cache()->get($sCacheId))) {
            $iExpireTime = (PHPFOX_TIME - (Phpfox::getParam('marketplace.days_to_expire_listing') * 86400));
            $where = 'm.view_id = 0 AND m.group_id = 0 AND m.is_sponsor = 1 AND s.module_id = \'marketplace\' AND s.is_active = 1';
            if (Phpfox::getParam('marketplace.days_to_expire_listing') > 0) {
                $where .= ' AND m.time_stamp >= ' . $iExpireTime;
            }
            $aListing = $this->database()->select('s.sponsor_id, m.title, m.currency_id, m.price, m.time_stamp, m.image_path, m.server_id')
                ->from($this->_sTable, 'm')
                ->join(Phpfox::getT('ad_sponsor'), 's', 's.item_id = m.listing_id')
                ->where($where)
                ->execute('getSlaveRows');

            $this->cache()->save($sCacheId, $aListing);
        }

        if ($aListing === true || (is_array($aListing) && !count($aListing))) {
            return array();
        }

        shuffle($aListing);

        $aOut = array();
        for ($iLoop = 0; $iLoop < Phpfox::getParam('marketplace.how_many_sponsored_listings'); $iLoop++) {
            if (empty($aListing)) {
                break;
            }
            $aOut[] = array_pop($aListing);
        }
        return $aOut;
    }

    public function getInvites($iListing, $iType, $iPage = 0, $iPageSize = 8)
    {
        $aInvites = array();
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('marketplace_invite'))
            ->where('listing_id = ' . (int)$iListing . ' AND visited_id = ' . (int)$iType)
            ->execute('getSlaveField');

        if ($iCnt) {
            $aInvites = $this->database()->select('ei.*, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('marketplace_invite'), 'ei')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ei.invited_user_id')
                ->where('ei.listing_id = ' . (int)$iListing . ' AND ei.visited_id = ' . (int)$iType)
                ->limit($iPage, $iPageSize, $iCnt)
                ->order('ei.invite_id DESC')
                ->execute('getSlaveRows');
        }

        return array($iCnt, $aInvites);
    }

    public function getUserListings($iListingId, $iUserId)
    {
        $iExpireTime = (PHPFOX_TIME - (Phpfox::getParam('marketplace.days_to_expire_listing') * 86400));
        $sExtraCond =  (Phpfox::getParam('marketplace.days_to_expire_listing') > 0) ? ' AND v.time_stamp >= ' . $iExpireTime : '';
        (($sPlugin = Phpfox_Plugin::get('marketplace.service_marketplace_getuserlistings_count')) ? eval($sPlugin) : false);

        $iCnt = $this->database()->select('COUNT(*)')
            ->from($this->_sTable, 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.listing_id != ' . (int)$iListingId . ' AND v.view_id = 0 AND v.user_id = ' . (int)$iUserId . $sExtraCond)
            ->execute('getSlaveField');

        (($sPlugin = Phpfox_Plugin::get('marketplace.service_marketplace_getuserlistings_query')) ? eval($sPlugin) : false);

        $aRows = $this->database()->select(Phpfox::getUserField() . (Phpfox::getUserField() ? ',' : '') . 'v.*')
            ->from($this->_sTable, 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.listing_id != ' . (int)$iListingId . ' AND v.view_id = 0 AND v.user_id = ' . (int)$iUserId . $sExtraCond)
            ->limit(Phpfox::getParam('marketplace.total_listing_more_from'))
            ->order('v.time_stamp DESC')
            ->execute('getSlaveRows');

        return array($iCnt, $aRows);
    }

    public function getPendingTotal()
    {
        return $this->database()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('view_id = 1')
            ->execute('getSlaveField');
    }

    public function isAlreadyInvited($iItemId, $aFriends)
    {
        if ((int)$iItemId === 0) {
            return false;
        }

        if (is_array($aFriends)) {
            if (!count($aFriends)) {
                return false;
            }

            $sIds = '';
            foreach ($aFriends as $aFriend) {
                if (!isset($aFriend['user_id'])) {
                    continue;
                }

                $sIds[] = $aFriend['user_id'];
            }

            $aInvites = $this->database()->select('invite_id, visited_id, invited_user_id')
                ->from(Phpfox::getT('marketplace_invite'))
                ->where('listing_id = ' . (int)$iItemId . ' AND invited_user_id IN(' . implode(', ', $sIds) . ')')
                ->execute('getSlaveRows');

            $aCache = array();
            foreach ($aInvites as $aInvite) {
                $aCache[$aInvite['invited_user_id']] = ($aInvite['visited_id'] ? _p('visted') : _p('invited'));
            }

            if (count($aCache)) {
                return $aCache;
            }
        }

        return false;
    }

    public function getFeatured()
    {
        $sCacheId = $this->cache()->set('marketplace_featured');

        if (!($aRows = $this->cache()->get($sCacheId))) {
            $iExpireTime = (PHPFOX_TIME - (Phpfox::getParam('marketplace.days_to_expire_listing') * 86400));
            $sExtraCond =  (Phpfox::getParam('marketplace.days_to_expire_listing') > 0) ? ' AND m.time_stamp >= ' . $iExpireTime : '';
            $aRows = $this->database()->select('m.*')
                ->from(Phpfox::getT('marketplace'), 'm')
                ->where('m.view_id = 0 AND m.privacy = 0 AND m.is_featured = 1' . $sExtraCond)
                ->execute('getSlaveRows');
            $iMoreImage = 3;
            foreach ($aRows as $iKey => $aRow) {
                $aRows[$iKey]['images'] = $this->getImages($aRow['listing_id'], $iMoreImage, $aRow['image_path']);
                $iCntImage = $this->countImages($aRow['listing_id']);
                if ($iCntImage > $iMoreImage) {
                    $aRows[$iKey]['more_image'] = $iCntImage - $iMoreImage;
                } else {
                    $aRows[$iKey]['more_image'] = 0;
                }
            }

            $this->cache()->save($sCacheId, $aRows);
        }

        if (!is_array($aRows)) {
            return array();
        }

        shuffle($aRows);

        $aReturn = array();
        $iCnt = 0;
        foreach ($aRows as $aRow) {
            $iCnt++;

            if ($iCnt > 5) {
                break;
            }

            $aReturn[] = $aRow;
        }

        return $aReturn;
    }

    public function getTotalInvites()
    {
        static $iCnt = null;

        if ($iCnt !== null) {
            return $iCnt;
        }

        $iCnt = (int)$this->database()->select('COUNT(m.listing_id)')
            ->from(Phpfox::getT('marketplace_invite'), 'mi')
            ->join(Phpfox::getT('marketplace'), 'm', 'm.listing_id = mi.listing_id AND m.view_id = 0')
            ->where('mi.visited_id = 0 AND mi.invited_user_id = ' . Phpfox::getUserId())
            ->execute('getSlaveField');

        return $iCnt;
    }

    public function getUserInvites()
    {
        $iCnt = $this->getTotalInvites();

        $aRows = $this->database()->select('m.*')
            ->from(Phpfox::getT('marketplace_invite'), 'mi')
            ->join(Phpfox::getT('marketplace'), 'm', 'm.listing_id = mi.listing_id')
            ->where('mi.visited_id = 0 AND mi.invited_user_id = ' . Phpfox::getUserId())
            ->limit(5)
            ->execute('getSlaveRows');

        return array($iCnt, $aRows);
    }

    public function getInfoForAction($aItem)
    {
        if (is_numeric($aItem)) {
            $aItem = array('item_id' => $aItem);
        }
        $aRow = $this->database()->select('m.listing_id, m.title, m.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('marketplace'), 'm')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
            ->where('m.listing_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        $aRow['link'] = Phpfox_Url::instance()->permalink('forum.thread', $aRow['listing_id'], $aRow['title']);
        return $aRow;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('marketplace.service_marketplace__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
