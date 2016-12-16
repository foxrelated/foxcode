<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Display the image details when viewing an image.
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Raymond Benc
 * @package          Module_Photo
 * @version          $Id: detail.class.php 254 2009-02-23 12:36:20Z Raymond_Benc $
 */
class Poll_Component_Block_Votes extends Phpfox_Component
{

    public function process()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_block_votes_start')) ? eval($sPlugin) : false);

        $iPollId = $this->request()->getInt('req2');
        $iPageSize = 10;
        $iPage = $this->request()->getInt('page', 1);

        $aVotes = [];
        if (empty($iPollId)) {
            $iPollId = $this->request()->get('poll_id');
        }
        if ($iPollId) {
            $aVotes = Poll_Service_Poll::instance()->getVotes($iPollId, $iPage, $iPageSize);
        }

        //Todo $aVotes return empty value.
        $this->template()->assign([
                'bPopup'  => true,
                'sAjax'   => 'poll.pageVotes',
                'page'    => $iPage,
                'aPager'  => [
                    'nextAjaxUrl' => $iPage + 1,
                    'sParamsAjax' => '&poll_id=' . $iPollId,
                ],
                'hasMore' => count($aVotes) == 10,
                'aVotes'  => $aVotes,
            ]
        );

        (($sPlugin = Phpfox_Plugin::get('poll.component_block_votes_end')) ? eval($sPlugin) : false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_block_detail_clean')) ? eval($sPlugin) : false);

    }
}