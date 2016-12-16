<?php

namespace Core\View;
use User_Service_Auth;

class Environment extends \Twig_Environment {
	public function render($name, array $params = array()) {

		$params['ActiveUser'] = (new \Api\User())->get(User_Service_Auth::instance()->getUserSession());
		$params['isPager'] = (isset($_GET['page']) ? true : false);
		$params['Is'] = new \Core\Is();

		return $this->loadTemplate($name)->render($params);
	}
}