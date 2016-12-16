<?php
defined('PHPFOX') or exit('NO DICE!');

class Photo_Component_Block_My_Photo extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aUser = $this->getParam('aUser');
        $aPhotos = Photo_Service_Photo::instance()->getForProfile($aUser['user_id'], 4);

        if (!User_Service_Privacy_Privacy::instance()->hasAccess($aUser['user_id'], 'photo.display_on_profile')) {
            return false;
        }

        if (count($aPhotos) >= 3) {
            $this->template()->assign([
                'aFooter' => [
                    _p('view_more_photos') => $this->url()->makeUrl($aUser['user_name'], 'photo')
                ]
            ]);

            if ($aPhotos > 3)
            {
                array_pop($aPhotos);
            }
        }
        $this->template()->assign([
                'sHeader' => _p('recent_photos'),
                'aPhotos' => $aPhotos,
                'iCount' => count($aPhotos),
                'aUser' => $aUser
            ]);


        if (Phpfox::getUserId() == $aUser['user_id']) {
            $this->template()->assign('sDeleteBlock', 'profile');
        }

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_block_my_photo_clean')) ? eval($sPlugin) : false);
    }
}