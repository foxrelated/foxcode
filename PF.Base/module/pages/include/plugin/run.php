<?php
defined('PHPFOX') or exit('NO DICE!');

		if (!PHPFOX_IS_AJAX && Phpfox::getUserBy('profile_page_id') > 0)
		{
			$bSend = true;
			if (defined('PHPFOX_IS_PAGE_ADMIN'))
			{
				$bSend = false;				
			}
			else
			{
				$aPage = Pages_Service_Pages::instance()->getPage(Phpfox::getUserBy('profile_page_id'));
				$sReq1 = Phpfox_Request::instance()->get('req1');
				if (empty($aPage['vanity_url']))
				{
					if ($sReq1 == 'pages')
					{
						// $bSend = false;
					}
				}
			}

			if ($bSend && !Pages_Service_Pages::instance()->isInPage())
			{
				Phpfox_Url::instance()->forward(Pages_Service_Pages::instance()->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']));
			}
		}