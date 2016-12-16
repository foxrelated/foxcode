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
 * @version 		$Id: index.class.php 5948 2013-05-24 08:26:41Z Miguel_Espinoza $
 */
class Pages_Component_Controller_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$bIsUserProfile = $this->getParam('bIsProfile');
		$aUser = [];
		if ($bIsUserProfile)
		{
			$aUser = $this->getParam('aUser');
		}
		Phpfox::getUserParam('pages.can_view_browse_pages', true);

		if ($this->request()->getInt('req2') > 0)
		{
			return Phpfox_Module::instance()->setController('pages.view');
		}

		if (($iDeleteId = $this->request()->getInt('delete')) && Pages_Service_Process::instance()->delete($iDeleteId))
		{
			$this->url()->send('pages', array(), _p('page_successfully_deleted'));
		}
		
		$sView = $this->request()->get('view');
		
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
		
        if ($bIsProfile)
        {
            $this->template()
                    ->setTitle(_p('full_name_s_pages', array('full_name' => $aUser['full_name'])))
                    ->setBreadCrumb(_p('pages'), $this->url()->makeUrl($aUser['user_name'], array('pages')));
        }
        else
        {
            $this->template()
                    ->setTitle(_p('pages'))
                    ->setBreadCrumb(_p('pages'), $this->url()->makeUrl('pages'));
        }

		$this->search()->set(array(
				'type' => 'pages',
				'field' => 'pages.page_id',				
				'search_tool' => array(
					'table_alias' => 'pages',
					'search' => array(
						'action' => ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('pages', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('pages', array('view' => $this->request()->get('view')))),
						'default_value' => _p('search_pages'),
						'name' => 'search',
						'field' => 'pages.title'
					),
					'sort' => array(
						'latest' => array('pages.time_stamp', _p('latest')),
						'most-liked' => array('pages.total_like', _p('most_liked'))						
					),
					'show' => array(10, 15, 20)
				)
			)
		);
		
		$aBrowseParams = array(
			'module_id' => 'pages',
			'alias' => 'pages',
			'field' => 'page_id',
			'table' => Phpfox::getT('pages'),
			'hide_view' => array('pending', 'my')				
		);			
		
		$aFilterMenu = array();
		if (!defined('PHPFOX_IS_USER_PROFILE'))
		{
			$aFilterMenu = array(
				_p('all_pages') => '',
				_p('my_pages') => 'my'
			);
			
			if (!Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend') && !Phpfox::getUserBy('profile_page_id'))
			{
				$aFilterMenu[_p('friends_pages')] = 'friend';
			}	
			
			if (Phpfox::getUserParam('pages.can_moderate_pages'))
			{
				$iPendingTotal = Pages_Service_Pages::instance()->getPendingTotal();
				
				if ($iPendingTotal)
				{
					$aFilterMenu['' . _p('pending_pages') . '<span class="pending">' . $iPendingTotal . '</span>'] = 'pending';
				}
			}				
		}
    $sView = trim($sView, '/');
		switch ($sView)
		{
			case 'my':
				Phpfox::isUser(true);
				$this->search()->setCondition('AND pages.app_id = 0 AND pages.view_id IN(0,1) AND pages.user_id = ' . Phpfox::getUserId());
				break;
			case 'pending':
				Phpfox::isUser(true);
				if (Phpfox::getUserParam('pages.can_moderate_pages'))
				{
					$this->search()->setCondition('AND pages.app_id = 0 AND pages.view_id = 1');
				}				
				break;
      case 'all':
        if ($bIsUserProfile) {
          Phpfox_Module::instance()->setController('pages.all');
        }
        break;
			default:
				if (Phpfox::getUserParam('privacy.can_view_all_items'))
				{
					$this->search()->setCondition('AND pages.app_id = 0 ');  
				}
				else
				{
				    $this->search()->setCondition('AND pages.app_id = 0 AND pages.view_id = 0 AND pages.privacy IN(%PRIVACY%)');
				}
				break;
		}		
		
		 $this->template()->buildSectionMenu('pages', $aFilterMenu);
		$bIsValidCategory = false;
		
		if ($this->request()->get('req2') == 'category' && ($iCategoryId = $this->request()->getInt('req3')) && ($aType = Pages_Service_Type_Type::instance()->getById($iCategoryId)))
		{
			$bIsValidCategory = true;
			$this->setParam('iCategory', $iCategoryId);
            $sType = (Core\Lib::phrase()->isPhrase($aType['name'])) ? _p($aType['name']) : Phpfox_Locale::instance()->convert($aType['name']);
			$this->template()->setBreadCrumb($sType, Phpfox::permalink('pages.category', $aType['type_id'], $sType) . ($sView ? 'view_' . $sView . '/' . '' : ''), true);
            $this->template()->assign('aType', $aType);
		}
		
		if ($this->request()->get('req2') == 'sub-category' && ($iSubCategoryId = $this->request()->getInt('req3')) && ($aCategory = Pages_Service_Category_Category::instance()->getById($iSubCategoryId)))
		{
			$bIsValidCategory = true;
			$this->setParam('iCategory', $aCategory['type_id']);
            $sTypeName = (Core\Lib::phrase()->isPhrase( $aCategory['type_name'])) ? _p( $aCategory['type_name']) :  Phpfox_Locale::instance()->convert($aCategory['type_name']);
			$this->template()->setBreadCrumb($sTypeName, Phpfox::permalink('pages.category', $aCategory['type_id'], $sTypeName) . ($sView ? 'view_' . $sView . '/' . '' : ''));
            $sCategoryName = (Core\Lib::phrase()->isPhrase($aCategory['name'])) ? _p( $aCategory['name']) :  Phpfox_Locale::instance()->convert($aCategory['name']);
			$this->template()->setBreadCrumb($sCategoryName, Phpfox::permalink('pages.sub-category', $aCategory['category_id'], $sCategoryName) . ($sView ? 'view_' . $sView . '/' . '' : ''), true);
		}
		
		if (isset($aType['type_id']))
		{
			$this->search()->setCondition('AND pages.type_id = ' . (int) $aType['type_id']);
		}
		
		if (isset($aType['category_id']))
		{
			$this->search()->setCondition('AND pages.category_id = ' . (int) $aType['category_id']);
		}
		elseif	(isset($aCategory['category_id']))
		{
			$this->search()->setCondition('AND pages.category_id = ' . (int) $aCategory['category_id']);
		}		
		
		if ($bIsUserProfile)
		{
			$this->search()->setCondition('AND pages.user_id = ' . (int) $aUser['user_id']);
		}

		$aPages = [];
		$aCategories = [];
		$bShowCategories = false;
		if ($this->search()->isSearch()) {
			$bIsValidCategory = true;
		}

		if ($bIsValidCategory) {
			$this->search()->browse()->params($aBrowseParams)->execute(function(\Phpfox_Search_Browse $browse) {
				$browse->database()->join(':pages_type', 'pages_type', 'pages_type.type_id = pages.type_id AND pages_type.item_type = 0');
            });
			$aPages = $this->search()->browse()->getRows();
            
			foreach ($aPages as $iKey => $aPage)
			{
				if (!isset($aPage['vanity_url']) || empty($aPage['vanity_url']))
				{
					$aPages[$iKey]['url'] = Phpfox::permalink('pages', $aPage['page_id'], $aPage['title']);
				}
				else
				{
					$aPages[$iKey]['url'] = $aPage['vanity_url'];
				}
			}

			Phpfox_Pager::instance()->set(array('page' => $this->search()->getPage(), 'size' => $this->search()->getDisplay(), 'count' => $this->search()->browse()->getCount()));
		}
		else {
			$bShowCategories = true;
			$iLimit = $this->request()->get('show', 10);
			$aCategories = Pages_Service_Category_Category::instance()->getForBrowse(0, true, ($bIsProfile ? $aUser['user_id'] : null), $iLimit);
		}
        $iCountPage = 0;
        if (count($aCategories)){
            foreach ($aCategories as $aCategory){
                if (isset($aCategory['pages']) && is_array($aCategory['pages'])){
                    $iCountPage += count($aCategory['pages']);
                }
            }
        }
		$this->template()->setHeader('cache', array(
					'pages.js' => 'module_pages'
				)
			)
			->assign(array(
					'sView' => $sView,
					'aPages' => $aPages,
					'aCategories' => $aCategories,
					'bShowCategories' => $bShowCategories,
                    'is_group' => 0,
					'iCountPage' => $iCountPage
				)
			);
			$this->setParam('global_moderation', array(
				'name' => 'pages',
				'ajax' => 'pages.pageModeration',
				'menu' => array(
					array(
						'phrase' => _p('delete'),
						'action' => 'delete'
					),
					array(
						'phrase' => _p('approve'),
						'action' => 'approve'
					)					
				)
			)
		);
				
				
		$iStartCheck = 0;
		if ($bIsValidCategory == true)
		{
			$iStartCheck = 5;
		}
		$aRediAllow = array('category');
		if (defined('PHPFOX_IS_USER_PROFILE') && PHPFOX_IS_USER_PROFILE)
		{
			$aRediAllow[] = 'pages';
		}
		$aCheckParams = array(
			'url' => $this->url()->makeUrl('pages'),
			'start' => $iStartCheck,
			'reqs' => array(
					'2' => $aRediAllow
				)
			);
		
		if (Phpfox::getParam('core.force_404_check') && !Core_Service_Redirect_Redirect::instance()->check404($aCheckParams))
		{
			return Phpfox_Module::instance()->setController('error.404');
		}
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('pages.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}