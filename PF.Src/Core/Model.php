<?php

namespace Core;

use User_Service_Auth;

class Model {
	/**
	 * @var \Core\Db
	 */
	protected $db;

	/**
	 * @var \Core\Cache
	 */
	protected $cache;

	/**
	 * @var \Core\Url
	 */
	protected $url;

	/**
	 * @var \Api\User\Object
	 */
	protected $active;

	protected $storage;

	public function __construct() {
		$this->db = new Db();
		$this->cache = new Cache();
		$this->active = (new \Api\User())->get(User_Service_Auth::instance()->getUserSession());
		$this->url = new Url();
		$this->storage = new Storage();
	}
}