<?php
namespace Core;

use Admincp_Service_Module_Process;
use Phpfox_Plugin;
use User_Service_Block_Block;
use Pages_Service_Pages;
use Language_Service_Phrase_Process;


event('app_settings', function ($settings){
	if (isset($settings['pf_video_enabled'])) {
		Admincp_Service_Module_Process::instance()->updateActivity('v', $settings['pf_video_enabled']);
	}
});

$sub_menu = [
	_p('All Videos') => url('/v'),
	_p('My Videos') => url('/v', ['view' => 'my'])
];

if (!setting('core.friends_only_community')) {
	$sub_menu[_p('Friends\' Videos')] = url('/v', ['view' => 'friends']);
}

event('notification_map_PHPfox_Videos', function ($type, $item, $noti) {
    switch ($type) {
        case '__like':
        case '__comment':
            if ($cache = storage()->get('feed_callback_' . $item['item_id']))
            {
                if (in_array($cache->value->module, ['groups', 'pages']))
                {
                    $noti->url = '/v/play/p-:id';
                }
            }
        break;
    }
});

event('feed_map_PHPfox_Videos', function ($map) {
	if (!setting('pf_video_enabled')) {
		$map->error = true;
	} else {
        $aReturn = [
            'share_type_id' => 'PHPfox_Videos'
        ];
		if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')) {
			$row = $map->data_row;
			if (!empty($row) && !empty($row['parent_user_id'])) {
				if (!\Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm($row['parent_user_id'], 'pf_video.view_browse_videos')) {
					$map->error = true;
				}
			}
            $aReturn = [
                'share_type_id' => 'v_pages'
            ];
			$map->link = str_replace('play/', 'play/p-', $map->link);
            $map->feed_table_prefix = 'pages_';
		} else {
			$row = $map->data_row;
            $feedId = (!empty($row['parent_feed_id'])) ? $row['parent_feed_id'] : $row['feed_id'];
            $aCallback = storage()->get('feed_callback_' . $feedId);
            if ($aCallback && !defined('PHPFOX_IS_PAGES_VIEW') && $aCallback->value->module && \Phpfox::isModule($aCallback->value->module)) {

                if ($aCallback->value->module == 'pages' || $aCallback->value->module == 'groups')
                {
                    $map->link = str_replace('play/', 'play/p-', $map->link);
                    $map->feed_table_prefix = 'pages_';
                    $aReturn['share_type_id'] = 'v_pages';

                    if (!empty($row['parent_feed_id']))
                    {
                        $parentFeed = \Feed_Service_Feed::instance()->getFeed($row['parent_feed_id'], 'pages_');
                        $App = (new App())->get('PHPfox_Videos');
                        if ($parentFeed)
                        {
                            $newMap = $App->map($parentFeed['content'], $parentFeed);
                            $map->title = $newMap->title;
                            $map->feed_info = $newMap->feed_info;
                        }
                        else
                        {
                            $mainFeedId = (!empty($row['main_feed_id'])) ? $row['main_feed_id'] : $row['feed_id'];
                            storage()->del('feed_callback_' . $feedId);
                            (new \Api\Feed())->delete($mainFeedId, false, 'PHPfox_Videos');
                            $map->error = true;
                            return;
                        }
                        
                        $aPage = db()->select('p.*, pu.vanity_url, ' . \Phpfox::getUserField('u', 'parent_'))
                            ->from(':pages', 'p')
                            ->join(':user', 'u', 'p.page_id=u.profile_page_id')
                            ->leftJoin(\Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
                            ->where('p.page_id=' . (int)$aCallback->value->item_id)
                            ->execute('getRow');
                        $aReturn['parent_user_name'] = \Phpfox::getService($aCallback->value->module)->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
                        if ($row['user_id'] != $aPage['parent_user_id']) {
                            $aReturn['parent_user'] = \User_Service_User::instance()->getUserFields(true, $aPage, 'parent_');
                            $aReturn['feed_info'] = null;
                        }
                    }
                }
            }

		}
        $map->more_params = $aReturn;
	}
});


if (setting('pf_video_enabled')) {
	\Phpfox_Module::instance()
		->addServiceNames(['v.callback' => '\Apps\PHPfox_Videos\Service\Callback'])
		->addAliasNames('v', 'PHPfox_Videos');
}

