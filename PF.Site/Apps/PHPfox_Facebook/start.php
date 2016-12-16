<?php

/**
 * Using the Event handler we add JS & CSS to the <head></head>
 */
new Core\Event([
	// event to attach to
	'lib_phpfox_template_getheader' => function(Phpfox_Template $Template) {
			if (!setting('m9_facebook_enabled')) {
				$Template->setHeader('<script>var Fb_Login_Disabled = true;</script>');
				$Template->setHeader('<style>.fb_login_go, #header_menu #js_block_border_user_login-block form > .table:first-of-type:before {display:none !important;} #header_menu #js_block_border_user_login-block .title { margin-bottom: 0px; }</style>');
			}
		}
]);

// Make sure the app is enabled
if (!setting('m9_facebook_enabled')) {
	return;
}

if (auth()->isLoggedIn() && setting('m9_facebook_require_email') && ($cached = storage()->get('fb_force_email_' . user()->id))
	&& request()->segment(1) != 'fb'
	&& request()->segment(2) != 'email'
	&& request()->segment(1) != 'logout'
	&& (request()->segment(1) != 'user' && request()->segment(1) != 'logout')
) {
	url()->send('/fb/email');
}

route('/fb/email', function() {
	auth()->membersOnly();

	if (request()->isPost()) {
		$val = request()->get('val');

		$validator = validator()->rule('email')->email();
		if ($validator->make()) {
			$users = db()->select('COUNT(*)')->from(':user')->where(['email' => $val['email']])->count();
			if ($users) {
				error(_p('Email is already in use.'));
			}

			db()->update(':user', ['email' => $val['email']], ['user_id' => user()->id]);
			storage()->del('fb_force_email_' . user()->id);

			url()->send('/', 'Thank you for adding your email!');
		}
	}

	section(_p('Active Email'), '/fb/email');

	$email = user()->email;
	if (substr($email, -3) == '@fb' || substr($email, -13) == '@facebook.com') {
		$email = '';
	}

	return view('email.html', [
		'email' => $email
	]);
});

// Use the FB SDK to set the apps ID & Secret
Facebook\FacebookSession::setDefaultApplication(setting('m9_facebook_app_id'), setting('m9_facebook_app_secret'));

// We override the main settings page since their account is connected to FB
$Url = new Core\Url();
if (Phpfox::isUser() && $Url->uri() == '/user/setting/' && substr(Phpfox::getUserBy('email'), -3) == '@fb') {
	(new Core\Route('/user/setting'))->run(function(\Core\Controller $Controller) {
		return $Controller->render('setting.html');
	});
}

/**
 * Controller for the FB login routine
 */
(new Core\Route('/fb/login'))->run(function(\Core\Controller $Controller) {
	$helper = new Facebook\FacebookRedirectLoginHelper($Controller->url->make('/fb/auth'));
	$loginUrl = $helper->getLoginUrl(['public_profile', 'email', 'user_website', 'user_location', 'user_hometown', 'user_birthday']);

	header('Location: ' . $loginUrl);
	exit;
});

/**
 * Auth routine for FB Connect. This is where we either create the new user or log them in if they are already a user.
 */
(new Core\Route('/fb/auth'))->run(function(\Core\Controller $Controller) {
	$helper = new Facebook\FacebookRedirectLoginHelper($Controller->url->make('/fb/auth'));
	$session = $helper->getSessionFromRedirect();

	if ($session) {
		$request = new Facebook\FacebookRequest($session, 'GET', '/me');
		$response = $request->execute();

		$user = $response->getGraphObject(Facebook\GraphUser::className());
		if ($user instanceof Facebook\GraphUser) {
			$Service = new \Apps\PHPfox_Facebook\Model\Service();
			$Service->create($user);

			$Controller->url->send('/');
		}
	}
});