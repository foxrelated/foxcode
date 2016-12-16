<?php

event('app_settings', function($settings) {
	$redis_file = PHPFOX_DIR_SETTINGS . 'redis.sett.php';
	if (isset($settings['pf_core_redis']) && $settings['pf_core_redis'] == '1' && !empty($settings['pf_core_redis_host'])) {
		file_put_contents($redis_file, "<?php\nreturn ['host' => '{$settings['pf_core_redis_host']}', 'enabled' => 1];\n");
	}
    else if (isset($settings['pf_core_redis']) && !$settings['pf_core_redis'] && isset($settings['pf_core_redis_host'])) {
        file_put_contents($redis_file, "<?php\nreturn ['host' => '{$settings['pf_core_redis_host']}', 'enabled' => 0];\n");
    }

	$cache_file = PHPFOX_DIR_SETTINGS . 'cache.sett.php';
	$cache_file_data = [];
	if (isset($settings['pf_core_cache_driver'])) {
		$cache_file_data['driver'] = $settings['pf_core_cache_driver'];
		switch ($cache_file_data['driver']) {
			case 'redis':
				$cache_file_data['redis'] = [
					'host' => $settings['pf_core_cache_redis_host'],
					'port' => $settings['pf_core_cache_redis_port']
				];
				break;
			case 'memcached':
				$cache_file_data['memcached'] = [
					[$settings['pf_core_cache_memcached_host'], $settings['pf_core_cache_memcached_port'], 1]
				];
				break;
		}

		file_put_contents($cache_file, "<?php\n return " . var_export($cache_file_data, true) . ";\n");
	}
});