<?php

namespace Apps\PHPfox_Groups\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
use Phpfox_Request;

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
 * @version          $Id: category.class.php 3144 2011-09-20 20:39:58Z Raymond_Benc $
 */
class GroupCategory extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $iCategoryId = $this->getParam('iCategory', 0);

        $aCategories = Phpfox::getService('groups.category')->getForBrowse($iCategoryId);

        if (!is_array($aCategories)) {
            return false;
        }

        if (!count($aCategories)) {
            return false;
        }

        if ($sView = Phpfox_Request::instance()->get('view'))
        {
            $sView = Phpfox::getLib('parse.input')->clean($sView);
            if (in_array($sView, ['my', 'pending', 'alll']))
            {
                foreach ($aCategories as $iKey => $aCategory) {
                    $aCategories[ $iKey ]['link'] = $aCategory['link'] . '?view=' . $sView . '/';
                }
            }
        }


        $this->template()->assign([
                'sHeader'     => ($iCategoryId ?_p('Sub categories') : _p('Categories')),
                'aCategories' => $aCategories,
            ]
        );

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_block_category_clean')) ? eval($sPlugin) : false);
    }
}