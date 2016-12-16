<?php
defined('PHPFOX') or exit('NO DICE!');

if (isset($this->_aUser['user_id']) && $this->_aUser['user_id'] > 0 && Phpfox::getParam('subscribe.subscribe_is_required_on_sign_up') && $this->_aUser['user_group_id'] == '2' && $this->_aUser['subscribe_id'] > 0)
{
	$bSetDirect = true;	
	$bSetDirect = (((Phpfox_Request::instance()->get('req1') == 'subscribe' && Phpfox_Request::instance()->get('req2') == 'register')) || ((Phpfox_Request::instance()->get('req1') == 'user' && Phpfox_Request::instance()->get('req2') == 'logout'))) ? false : true;

	if ($bSetDirect === true)			
	{
        Subscribe_Service_Purchase_Purchase::instance()->setRedirectId($this->_aUser['subscribe_id']);
	}
}
?>