<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

define('PHPFOX_IS_PAGES_VIEW', true);
define('PHPFOX_PAGES_ITEM_TYPE', 'pages');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Component
 * @version 		$Id: controller.class.php 103 2009-01-27 11:32:36Z Raymond_Benc $
 */
class Pages_Component_Controller_View extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::getUserParam('pages.can_view_browse_pages', true);

		$mId = $this->request()->getInt('req2');

		if (request()->segment(2) == 'add') {
			if (!defined('IS_GROUPS_MODULE')) {
                define('IS_GROUPS_MODULE', true);
            }

			return Phpfox_Module::instance()->setController('pages.add');
		}
		
		if (!($aPage = Pages_Service_Pages::instance()->getForView($mId)))
		{
			return Phpfox_Error::display(_p('the_page_you_are_looking_for_cannot_be_found'));
		}

		if (($this->request()->get('req3')) != '')
		{
			$this->template()->assign(array(
				'bRefreshPhoto' => true
			));
		}
        if (Phpfox::getUserParam('pages.can_moderate_pages') || $aPage['is_admin']) {

        } else {
            if ($aPage['view_id'] != '0') {
                return Phpfox_Error::display(_p('the_page_you_are_looking_for_cannot_be_found'));
            }
        }
		
		if ($aPage['view_id'] == '2')
		{
			return Phpfox_Error::display(_p('the_page_you_are_looking_for_cannot_be_found'));
		}		

		if (Phpfox::getUserBy('profile_page_id') <= 0 && Phpfox::isModule('privacy'))
		{
			Privacy_Service_Privacy::instance()->check('pages', $aPage['page_id'], $aPage['user_id'], $aPage['privacy'], (isset($aPage['is_friend']) ? $aPage['is_friend'] : 0));
		}		
		
		$bCanViewPage = true;
		$sCurrentModule = Phpfox_Url::instance()->reverseRewrite($this->request()->get((($this->request()->get('req1') == 'pages' || $this->request()->get('req1') == 'groups') ? 'req3' : 'req2')));
		
		Pages_Service_Pages::instance()->buildWidgets($aPage['page_id']);

		(($sPlugin = Phpfox_Plugin::get('pages.component_controller_view_build')) ? eval($sPlugin) : false);

		
		$this->setParam('aParentModule', array(			
				'module_id' => 'pages',
				'item_id' => $aPage['page_id'],
				'url' => Pages_Service_Pages::instance()->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url'])
			)
		);
		
		if (isset($aPage['is_admin']) && $aPage['is_admin'])
		{
			define('PHPFOX_IS_PAGE_ADMIN', true);
		}
		
		$sModule = $sCurrentModule;
		
		if (empty($sModule) && !empty($aPage['landing_page']))
		{
			$sModule = $aPage['landing_page'];
			$sCurrentModule = $aPage['landing_page'];
		}
		
		(($sPlugin = Phpfox_Plugin::get('pages.component_controller_view_assign')) ? eval($sPlugin) : false);

		$this->setParam('aPage', $aPage);
		
		$this->template()			
			->assign(array(
                'aPage' => $aPage,
                'sCurrentModule' => $sCurrentModule,
                'bCanViewPage' => $bCanViewPage,
                'iViewCommentId' => $this->request()->getInt('comment-id'),
                'bHasPermToViewPageFeed' => Pages_Service_Pages::instance()->hasPerm($aPage['page_id'], 'pages.view_browse_updates')
				)
			)
			->setHeader('cache', array(				
				'pages.js' => 'module_pages'
			)
		);
		if ($bCanViewPage
			&& $sModule
			&& Phpfox::isModule($sModule)
			&& Phpfox::hasCallback($sModule, 'getPageSubMenu')
			&& !$this->request()->getInt('comment-id'))
		{
			if (Phpfox::hasCallback($sModule, 'canViewPageSection') && !Phpfox::callback($sModule . '.canViewPageSection', $aPage['page_id']))
			{
				return Phpfox_Error::display(_p('unable_to_view_this_section_due_to_privacy_settings'));
			}
			
			$this->template()->assign('bIsPagesViewSection', true);
			$this->setParam('bIsPagesViewSection', true);
			$this->setParam('sCurrentPageModule', $sModule);

			Phpfox::getComponent($sModule . '.index', array('bNoTemplate' => true), 'controller');

			Phpfox_Module::instance()->resetBlocks();
		}
		elseif ($bCanViewPage
			&& !Pages_Service_Pages::instance()->isWidget($sModule)
			&& !$this->request()->getInt('comment-id')
			&& $sModule
            && Phpfox::isAppAlias($sModule)
        ) {

			if (Phpfox::hasCallback($sModule, 'canViewPageSection') && !Phpfox::callback($sModule . '.canViewPageSection', $aPage['page_id']))
			{
				return Phpfox_Error::display(_p('unable_to_view_this_section_due_to_privacy_settings'));
			}
			$app_content = Core\Event::trigger('pages_view_' . $sModule);

			Phpfox_Module::instance()->resetBlocks();

			event('lib_module_page_id', function($obj) use ($sModule) {
				$obj->id = 'pages_'.$sModule;
			});

			$this->template()->assign([
				'app_content' => $app_content
			]);

		}
		elseif ($bCanViewPage && $sModule && Pages_Service_Pages::instance()->isWidget($sModule) && !$this->request()->getInt('comment-id'))
		{
			define('PHPFOX_IS_PAGES_WIDGET', true);
			$this->template()->assign(array(
					'aWidget' => Pages_Service_Pages::instance()->getWidget($sModule)
				)
			);
		}
		else
		{
			$bCanPostComment = true;
			if ($sCurrentModule == 'pending')
			{
				$this->template()->assign('aPendingUsers', Pages_Service_Pages::instance()->getPendingUsers($aPage['page_id']));
				$this->setParam('global_moderation', array(
                    'name' => 'pages',
                    'ajax' => 'pages.moderation',
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
			}
			
			if (Pages_Service_Pages::instance()->isAdmin($aPage))
			{
				define('PHPFOX_FEED_CAN_DELETE', true);
			}
			
			if (Phpfox::getUserId())
			{
				$bIsBlocked = User_Service_Block_Block::instance()->isBlocked($aPage['user_id'], Phpfox::getUserId());
				if ($bIsBlocked)
				{
					$bCanPostComment = false;
				}
			}			
			
			if($sCurrentModule != 'info')
			{
				define('PHPFOX_IS_PAGES_IS_INDEX', true);
			}

			$this->setParam('aFeedCallback', array(
					'module' => 'pages',
					'table_prefix' => 'pages_',
					'ajax_request' => 'pages.addFeedComment',
					'item_id' => $aPage['page_id'],
					'disable_share' => ($bCanPostComment ? false : true),
					'feed_comment' => 'pages_comment'				
				)
			);			
			if (isset($aPage['text']) && !empty($aPage['text']))
			{
				$this->template()->setMeta('description', $aPage['text']);
			}
			$this->template()->setTitle($aPage['title'])
				->setEditor()
				->setHeader('cache', array(
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                    'index.css' => 'module_pages',
					)
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
		(($sPlugin = Phpfox_Plugin::get('pages.component_controller_view_clean')) ? eval($sPlugin) : false);
	}
}