<?php
defined('PHPFOX') or exit('NO DICE!');

if (!PHPFOX_IS_AJAX)
{
	$mRedirectId = Subscribe_Service_Purchase_Purchase::instance()->getRedirectId();
	if (is_numeric($mRedirectId) && $mRedirectId > 0)
	{
		Phpfox_Url::instance()->send('subscribe.register', array('id' => $mRedirectId), _p('please_complete_your_purchase'));
	}

    $mRedirectId = Subscribe_Service_Purchase_Purchase::instance()->isCompleteSubscribe();
	if (is_numeric($mRedirectId) && $mRedirectId > 0)
	{
		Phpfox_Url::instance()->send('subscribe.register', array('id' => $mRedirectId), _p('please_complete_your_purchase'));
	}
}
?>