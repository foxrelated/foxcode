<?php

if ((setting('pf_video_enabled') != '1' || !(user('pf_video_view', 1))) && $sConnection == 'main') {
	foreach ($aMenus as $key => $menu) {
		if ($menu['url'] == '/v') {
			unset($aMenus[$key]);
		}
	}
}
