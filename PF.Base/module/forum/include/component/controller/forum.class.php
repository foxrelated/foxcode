<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Raymond Benc
 * @package          Module_Forum
 * @version          $Id: controller.class.php 103 2009-01-27 11:32:36Z Raymond_Benc $
 */
class Forum_Component_Controller_Forum extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{

		Phpfox::getUserParam('forum.can_view_forum', true);

		$aParentModule = $this->getParam('aParentModule');

		$bIsSearch = ($this->request()->get('search') ? true : false);
		$aCallback = $this->getParam('aCallback', null);
		$sView = $this->request()->get('view');
		$aSearchValues = [
			'user'       => '',
			'adv_search' => 0,
		];
		$aSearchForumId = [];

		$bIsTagSearch = false;
		$bIsModuleTagSearch = false;
		if ($this->request()->get('req2') == 'tag' && $this->request()->get('req3')) {
			$bIsSearch = true;
			$bIsTagSearch = true;
		}

		if ($this->request()->get('req2') == 'tag' && $this->request()->get('req5') && $this->request()->get('module')) {
			if ($aCallback = Phpfox::getService('group')->getGroup($this->request()->get('item'))) {
				$bIsSearch = true;
				$bIsTagSearch = true;
				$bIsModuleTagSearch = true;
				$aCallback['url_home'] = 'group.' . $aCallback['title_url'] . '.forum';
			}
		}

		$oSearch = Forum_Service_Forum::instance()->getSearchFilter($this->getParam('bIsSearchQuery', false), ($this->request()->get('forum_id') ? $this->request()->get('forum_id') : $this->request()->getInt('req2')), $aParentModule);

		$bIsAdvSearch = ($oSearch->get('adv_search') ? true : false);

		if (empty($module_id) && $oSearch->isSearch() && $this->request()->getInt('req2') == 'search') {
			$aIds = [];

			if ($bIsAdvSearch) {
				$aForums = ($oSearch->get('forum')) ? $oSearch->get('forum') : [];
				$aSearchForumId = array_unique((!empty($aForums) && is_array($aForums)) ? $aForums : []);
				foreach ($aSearchForumId as $iSearchForum) {
					if (!is_numeric($iSearchForum)) {
						continue;
					}
					$aIds[] = $iSearchForum;
				}
			} else {
				$aForums = ($this->request()->get('forum_id') ? Forum_Service_Forum::instance()->id($this->request()->get('forum_id'))->live()->getForums() : Forum_Service_Forum::instance()->live()->getForums());
				if ($this->request()->get('forum_id')) {
					$aIds[] = $this->request()->get('forum_id');
				}
				foreach ($aForums as $aForum) {
					if ($aForum['forum_id'])
						$aIds[] = $aForum['forum_id'];

					$aChilds = (array)Forum_Service_Forum::instance()->id($aForum['forum_id'])->getChildren();
					foreach ($aChilds as $iId) {
						if ($iId)
							$aIds[] = $iId;
					}
				}
			}

			$aIds = Forum_Service_Thread_Thread::instance()->getCanViewForumIdList($aIds);

			$oSearch->setCondition('AND ft.forum_id IN(' . implode(',', $aIds) . ')');

		} elseif (!empty($module_id)) {
			$oSearch->setCondition('AND ft.forum_id = 0 AND ft.group_id = ' . $aParentModule['item_id'] . ' AND ft.is_announcement = 0');
		}
		
		$iPage = $this->request()->getInt('page');
		$iPageSize = $oSearch->getDisplay();

