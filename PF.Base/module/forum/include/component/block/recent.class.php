<?php
defined('PHPFOX') or exit('NO DICE!');

class Forum_Component_Block_Recent extends Phpfox_Component {
	public function process() {
		if ($this->request()->segment(2) == 'search') {
			return false;
		}

		$type = 'threads';
		if (Phpfox_Module::instance()->getFullControllerName() == 'forum.forum') {
			$title = _p('recent_posts');

			if (redis()->enabled()) {
				$threads = [];
				$rows = redis()->lrange('forum/recent/reply/' . $this->request()->segment(2), 0, 20);
				foreach($rows as $post_id) {
					$post = redis()->get('forum/reply/' . $post_id);
					$thread = redis()->get('forum/thread/' . $post->thread_id);
					$post->post_id = $post_id;
					$post->thread_title = $thread->title;

					$threads[] = array_merge(redis()->user($post->user_id), (array) $post);
				}

			} else {
				$threads = Forum_Service_Post_Post::instance()->getRecentForForum($this->request()->segment(2));
			}

			$type = 'posts';
		}
		else {
			$title = _p('recent_discussions');

			if (redis()->enabled()) {
				$threads = [];
				$rows = redis()->lrange('forum/recent/threads', 0, 20);
				foreach ($rows as $thread_id) {
					$thread = redis()->get('forum/thread/' . $thread_id);
					if (isset($thread->title)) {
						$thread->thread_id = $thread_id;
						$thread->view_id = 0;
						$thread->css_class = '';
						$thread->css_class_phrase = '';
						$thread->total_post = 0;
						$thread->total_view = 0;

						$threads[] = array_merge(redis()->user($thread->user_id), (array) $thread);
					}
				}

			} else {
				$ids = [];
				$forums = Forum_Service_Forum::instance()->getForums();
				foreach ($forums as $forum) {
					$ids[] = $forum['forum_id'];
					$childs = Forum_Service_Forum::instance()->id($forum['forum_id'])->getChildren();
					if ($childs) {
						foreach ($childs as $id) {
							$ids[] = $id;
						}
					}
				}

				if (empty($ids)) {
					$ids = array(0);
				}
                $aForumLists = array_map(function($id){return intval($id);}, $ids);
                $aForumLists = Forum_Service_Thread_Thread::instance()->getCanViewForumIdList($aForumLists);
				$iLimit = (Phpfox::getParam('forum.total_recent_discussions_display')) ? Phpfox::getParam('forum.total_recent_discussions_display') : 20;
				$cond[] = 'ft.forum_id IN(' . implode(',', $aForumLists) . ') AND ft.group_id = 0 AND ft.view_id >= 0';
				list($cnt, $threads) = Forum_Service_Thread_Thread::instance()
					->getRecentDiscussions($cond, 'ft.time_update DESC', 0, $iLimit, true, true);
			}
		}

		if (empty($threads)) return false;
		$this->template()->assign([
			'sHeader' => $title,
			'threads' => $threads,
			'type' => $type
		]);

		return 'block';
	}
}