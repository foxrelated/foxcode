<?php

if (setting('pf_video_enabled')) {
	if (\Pages_Service_Pages::instance()->hasPerm($aPage['page_id'], 'pf_video.view_browse_videos')) {
		$aMenus[] = [
			'phrase'  => _p('Videos'),
			'icon'    => '',
			'url'     => Pages_Service_Pages::instance()->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'v/',
			'landing' => 'v'
		];
	}
}