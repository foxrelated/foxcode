<?php

namespace Apps\PHPfox_Groups\Controller;

defined('PHPFOX') or exit('NO DICE!');
use Phpfox;
use Phpfox_Component;
use Phpfox_Locale;
use Phpfox_Module;
use Phpfox_Pager;
use Phpfox_Plugin;
use User_Service_User;
use Core;


class IndexController extends Phpfox_Component
{
    public function process()
    {
        
        $bIsUserProfile = $this->getParam('bIsProfile');
        $aUser = [];
        if ($bIsUserProfile) {
            $aUser = $this->getParam('aUser');
        }

        user('pf_group_browse' , null, null, true);

        if (($iDeleteId = $this->request()->getInt('delete')) && Phpfox::getService('groups.process')->delete($iDeleteId)) {
            $this->url()->send('groups', [], _p('Group successfully deleted.'));
        }

        $sView = $this->request()->get('view');

        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $bIsProfile = true;
            $aUser = User_Service_User::instance()->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        } else {
            $bIsProfile = $this->getParam('bIsProfile');
            if ($bIsProfile === true) {
                $aUser = $this->getParam('aUser');
            }
        }

        if ($bIsProfile) {
            section(_p('Groups'), url('/' . $aUser['user_name'] . '/groups'));
        } else {
            section(_p('Groups'), url('/groups'));
        }

        $this->search()->set([
                'type'        => 'groups',
                'field'       => 'pages.page_id',
                'search_tool' => [
                    'table_alias' => 'pages',
                    'search'      => [
                        'action'        => ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'], ['groups', 'view' => $this->request()->get('view')]) : $this->url()->makeUrl('groups', ['view' => $this->request()->get('view')])),
                        'default_value' => _p('Search groups'),
                        'name'          => 'search',
                        'field'         => 'pages.title',
                    ],
                    'sort'        => [
                        'latest'     => ['pages.time_stamp', _p('Latest')],
                        'most-liked' => ['pages.total_like', _p('Most Popular')],
                    ],
                    'show'        => [10, 15, 20],
                ],
            ]
        );

        $aBrowseParams = [
            'module_id' => 'groups',
            'alias'     => 'pages',
            'field'     => 'page_id',
            'table'     => Phpfox::getT('pages'),
            'hide_view' => ['pending', 'my'],
        ];

        $aFilterMenu = [];
        if (!defined('PHPFOX_IS_USER_PROFILE')) {
            $aFilterMenu = [
                _p('All Groups') => '',
                _p('My Groups')   => 'my',
            ];

            if (!Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend') && !Phpfox::getUserBy('profile_page_id')) {
                $aFilterMenu[_p('Friends\' Groups')] = 'friend';
            }
            if (user('pf_group_moderate' , 0)) {
                $iPendingTotal = Core\Lib::appsGroup()->getPendingTotal();

                if ($iPendingTotal) {
                    $aFilterMenu[_p('Pending Groups')  . '<span class="pending">' . $iPendingTotal . '</span>' ] = 'pending';
                }
            }
        }
        $sView = trim($sView, '/');
        switch ($sView) {
            case 'my':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND pages.app_id = 0 AND pages.view_id IN(0,1) AND pages.user_id = ' . Phpfox::getUserId());
                break;
            case 'pending':
                Phpfox::isUser(true);
                if (user('pf_group_moderate')) {
                    $this->search()->setCondition('AND pages.app_id = 0 AND pages.view_id = 1');
                }
                break;
            case 'all':
                if ($bIsUserProfile) {
                    Phpfox_Module::instance()->setController('groups.all');
                }
                break;
            default:
                if (Phpfox::getUserParam('privacy.can_view_all_items')) {
                    $this->search()->setCondition('AND pages.app_id = 0 ');
                } else {
                    $this->search()->setCondition('AND pages.app_id = 0 AND pages.view_id = 0 AND pages.privacy IN(%PRIVACY%)');
                }
                break;
        }

        $this->template()->buildSectionMenu('groups', $aFilterMenu);

        //add button to add new group
        if (user('pf_group_add' , '0') == '1') {
            sectionMenu(_p('Add a Group'), url('/groups/add'));
        }

        $bIsValidCategory = false;

        if ($this->request()->get('req2') == 'category' && ($iCategoryId = $this->request()->getInt('req3')) && ($aType = Phpfox::getService('groups.type')->getById($iCategoryId))) {
            $bIsValidCategory = true;
            $this->setParam('iCategory', $iCategoryId);
            $sType = (\Core\Lib::phrase()->isPhrase($aType['name'])) ? _p($aType['name']) : Phpfox_Locale::instance()->convert($aType['name']);
            $this->template()->setBreadCrumb($sType, Phpfox::permalink('groups.category', $aType['type_id'], $sType) . ($sView ? 'view_' . $sView . '/' . '' : ''), true);
            $this->template()->assign('aType', $aType);
        }