// Browsing all videos
$index = function($route, $category_id = null, $category_name = null, $parent = null) use($sub_menu) {
	user('pf_video_view', '1', null, true);
	$search_url = url('/v');
	if ($parent && defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE')) {
		$page = \Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getPage($parent);

		section($page['title'], \Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getUrl($page['page_id'], $page['title'], $page['vanity_url']));
		section(_p('Videos'), \Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getUrl($page['page_id'], $page['title'], $page['vanity_url']) . 'v');
		$search_url = \Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getUrl($page['page_id'], $page['title'], $page['vanity_url']) . 'v';
		if (\Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm($parent, 'pf_video.share_videos')) {
			sectionMenu(_p('Share a Video'), url('/v/share', ['module_id' => PHPFOX_PAGES_ITEM_TYPE, 'item_id' => $page['page_id']]), ['css_class' => 'popup']);
		}
	} else {
		title(_p('Videos'));
		section(_p('Videos'), url('/v'));
		sectionMenu(_p('Share a Video'), url('/v/share'), ['css_class' => 'popup']);
		subMenu('v', $sub_menu);
	}

	block(1, function() {
		$categories = storage()->order('DESC')->all('pf_video_category');

		if (!$categories) {
			$categories = [
				'Music',
				'Comedy',
				'Film & Entertainment',
				'Gaming'
			];

			foreach ($categories as $category) {
				storage()->set('pf_video_category', $category);
			}

			$categories = storage()->order('DESC')->all('pf_video_category');
		}

		return view('@PHPfox_Videos/categories.html', [
			'categories' => $categories
		]);
	});

	$params = [
        'type_id' => 'PHPfox_Videos',
        'limit' => 12,
        'page' => request()->get('page')
    ];
	$search = search()->field('content')
		->primary('feed_id')
		->show([12])
		->sort([
			'latest' => ['feed.time_stamp', _p('Latest')],
			'most-viewed' => ['feed.total_view', _p('Most Viewed')]
		])
		->url($search_url)
		->make('videos');

	if ($search->isSearch()) {
		$params['search'] = $search->get('search');
	}

	if (($sort = request()->get('sort')) && $sort == 'most-viewed') {
		$params['order'] = $search->getSort();
	}

    if ($iLastItem = request()->get('last-item')) {
		$params['last-item'] = $iLastItem;
	}
	if (($view = request()->get('view'))) {
		auth()->isLoggedIn(true);
		switch ($view) {
			case 'my':
				$params['user_id'] = user()->id;
				break;
			case 'friends':
				$params['friends'] = true;
				break;
		}
	}

	if ($category_id) {
		$category = storage()->getById($category_id);
		if (isset($category->value)) {
			$category_name = _p($category->value);
			h1($category_name, permalink('/v/category', $category_id, $category_name));

			$params['join_query'] = function () use ($category_id) {
				db()->join(':cache', '_c', ['_c.file_name' => 'pf_video_c', '_c.cache_data' => (int)$category_id, '_c.data_size' => ['=' => 'feed.feed_id']]);
			};
		}
	}

    $params['ignore_limit_feed'] = true;
	$videos = (new \Api\Feed())->get($params);
    
	foreach ($videos as $key => $video) {

		if (empty($video->content)) {
			unset($videos[$key]);
			continue;
		}
		if (is_object($video->content) && !isset($video->content->poster)) {
			$videos[$key]->content->poster = 1;
		}
	}
    defined('PHPFOX_VIDEOS_INTEGRATE_PAGE') or define('PHPFOX_VIDEOS_INTEGRATE_PAGE', true);
	return view(($parent === null ? '' : '@PHPfox_Videos/') . 'index.html', [
		'videos' => $videos,
        'last_id' => isset($video) ? $video->id : 0,
        'is_ajax' => PHPFOX_IS_AJAX
	]);
};

