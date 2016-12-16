<?php

namespace Apps\PHPfox_Groups\Controller;

use Pages_Service_Pages;
use Phpfox;
use Phpfox_Component;
use Phpfox_Module;
use Phpfox_Plugin;
use Privacy_Service_Privacy;
use Phpfox_Error;
use Phpfox_Url;
use User_Service_Block_Block;
use Core;

defined('PHPFOX') or exit('NO DICE!');

define('PHPFOX_IS_PAGES_VIEW', true);
define('PHPFOX_PAGES_ITEM_TYPE', 'groups');

class ViewController extends Phpfox_Component
{
    public function process()
    {
        user('pf_group_browse' , null, null, true);

        $mId = $this->request()->getInt('req2');

        if (!($aPage = Core\Lib::appsGroup()->getForView($mId))) {
            return Phpfox_Error::display(_p('The group you are looking for cannot be found.'));
        }

        if (($this->request()->get('req3')) != '') {
            $this->template()->assign([
                'bRefreshPhoto' => true,
            ]);
        }
        if (user('pf_group_moderate', '0') || $aPage['is_admin']) {

        } else {
            if ($aPage['view_id'] != '0') {
                return Phpfox_Error::display(_p('The group you are looking for cannot be found.'));
            }
        }

        if ($aPage['view_id'] == '2') {
            return Phpfox_Error::display(_p('The group you are looking for cannot be found.'));
        }

        if (Phpfox::getUserBy('profile_page_id') <= 0 && Phpfox::isModule('privacy')) {
            Privacy_Service_Privacy::instance()->check('groups', $aPage['page_id'], $aPage['user_id'], $aPage['privacy'], (isset($aPage['is_friend']) ? $aPage['is_friend'] : 0));
        }
        //Check group privacy
        if ($aPage['reg_method'] == 2 && !Phpfox::getService('groups')->isMember($aPage['page_id']) && !Phpfox::isAdmin()){
            if (!Phpfox::getService('groups')->isInvited($aPage['page_id'])){
                Phpfox_Url::instance()->send('privacy.invalid');
            }
        }
        $bCanViewPage = true;
        $sCurrentModule = Phpfox_Url::instance()->reverseRewrite($this->request()->get((($this->request()->get('req1') == 'groups') ? 'req3' : 'req2')));
    
        Core\Lib::appsGroup()->buildWidgets($aPage['page_id']);

        (($sPlugin = Phpfox_Plugin::get('groups.component_controller_view_build')) ? eval($sPlugin) : false);


        $this->setParam('aParentModule', [
                'module_id' => 'groups',
                'item_id'   => $aPage['page_id'],
                'url'       => Core\Lib::appsGroup()->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']),
            ]
        );

        if (isset($aPage['is_admin']) && $aPage['is_admin']) {
            defined('PHPFOX_IS_PAGE_ADMIN') or define('PHPFOX_IS_PAGE_ADMIN', true);
        }

        $sModule = $sCurrentModule;

        (($sPlugin = Phpfox_Plugin::get('groups.component_controller_view_assign')) ? eval($sPlugin) : false);

        $this->setParam('aPage', $aPage);

        $this->template()
            ->assign([
                    'aPage'                  => $aPage,
                    'sCurrentModule'         => $sCurrentModule,
                    'bCanViewPage'           => $bCanViewPage,
                    'iViewCommentId'         => $this->request()->getInt('comment-id'),
                    'bHasPermToViewPageFeed' => Core\Lib::appsGroup()->hasPerm($aPage['page_id'], 'groups.view_browse_updates'),
                ]
            );

        if ($bCanViewPage
            && $sModule
            && Phpfox::isModule($sModule)
            && Phpfox::hasCallback($sModule, 'getGroupSubMenu')
            && !$this->request()->getInt('comment-id')
        ) {
            if (Phpfox::hasCallback($sModule, 'canViewGroupSection') && !Phpfox::callback($sModule . '.canViewGroupSection', $aPage['page_id'])) {
                return Phpfox_Error::display(_p('Unable to view this section due to privacy settings.'));
            }

            $this->template()->assign('bIsPagesViewSection', true);
            $this->setParam('bIsPagesViewSection', true);
            $this->setParam('sCurrentPageModule', $sModule);

            Phpfox::getComponent($sModule . '.index', ['bNoTemplate' => true], 'controller');

            Phpfox_Module::instance()->resetBlocks();
        } elseif ($bCanViewPage
            && !Core\Lib::appsGroup()->isWidget($sModule)
            && !$this->request()->getInt('comment-id')
            && $sModule
            && Phpfox::isAppAlias($sModule)
        ) {

            if (Phpfox::hasCallback($sModule, 'canViewGroupSection') && !Phpfox::callback($sModule . '.canViewGroupSection', $aPage['page_id'])) {
                return Phpfox_Error::display(_p('Unable to view this section due to privacy settings.'));
            }

            $app_content = \Core\Event::trigger('groups_view_' . $sModule);

            Phpfox_Module::instance()->resetBlocks();

            event('lib_module_page_id', function ($obj) use ($sModule){
                $obj->id = 'groups_' . $sModule;
            });

            $this->template()->assign([
                'app_content' => $app_content,
            ]);

        } elseif ($bCanViewPage && $sModule && Core\Lib::appsGroup()->isWidget($sModule) && !$this->request()->getInt('comment-id')) {
            define('PHPFOX_IS_PAGES_WIDGET', true);
            $this->template()->assign([
                    'aWidget' => Core\Lib::appsGroup()->getWidget($sModule),
                ]
            );
        } else {
            $bCanPostComment = true;
            if ($sCurrentModule == 'pending') {
                $this->template()->assign('aPendingUsers', Phpfox::getService('groups')->getPendingUsers($aPage['page_id']));
                $this->setParam('global_moderation', [
                        'name' => 'groups',
                        'ajax' => 'PHPfox_Groups.moderation',
                        'menu' => [
                            [
                                'phrase' => _p('Delete'),
                                'action' => 'delete',
                            ],
                            [
                                'phrase' => _p('Approve'),
                                'action' => 'approve',
                            ],
                        ],
                    ]
                );
            }

            if (Core\Lib::appsGroup()->isAdmin($aPage)) {
               defined('PHPFOX_FEED_CAN_DELETE') or define('PHPFOX_FEED_CAN_DELETE', true);
            }

            if (Phpfox::getUserId()) {
                $bIsBlocked = User_Service_Block_Block::instance()->isBlocked($aPage['user_id'], Phpfox::getUserId());
                if ($bIsBlocked) {
                    $bCanPostComment = false;
                }
            }

            if ($sCurrentModule != 'info') {
                defined('PHPFOX_IS_PAGES_IS_INDEX') or define('PHPFOX_IS_PAGES_IS_INDEX', true);
            }

            $this->setParam('aFeedCallback', [
                    'module'        => 'groups',
                    'table_prefix'  => 'pages_',
                    'ajax_request'  => 'groups.addFeedComment',
                    'item_id'       => $aPage['page_id'],
                    'disable_share' => ($bCanPostComment ? false : true),
                    'feed_comment'  => 'groups_comment',
                ]
            );
            if (isset($aPage['text']) && !empty($aPage['text'])) {
                $this->template()->setMeta('description', $aPage['text']);
            }
            $this->template()->setTitle($aPage['title'])
                ->setEditor()
                ->setHeader('cache', [
                        'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                        'jquery/plugin/jquery.scrollTo.js'      => 'static_script',
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
        (($sPlugin = Phpfox_Plugin::get('groups.component_controller_view_clean')) ? eval($sPlugin) : false);
    }
}