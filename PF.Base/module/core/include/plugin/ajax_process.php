<?php

if (!empty($_POST) && Phpfox::isModule('feed') && Phpfox::getParam('feed.cache_each_feed_entry') && PHPFOX_IS_AJAX)
{
	$oReq = Phpfox_Request::instance();
	$oDb = Phpfox_Database::instance();

		$aCoreCall = $oReq->getArray('core');
		if (isset($aCoreCall['call']))
		{
			switch ($aCoreCall['call'])
			{
				case 'comment.updateText':
					$aComment = $oDb->select('*')
						->from(Phpfox::getT('comment'))
						->where('comment_id = ' . (int) $oReq->get('comment_id'))
						->execute('getSlaveRow');
					if (isset($aComment['comment_id']))
					{
						Feed_Service_Process::instance()->clearCache($aComment['type_id'], $aComment['item_id']);
					}
					break;
				case 'blog.moderation':
					if ($oReq->get('action') == 'delete')
					{
						foreach ((array) $oReq->get('item_moderate') as $iId)
						{
							Feed_Service_Process::instance()->clearCache('blog', $iId);
						}
					}
					break;
			}
		}		
	
}

?>