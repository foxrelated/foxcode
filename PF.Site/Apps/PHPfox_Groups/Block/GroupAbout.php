<?php

namespace Apps\PHPfox_Groups\Block;

use Phpfox_Component;
use Phpfox_Parse_Output;

/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Raymond_Benc
 * @package          Phpfox_Component
 * @version          $Id: profile.class.php 5840 2013-05-09 06:14:35Z Raymond_Benc $
 */
class GroupAbout extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $page = $this->getParam('aPage');
        $aUser = [
           'user_id' => $page['owner_user_id'],
           'profile_page_id' => $page['owner_profile_page_id'],
           'server_id' => $page['owner_server_id'],
           'user_name' => $page['owner_user_name'],
           'full_name' => $page['owner_full_name'],
           'gender' => $page['owner_gender'],
           'user_image' => $page['owner_user_image'],
           'is_invisible' => $page['owner_is_invisible'],
           'user_group_id' => $page['owner_user_group_id'],
           'language_id' => $page['owner_language_id'],
           'birthday' => $page['owner_birthday'],
           'country_iso' => $page['owner_country_iso'],
        ];
        $this->template()->assign([
            'about' => Phpfox_Parse_Output::instance()->parse($page['text']),
            'page'  => $page,
            'aUser'  => $aUser,
        ]);
        return null;
    }
}