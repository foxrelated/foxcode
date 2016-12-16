<?php

if (!empty($_POST) && isset($_POST['id']) && Phpfox::isModule('feed') && Phpfox::getParam('feed.cache_each_feed_entry') && !PHPFOX_IS_AJAX)
{
	$oReq = Phpfox_Request::instance();
	$oDb = Phpfox_Database::instance();
	
		$sCustomCurrentUrl = Phpfox_Module::instance()->getFullControllerName();
		$aVals = $oReq->getArray('val');		
		if (!empty($aVals))
		{
			switch ($sCustomCurrentUrl)
			{
				case 'blog.add':
					Feed_Service_Process::instance()->clearCache('blog', $_POST['id']);
					break;
				case 'pages.add':
					Feed_Service_Process::instance()->clearCache('pages_itemLiked', $_POST['id']);
					break;					
				case 'blog.delete':
					Feed_Service_Process::instance()->clearCache('blog', $oReq->get('id'));
					break;
			}
		}
	
}

?>