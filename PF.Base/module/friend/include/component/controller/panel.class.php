<?php
defined('PHPFOX') or exit('NO DICE!');

class Friend_Component_Controller_Panel extends Phpfox_Component {
	public function process() {
		Phpfox::isUser(true);

		list($iCnt, $aFriends) = Friend_Service_Request_Request::instance()->get(0, 100);
		foreach ($aFriends as $key => $friend) {
			if ($friend['relation_data_id']) {
				$sRelationShipName = Custom_Service_Relation_Relation::instance()->getRelationName($friend['relation_id']);
                if (isset($sRelationShipName) && !empty($sRelationShipName)){
                  $aFriends[$key]['relation_name'] = $sRelationShipName;
                } else {
                  //This relationship was removed
                  unset($aFriends[$key]);
                }
			}
		}
        $iNumberFriendRequest = 0;
        foreach ($aFriends as $aFriend){
            if (isset($aFriend['is_read']) && $aFriend['is_read'] == 1){
                continue;
            }
            $iNumberFriendRequest++;
        }
        if ($iNumberFriendRequest){
            $sScript = '$("span#js_total_new_friend_requests").html("'.$iNumberFriendRequest.'");';
        } else {
            $sScript = '$("span#js_total_new_friend_requests").hide();';
        }
        $sScript = '<script>$Behavior.resetFriendRequestCount = function() {'. $sScript . '};</script>';
		$this->template()->assign([
			'aFriends' => $aFriends,
			'sScript' => $sScript,
		]);
	}
}