        if ($this->request()->get('req2') == 'sub-category' && ($iSubCategoryId = $this->request()->getInt('req3')) && ($aCategory = Phpfox::getService('groups.category')->getById($iSubCategoryId))) {
            $bIsValidCategory = true;
            $this->setParam('iCategory', $aCategory['type_id']);
            $sTypeName = (\Core\Lib::phrase()->isPhrase($aCategory['type_name'])) ? _p($aCategory['type_name']) : Phpfox_Locale::instance()->convert($aCategory['type_name']);
            $this->template()->setBreadCrumb($sTypeName, Phpfox::permalink('groups.category', $aCategory['type_id'], $sTypeName) . ($sView ? 'view_' . $sView . '/' . '' : ''));
            $sCategoryName = (\Core\Lib::phrase()->isPhrase($aCategory['name'])) ? _p($aCategory['name']) : Phpfox_Locale::instance()->convert($aCategory['name']);
            $this->template()->setBreadCrumb($sCategoryName, Phpfox::permalink('groups.sub-category', $aCategory['category_id'], $sCategoryName) . ($sView ? 'view_' . $sView . '/' . '' : ''), true);
        }

        if (isset($aType['type_id'])) {
            $this->search()->setCondition('AND pages.type_id = ' . (int)$aType['type_id']);
        }

        if (isset($aType['category_id'])) {
            $this->search()->setCondition('AND pages.category_id = ' . (int)$aType['category_id']);
        } elseif (isset($aCategory['category_id'])) {
            $this->search()->setCondition('AND pages.category_id = ' . (int)$aCategory['category_id']);
        }

        if ($bIsUserProfile) {
            $this->search()->setCondition('AND pages.user_id = ' . (int)$aUser['user_id']);
            if ($aUser['user_id'] != Phpfox::getUserId() && !Phpfox::getUserParam('core.can_view_private_items'))
            {
                $this->search()->setCondition('AND pages.reg_method <> 2');
            }
        }

        $aPages = [];
        $aCategories = [];
        $bShowCategories = false;
        if ($this->search()->isSearch()) {
            $bIsValidCategory = true;
        }

        if ($bIsValidCategory) {
            $this->search()->setCondition(Phpfox::callback('groups.getExtraBrowseConditions', 'pages'));
            $this->search()->browse()->params($aBrowseParams)->execute(function (\Phpfox_Search_Browse $browse){
                $browse->database()->join(':pages_type', 'pages_type', 'pages_type.type_id = pages.type_id AND pages_type.item_type = 1');
            });
            $aPages = $this->search()->browse()->getRows();
            foreach ($aPages as $iKey => $aPage) {
                if (!isset($aPage['vanity_url']) || empty($aPage['vanity_url'])) {
                    $aPages[ $iKey ]['url'] = Phpfox::permalink('groups', $aPage['page_id'], $aPage['title']);
                } else {
                    $aPages[ $iKey ]['url'] = $aPage['vanity_url'];
                }
            }

            Phpfox_Pager::instance()->set(['page' => $this->search()->getPage(), 'size' => $this->search()->getDisplay(), 'count' => $this->search()->browse()->getCount()]);
        } else {
            $bShowCategories = true;
            $iLimit = $this->request()->get('show', 10);
            $aCategories = Phpfox::getService('groups.category')->getForBrowse(0, true, ($bIsProfile ? $aUser['user_id'] : null), $iLimit);
        }
        $iCountPage = 0;
        if (count($aCategories)) {
            foreach ($aCategories as $aCategory) {
                if (isset($aCategory['pages']) && is_array($aCategory['pages'])) {
                    $iCountPage += count($aCategory['pages']);
                }
            }
        }
        $this->template()->setHeader('cache', [
                'pages.js' => 'module_pages',
            ]
        )
            ->assign([
                    'sView'           => $sView,
                    'aPages'          => $aPages,
                    'aCategories'     => $aCategories,
                    'bShowCategories' => $bShowCategories,
                    'iCountPage'      => $iCountPage,
                ]
            );
        $this->setParam('global_moderation', [
                'name' => 'pages',
                'ajax' => 'PHPfox_Groups.pageModeration',
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


        $iStartCheck = 0;
        if ($bIsValidCategory == true) {
            $iStartCheck = 5;
        }
        $aRediAllow = ['category'];
        if (defined('PHPFOX_IS_USER_PROFILE') && PHPFOX_IS_USER_PROFILE) {
            $aRediAllow[] = 'groups';
        }
        $aCheckParams = [
            'url'   => $this->url()->makeUrl('groups'),
            'start' => $iStartCheck,
            'reqs'  => [
                '2' => $aRediAllow,
            ],
        ];

        if (Phpfox::getParam('core.force_404_check') && !\Core_Service_Redirect_Redirect::instance()->check404($aCheckParams)) {
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
        (($sPlugin = Phpfox_Plugin::get('groups.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}