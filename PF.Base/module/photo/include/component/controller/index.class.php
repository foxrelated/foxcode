<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: index.class.php 7255 2014-04-07 17:39:00Z Fern $
 */
class Photo_Component_Controller_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (Phpfox::getParam('photo.show_info_on_mouseover'))
		{
		    $this->template()->setHeader(array(
			'index.css' => 'module_photo',
			'index.js' => 'module_photo'
			));
		}

		if (defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW'))
		{
			$aUser = (!defined('PHPFOX_IS_PAGES_VIEW')) ? $this->getParam('aUser') : $this->getParam('aPage');
			$bShowPhotos = $this->request()->get('req3') != 'albums' || $this->request()->get('req4') != 'albums';

			if ($this->request()->get('req3') == '' || $this->request()->get('req4') == '')
			{
				$bShowPhotos = Phpfox::getParam('photo.in_main_photo_section_show') != 'albums';
			}

			if(defined('PHPFOX_IS_PAGES_VIEW'))
			{
				$this->template()->setHeader(array(
						'photo.css' => 'module_pages'
					)
				);
				if(empty($aUser['vanity_url']))
				{
                    $sStyle = defined('PHPFOX_PAGES_ITEM_TYPE') ? PHPFOX_PAGES_ITEM_TYPE : 'pages';
					$aUser['user_name'] = $sStyle .'.' . $aUser['page_id'];
				}
				else
				{
					$aUser['user_name'] = $aUser['vanity_url'];
				}
				$aUser['profile_page_id'] = 0;

				$aInfo = array(
					'total_albums' => Phpfox::callback('pages.getAlbumCount', $aUser['page_id']),
					'total_photos' => Phpfox::callback('pages.getPhotoCount', $aUser['page_id'])
				);
			}
			else
			{
				$aInfo = array(
					'total_albums' => Photo_Service_Album_Album::instance()->getAlbumCount($aUser['user_id']),
					'total_photos' => $aUser['total_photo']
				);
			}

			$bSpecialMenu = (!defined('PHPFOX_IS_AJAX_CONTROLLER'));
			$this->template()->assign(array(
			'bSpecialMenu' => $bSpecialMenu,
			'aInfo' => $aInfo,
			'bShowPhotos' => $bShowPhotos,
			'sLinkPhotos' => $this->url()->makeUrl($aUser['user_name'] . '.photo.photos'),
			'sLinkAlbums' => $this->url()->makeUrl($aUser['user_name'] . '.photo.albums'))
			);
		}
		else
		{
			$this->template()->assign(array('bSpecialMenu' => false));
		}

		if (!$this->request()->get('delete') && defined('PHPFOX_IS_PAGES_VIEW') && ($this->request()->get('req3') == 'albums' || $this->request()->get('req4') == 'albums'))
		{
			Phpfox::getComponent('photo.albums', array('bNoTemplate' => true), 'controller');
			return null;
		}

		if (
			( (defined('PHPFOX_IS_USER_PROFILE') )
			    || !defined('PHPFOX_IS_USER_PROFILE'))
			&& $this->request()->get('req3') != 'photos' && !in_array($this->request()->get('view'), array('my','photos', 'pending')) && !is_numeric($this->request()->get('req2'))
			&& Phpfox::getParam('photo.in_main_photo_section_show') == 'albums'
			&& !$this->request()->get('delete')
            && !$this->request()->get('search-id')
		    )
		{

		    Phpfox::getComponent('photo.albums', array('bNoTemplate' => true), 'controller');
		    return null;
		}

		$sAssert = $this->request()->get('req4', false);
		if (($this->request()->get('req3') == 'photos' || $this->request()->get('req3') == 'albums')
			&& $sAssert == false)
		{

		}
		else if (defined('PHPFOX_IS_USER_PROFILE') && ($sLegacyTitle = $this->request()->get('req3')) && !empty($sLegacyTitle))
		{
			if (($sLegacyPhoto = $this->request()->get('req4')) && !empty($sLegacyPhoto))
			{
				$aLegacyItem = Core_Service_Core::instance()->getLegacyItem(array(
						'field' => array('photo_id', 'title'),
						'table' => 'photo',
						'redirect' => 'photo',
						'title' => $sLegacyPhoto
					)
				);
			}
		}

		Phpfox::getUserParam('photo.can_view_photos', true);
		if ($this->request()->get('req2') == 'category')
		{
			$_SESSION['photo_category'] = $this->request()->get('req3');
			$this->template()->setHeader(array('<script type="text/javascript"> var sPhotoCategory = "' . $this->request()->get('req3') . '"; </script>'))
				->assign(array('sPhotoCategory' => $this->request()->get('req3')));
		}
		else
		{
			$_SESSION['photo_category'] = '';
		}
		$aParentModule = $this->getParam('aParentModule');

		if (($iRedirectId = $this->request()->getInt('redirect')) && ($aPhoto = Photo_Service_Photo::instance()->getForEdit($iRedirectId)))
		{
			if ($aPhoto['group_id'])
			{
				$aGroup = Phpfox::getService('group')->getGroup($aPhoto['group_id'], true);

				$this->url()->send('group', array($aGroup['title_url'], 'photo', 'view', $aPhoto['title_url']));
			}
			else
			{
				$this->url()->send($aPhoto['user_name'], array('photo', ($aPhoto['album_id'] ? $aPhoto['album_url'] : 'view'), $aPhoto['title_url']));
			}
		}

		if (($iRedirectAlbumId = $this->request()->getInt('aredirect')) && ($aAlbum = Photo_Service_Album_Album::instance()->getForEdit($iRedirectAlbumId)))
		{
			$this->url()->send($aAlbum['user_name'], array('photo'));
		}

		if (($iUnFeature = $this->request()->getInt('unfeature')) && Phpfox::getUserParam('photo.can_feature_photo'))
		{
			if (Photo_Service_Process::instance()->feature($iUnFeature, 0))
			{
				$this->url()->send('photo', null, _p('photo_successfully_unfeatured'));
			}
		}

		if(empty($aParentModule) && ($this->request()->get('req1') == 'pages'))
		{
			$aParentModule = array(
					'module_id' => 'pages',
					'item_id' => $this->request()->get('req2'),
					'url' => Pages_Service_Pages::instance()->getUrl($this->request()->get('req2'))
			);
			define('PHPFOX_IS_PAGES_VIEW', true);
			define('PHPFOX_IS_PAGES_ITEM_TYPE', 'pages');
		}

		if ($aParentModule === null && $this->request()->getInt('req2') > 0)
		{
			return Phpfox_Module::instance()->setController('photo.view');
		}

		if (($sLegacyTitle = $this->request()->get('req2')) && !empty($sLegacyTitle) && !is_numeric($sLegacyTitle))
		{
			if ((defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW')) && $sLegacyTitle == 'photo')
			{

			}
			else
			{
				if ($this->request()->get('req3') != '')
				{
					$sLegacyTitle = $this->request()->get('req3');
				}

				$aLegacyItem = Core_Service_Core::instance()->getLegacyItem(array(
						'field' => array('category_id', 'name'),
						'table' => 'photo_category',
						'redirect' => 'photo.category',
						'title' => $sLegacyTitle,
						'search' => 'name_url'
					)
				);
			}
		}

		$bIsUserProfile = false;
		if (defined('PHPFOX_IS_AJAX_CONTROLLER') || defined('PHPFOX_LOADING_DELAYED'))
		{
			if ($this->request()->get('profile_id', null) !== null)
			{
			    $aUser = User_Service_User::instance()->get($this->request()->get('profile_id'));
			    $bIsUserProfile = true;
			    $this->setParam('aUser', $aUser);
			}
			else if ($this->request()->get('req1', null) !== null)
			{
			    if (($aUser = User_Service_User::instance()->get($this->request()->get('req1'), false)))
			    {
					$bIsUserProfile = true;
					$this->setParam('aUser', $aUser);
			    }
			}
		}

		// Used to control privacy
		$bNoAccess = false;
		if (defined('PHPFOX_IS_USER_PROFILE'))
		{
			$bIsUserProfile = true;
			$aUser = $this->getParam('aUser');
			if (!User_Service_Privacy_Privacy::instance()->hasAccess($aUser['user_id'], 'photo.display_on_profile'))
			{
				$bNoAccess = true;
			}
		}

		if(isset($aUser) && $aUser['profile_page_id'] != 0)
		{
			$bIsUserProfile = false;

			$aParentModule = array(
					'module_id' => 'pages',
					'item_id' => $aUser['profile_page_id'],
					'url' => Pages_Service_Pages::instance()->getUrl($aUser['profile_page_id'])
			);
			if (!defined('PHPFOX_IS_PAGES_VIEW')) {
				define('PHPFOX_IS_PAGES_VIEW', true);
				define('PHPFOX_PAGES_ITEM_TYPE', 'pages');
			}
		}

		if(!isset($aUser) && defined('PHPFOX_IS_PAGES_VIEW'))
		{
			$aUser = $this->getParam('aUser');
		}

		$sCategory = null;
		$sPhotoUrl = ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'], 'photo') : ($aParentModule === null ? $this->url()->makeUrl('photo') : $aParentModule['url'] . 'photo/'));
		$this->setParam('sTagType', 'photo');
		$sView = $this->request()->get('view', false);

		if ($iDeleteId = $this->request()->get('delete'))
		{
			if (Photo_Service_Process::instance()->delete($iDeleteId))
			{
				$this->url()->forward($sPhotoUrl, _p('photo_successfully_deleted'));
			}
		}

		$aSort = array(
			'latest' => array('photo.photo_id', _p('latest')),
			'most-viewed' => array('photo.total_view', _p('most_viewed')),
			'most-talked' => array('photo.total_comment', _p('most_discussed'))
		);

		$aPhotoDisplays = Phpfox::getUserParam('photo.total_photos_displays');
        $aSearchParam = array(
            'type' => 'photo',
            'field' => 'photo.photo_id',
            'ignore_blocked' => true,
            'search_tool' => array(
                'table_alias' => 'photo',
                'search' => array(
                    'action' => $sPhotoUrl,
                    'default_value' => _p('search_photos'),
                    'name' => 'search',
                    'field' => 'photo.title'
                ),
                'sort' => $aSort,
                'show' => (array) $aPhotoDisplays
            )
        );
        if (!Phpfox::getUserParam('photo.can_search_for_photos')){
            unset($aSearchParam['search_tool']['search']);
        }
		$this->search()->set($aSearchParam);
		$aBrowseParams = array(
			'module_id' => 'photo',
			'alias' => 'photo',
			'field' => 'photo_id',
			'table' => Phpfox::getT('photo'),
			'hide_view' => array('pending', 'my')
		);

		$bIsMassEditUpload = false;
		$bRunPlugin = false;
		if ( ($sPlugin = Phpfox_Plugin::get('photo.component_controller_index_brunplugin1')) && ( eval($sPlugin) === false))
		{
			return false;
		}

		switch ($sView)
		{
			case 'pending':
				Phpfox::getUserParam('photo.can_approve_photos', true);
				$this->search()->setCondition('AND photo.view_id = 1');
				$this->template()->assign('bIsInApproveMode', true);
				break;
			case 'my':
				Phpfox::isUser(true);
				$this->search()->setCondition('AND photo.user_id = ' . Phpfox::getUserId());
				if ($this->request()->get('mode') == 'edit')
				{
					list($iAlbumCnt, $aAlbums) = Photo_Service_Album_Album::instance()->get('pa.user_id = ' . Phpfox::getUserId());
					$this->template()->assign('bIsEditMode', true);
					$this->template()->assign('aAlbums', $aAlbums);
					if (($aEditPhotos = $this->request()->get('photos')))
					{
						$sPhotoList = '';
						foreach ($aEditPhotos as $iPhotoId)
						{
							$iPhotoId = rtrim($iPhotoId, ',');
							if (empty($iPhotoId))
							{
								continue;
							}

							$sPhotoList .= (int) $iPhotoId . ',';
						}
						$sPhotoList = rtrim($sPhotoList, ',');
						if (!empty($sPhotoList))
						{
							$bIsMassEditUpload = true;
							$this->search()->setCondition('AND photo.photo_id IN(' . $sPhotoList . ')');
						}
					}
				}
				break;
			default:
				if ($bRunPlugin)
				{
					(($sPlugin = Phpfox_Plugin::get('photo.component_controller_index_plugin1')) ? eval($sPlugin) : false);
				}
				elseif ($bIsUserProfile)
				{
					$this->search()->setCondition('AND photo.view_id ' . ($aUser['user_id'] == Phpfox::getUserId() ? 'IN(0,2)' : '= 0') . ' AND photo.group_id = 0 AND photo.type_id = 0 AND photo.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Core_Service_Core::instance()->getForBrowse($aUser)) . ') AND photo.user_id = ' . (int) $aUser['user_id']);
				}
				else
				{
					if (defined('PHPFOX_IS_PAGES_VIEW'))
					{
						$this->search()->setCondition('AND photo.view_id = 0 AND photo.module_id = \'' . Phpfox_Database::instance()->escape($aParentModule['module_id']) . '\' AND photo.group_id = ' . (int) $aParentModule['item_id'] . ' AND photo.privacy IN(%PRIVACY%)');
					}
					else
					{
						$this->search()->setCondition('AND photo.view_id = 0 AND photo.group_id = 0 AND photo.type_id = 0 AND photo.privacy IN(%PRIVACY%)');
					}
				}
				break;
		}

		if ($this->request()->get('req2') == 'category')
		{
			$sCategory = $iCategory = $this->request()->getInt('req3');
			$sWhere = 'AND pcd.category_id = ' . (int) $sCategory;

			if (!is_int($iCategory))
			{
				$iCategory = Photo_Service_Category_Category::instance()->getCategoryId($sCategory);

			}

			// Get sub-categories
			$aSubCategories = Photo_Service_Category_Category::instance()->getForBrowse($iCategory);

			if (!empty($aSubCategories) && is_array($aSubCategories))
			{
				$aSubIds = Photo_Service_Category_Category::instance()->extractCategories($aSubCategories);
				if (!empty($aSubIds))
				{
					$sWhere = 'AND pcd.category_id IN (' . (int)$sCategory . ',' . join(',', $aSubIds) . ')';
				}
			}

			$this->search()->setCondition($sWhere);
			$this->setParam('hasSubCategories', true);
		}

		if ($this->request()->get('req2') == 'tag')
		{
			if (!defined('PHPFOX_GET_FORCE_REQ')) define('PHPFOX_GET_FORCE_REQ', true);
			if (($aTag = Tag_Service_Tag::instance()->getTagInfo('photo', $this->request()->get('req3'))))
			{
				$this->template()->setBreadCrumb(_p('topic') . ': ' . $aTag['tag_text'] . '', $this->url()->makeUrl('current'), true);

				$this->search()->setCondition('AND tag.tag_text = \'' . urldecode(Phpfox_Database::instance()->escape($aTag['tag_text'])) . '\'');
			}
		}

		if ($sView == 'featured')
		{
			$this->search()->setCondition('AND photo.is_featured = 1');
		}

		Photo_Service_Browse::instance()->category($sCategory);

		if (!Phpfox::getParam('photo.display_profile_photo_within_gallery')) {
			 $this->search()->setCondition('AND photo.is_profile_photo IN (0)');
		}

		$this->search()->setContinueSearch(true);
		$this->search()->browse()->params($aBrowseParams)->execute();

		if ($bNoAccess == false)
		{
			$aPhotos = $this->search()->browse()->getRows();
			$iCnt = $this->search()->browse()->getCount();
		}
		else
		{
			$aPhotos = array();
			$iCnt = 0;
		}


		foreach ($aPhotos as $aPhoto)
		{
			$this->template()->setMeta('keywords', $this->template()->getKeywords($aPhoto['title']));
		}

		$aPager = array(
				'page' => $this->search()->getPage(),
				'size' => $this->search()->getDisplay(),
				'count' => $this->search()->browse()->getCount()
			);
		if (Phpfox::getParam('photo.show_info_on_mouseover'))
		{
		    $aPager['ajax'] = 'photo.browse';
		}

		Phpfox_Pager::instance()->set($aPager);

		$this->template()->setTitle(($bIsUserProfile ? _p('full_name_s_photos', array('full_name' => $aUser['full_name'])) : _p('photos')))
			->setBreadCrumb(_p('photos'), $sPhotoUrl)
			->setMeta('keywords', Phpfox::getParam('photo.photo_meta_keywords'))
			->setMeta('description', Phpfox::getParam('photo.photo_meta_description'));

		if(defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW'))
		{
			$this->template()->setMeta('description', _p('site_title_has_a_total_of_total_photo_s', array('site_title' => $aUser['full_name'], 'total' => $iCnt)));
		}
		else
		{
			$this->template()->setMeta('description', _p('site_title_has_a_total_of_total_photo_s', array('site_title' => Phpfox::getParam('core.site_title'), 'total' => $iCnt)));
		}

		foreach ($aPhotos as $key => $photo) {
			$aPhotos[$key]['can_view'] = true;
			if ($photo['user_id'] != Phpfox::getUserId()) {
				if ($photo['mature'] == 1 && Phpfox::getUserParam(array(
							'photo.photo_mature_age_limit' => array(
								'>',
								(int)Phpfox::getUserBy('age')
							)
						)
					)
				) {
					// warning check cookie
					$aPhotos[$key]['can_view'] = false;
				} elseif ($photo['mature'] == 2 && Phpfox::getUserParam(array(
							'photo.photo_mature_age_limit' => array(
								'>',
								(int)Phpfox::getUserBy('age')
							)
						)
					)
				) {
					$aPhotos[$key]['can_view'] = false;
				}
			}
		}

		$this->template()->setPhrase(array(
					'loading'
				)
			)
			->setHeader('cache', array(
					'jquery/plugin/jquery.mosaicflow.min.js' => 'static_script',
					'photo.js' => 'module_photo'
				)
			)
			->assign(array(
					'aPhotos' => $aPhotos,
					'bIsAjax' => PHPFOX_IS_AJAX,
					'sPhotoUrl' => $sPhotoUrl,
					'sView' => $sView,
					'bIsMassEditUpload' => $bIsMassEditUpload,
					'iPhotosPerRow' => 3
				)
			);


		if ($aParentModule === null)
		{
            Photo_Service_Photo::instance()->buildMenu();
		}
		if (!empty($sCategory))
		{
			$aCategories = Photo_Service_Category_Category::instance()->getParentBreadcrumb($sCategory);
			$iCnt = 0;
			foreach ($aCategories as $aCategory)
			{
				$iCnt++;

				$this->template()->setTitle($aCategory[0]);
				$this->template()->setBreadCrumb($aCategory[0], $aCategory[1], ($iCnt === count($aCategories) ? true : false));
			}
		} else if ($this->request()->get('req2') == 'category' && isset($aPhoto) && isset($aPhoto['category_name']) && isset($aPhoto['category_id'])) {
            $sCatUrl = str_replace(' ', '-', strtolower($aPhoto['category_name']));
            $this->template()->setBreadCrumb($aPhoto['category_name'], $this->url()->makeUrl('photo.category.' . $aPhoto['category_id'] . '.'). $sCatUrl .'/');
        }

		$this->setParam('sCurrentCategory', $sCategory);

		$aModerationMenu = array (
			array(
				'phrase' => _p('delete'),
				'action' => 'delete'
			)
		);
		if ($sView == 'pending') {
			$aModerationMenu[] = array(
				'phrase' => _p('approve'),
				'action' => 'approve'
			);
		}

		$this->setParam('global_moderation', array(
				'name' => 'photo',
				'ajax' => 'photo.moderation',
				'menu' => $aModerationMenu
			)
		);


		$iStartCheck = 0;
		if (!empty($sCategory))
		{
			$iStartCheck = 5;
		}

		if (!defined('PHPFOX_ALLOW_ID_404_CHECK'))
		{
			$iAllowIds = uniqid();
			define('PHPFOX_ALLOW_ID_404_CHECK', $iAllowIds);
		}
		else
		{
			$iAllowIds = PHPFOX_ALLOW_ID_404_CHECK;
		}

		$aRediAllow = array('category', $iAllowIds);
		if (defined('PHPFOX_IS_USER_PROFILE') && PHPFOX_IS_USER_PROFILE)
		{
			$aRediAllow[] = 'photo';
		}
		$aCheckParams = array(
			'url' => $this->url()->makeUrl('photo'),
			'start' => $iStartCheck,
			'reqs' => array(
					'2' => $aRediAllow,
					'3' => $aRediAllow
				),
			'reserved' => array('mode', 'photos')
			);

		if (Phpfox::getParam('core.force_404_check') && !Core_Service_Redirect_Redirect::instance()->check404($aCheckParams))
		{
			return Phpfox_Module::instance()->setController('error.404');
		}

        //Special breadcrumb for pages
		if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')){
			if (Phpfox::hasCallback(PHPFOX_PAGES_ITEM_TYPE, 'checkPermission') && !Phpfox::callback(PHPFOX_PAGES_ITEM_TYPE . '.checkPermission', $aParentModule['item_id'], 'photo.view_browse_photos')) {
				$this->template()->assign(['aSearchTool' => []]);
				return Phpfox_Error::display(_p('Cannot display this section due to privacy.'));
			}
			$this->template()
				->clearBreadCrumb();
			$this->template()
				->setBreadCrumb(Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']), $aParentModule['url'])
				->setBreadCrumb(_p('photos'), $sPhotoUrl);
		}
    return null;
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('photo.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}
