<?php
namespace Apps\PHPfox_Groups\Ajax;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Ajax;
use Core_Service_Process;
use User_Service_Auth;
use Feed_Service_Process;
use Phpfox_Image;
use Core;
use Phpfox_Image_Helper;

/**
 * Class Ajax
 *
 * @package Apps\PHPfox_Groups\Ajax
 */
class Ajax extends Phpfox_Ajax
{

    public function request()
    {
        Phpfox::getBlock('groups.category');
    }

    public function add()
    {
        Phpfox::isUser(true);
        if (($iId = Phpfox::getService('groups.process')->add($this->get('val'))))
        {
            $aPage = Core\Lib::appsGroup()->getPage($iId);

            $this->call('window.location.href = \'' . \Phpfox_Url::instance()->makeUrl('groups.add', array('id' => $aPage['page_id'], 'new' => '1')) . '\';');
        }
        else
        {
            $sError = \Phpfox_Error::get();
            $sError = implode('<br />', $sError);
            $this->alert($sError);
            $this->call('$Core.processForm(\'#js_groups_add_submit_button\', true);');
        }
    }

    public function removeLogo()
    {
        if (($aPage = Phpfox::getService('groups.process')->removeLogo($this->get('page_id'))) !== false)
        {
            $this->call('window.location.href = \'' . $aPage['link'] . '\';');
        }
    }

    public function deleteWidget()
    {
        if (Phpfox::getService('groups.process')->deleteWidget($this->get('widget_id')))
        {
            $this->slideUp('#js_groups_widget_' . $this->get('widget_id'));
        }
    }

    public function widget()
    {
        $this->setTitle(_p('Widgets'));
        Phpfox::getComponent('groups.widget', [], 'controller');

        (($sPlugin = Phpfox_Plugin::get('groups.component_ajax_widget')) ? eval($sPlugin) : false);

        echo '<script type="text/javascript">$Core.loadInit();</script>';
    }