if (setting('pf_video_enabled')) {
	event('pages_view_v', function () use ($index) {
		$aPages = Pages_Service_Pages::instance()->getActivePage();
		if (isset($aPages['page_id'])) {
			\Feed_Service_Feed::instance()->callback([
				'module'       => 'pages',
				'item_id'      => $aPages['page_id'],
				'table_prefix' => 'pages_'
			]);

			return $index('/', null, null, $aPages['page_id']);
		}
	});

	event('groups_view_v', function () use ($index) {
		$aPages = Lib::appsGroup()->getActivePage();
		if (isset($aPages['page_id'])) {
			\Feed_Service_Feed::instance()->callback([
				'module'       => 'groups',
				'item_id'      => $aPages['page_id'],
				'table_prefix' => 'pages_'
			]);

			return $index('/', null, null, $aPages['page_id']);
		}
	});

	event('profile', function ($app, $user) {
		if ($app == 'PHPfox_Videos') {
			section(_p('Videos'), url('/' . $user['user_name'] . '/v'));

			$params = ['type_id' => 'PHPfox_Videos', 'limit' => 12, 'user_id' => $user['user_id']];

			if ($iLastItem = request()->get('last-item')) {
				$params['last-item'] = $iLastItem;
			}

			$search = search()->field('content')
				->primary('feed_id')
				->show([12])
				->sort([
					'latest'      => ['feed.time_stamp', 'Latest'],
					'most-viewed' => ['feed.total_view', 'Most Viewed']
				])
				->url(url('/' . $user['user_name'] . '/v'))
				->make('videos');

			if ($search->isSearch()) {
				$params['search'] = $search->get('search');
			}

			if (($sort = request()->get('sort')) && $sort == 'most-viewed') {
				$params['order'] = $search->getSort();
			}

			if ($iLastItem = request()->get('last-item')) {
				$params['last-item'] = $iLastItem;
			}

			$videos = (new \Api\Feed())->get($params);
			foreach ($videos as $key => $video) {
				if (!isset($video->content->poster)) {
					$videos[ $key ]->content->poster = 1;
				}
			}

			return view('@PHPfox_Videos/index.html', [
				'videos'  => $videos,
				'last_id' => isset($video) ? $video->id : 0,
                'is_ajax' => PHPFOX_IS_AJAX
			]);
		}
	});
}

