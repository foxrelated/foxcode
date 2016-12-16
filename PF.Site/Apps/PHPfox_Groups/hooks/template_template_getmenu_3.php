<?php

if (setting('pf_group_enabled') != '1' && $sConnection == 'main') {
	foreach ($aMenus as $key => $menu) {
		if ($menu['url'] == '/groups') {
			unset($aMenus[$key]);
		}
	}
}
