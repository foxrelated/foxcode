<?php

namespace Api\Feed;

class Counter extends \Core\Api {
	public function incr($id) {
		$this->db->updateCounter('feed', 'total_view', 'feed_id', $id);
	}

	public function decr($id) {
		$this->db->updateCounter('feed', 'total_view', 'feed_id', $id, true);
	}
}