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
 * @package  		Module_Music
 * @version 		$Id: index.class.php 7230 2014-03-26 21:14:12Z Fern $
 */
class Music_Component_Controller_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (defined('PHPFOX_IS_USER_PROFILE') && ($sLegacyTitle = $this->request()->get('req4')) && !empty($sLegacyTitle))
		{
            Core_Service_Core::instance()->getLegacyItem(array(
					'field' => array('song_id', 'title'),
					'table' => 'music_song',		
					'redirect' => 'music',
					'title' => $sLegacyTitle
				)
			);
		}
		
		Phpfox::getUserParam('music.can_access_music', true);
		
		$aParentModule = $this->getParam('aParentModule');	
		
		if ($this->request()->get('req2') == 'delete' && ($iDeleteId = $this->request()->getInt('id')) && ($mDeleteReturn = Music_Service_Process::instance()->delete($iDeleteId)))
		{
			if (is_bool($mDeleteReturn))
			{
				$this->url()->send('music', null, _p('song_successfully_deleted'));
			}
			else
			{
				$this->url()->forward($mDeleteReturn, _p('song_successfully_deleted'));
			}
		}

		$sView = $this->request()->get('view');
		
		if (($sRedirect = $this->request()->getInt('redirect')) && ($aSong = Music_Service_Music::instance()->getSong(Phpfox::getUserId(), $sRedirect, true)))
		{
			$this->url()->send($aSong['user_name'], array('music', ($aSong['album_id'] ? $aSong['album_url'] : 'view'), $aSong['title_url']));
		}
		
		if ($aParentModule === null && $this->request()->getInt('req2'))
		{
			return Phpfox_Module::instance()->setController('music.view');
		}

		if (defined('PHPFOX_IS_AJAX_CONTROLLER'))
		{
			$bIsProfile = true;
			$aUser = User_Service_User::instance()->get($this->request()->get('profile_id'));
			$this->setParam('aUser', $aUser);
		}
		else 
		{		
			$bIsProfile = $this->getParam('bIsProfile');	
			if ($bIsProfile === true)
			{
				$aUser = $this->getParam('aUser');
			}
		}			

		$this->template()->setTitle(($bIsProfile ? _p('fullname_s_songs', array('full_name' => $aUser['full_name'])) : _p('music')))->setBreadCrumb(_p('music'), ($bIsProfile ? $this->url()->makeUrl($aUser['user_name'], 'music') : $this->url()->makeUrl('music')));
		
		if ($aParentModule === null)
		{
            Music_Service_Music::instance()->getSectionMenu();
		}		
		
		$this->search()->set(array(
				'type' => 'music_song',
				'field' => 'm.song_id',
                'ignore_blocked' => true,
				'search_tool' => array(
					'table_alias' => 'm',
					'search' => array(
						'action' => (defined('PHPFOX_IS_PAGES_VIEW') ? $aParentModule['url'] . 'music/' : ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('music', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('music', array('view' => $this->request()->get('view'))))),
						'default_value' => _p('search_songs'),
						'name' => 'search',
						'field' => 'm.title'
					),
					'sort' => array(
						'latest' => array('m.time_stamp', _p('latest')),
						'most-viewed' => array('m.total_play', _p('most_viewed')),
						'most-liked' => array('m.total_like', _p('most_liked')),
						'most-talked' => array('m.total_comment', _p('most_discussed'))
					),
					'show' => array(10, 20, 30)
				)
			)
		);				
		
		$aBrowseParams = array(
			'module_id' => 'music.song',
			'alias' => 'm',
			'field' => 'song_id',
			'table' => Phpfox::getT('music_song'),
			'hide_view' => array('pending', 'my')				
		);

		$iGenre = $this->request()->getInt('req3');

		switch ($sView)
		{
			case 'my':
				Phpfox::isUser(true);
				$this->search()->setCondition('AND m.user_id = ' . Phpfox::getUserId());
				break;
			case 'pending':
				Phpfox::isUser(true);
				Phpfox::getUserParam('music.can_approve_songs', true);
				$this->search()->setCondition('AND m.view_id = 1');
				$this->template()->assign('bIsInPendingMode', true);
				break;
			default:
				if ($bIsProfile === true)
				{
					$this->search()->setCondition("AND m.view_id IN(" . ($aUser['user_id'] == Phpfox::getUserId() ? '0,1' : '0') . ") AND m.privacy IN(" . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Core_Service_Core::instance()->getForBrowse($aUser)) . ") AND m.user_id = " . $aUser['user_id'] . "");
				}
				else
				{				
					$this->search()->setCondition("AND m.view_id = 0 AND m.privacy IN(%PRIVACY%)");	
					if ($sView == 'featured')
					{
						$this->search()->setCondition('AND m.is_featured = 1');
					}
				}
				break;
		}
		
		if ($iGenre && ($aGenre = Music_Service_Genre_Genre::instance()->getGenre($iGenre)))
		{
			$this->search()->setCondition('AND m.genre_id = ' . (int) $iGenre);	
			$this->template()->setBreadCrumb(Phpfox::getSoftPhrase($aGenre['name']), $this->url()->permalink('browse.song.genre', $aGenre['genre_id'], Phpfox::getSoftPhrase($aGenre['name'])), true);
		}		
		
		if ($aParentModule !== null)
		{
			$this->search()->setCondition("AND m.module_id = '" . Phpfox_Database::instance()->escape($aParentModule['module_id']) . "' AND m.item_id = " . (int) $aParentModule['item_id']);
		}
		else
		{
			if ($sView != 'pending')
			{
				$this->search()->setCondition('AND m.item_id = 0');
			}
		}

		$this->search()->setContinueSearch(true);
		$this->search()->browse()->params($aBrowseParams)->execute();
		
		$aSongs = $this->search()->browse()->getRows();

		Phpfox_Pager::instance()->set(array('page' => $this->search()->getPage(), 'size' => $this->search()->getDisplay(), 'count' => $this->search()->browse()->getCount()));
		
		if ($sPlugin = Phpfox_Plugin::get('music.component_controller_music_index')){ eval($sPlugin); }
		
		$this->template()->setHeader('cache', array(
					'browse.css' => 'module_music'
				)
			)			
			->assign(array(
				'aSongs' => $aSongs,
				'sMusicView' => $sView
			)
		);
		
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
		else {
			$aModerationMenu[] = array(
						'phrase' => _p('feature'),
						'action' => 'feature'
			);
			$aModerationMenu[] = array(
						'phrase' => _p('un_feature'),
						'action' => 'un-feature'
			);
		}

		$this->setParam('global_moderation', array(
				'name' => 'musicsong',
				'ajax' => 'music.moderation',
				'menu' => $aModerationMenu
			)
		);

		//Special breadcrumb for pages
		if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')){
			if (Phpfox::hasCallback(PHPFOX_PAGES_ITEM_TYPE, 'checkPermission') && !Phpfox::callback(PHPFOX_PAGES_ITEM_TYPE . '.checkPermission', $aParentModule['item_id'], 'music.view_browse_music')) {
				$this->template()->assign(['aSearchTool' => []]);
				return Phpfox_Error::display(_p('Cannot display this section due to privacy.'));
			}
			$this->template()
				->clearBreadCrumb();
			$this->template()
				->setBreadCrumb(Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']), $aParentModule['url'])
				->setBreadCrumb(_p('music'), $aParentModule['url'] . 'music/');
		}
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('music.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}