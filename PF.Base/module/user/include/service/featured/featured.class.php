<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_User
 * @version 		$Id: featured.class.php 6585 2013-09-05 10:01:48Z Miguel_Espinoza $
 */
class User_Service_Featured_Featured extends Phpfox_Service
{	
	
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('user_featured');
	}

	/**
	 * Gets the featured members according to Phpfox::getParam('user.how_many_featured_members').
	 * Uses cache to save a query (stores a cache if none found)
	 * @return array( array of users, int total featured users )
	 */
	public function get()
	{
		if ($sPlugin = Phpfox_Plugin::get('user.service_featured_get_1'))
		{
			eval($sPlugin);
			if (isset($mPluginReturn)){ return $mPluginReturn; }
		}
		$iTotal = Phpfox::getParam('user.how_many_featured_members');
		// the random will be done with php logic
		$sCacheId = $this->cache()->set('featured_users');
		if (!($aUsers = $this->cache()->get($sCacheId)))
		{
			$aUsers = $this->database()->select(Phpfox::getUserField() . ', uf.ordering')
				->from(Phpfox::getT('user'), 'u')
				->join($this->_sTable, 'uf', 'uf.user_id = u.user_id')
				->order('ordering DESC')
                ->limit(100)
				->execute('getSlaveRows');

			$this->cache()->save($sCacheId, $aUsers);
		}

		if (!is_array($aUsers)) return array(array(), 0);		
		$aOut = array();
        shuffle($aUsers);
		
		$iCount = count($aUsers); // using count instead of $this->database()->limit to measure the real value
		for ($i = 0; $i <= $iTotal; $i++)
		{
			if (!isset($aUsers[$iCount -$i])) continue; // availability check
			$aOut[] = $aUsers[$iCount - $i];
		}
		
		return array($aOut, count($aUsers));
	}

	public function getOtherGender() {
		$gender = 2;
		if (Phpfox::getUserBy('gender') == '2') {
			$gender = 1;
		}
        $aWhere = ['u.profile_page_id' => 0, 'u.view_id' => 0, 'u.gender' => $gender];
        $sCacheName = 'rec_users';
        if (Phpfox::isUser()) {
            $sCacheName .= '_' . Phpfox::getUserId();
            $aBlockedUserIds = User_Service_Block_Block::instance()->get(null, true);
            if (!empty($aBlockedUserIds)) {
                $aWhere[] = ' AND u.user_id NOT IN (' . implode(',', $aBlockedUserIds) . ')';
            }

        }
		$cache = $this->cache()->set($sCacheName);
		$users = $this->cache()->get($cache, 360);
		if ($users === false) {
			$users = $this->database()
                ->select('uf.total_friend, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('user'), 'u')
                ->join(Phpfox::getT('user_field'), 'uf', 'u.user_id = uf.user_id')
				->where($aWhere)
				->limit(12)
				->order('RAND()')
				->all();

			$this->cache()->save($cache, $users);
		}

		return $users;
	}

	public function getNewUsers() {
        $sCacheName = 'new_users';
        $aWhere = ['u.profile_page_id' => 0, 'u.view_id' => 0];
        if (Phpfox::isUser()) {
            $sCacheName .= '_' . Phpfox::getUserId();
            $aBlockedUserIds = User_Service_Block_Block::instance()->get(null, true);
            if (!empty($aBlockedUserIds)) {
                $aWhere[] = ' AND u.user_id NOT IN (' . implode(',', $aBlockedUserIds) . ')';
            }

        }
        $cache = $this->cache()->set($sCacheName);
		$users = $this->cache()->get($cache, 360);
		if ($users === false) {
			$users = $this->database()
				->select('u.*')
				->from(':user', 'u')
				->where($aWhere)
				->limit(12)
				->order('u.joined DESC')
				->all();

			$this->cache()->save($cache, $users);
		}

		return $users;
	}
  	public function getRecentActiveUsers() {
        $sCacheName = 'recent_active_users';
        $aWhere = ['u.profile_page_id' => 0, 'u.view_id' => 0, 'u.is_invisible' => 0];
        if (Phpfox::isUser()) {
            $sCacheName .= '_' . Phpfox::getUserId();
            $aBlockedUserIds = User_Service_Block_Block::instance()->get(null, true);
            if (!empty($aBlockedUserIds)) {
                $aWhere[] = ' AND u.user_id NOT IN (' . implode(',', $aBlockedUserIds) . ')';
            }

        }
        $cache = $this->cache()->set($sCacheName);
		// We should cached it only 2 minutes. This block always changes
		$users = $this->cache()->get($cache, 2);
		if ($users === false) {
			$users = $this->database()
                ->select('uf.total_friend, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('user'), 'u')
                ->join(Phpfox::getT('user_field'), 'uf', 'u.user_id = uf.user_id')
				->where($aWhere)
				->limit(12)
				->order('u.last_activity DESC')
				->all();

			$this->cache()->save($cache, $users);
		}

		return $users;
	}

	public function getFeaturedUsers() {
        $sCacheName = 'featured-users-pages-items';
        $sJoinCond = 'uf.user_id = u.user_id';
        if (Phpfox::isUser()) {
            $sCacheName .= '_' . Phpfox::getUserId();
            $aBlockedUserIds = User_Service_Block_Block::instance()->get(null, true);
            if (!empty($aBlockedUserIds)) {
                $sJoinCond .= ' AND u.user_id NOT IN (' . implode(',', $aBlockedUserIds) . ')';
            }

        }
        $cache = $this->cache()->set($sCacheName);
		$users = $this->cache()->get($cache, 200);
		if ($users === false) {
			$users = $this->database()
				->select('u.*')
				->from(':user', 'u')
				->join(':user_featured', 'uf', $sJoinCond)
				->order('uf	.ordering DESC')
				->execute('getSlaveRows');
			$this->cache()->save($cache, $users);
		}

		return $users;
	}

	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('user.service_featured__call'))
		{
			eval($sPlugin);
            return null;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}