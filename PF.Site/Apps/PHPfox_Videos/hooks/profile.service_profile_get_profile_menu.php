<?php

if (setting('pf_video_enabled')) {
	$total = db()->select('COUNT(*)')->from(':feed')->where(['type_id' => 'PHPfox_Videos', 'user_id' => $aUser['user_id']])->count();
	if ($total > 0) {
		$aMenus[] = [
			'phrase' => _p('Video'),
			'url' => $aUser['user_name'] . '.v',
			'total' => $total,
			'actual_url' => 'profile_videos'
		];
	}
}