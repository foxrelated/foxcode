<?php

defined('PHPFOX') or exit('No dice!');

class Poke_Component_Block_Poke extends Phpfox_Component
{
	public function process()
	{
		$aArr= array();
		$aUser = User_Service_User::instance()->getUserFields(false, $aArr, null, $this->request()->get('user_id'));
		
		$this->template()->assign(array(
			'aUser' => $aUser,
			'bCanPoke' => Poke_Service_Poke::instance()->canSendPoke($this->request()->get('user_id'))
			));
		return 'block';
	}
}
?>