		$sViewId = 'ft.view_id = 0';
		if ($aCallback === null) {
			$iForumId = $this->request()->getInt('req2');
			if (Phpfox::getUserParam('forum.can_approve_forum_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess($iForumId, 'approve_thread')) {
				$sViewId = 'ft.view_id >= 0';
			}
		}
		if ($aParentModule == null) {
			$iForumId = $this->request()->getInt('req2');

			$aForums = Forum_Service_Forum::instance()->live()->id($iForumId)->getForums();
			$aForum = Forum_Service_Forum::instance()->id($iForumId)->getForum();
			$this->template()->assign('isSubForumList', true);
		} else {
			$aForum = [];
			$aForums = [];
		}

		if (!$bIsSearch && $this->request()->get('view') != 'pending-post') {
			if ($aParentModule === null) {
				if (!isset($aForum['forum_id']) && empty($sView)) {
					return Phpfox_Error::display(_p('not_a_valid_forum'));
				}

				if (isset($aForum['forum_id'])) {
					$this->setParam('iActiveForumId', $aForum['forum_id']);
				}

				if (!empty($sView)) {
					switch ($sView) {
						case 'my-thread':
							$oSearch->setCondition('AND ft.user_id = ' . Phpfox::getUserId());
							break;
						case 'pending-thread':
							if (Phpfox::getUserParam('forum.can_approve_forum_thread')) {
								$sViewId = 'ft.view_id = 1';
							}
							break;
						default:

							break;
					}

					$oSearch->setCondition('AND ft.group_id = 0 AND ' . $sViewId . ' AND ft.is_announcement = 0');

					$bIsSearch = true;
				} else {
					$oSearch->setCondition('AND ft.forum_id = ' . $aForum['forum_id'] . ' AND ft.group_id = 0 AND ' . $sViewId . ' AND ft.is_announcement = 0');
				}
			} else {
				$oSearch->setCondition('AND ft.forum_id = 0 AND ft.group_id = ' . $aParentModule['item_id'] . ' AND ' . $sViewId . ' AND ft.is_announcement = 0');
			}

			// get the forums that we cant access
			$aForbiddenForums = Forum_Service_Forum::instance()->getForbiddenForums();
			if (!empty($aForbiddenForums)) {
				$oSearch->setCondition(' AND ft.forum_id NOT IN (' . implode(',', $aForbiddenForums) . ')');
			}
		} else {
			if ($aParentModule !== null) {
				$oSearch->setCondition('AND ft.forum_id = 0 AND ft.group_id = ' . $aParentModule['item_id'] . ' AND ' . $sViewId . ' AND ft.is_announcement = 0');
			}
		}


		if ($bIsAdvSearch) {
			if ($oSearch->get('user')) {
				$oSearch->search('like%', 'u.full_name', $oSearch->get('user'));
			}
			$aSearchValues = array_merge($aSearchValues, $oSearch->get());
		}

		if ($oSearch->get('result') || ($this->request()->get('view') == 'pending-post')) {
			if ($this->request()->get('view') == 'pending-post') {
				$bIsSearch = true;
				$bForceResult = true;
				$oSearch->setCondition('AND fp.view_id = 1');
			}

			if ($oSearch->get('search')) {
				$oSearch->search('like%', ['fp.title', 'fpt.text'], $oSearch->get('search'));
			}
			list($iCnt, $aThreads) = Forum_Service_Post_Post::instance()->callback($aCallback)->get($oSearch->getConditions(), $oSearch->getSort(), $oSearch->getPage(), $iPageSize);
		} else {
			if (($iDaysPrune = $oSearch->get('days_prune')) && $iDaysPrune != '-1') {
				$oSearch->setCondition('AND ft.time_stamp >= ' . (PHPFOX_TIME - ($iDaysPrune * 86400)));
			}

			if ($bIsTagSearch === true) {
				if ($bIsModuleTagSearch) {
					$oSearch->setCondition("AND ft.group_id = " . (int)$aCallback['group_id'] . " AND tag.tag_url = '" . Phpfox_Database::instance()->escape($this->request()->get('req5')) . "'");
				} else {
					$oSearch->setCondition("AND ft.group_id = 0 AND tag.tag_url = '" . Phpfox_Database::instance()->escape($this->request()->get('req3')) . "'");
				}
			}

			if ($oSearch->get('search')) {
				$oSearch->search('like%', ['ft.title'], $oSearch->get('search'));
			}

			list($iCnt, $aThreads) = Forum_Service_Thread_Thread::instance()->isSearch($bIsSearch)
				->isTagSearch($bIsTagSearch)
				->isNewSearch(($sView == 'new' ? true : false))
				->isSubscribeSearch(($sView == 'subscribed' ? true : false))
				->isModuleSearch($bIsModuleTagSearch)
				->get($oSearch->getConditions(), 'ft.order_id DESC, ' . $oSearch->getSort(), $oSearch->getPage(), $iPageSize);
		}


		$aAccess = Forum_Service_Forum::instance()->getUserGroupAccess($iForumId, Phpfox::getUserBy('user_group_id'));

		Phpfox_Pager::instance()->set(['page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt]);
		$this->template()->assign([
				'aThreads'      => $aThreads,
				'iSearchId'     => $this->request()->getInt('search-id'),
				'aCallback'     => $aParentModule,
				'sView'         => $sView,
				'aPermissions'  => $aAccess,
				'aSearchValues' => $aSearchValues,
				'sForumList'    => Forum_Service_Forum::instance()->getJumpTool(true, false, $aSearchForumId, !$bIsAdvSearch),
			]
		)
			->setHeader('cache', [
					'selector.js' => 'static_script',
				]
			);
		if ($bIsSearch && !isset($aForum['forum_id'])) {
			if (is_array($aCallback)) {
				$this->template()
					->setBreadCrumb(_p('pages'), $this->url()->makeUrl('pages'))
					->setBreadCrumb($aCallback['title'], $aCallback['url_home']);
			} else {
				$this->template()->setBreadCrumb(_p('forum'), $this->url()->makeUrl('forum'));
			}

			if ($bIsTagSearch) {
				$aTag = Tag_Service_Tag::instance()->getTagInfo('forum', ($bIsModuleTagSearch ? $this->request()->get('req5') : $this->request()->get('req3')));
				if (!empty($aTag['tag_text'])) {
					if ($bIsModuleTagSearch) {
						$this->template()->setBreadCrumb(_p('threads_tagged_with') . ': ' . $aTag['tag_text'], $this->url()->makeUrl('forum.tag.module_group.item_' . $this->request()->get('item') . '.' . $this->request()->get('req5')), true);
					} else {
						$this->template()->setBreadCrumb(_p('tags'), $this->url()->makeUrl('forum.tag'))->setBreadCrumb(_p('threads_tagged_with') . ': ' . $aTag['tag_text'], $this->url()->makeUrl('forum.tag.' . $this->request()->get('req3')), true);
					}
				}
			}
			$this->template()->assign([
					'bIsSearch'     => true,
					'bResult'       => (isset($bForceResult) ? true : $oSearch->get('result')),
					'aForumResults' => $oSearch->get('forum'),
					'bIsTagSearch'  => $bIsTagSearch,
				]
			);
		} else {
			if (Phpfox::getParam('forum.rss_feed_on_each_forum')) {
				if ($aParentModule === null) {
					$this->template()->setHeader('<link rel="alternate" type="application/rss+xml" title="' . _p('forum') . ': ' . Phpfox::getSoftPhrase($aForum['name']) . '" href="' . $this->url()->makeUrl('forum', ['rss', 'forum' => $aForum['forum_id']]) . '" />');
				} else {
					$this->template()->setHeader('<link rel="alternate" type="application/rss+xml" title="' . _p('group_forum') . ': ' . $aCallback['title'] . '" href="' . $this->url()->makeUrl('forum', ['rss', 'group' => $aCallback['group_id']]) . '" />');
				}
			}

			if ($aCallback === null && $aParentModule === null) {
				if (!$aForum['is_closed'] && Phpfox::getUserParam('forum.can_add_new_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess($aForum['forum_id'], 'add_thread')) {
					$this->template()->setMenu([
						'forum.forum' => [
							'menu_id'  => null,
							'module'   => 'forum',
							'url'      => $this->url()->makeUrl('forum.post.thread', ['id' => $aForum['forum_id']]),
							'var_name' => 'new_thread',
						],
					]);
				}
			} else {
				if ($aParentModule !== null) {
					$this->template()->setMenu([
						'forum.forum' => [
							'menu_id'  => null,
							'module'   => 'forum',
							'url'      => $this->url()->makeUrl('forum.post.thread', ['module' => $aParentModule['module_id'], 'item' => $aParentModule['item_id']]),
							'var_name' => 'new_thread',
						],
					]);
				}
			}
			if ($aParentModule === null) {
				if (!Forum_Service_Forum::instance()->hasAccess($aForum['forum_id'], 'can_view_forum')) {
					$this->url()->send('forum');
				}
				$this->template()->setTitle(Phpfox_Locale::instance()->convert(Phpfox::getSoftPhrase($aForum['name'])))
					->setBreadCrumb($aForum['breadcrumb'])
					->setBreadCrumb(Phpfox::getSoftPhrase($aForum['name']), $this->url()->permalink('forum', $aForum['forum_id'], Phpfox::getSoftPhrase($aForum['name'])), true)
					->assign([
							'bDisplayThreads' => true,
							'aAnnouncements'  => Forum_Service_Thread_Thread::instance()->getAnnoucements($iForumId),
							'aForums'         => $aForums,
							'aForumData'      => $aForum,
							'bHasCategory'    => false,
							'bIsSubForum'     => true,
							'bIsSearch'       => false,
							'bIsTagSearch'    => false,
						]
					);
			} else {
				$this->template()
					->setTitle(_p('discussions'))
					->assign([
							'bDisplayThreads' => true,
							'bHasCategory'    => false,
							'bIsSubForum'     => true,
							'bIsSearch'       => false,
							'bIsTagSearch'    => false,
							'aAnnouncements'  => Forum_Service_Thread_Thread::instance()->getAnnoucements(null, isset($aParentModule['item_id']) ? $aParentModule['item_id'] : 1),
						]
					);
			}
		}

		//Special breadcrumb for pages
		if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')){
			if (Phpfox::hasCallback(PHPFOX_PAGES_ITEM_TYPE, 'checkPermission') && !Phpfox::callback(PHPFOX_PAGES_ITEM_TYPE . '.checkPermission', $aParentModule['item_id'], 'forum.view_browse_forum')) {
				$this->template()->assign(['aSearchTool' => []]);
				return Phpfox_Error::display(_p('Cannot display this section due to privacy.'));
			}
			$this->template()
				->clearBreadCrumb();
			$this->template()
				->setBreadCrumb(Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']), $aParentModule['url'])
				->setBreadCrumb(_p('forum'), $aParentModule['url'] . 'forum/');
		}

		if ($bIsSearch && (isset($bForceResult) || $oSearch->get('result'))) {
			if (isset($bForceResult)) {
				$aModerationMenu = [
					[
						'phrase' => _p('delete'),
						'action' => 'delete',
					],
				];
				if ($this->request()->get('view') == 'pending-post') {
					$aModerationMenu[] = [
						'phrase' => _p('approve'),
						'action' => 'approve',
					];
				}
				$this->setParam('global_moderation', [
						'name' => 'forumpost',
						'ajax' => 'forum.postModeration',
						'menu' => $aModerationMenu,
					]
				);
			} else {
				$this->template()->assign('bIsPostSearch', true);
			}
		} else {
			$aModerationMenu = [
				[
					'phrase' => _p('delete'),
					'action' => 'delete',
				],
			];
			if ($this->request()->get('view') == 'pending-thread') {
				$aModerationMenu[] = [
					'phrase' => _p('approve'),
					'action' => 'approve',
				];
			}
			$this->setParam('global_moderation', [
					'name' => 'forum',
					'ajax' => 'forum.moderation',
					'menu' => $aModerationMenu,
				]
			);
		}

		return null;
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('forum.component_controller_forum_clean')) ? eval($sPlugin) : false);
	}
}
