<?php
defined('PHPFOX') or exit('NO DICE!');

if (Phpfox_Request::instance()->get('module') == 'pages')
{
	$aPage = Pages_Service_Pages::instance()->getPage($aFeed['parent_user_id']);
	if (isset($aPage['page_id']) && Pages_Service_Pages::instance()->isAdmin($aPage))
	{
		define('PHPFOX_FEED_CAN_DELETE', true);
	}
}
?>