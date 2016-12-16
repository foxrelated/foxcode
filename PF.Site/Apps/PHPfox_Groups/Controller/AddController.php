<?php

namespace Apps\PHPfox_Groups\Controller;

use Core;
use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');


class AddController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::isUser(true);
        user('pf_group_add', null, null, true);
    
        Core\Lib::appsGroup()->setIsInPage();

        $bIsEdit = false;
        $bIsNewPage = false;
        $sStep = $this->request()->get('req3');
        $aPage = [];
        if (($iEditId = $this->request()->getInt('id')) && ($aPage = Core\Lib::appsGroup()->getForEdit($iEditId))) {
            $bIsEdit = true;
            $this->template()->assign('aForms', $aPage);

            $aMenus = [
                'detail' => _p('Details'),
                'info'   => _p('Info'),
            ];

            if (!$aPage['is_app']) {
                $aMenus['photo'] = _p('Photo');
            }
            $aMenus['permissions'] = _p('Permissions');
            if (Phpfox::isModule('friend') && Phpfox::getUserBy('profile_page_id') == 0) {
                $aMenus['invite'] = _p('Invite');
            }
            if (!$bIsNewPage) {
                $aMenus['url'] = _p('Url');
                $aMenus['admins'] = _p('Admins');
                $aMenus['widget'] = _p('Widgets');
            }

            if ($bIsNewPage) {
                $iCnt = 0;
                foreach ($aMenus as $sMenuName => $sMenuValue) {
                    $iCnt++;
                    $aMenus[ $sMenuName ] = _p('Step count', ['count' => $iCnt]) . ': ' . $sMenuValue;
                }
            }


            $this->template()->buildPageMenu('js_groups_block',
                $aMenus,
                [
                    'link'   => Core\Lib::appsGroup()->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']),
                    'phrase' => ($bIsNewPage ? _p('Skip view this page') : _p('View this page')),
                ]
            );

            if (($aVals = $this->request()->getArray('val'))) {
                if (Phpfox::getService('groups.process')->update($aPage['page_id'], $aVals, $aPage)) {
                    if ($bIsNewPage && $this->request()->getInt('action') == '1') {
                        switch ($sStep) {
                            case 'invite':
                                if (Phpfox::isModule('friend')) {
                                    $this->url()->send('groups.add.url', ['id' => $aPage['page_id'], 'new' => '1']);
                                }
                                break;
                            case 'permissions':
                                $this->url()->send('groups.add.invite', ['id' => $aPage['page_id'], 'new' => '1']);
                                break;
                            case 'photo':
                                $this->url()->send('groups.add.permissions', ['id' => $aPage['page_id'], 'new' => '1']);
                                break;
                            case 'info':
                                $this->url()->send('groups.add.photo', ['id' => $aPage['page_id'], 'new' => '1']);
                                break;
                            default:
                                $this->url()->send('groups.add.info', ['id' => $aPage['page_id'], 'new' => '1']);
                                break;
                        }
                    }

                    $aNewPage = Core\Lib::appsGroup()->getForEdit($aPage['page_id']);

                    $this->url()->forward(Core\Lib::appsGroup()->getUrl($aNewPage['page_id'], $aNewPage['title'], $aNewPage['vanity_url']));
                }
            }
        }

        $this->template()->setTitle(($bIsEdit ? '' . _p('Editing Group') . ': ' . $aPage['title'] : _p('Creating a Group')))
            ->setBreadCrumb(_p('Groups'), $this->url()->makeUrl('groups'))
            ->setBreadCrumb(($bIsEdit ? '' . _p('Editing Group') . ': ' . $aPage['title'] : _p('Creating a Group')), $this->url()->makeUrl('groups.add', ['id' => $iEditId]), true)
            ->setPhrase([
                    'select_a_file_to_upload',
                ]
            )
            ->setHeader([
                    'privacy.css' => 'module_user',
                    'progress.js' => 'static_script',
                ]
            )
            ->setHeader(['<script type="text/javascript">$Behavior.groupsProgressBarSettings = function(){ if ($Core.exists(\'#js_groups_block_customize_holder\')) { oProgressBar = {holder: \'#js_groups_block_customize_holder\', progress_id: \'#js_progress_bar\', uploader: \'#js_progress_uploader\', add_more: false, max_upload: 1, total: 1, frame_id: \'js_upload_frame\', file_id: \'image\'}; $Core.progressBarInit(); } }</script>'])
            ->assign([
                    'aPermissions' => (isset($aPage) && isset($aPage['page_id']) ? Core\Lib::appsGroup()->getPerms($aPage['page_id']) : []),
                    'aTypes'       => Phpfox::getService('groups.type')->get(),
                    'bIsEdit'      => $bIsEdit,
                    'iMaxFileSize' => user('pf_group_max_upload_size', 500) ? Phpfox::getLib('phpfox.file')->filesize((user('pf_group_max_upload_size', 500) / 1024) * 1048576) : null,
                    'aWidgetEdits' => Core\Lib::appsGroup()->getWidgetsForEdit(),
                    'bIsNewPage'   => $bIsNewPage,
                    'sStep'        => $sStep,
                ]
            );
        $this->template()->setHeader([
            'jquery.cropit.js' => 'module_user',
        ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_controller_add_clean')) ? eval($sPlugin) : false);
    }
}