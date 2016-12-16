<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Component
 * @version 		$Id: albums.class.php 7255 2014-04-07 17:39:00Z Fern $
 */
class Photo_Component_Controller_Albums extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::getUserParam('photo.can_view_photos', true);
		if (defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW'))
		{
			$aTplParam = array('bSpecialMenu' => true);
			if(defined('PHPFOX_IS_PAGES_VIEW'))
			{
				$aTplParam['bShowPhotos'] = false;
			}
		    $this->template()->assign($aTplParam);
		}
		else
		{		    
		    $this->template()->assign(array('bSpecialMenu' => false));
		}
		$aParentModule = $this->getParam('aParentModule');	
		
		if ($iDeleteId = $this->request()->getInt('delete'))
		{
			if (Photo_Service_Album_Process::instance()->delete($iDeleteId))
			{
				$this->url()->send('photo.albums', null, _p('photo_album_successfully_deleted'));
			}
		}		
		
		$bIsUserProfile = false;
		if (defined('PHPFOX_IS_AJAX_CONTROLLER'))
		{
			$bIsUserProfile = true;
			$aUser = User_Service_User::instance()->get($this->request()->get('profile_id'));
			$this->setParam('aUser', $aUser);
        } else{
            $aUser = [];
        }

		if (defined('PHPFOX_IS_USER_PROFILE'))
		{
			$bIsUserProfile = true;
			$aUser = $this->getParam('aUser');
		}
		
		$sPhotoUrl = ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name']. '.photo') : ($aParentModule === null ? $this->url()->makeUrl('photo', array('view' => $this->request()->get('view'))) : $aParentModule['url'] . 'photo/'));
		$sSearchPhotoUrl = ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name']. '.photo.albums') : ($aParentModule === null ? $this->url()->makeUrl('photo.albums', array('view' => $this->request()->get('view'))) : $aParentModule['url'] . 'photo/albums/'));
		
		$aBrowseParams = array(
			'module_id' => 'photo.album',
			'alias' => 'pa',
			'field' => 'album_id',
			'table' => Phpfox::getT('photo_album'),
			'hide_view' => array('pending', 'myalbums')
		);
        $aSearchParam = array(
            'type' => 'photo.album',
            'field' => 'pa.album_id',
            'ignore_blocked' => true,
            'search_tool' => array(
                'table_alias' => 'pa',
                'search' => array(
                    'action' => $sSearchPhotoUrl,
                    'default_value' => _p('search_photo_albums'),
                    'name' => 'search',
                    'field' => 'pa.name'
                ),
                'sort' => array(
                    'latest' => array('pa.time_stamp', _p('latest')),
                    'most-talked' => array('pa.total_comment', _p('most_discussed'))
                ),
                'show' => array(9, 12, 15)
            )
        );
        if (!Phpfox::getUserParam('photo.can_search_for_photos')){
            unset($aSearchParam['search_tool']['search']);
        }
		$this->search()->set($aSearchParam);
		
		if ($bIsUserProfile)
		{
			$this->search()->setCondition('AND pa.view_id ' . ($aUser['user_id'] == Phpfox::getUserId() ? 'IN(0,2)' : '= 0') . ' AND pa.group_id = 0 AND pa.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Core_Service_Core::instance()->getForBrowse($aUser)) . ') AND pa.user_id = ' . (int) $aUser['user_id']);
		}
		else
		{	
			if ($this->request()->get('view') == 'myalbums')
			{
				Phpfox::isUser(true);
				$this->search()->setCondition('AND pa.user_id = ' . Phpfox::getUserId());
			}
			else
			{
				$this->search()->setCondition('AND pa.view_id = 0 AND pa.privacy IN(%PRIVACY%) AND pa.total_photo > 0');
			}
		}	
		
		if ($aParentModule !== null && !empty($aParentModule['item_id']))
		{
			$this->search()->setCondition('AND pa.module_id = \'' . $aParentModule['module_id']. '\' AND pa.group_id = ' . (int) $aParentModule['item_id']);
		}
		else
		{
			$this->search()->setCondition("AND (pa.module_id IS NULL OR pa.module_id = '')");
		}

        if ($this->request()->get('view') != 'myalbums' && !Phpfox::getParam('photo.display_profile_photo_within_gallery')) {
            $this->search()->setCondition('AND pa.profile_id = 0');
            $this->search()->setCondition('AND pa.cover_id = 0');
        }
		
		$this->search()->browse()->params($aBrowseParams)->execute();
		
		$aAlbums = $this->search()->browse()->getRows();
		
		if (defined('PHPFOX_IS_USER_PROFILE'))
		{
			$aUser = $this->getParam('aUser');
			if (!User_Service_Privacy_Privacy::instance()->hasAccess($aUser['user_id'], 'photo.display_on_profile'))
			{
				$aAlbums = array();
			}
		}			
		
		$aPager = array(
			'page' => $this->search()->getPage(), 
			'size' => $this->search()->getDisplay(), 
			'count' => $this->search()->browse()->getCount()
		);

		Phpfox_Pager::instance()->set($aPager);
		
		if (Phpfox::getParam('photo.show_info_on_mouseover') && isset($aUser['use_timeline']) && $aUser['use_timeline'])
		{
		    $this->template()->setFullSite();
		} elseif (Phpfox::getParam('photo.show_info_on_mouseover'))
		{
			$this->template()
				->setHeader(array(
                    'index.css' => 'module_photo',
                    'index.js' => 'module_photo',
                )
			);
		}
		$this->template()
            ->clearBreadCrumb()
            ->setBreadCrumb(_p('photos'), $sPhotoUrl)
            ->setBreadCrumb(_p('albums'), null, false)
			->setHeader(array(
                'albums.css' => 'module_photo'
            ))
			->assign(array(
				'aAlbums' => $aAlbums
			)
		);	
		
		if ($aParentModule === null)
		{
            Photo_Service_Photo::instance()->buildMenu();
		}

		//Special breadcrumb for pages
		if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')){
			$this->template()
				->clearBreadCrumb();
			$this->template()
				->setBreadCrumb(Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']), $aParentModule['url'])
				->setBreadCrumb(_p('albums'), $sPhotoUrl);
		}
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('photo.component_controller_albums_clean')) ? eval($sPlugin) : false);
	}
}