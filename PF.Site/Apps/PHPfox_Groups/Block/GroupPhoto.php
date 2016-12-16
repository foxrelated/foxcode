<?php

namespace Apps\PHPfox_Groups\Block;

use Core;
use Phpfox_Component;
use Phpfox_Plugin;
use Photo_Service_Photo;

defined('PHPFOX') or exit('NO DICE!');

class GroupPhoto extends Phpfox_Component
{
    public function process()
    {
        if (!defined('PHPFOX_IS_PAGES_VIEW')) {
            return false;
        }

        $aPage = $this->getParam('aPage');

        if (!isset($aPage['page_id']) || empty($aPage['page_id'])) {
            return false;
        }

        $aCoverPhoto = ($aPage['cover_photo_id'] ? Photo_Service_Photo::instance()->getCoverPhoto($aPage['cover_photo_id']) : false);
        $iCoverPhotoPosition = $aPage['cover_photo_position'];

        $aPageMenus = Core\Lib::appsGroup()->getMenu($aPage);
        foreach ($aPageMenus as $key => $value) {

            if ($value['landing'] == 'info') {
                unset($aPageMenus[ $key ]);
                continue;
            }

            switch ($value['landing']) {
                case 'blog':
                    $i = 'pencil-square-o';
                    break;
                case 'event':
                    $i = 'calendar';
                    break;
                case 'forum':
                    $i = 'comments';
                    break;
                case 'music':
                    $i = 'music';
                    break;
                case 'photo':
                    $i = 'image';
                    break;
                case 'v':
                    $i = 'play-circle';
                    break;
                default:
                    $i = 'feed';
                    break;
            }

            $aPageMenus[ $key ]['favicon'] = $i;
        }

        $this->template()->assign([
            'aCoverPhoto'          => $aCoverPhoto,
            'iConverPhotoPosition' => $iCoverPhotoPosition,
            'aPageMenus'           => $aPageMenus,
            'sCoverDefaultUrl'     => flavor()->active->default_photo('groups_cover_default', true),
        ]);

        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_block_photo_clean')) ? eval($sPlugin) : false);
    }
}