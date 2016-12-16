<?php

if (setting('pf_video_enabled')) {
	if (Core\Lib::appsGroup()->hasPerm($aPage['page_id'], 'pf_video.view_browse_videos')) {
		$aMenus[] = [
			'phrase'  => _p('Videos'),
			'icon'    => '',
			'url'     => Core\Lib::appsGroup()->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'v/',
			'landing' => 'v'
		];
	}
}