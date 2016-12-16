<?php
defined('PHPFOX') or exit('NO DICE!');

if ($sFriendModuleId == 'marketplace')
{
	$aInviteCache = Marketplace_Service_Marketplace::instance()->isAlreadyInvited($this->getParam('friend_item_id', '0'), $aFriends);
	if (is_array($aInviteCache))
	{
		foreach ($aFriends as $iKey => $aFriend)
		{
			if (isset($aInviteCache[$aFriend['user_id']]))
			{
				$aFriends[$iKey]['is_active'] = $aInviteCache[$aFriend['user_id']];
			}
		}
	
		$this->template()->assign(array(
				'aFriends' => $aFriends
			)
		);	
	}
}
?>