    public function addFeedComment()
    {
        Phpfox::isUser(true);

        $aVals = (array) $this->get('val');
        $iCustomPageId = isset($_REQUEST['custom_pages_post_as_page']) ? $_REQUEST['custom_pages_post_as_page'] : 0;
        if (($iCustomPageId && $iCustomPageId != $aVals['callback_item_id']) || !Core\Lib::appsGroup()->hasPerm($aVals['callback_item_id'], 'groups.share_updates')){
            $this->alert(_p('You do not have permission to add comments'));
            $this->call('$Core.activityFeedProcess(false);');
            return;
        }


        if (Phpfox::getLib('parse.format')->isEmpty($aVals['user_status']))
        {
            $this->alert(_p('add_some_text_to_share'));
            $this->call('$Core.activityFeedProcess(false);');
            return;
        }

        $aPage = Core\Lib::appsGroup()->getPage($aVals['callback_item_id']);

        if (!isset($aPage['page_id']))
        {
            $this->alert(_p('Unable to find the page you are trying to comment on.'));
            $this->call('$Core.activityFeedProcess(false);');
            return;
        }

        $sLink = Core\Lib::appsGroup()->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
        $aCallback = array(
            'module' => 'groups',
            'table_prefix' => 'pages_',
            'link' => $sLink,
            'email_user_id' => $aPage['user_id'],
            'subject' => _p('{{ full_name }} wrote a comment on your group "{{ title }}".', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aPage['title'])),
            'message' => _p('{{ full_name }} wrote a comment on your group "<a href="{{ link }}">{{ title }}</a>". To see the comment thread, follow the link below: <a href="{{ link }}">{{ link }}</a>', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aPage['title'])),
            'notification' => null,
            'feed_id' => 'groups_comment',
            'item_id' => $aPage['page_id'],
            'mail_translated' => true,
            'add_to_main_feed' => true,
            'add_tag' => true
        );

        $aVals['parent_user_id'] = $aVals['callback_item_id'];

        if (isset($aVals['user_status']) && ($iId = Feed_Service_Process::instance()->callback($aCallback)->addComment($aVals)))
        {
            \Phpfox_Database::instance()->updateCounter('pages', 'total_comment', 'page_id', $aPage['page_id']);

            \Feed_Service_Feed::instance()->callback($aCallback)->processAjax($iId);

            if (Phpfox::isModule('notification')  && Phpfox::hasCallback('groups', 'addItemNotification') && defined('PHPFOX_NEW_USER_STATUS_ID'))
            {
                Phpfox::callback('groups.addItemNotification', ['page_id' => $aPage['page_id'], 'item_perm' => 'groups.view_browse_updates', 'item_type' => 'groups_status', 'item_id' => PHPFOX_NEW_USER_STATUS_ID, 'owner_id' => Phpfox::getUserId()]);
            }
        }
        else
        {
            $this->call('$Core.activityFeedProcess(false);');
        }
    }

    public function changeUrl()
    {
        Phpfox::isUser(true);

        if (($aPage = Core\Lib::appsGroup()->getForEdit($this->get('id'))))
        {
            $aVals = $this->get('val');

            $sNewTitle = Phpfox::getLib('parse.input')->cleanTitle($aVals['vanity_url']);

            if (Phpfox::getLib('parse.input')->allowTitle($sNewTitle, _p('Group name not allowed. Please select another name.')))
            {
                if (Phpfox::getService('groups.process')->updateTitle($this->get('id'), $sNewTitle))
                {
                    $this->alert(_p('Successfully updated your group URL.'), _p('URL Updated!'), 300, 150, true);
                }
            }
        }

        $this->call('$Core.processForm(\'#js_groups_vanity_url_button\', true);');
    }

    public function signup()
    {
        Phpfox::isUser(true);
        if (Phpfox::getService('groups.process')->register($this->get('page_id')))
        {
            $this->alert(_p('Successfully registered for this group. Your membership is pending an admins approval. As soon as your membership has been approved you will be notified.'));
        }
    }

    public function moderation()
    {
        Phpfox::isUser(true);
        $sAction = $this->get('action');

        if (Phpfox::getService('groups.process')->moderation($this->get('item_moderate'), $this->get('action')))
        {
            foreach ((array) $this->get('item_moderate') as $iId)
            {
                $this->remove('#js_pages_user_entry_' . $iId);
            }

            $this->updateCount();
            switch ($sAction) {
                case 'delete':
                    $sMessage = _p('Successfully deleted user(s).');
                    break;
                case 'approve':
                    $sMessage = _p('Successfully approved user(s).');
                    break;
                default:
                    $sMessage = _p('Successfully moderated user(s).');
                    break;
            }
            $this->alert($sMessage, _p('Moderation'), 300, 150, true);
        }

        $this->hide('.moderation_process');
    }

    public function logBackUser()
    {
        $this->error(false);
        Phpfox::isUser(true);
        $aUser = Core\Lib::appsGroup()->getLastLogin();
        list ($bPass, ) = User_Service_Auth::instance()->login($aUser['email'], $this->get('password'), true, $sType = 'email');
        if ($bPass)
        {
            Phpfox::getService('groups.process')->clearLogin($aUser['user_id']);

            $this->call('window.location.href = \'' . \Phpfox_Url::instance()->makeUrl('') . '\';');
        }
        else
        {
            $this->html('#js_error_pages_login_user', '<div class="error_message">' . implode('<br />', \Phpfox_Error::get()) . '</div>');
        }
    }

    public function pageModeration()
    {
        Phpfox::isUser(true);
        user('pf_group_moderate', null, null, true);

        switch ($this->get('action'))
        {
            case 'approve':
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('groups.process')->approve($iId);
                    $this->remove('#js_pages_' . $iId);
                }
                $sMessage = _p('Group(s) successfully approved.');
                break;
            case 'delete':
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('groups.process')->delete($iId);
                    $this->slideUp('#js_pages_' . $iId);
                }
                $sMessage = _p('Group(s) successfully deleted.');
                break;
            default:
                $sMessage = '';
                break;
        }

        $this->updateCount();

        $this->alert($sMessage, _p('Moderation'), 300, 150, true);
        $this->hide('.moderation_process');
    }

    public function approve()
    {
        if (Phpfox::getService('groups.process')->approve($this->get('page_id')))
        {
            $this->alert(_p('Group has been approved.'), _p('Group Approved'), 300, 100, true);
            $this->hide('#js_item_bar_approve_image');
            $this->hide('.js_moderation_off');
            $this->show('.js_moderation_on');
        }
    }

    public function updateActivity()
    {
        if (Phpfox::getService('groups.process')->updateActivity($this->get('id'), $this->get('active'), $this->get('sub')))
        {

        }
    }

    public function categoryOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Core_Service_Process::instance()->updateOrdering(array(
                'table' => 'pages_type',
                'key' => 'type_id',
                'values' => $aVals['ordering']
            )
        );

        Phpfox::getLib('cache')->remove('groups', 'substr');
    }

    public function categorySubOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Core_Service_Process::instance()->updateOrdering(array(
                'table' => 'pages_category',
                'key' => 'category_id',
                'values' => $aVals['ordering']
            )
        );

        Phpfox::getLib('cache')->remove('groups', 'substr');
    }


    public function setCoverPhoto()
    {
        $iPageId = $this->get('page_id');
        $iPhotoId = $this->get('photo_id');

        if (Phpfox::getService('groups.process')->setCoverPhoto($iPageId , $iPhotoId))
        {
            $this->call('window.location.href = "' . Phpfox::permalink('groups', $this->get('page_id'), '') . 'coverupdate_1";');

        }
    }

    public function repositionCoverPhoto()
    {
        if (Phpfox::getService('groups.process')->updateCoverPosition($this->get('id'), $this->get('position')))
        {
            Phpfox::addMessage(_p('Position set correctly.'));
        }
    }

    public function updateCoverPosition()
    {
        if (Phpfox::getService('groups.process')->updateCoverPosition($this->get('page_id'), $this->get('position')))
        {
            $this->call('window.location.href = "' . Phpfox::permalink('groups', $this->get('page_id'), '') . '";');
            Phpfox::addMessage(_p('Position set correctly.'));
        }
    }
    
    public function removeCoverPhoto()
    {
        if (Phpfox::getService('groups.process')->removeCoverPhoto($this->get('page_id'))) {
            $this->call('window.location.href=window.location.href;');
        }
    }
    
    public function cropme()
    {
        Phpfox::getBlock('groups.cropme');
        $this->call('<script>$Behavior.crop_groups_image_photo();</script>');
    }
    
    public function processCropme()
    {
        $aVals = $this->get('val');
        $aPage = Core\Lib::appsGroup()->getForEdit($aVals['page_id']);
        if (!Core\Lib::appsGroup()->isAdmin($aPage)){
            return false;
        }
        //Process crop image
        if (isset($aVals['crop-data']) && !empty($aVals['crop-data'])){
            $sTempPath = PHPFOX_DIR_CACHE . md5('pages_avatar' . $aVals['page_id']) . '.png';
            list(, $data) = explode(';', $aVals['crop-data']);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            file_put_contents($sTempPath, $data);
            $oImage = Phpfox_Image::instance();
            $aSize = [
                '50' => '',
                '120' => '',
                '200' => 'square',
            ];
            foreach ($aSize as $iSize => $value){
                $oImage->createThumbnail(sprintf($sTempPath, ''), Phpfox::getParam('pages.dir_image') . sprintf($aPage['image_path'], '_' . $iSize), $iSize, $iSize, false);
                if ($value == 'square'){
                    $oImage->createThumbnail(sprintf($sTempPath, ''), Phpfox::getParam('pages.dir_image') . sprintf($aPage['image_path'], '_' . $iSize . '_square'), $iSize, $iSize, false);
                }
            }
            @unlink($sTempPath);
        }
        //End crop image
        $sImagePath = Phpfox_Image_Helper::instance()->display([
            'server_id' => $aPage['image_server_id'],
            'path' => 'pages.url_image',
            'file' => $aPage['image_path'],
            'suffix' => '_120',
            'max_width' => '120',
            'max_height' => '120',
            'thickbox' => true,
            'time_stamp' => true
        ]);
        $sImagePath = str_replace(array("\n", "\t"), '', $sImagePath);
        $sImagePath = str_replace('\\', '\\\\', $sImagePath);
        $sImagePath = str_replace("'", "\\'", $sImagePath);
        $sImagePath = str_replace('"', '\"', $sImagePath);
        $this->call('$("#js_event_current_image").html("' . $sImagePath . '");');
        $this->call("tb_remove();");
        return null;
    }
}