new Route\Group('/v', function() use($index, $sub_menu) {

	$storage = new Storage();

	route('/admincp/iframe', function() {
		auth()->isAdmin(true);

		$content = file_get_contents('https://docs.phpfox.com/plugins/viewsource/viewpagesrc.action?pageId=1343935');
		$content = str_replace('</title>', '</title><base href="https://docs.phpfox.com/" target="_blank">', $content);

		echo $content;
		exit;
	});

	route('/admincp/category/delete', function() {
		auth()->isAdmin(true);

		storage()->delById(request()->get('id'));

		\Phpfox::addMessage(_p('Category successfully deleted.'));

		return url()->send('admincp.app', ['id' => 'PHPfox_Videos']);
	});

	route('/admincp/category/order', function() {
		auth()->isAdmin(true);

		storage()->updateOrderById(request()->get('ids'));

		return true;
	});

	route('/admincp/category', function() {
		auth()->isAdmin(true);

		$is_edit = false;
		$category_name = '';
		if (($id = request()->get('id'))) {
			$is_edit = true;
			$category = storage()->getById($id);
			$category_name = $category->value;
		}

		if ($val = request()->get('val')) {
            $aLanguages = \Language_Service_Language::instance()->getAll();
			if ($is_edit) {
                if (\Phpfox::isPhrase($category_name)){
                    $phrase_var_name = $category_name;
                } else {
                    $phrase_var_name = 'language.app_' . md5($category_name);
                }
                foreach ($aLanguages as $aLanguage){
                    if (isset($val['name' . $aLanguage['language_id']])){
                        $name = $val['name' . $aLanguage['language_id']];
                        \Language_Service_Phrase_Process::instance()->updateVarName($aLanguage['language_id'], $phrase_var_name, $name);
                    }
                }
			} else {
                $name = $val['name' . $aLanguages[0]['language_id']];
                $phrase_var_name = 'app_' . md5('Video Category: '. $name);
                //Add phrases
                $aText = [];
                foreach ($aLanguages as $aLanguage){
                    if (isset($val['name' . $aLanguage['language_id']]) && !empty($val['name' . $aLanguage['language_id']])){
                        $aText[$aLanguage['language_id']] = $val['name' . $aLanguage['language_id']];
                    } else {
                        error(_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']]));
                    }
                }
                $aValsPhrase = [
                    'var_name' => $phrase_var_name,
                    'text' => $aText
                ];
                Language_Service_Phrase_Process::instance()->add($aValsPhrase);
				storage()->set('pf_video_category', 'language.' . $phrase_var_name);
			}

			\Phpfox::addMessage(_p('Category successfully added.'));

			return url()->send('admincp.app', ['id' => 'PHPfox_Videos']);
		}
        if ($id){
            title(_p('Edit Category'));
        } else {
            title(_p('New Category'));
        }
        $aLanguage = \Language_Service_Language::instance()->getAll();
		return view('admincp_category.html', [
			'category_id' => $id,
			'category_name' => $category_name,
            'aLanguage' => $aLanguage
		]);
	});

	route('/admincp', function() {
		auth()->isAdmin(true);

		$c = storage()->order('DESC')->all('pf_video_category');

		return view('admincp.html', [
			'categories' => $c
		]);
	});

	// Share a URL
	route('/url', function() {
		auth()->isLoggedIn(true);
		$val = request()->get('val');
		if (isset($val['callback_module']) && isset($val['callback_item_id'])) {
			if (\Phpfox::isModule($val['callback_module']) &&
				\Phpfox::hasCallback($val['callback_module'], 'checkPermission') &&
				!\Phpfox::callback($val['callback_module'] . '.checkPermission', $val['callback_item_id'], 'pf_video.share_videos')
			)
			{
				return error(_p('You don\'t have permission to share videos on this.'));
			}
		}

		if (!isset($val['status_info'])) $val['status_info'] = '';
		if (!isset($val['caption'])) $val['caption'] = '';
		
		if (isset($_POST['pf_video_id'])) {
			if (empty($_POST['pf_video_id'])) {
				return [
					'skip' => 'yes'
				];
			}

			$storage = new Storage();
			$id = $storage->update('pf_video_' . request()->get('pf_video_id'), [
				'privacy' => (isset($val['privacy']) ? (int) $val['privacy'] : 0),
				'status_update' => text()->clean($val['status_info']),
				'caption' => text()->clean($val['caption'])
			]);

			$run = "$('.pf_v_message_cancel').parent().removeClass('hide_it');$('.pf_video_message').hide();$('.pf_upload_form').prepend('<div class=\"message\">" . _p('Your video has successfully been saved and will be published when we are done processing it.') . "</div>').addClass('completed').show(); ";
			$run .="$('.pf_v_video_info').hide();$('#activity_feed_textarea_status_info').val('');$('.activity_feed_form_button_status_info').hide();$('.pf_video_caption').hide();";
			return [
				'run' => $run
			];
		}

		$url = trim($val['url']);
		if (substr($url, 0, 7) != 'http://' && substr($url, 0, 8) != 'https://') {
			error(_p('Please provide a valid URL.'));
		}

		$parsed = \Link_Service_Link::instance()->getLink($url);

		if (empty($parsed['embed_code'])) {
			error(_p('Unable to load a video to embed.'));
		}

		$parsed['embed_code'] = str_replace('http://player.vimeo.com/', 'https://player.vimeo.com/', $parsed['embed_code']);

		$status = text()->clean($val['status_info']);

		$feed = (new \Api\Feed())->post([
			'privacy' => (isset($val['privacy']) ? (int) $val['privacy'] : 0),
			'type_id' => 'PHPfox_Videos',
			'module_id' => (isset($val['callback_module']) ? $val['callback_module'] : false),
			'module_item_id' => (isset($val['callback_item_id']) ? $val['callback_item_id'] : false),
			'content' => [
				'status_update' => $status,
				'caption' => $parsed['title'],
				'embed_image' => $parsed['default_image'],
				'embed_code' => $parsed['embed_code']
			]
		]);

		if (\Phpfox::isModule('notification') && isset($val['callback_module']) && isset($val['callback_item_id']) && \Phpfox::isModule($val['callback_module']) && \Phpfox::hasCallback($val['callback_module'], 'addItemNotification'))
		{
			\Phpfox::callback($val['callback_module'] . '.addItemNotification', ['page_id' => $val['callback_item_id'], 'item_perm' => 'pf_video.view_browse_videos', 'item_type' => 'v', 'item_id' => $feed->id, 'owner_id' => user()->id]);
		}
        (($sPlugin = Phpfox_Plugin::get('v.service_process_url__end')) ? eval($sPlugin) : false);
		return [
			'run' => 'window.location.href = "' . url()->make('/v/play/' . (isset($val['callback_module']) ? 'p-' : '') . $feed->id) . '"; $(".js_box_content").html("<i class=\"fa fa-spin fa-circle-o-notch\"></i>");'
		];
	});

	// Share a video
	route('/share', function() {
		auth()->isLoggedIn(true);
		title(_p('Share a Video'));
		section(_p('Videos'), url('/v'));
		h1(_p('Share a Video'), url('/v/share'));

		if (request()->get('module_id') && request()->get('item_id')) {
			if (\Phpfox::isModule(request()->get('module_id')) &&
				\Phpfox::hasCallback(request()->get('module_id'), 'checkPermission') &&
				!\Phpfox::callback(request()->get('module_id') . '.checkPermission', request()->get('item_id'), 'pf_video.share_videos')
			)
			{
				return error(_p('You don\'t have permission to share videos on this.'));
			}
		}
		
		return render('share.html', [
			'is_ajax_browsing' => (!request()->get('is_ajax_browsing') ? true : false),
			'module_item_id' => (request()->get('item_id') ? request()->get('item_id') : 0),
			'module_id' => (request()->get('module_id') ? request()->get('module_id') : '')
		]);
	});

	// Deleting a video
	route('/delete/:id', function($id) {

		$is_page = false;
		if (substr($id, 0, 2) == 'p-') {
			$id = str_replace('p-', '', $id);

			$feed = (new \Api\Feed())->get($id, 'PHPfox_Videos', true);
			$is_page = true;
		} else {
			$feed = (new \Api\Feed())->get($id);
		}

		if (!auth()->isAdmin() && $feed->user->id != user()->id) {
			error(_p('Unable to delete this video.'));
		}

		if (request()->get('process') == 'yes') {
			if (isset($feed->content->path)) {
				$s3 = new \S3(setting('pf_video_s3_key'), setting('pf_video_s3_secret'));
				foreach (['.webm', '-low.mp4', '.ogg', '.mp4', '.png/frame_0000.png', '.png/frame_0001.png', '.png/frame_0002.png'] as $ext) {
					$s3->deleteObject(setting('pf_video_s3_bucket'), $feed->content->path . $ext);
				}
			}

			(new \Api\Feed())->setUser(user()->id)->delete($feed->id, $is_page, 'PHPfox_Videos');
            (\Phpfox::isModule('notification') ? \Notification_Service_Process::instance()->deleteAllOfItem(['PHPfox_Videos/__like'],(int) $id) : null);

			url()->send('/v', _p('Video successfully deleted.'));
		}

		section('Deleting Video', url('current'));

		if ($is_page) {
			$feed->id = 'p-' . $feed->id;
		}
		return render('delete.html', [
			'id' => $feed->id
		]);
	});

	// Set a video poster image
	(new Route('/poster', function() {
		auth()->membersOnly();

		$feed = (new \Api\Feed())->get(request()->get('id'));

		if ($feed->user->id != user()->id && !user()->isAdmin()) {
			error(_p('Unable to access this item.'));
		}

		(new \Api\Feed())->put($feed->id, [
			'content' => ['poster' => (int) request()->get('frame')]
		]);

	}))->accept('POST');

	// Publish a video, once its ready
	new Route('/publish', function(Controller $controller) use($storage) {
		auth()->membersOnly();

		$val = (object) $controller->request->get('val');
		if (empty($val->caption)) {
			error(_p('Provide a caption for your video.'));
		}

		$encoding = $storage->getById($controller->request->get('id'));
		$feed = (new \Api\Feed())->setUser($encoding->value->user_id)->post([
			'type_id' => 'PHPfox_Videos',
			'content' => [
				'caption' => text()->clean($val->caption, 150),
				'encoding_id' => $encoding->value->encoding_id,
				'path' => $encoding->value->path,
				'poster' => 1
			]
		]);
        (($sPlugin = Phpfox_Plugin::get('v.service_process_publish__end')) ? eval($sPlugin) : false);
		$storage->update($encoding->key, [
			'feed_id' => $feed
		]);

		return [
			'run' => 'window.location.href = "' . $controller->url->make('/v/play/' . $feed->id) . '";'
		];
	});

	route('/callback', function() use($storage) {
		$notification = json_decode(trim(file_get_contents('php://input')), true);
		if (isset($notification['job']) && isset($notification['job']['state'])) {
			switch ($notification['job']['state']) {
				case 'finished':
					$encoding = $storage->get('pf_video_' . $notification['job']['id']);
					if (empty($encoding->value->status_update)) {
						$encoding->value->status_update = '';
					}
					if (empty($encoding->value->caption)) {
						$encoding->value->caption = '';
					}
					$feed = (new \Api\Feed())->setUser($encoding->value->user_id)->post([
						'privacy' => (isset($encoding->value->privacy) ? (int) $encoding->value->privacy : 0),
						'type_id' => 'PHPfox_Videos',
						'module_id' => (isset($encoding->value->module_id) ? $encoding->value->module_id : false),
						'module_item_id' => (isset($encoding->value->module_item_id) ? $encoding->value->module_item_id : false),
						'content' => [
							'caption' => text()->clean($encoding->value->caption, 150),
							'status_update' => $encoding->value->status_update,
							'encoding_id' => $encoding->value->encoding_id,
							'path' => $encoding->value->path,
							'poster' => 1
						]
					]);

					if (\Phpfox::isModule('notification') && $encoding->value->module_id && $encoding->value->module_item_id && \Phpfox::isModule($encoding->value->module_id) && \Phpfox::hasCallback($encoding->value->module_id, 'addItemNotification'))
					{
						\Phpfox::callback($encoding->value->module_id . '.addItemNotification', ['page_id' => $encoding->value->module_item_id, 'item_perm' => 'pf_video.view_browse_videos', 'item_type' => 'v', 'item_id' => $feed->id, 'owner_id' => $encoding->value->user_id]);
					}


					\Phpfox_Mail::instance()->to($encoding->value->user_id)
						->subject(_p('Video is ready!'))
						->message('Your video is ready.<br />' . url('/v/play/' . (($encoding->value->module_id) ? 'p-' : '') . $feed->id))
						->send();

					$file = PHPFOX_DIR_FILE . 'static/' . $encoding->value->id . '.' . $encoding->value->ext;
					if (file_exists($file)) {
						unlink($file);
					}

					if ($encoding->value->module_id) {
						notify('PHPfox_Videos', 'video_ready_p', $feed->id, $encoding->value->user_id);
					}
					else {
						notify('PHPfox_Videos', 'video_ready', $feed->id, $encoding->value->user_id);
					}

					break;
			}
		}

		exit;
	});

	// Process a video, wait till its done before allowing the user to publish
	new Route('/process', function(Controller $controller) use($storage) {
		$encoding = $storage->getById($controller->request->get('id'));
		if (isset($encoding->value) && isset($encoding->value->feed_id)) {
			$controller->url->send('/v/play/' . $encoding->value->feed_id->id);
		}

		if ($controller->request->get('check')) {
			$zencoder = new \Services_Zencoder(setting('pf_video_key'));
			$json = $zencoder->jobs->progress($encoding->value->encoding_id);

			if ($json->state == 'finished') {
				$j = (string) (new jQuery('.pf-video-form > form > .message'))
					->html(_p('Your video is ready! Don\'t forget to hit publish below once you are ready.<div style="padding-top:10px;"><input type="submit" class="button" value="Publish">'))
					->removeClass('message')->addClass('valid_message');

				$j .= (string) (new jQuery('.pf-video-content'))->html('<i class="fa fa-check-circle"></i>');

				return [
					'run' => $j
				];
			}
			elseif ($json->state == 'failed') {
				$json = $zencoder->jobs->details($encoding->value->encoding_id);
				$j = "$('#pf-video-process').fadeOut(); ";
				$j .= " $('body').prepend('<div id=\"pf-video-process-main\" style=\"visibility:visible !important;\"></div>'); ";
				$j .= " $('#pf-error-message').remove(); ";
				$j .= " var d = $('<div id=\"pf-error-message\">{$json->outputs['mp4 high']->error_message}</div>'); ";
				$j .= " d.prependTo('#pf-video-process-main'); ";
				$j .= " $('#pf-video-process-main i').remove(); ";
				$j .= " var i = $('<i class=\"fa fa-times-circle\"></i>'); ";
				$j .= " i.prependTo('#pf-error-message'); ";
				$j .= " i.click(function() { $('#pf-video-process-main').fadeOut(); window.location.href = '" . url()->make('/v/share') . "'; }); ";

				return [
					'run' => $j
				];
			}

			return [
				'run' => 'videoCheck("' . $controller->url->make('/v/process', ['id' => $controller->request->get('id')]) . '");'
			];
		}

		return $controller->render('process.html', [
			'id' => $controller->request->get('id')
		]);
	});

	// Accept an upload of a video
	new Route('/upload', function(Controller $controller) use($storage) {
		if (empty($_FILES['ajax_upload']['tmp_name'])) {
			switch ($_FILES['ajax_upload']['error']) {
				case UPLOAD_ERR_INI_SIZE:
					$message = "The uploaded file exceeds the upload_max_filesize (" . ini_get('upload_max_filesize') . ") directive in php.ini";
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
					break;
				case UPLOAD_ERR_PARTIAL:
					$message = "The uploaded file was only partially uploaded";
					break;
				case UPLOAD_ERR_NO_FILE:
					$message = "No file was uploaded";
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$message = "Missing a temporary folder";
					break;
				case UPLOAD_ERR_CANT_WRITE:
					$message = "Failed to write file to disk";
					break;
				case UPLOAD_ERR_EXTENSION:
					$message = "File upload stopped by extension";
					break;

				default:
					$message = "Unknown upload error";
					break;
			}

			http_response_code(400);
			return [
				'error' => _p($message)
			];
		}

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: text/plain');
		header('Content-Length: ' . strlen(file_get_contents($_FILES['ajax_upload']['tmp_name'])));
		header('Accept-Ranges: bytes');

		$controller->auth->membersOnly();

		$file_size = user('pf_video_file_size', '10');
		$path = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS;
		$id = md5(uniqid() . $controller->active->id);
		$realName = $id . '.' . \Phpfox_File::instance()->getFileExt($_FILES['ajax_upload']['name']);
		$date = date('y/m/d/');
		$name = $date . $realName;
		$bucket = setting('pf_video_s3_bucket');
		$post = [];

		if (!empty($_SERVER['HTTP_X_POST_FORM'])) {
			foreach (explode('&', $_SERVER['HTTP_X_POST_FORM']) as $posts) {
				$part = explode('=', $posts);
				if (empty($part[0])) {
					continue;
				}

				$post[$part[0]] = (isset($part[1]) ? $part[1] : '');
			}
		}

		if (isset($post['val[callback_module]']) && isset($post['val[callback_item_id]']))
		{
			if (\Phpfox::isModule($post['val[callback_module]']) &&
				\Phpfox::hasCallback($post['val[callback_module]'], 'checkPermission') &&
				!\Phpfox::callback($post['val[callback_module]'] . '.checkPermission', $post['val[callback_item_id]'], 'pf_video.share_videos')
			) {
				http_response_code(400);
				return [
					'error' => _p('You don\'t have permission to share videos on this page.')
				];
			}
		}

		$ext = '3gp, aac, ac3, ec3, flv, m4f, mov, mj2, mkv, mp3, mp4, mxf, ogg, ts, webm, wmv, avi';
		$file = \Phpfox_File::instance()->load('ajax_upload', array_map('trim', explode(',', $ext)), $file_size);
		if ($file === false) {
			http_response_code(400);
			return [
				'error' => implode('', \Phpfox_Error::get())
			];
		}

		if (!@move_uploaded_file($_FILES['ajax_upload']['tmp_name'], $path . $realName)) {
			http_response_code(400);
			return [
				'error' => _p('Unable to upload file due to a server error or restriction.')
			];
		}

		$s3 = new \S3(setting('pf_video_s3_key'), setting('pf_video_s3_secret'));
		$s3->putObjectFile($path . $realName, $bucket, $name, \S3::ACL_PUBLIC_READ);

		try {
			$zencoder = new \Services_Zencoder(setting('pf_video_key'));

			$params = [
				"input" => 's3://' . $bucket . '/' . $name,
				'notifications' => [
					'url' => url('/v/callback')
				],
				"outputs" => [
					[
						"label" => "webm",
						'format' => 'webm',
						'url' => 's3://' . $bucket . '/' . $date . $id . '.webm',
						'public' => true
					],
					[
						"label" => "mp4 high",
						'h264_profile' => 'high',
						'url' => 's3://' . $bucket . '/' . $date . $id . '.mp4',
						'public' => true,
						'thumbnails' => [
							'label' => 'thumb',
							'size' => '400x300',
							'base_url' => 's3://' . $bucket . '/' . $date . $id . '.png',
							'number' => 3
						]
					],
					[
						"label" => "ogg",
						'format' => 'ogg',
						'url' => 's3://' . $bucket . '/' . $date . $id . '.ogg',
						'public' => true,
					],
					[
						"label" => "mp4 low",
						'size' => '640x480',
						'url' => 's3://' . $bucket . '/' . $date . $id . '-low.mp4',
						'public' => true,
					]
				]
			];

			if (defined('APP_VIDEOS_LOCAL')) {
				unset($params['notifications']);
			}

			$encoding_job = $zencoder->jobs->create($params);

			$s_id = $storage->set('pf_video_' . $encoding_job->id, [
				'encoding_id' => $encoding_job->id,
				'path' => $date . $id,
				'user_id' => user()->id,
				'id' => $id,
				'ext' => \Phpfox_File::instance()->getFileExt($_FILES['ajax_upload']['name']),
				'module_id' => (isset($post['val[callback_module]']) ? $post['val[callback_module]'] : false),
				'module_item_id' => (isset($post['val[callback_item_id]']) ? $post['val[callback_item_id]'] : false),
			]);

			return [
				'upload' => true,
				'image' => setting('pf_video_s3_url') . $date . $id . '.png/frame_0001.png',
				'id' => $encoding_job->id
			];

		} catch (\Services_Zencoder_Exception $e) {
			http_response_code(400);
			return [
				'error' => $e->getMessage()
			];
		}
	});

	// Manage a Video
	new Route('/manage', function(Controller $controller) {
		auth()->isLoggedIn(true);

		$id = request()->get('id');
		$is_page = false;
        define('PHPFOX_APP_DETAIL_PAGE', 1);

		if (substr($id, 0, 2) == 'p-') {
			$id = str_replace('p-', '', $id);

			$feed = (new \Api\Feed())->get($id, 'PHPfox_Videos', true);
			$is_page = true;
		} else {
			$feed = (new \Api\Feed())->get($id);
		}

		$category_cache_key  = $is_page?'pf_video_pc': 'pf_video_c';

		if ($feed->user->id != user()->id  && !user()->isAdmin()) {
			error(_p('Unable to access this item.'));
		}

		title(_p('Managing:') . '' . $feed->content->caption);
		section(_p('Videos'), '/v');
		h1(_p('Managing:') . ' ' . $feed->content->caption, url('/v/manage', ['id' => ($is_page ? 'p-' : '') . $feed->id]));
		form()->assign([
			'privacy' => (isset($feed->privacy) ? $feed->privacy : 0)
		]);
		$valid = (new Validator())->rule('caption')->required();
		if ($valid->make()) {
			$val = (object) request()->get('val');


            if (isset($val->category_id)) {
                storage()->del($category_cache_key, $id);
                if ($val->category_id)
                {
                    storage()->set($category_cache_key, (int) $val->category_id, $id);
                }
            }

			(new \Api\Feed())->put($feed->id, [
				'privacy' => isset($val->privacy) ? $val->privacy : 0,
				'content' => [
					'caption' => text()->clean($val->caption, 150)
				]
			], $is_page);

			return url()->send('/v/play/' . ($is_page ? 'p-' : '' ) . $feed->id, [], _p('Video successfully updated!'));
		}

		$listing = ['0' => _p('None')];
		$categories = storage()->all('pf_video_category');
		foreach ($categories as $category) {
			$listing[$category->id] = _p($category->value);
		}

        $active = storage()->get($category_cache_key, $feed->id);



		if ($is_page) {
			$feed->id = 'p-' . $feed->id;
		}

		return render('manage.html', [
			'feed' => $feed,
			'listing' => $listing,
			'active' => (isset($active->value) ? $active->value : 0)
		]);
	});

	// View a video
	(new Route('/play/:id/*', function(Controller $controller, $id) use($storage, $sub_menu) {
		user('pf_video_view', '1', null, true);

		define('PHPFOX_APP_DETAIL_PAGE', 1);
		if (substr($id, 0, 2) == 'p-') {
			$id = str_replace('p-', '', $id);
			$feed = (new \Api\Feed())->get(['id' => $id, 'isSingle' => true, 'share_type_id' => 'v_pages', 'feed_table_prefix' => 'pages_'], 'PHPfox_Videos', true);
		} else {
			$feed = (new \Api\Feed())->get($id, 'PHPfox_Videos');
		}
		$video = $feed->content;
        if (auth()->isLoggedIn() && User_Service_Block_Block::instance()->isBlocked(null, $feed->user->id))
        {
            return url()->send('error.invalid');
        }

        //Check permission of this video
        if (\Phpfox::isModule('privacy'))
        {
            \Privacy_Service_Privacy::instance()->check('feed', $id, $feed->user->id, $feed->privacy);
        }
		(new \Api\Feed\Counter())->incr($id);

		$controller->title($video->caption);
		$is_page = false;
		if ($feed->module_id) {
			$page = \Phpfox::getService($feed->module_id)->getForView($feed->module_item_id);
			if(!\Phpfox::getService($feed->module_id)->hasPerm($page['page_id'], 'pf_video.view_browse_videos'))
			{
				return error(_p('Unable to view this item due to privacy settings.'));
			}

			if (\Phpfox::hasCallback($feed->module_id, 'getItem')) {
				$aCallback = \Phpfox::callback($feed->module_id . '.getItem', $page['page_id']);
				if ($aCallback && isset($aCallback['module_title'])) {
					section($aCallback['module_title'], '/'.$feed->module_id);
				}
			}
			section($page['title'], \Phpfox::getService($feed->module_id)->getUrl($page['page_id'], $page['title'], $page['vanity_url']));
			section(_p('Videos'), \Phpfox::getService($feed->module_id)->getUrl($page['page_id'], $page['title'], $page['vanity_url']) . 'v');
			h1($video->caption, '/v/play/p-' . $id);
			$is_page = true;
		} else {
			section(_p('Videos'), '/v');
			h1($video->caption, '/v/play/' . $id);
			subMenu('v', $sub_menu);
		}

		$category = false;
		$category_id = 0;
		$active = storage()->get('pf_video_c', $feed->id);
		if (isset($active->value)) {
			$category = storage()->getById($active->value);
			$category_id = $active->value;
		}

		if ($is_page) {
			$feed->id = 'p-' . $feed->id;
		}
		return $controller->render('view.html', [
			'feed' => $feed,
			'category' => (isset($category->value) ? \Phpfox::getSoftPhrase($category->value) : false),
			'category_id' => $category_id,
			'is_page' => $is_page
		]);
	}));

	new Route('/category/:id/:name', $index);
	new Route('/', $index);
});