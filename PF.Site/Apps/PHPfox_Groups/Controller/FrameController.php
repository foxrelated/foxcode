<?php

namespace Apps\PHPfox_Groups\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Url;
use Phpfox_Error;

defined('PHPFOX') or exit('NO DICE!');

class FrameController extends Phpfox_Component
{
    public function process()
    {
        if (($aVals = $this->request()->getArray('val'))) {
            if (($this->request()->get('widget_id') ? Phpfox::getService('groups.process')->updateWidget($this->request()->get('widget_id'), $this->request()->get('val')) : Phpfox::getService('groups.process')->addWidget($this->request()->get('val')))) {
                $aVals = $this->request()->get('val');
                echo '<script type="text/javascript">window.parent.location.href = \'' . Phpfox_Url::instance()->makeUrl('groups.add.widget', ['id' => $aVals['page_id']]) . '\';</script>';
            } else {
                echo '<script type="text/javascript">';
                echo 'window.parent.$(\'#js_groups_widget_error\').html(\'<div class="error_message">' . implode('', Phpfox_Error::get()) . '</div>\');';
                echo '</script>';
            }
            exit;
        } else {
            $this->url()->send('groups');
        }
    }
}