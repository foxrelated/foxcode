<?php

/**
 * Disable mail module
 */
/*
new Core\Event('lib_module_get_blocks', function($obj) {
	if (isset($obj->_aModules['mail'])) {
		unset($obj->_aModules['mail']);
	}
});
*/

$package_id = 0;
$is_hosted = false;
$cache = cache('im/hosted');
if (request()->get('im-reset-cache')) {
	$cache->del();
}
if (!($host = $cache->get()) || defined('PF_IM_DEBUG_URL')) {
    if (defined('PHPFOX_TRIAL_MODE')) {

    } else {
        $home = new Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY);
        $hosted = $home->im();
        if (!isset($hosted->license_id)) {

        } else {
            $package_id = $hosted->package_id;
        }
    }

	$cache->set('im/hosted', [
		'package_id' => $package_id
	]);

	$host = cache('im/hosted')->get();
}

if (!empty($host['package_id']) && request()->segment(2) != 'hosting') {
	$is_hosted = true;

	define('PF_IM_PACKAGE_ID', $host['package_id']);

	if (PF_IM_PACKAGE_ID) {
		$url = (defined('PF_IM_DEBUG_URL') ? PF_IM_DEBUG_URL : 'https://im-node.phpfox.com/');
		setting()->set('pf_im_node_server', $url);

		event('lib_phpfox_template_getfooter', function(\Phpfox_Template $template) {
			$token = cache('im/host/token')->get(null, 1440);
			if (!$token) {
				$token = (new Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY))->im_token();
				if (!isset($token->token)) {
					url()->send('/im/hosting');
				}

				cache()->set('im/host/token', $token);
			}
			$token = (object) $token;

			if (isset($token->token)) {
				$template->footer .= '<div id="pf-im-host" data-token="' . $token->token . '"></div>';
			}
		});
	}
}

define('PF_IM_IS_HOSTED', $is_hosted);

/**
 * IM namespace : /im/*
 */
group('/im', function() {

	// No host
	route('/no-hosting', function() {
		auth()->isAdmin(true);

		storage()->del('im/no/host');
		storage()->set('im/no/host', 1);

		return j('.pf_im_hosting')->remove();
	});

	// AdminCP
	route('/admincp', function() {
		auth()->isAdmin(true);

		// storage()->del('im/no/host');

		$url = url()->make('/admincp/app', ['id' => 'PHPfox_IM', 'im-reset-cache' => '1']);
		return view('admincp.html', [
			'callback' => Core\Home::store() . 'pay/im_hosting?auth=' . PHPFOX_LICENSE_ID . ':' . PHPFOX_LICENSE_KEY . '&return_url=' . urlencode($url),
			'is_hosted' => PF_IM_IS_HOSTED,
			'package_id' => (defined('PF_IM_PACKAGE_ID') ? PF_IM_PACKAGE_ID : 0),
			'no_hosting' => storage()->get('im/no/host')
		]);
	});

	route('/link', function() {
		if (($val = request()->get('val'))) {
			$link = \Link_Service_Link::instance()->getLink($val['url']);
			if (!isset($link['link'])) {
				error(_p('Unable to attach this link.'));
			}

			if (empty($link['title'])) {
				error(_p('Unable to attach a valid link with this URL.'));
			}

			$id = 'im_attachment_' . uniqid();
			storage()->set($id, $link);

			return [
				'run' => 'im_attachment(\'' . $id . '\', ' . json_encode($link) . ');'
			];
		}

		return view('link.html', [
			'thread_id' => request()->get('thread_id')
		]);
	});

	// IM Hosting Failed
	route('/hosting', function() {
		return view('hosting.html');
	});

	// IM in popup mode
	route('/popup', function() {
		Core\View::$template = 'blank';

        $image = Phpfox_Image_Helper::instance()->display([
            'user' => Phpfox::getUserBy(),
            'suffix' => '_50_square'
        ]);

        $imageUrl = Phpfox_Image_Helper::instance()->display([
            'user' => Phpfox::getUserBy(),
            'suffix' => '_50_square',
            'return_url' => true
        ]);

        $image = htmlspecialchars($image);
        $image = str_replace(['<', '>'], ['&lt;', '&gt;'], $image);

        $sticky_bar = '<div id="auth-user" data-image-url="' . str_replace("\"", '\'', $imageUrl) . '" data-user-name="' . Phpfox::getUserBy('user_name') . '" data-id="' . Phpfox::getUserId() . '" data-name="' . Phpfox::getUserBy('full_name') . '" data-image="' . $image . '"></div>';

		return render('popup.html', [
            'sticky_bar' => $sticky_bar
        ]);
	});

	route('/failed', function() {
		h1('Messenger', '#');

		return render('failed.html');
	});

	// Load friends
	route('/friends', function() {

		$friends = (new Api\Friend())->get([
			'limit' => 1000
		]);

		return render('friends.html', [
			'friends' => $friends
		]);
	});

	route('/panel', function() {
		$cache = [];
		$users = request()->get('users');
		foreach (explode(',', $users) as $user) {
			if (empty($user)) {
				continue;
			}
			$cache[$user] = true;
		}

		$threads = [];
		foreach ($cache as $id => $value) {
			$u = (new \Api\User())->get($id);
			$threads[$id] = $u;
		}

		return $threads;
	});

	route('/conversation', function() {
		$user = null;
		$listing = null;

		if (!request()->get('listing_id') && Phpfox::isModule('friend') && !Friend_Service_Friend::instance()->isFriend(user()->id, request()->get('user_id'))) {
			return [
				'error' => 'not_friends'
			];
		}

		if (request()->get('listing_id')) {
			$listing = Marketplace_Service_Marketplace::instance()->getListing(request()->get('listing_id'));
		}

		return [
			'user' => (new \Api\User())->get(request()->get('user_id')),
			'listing' => $listing
		];
	});
});