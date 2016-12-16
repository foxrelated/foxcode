<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Raymond Benc
 * @package          Phpfox_Component
 * @version          $Id: stat.class.php 5385 2013-02-19 09:08:40Z Miguel_Espinoza $
 */
class Quiz_Component_Block_Takenby extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $sQuizUrl = $this->request()->getInt('quiz_id');
        $iPage = $this->request()->getInt('page', 2);
        $iPageSize = 10;
        $aQuiz = Quiz_Service_Quiz::instance()->getQuizByUrl($sQuizUrl, false, false, $iPage);

        $this->template()
            ->assign([
                'bPopup'=>true,
                'sAjax'=>'quiz.browseUsers',
                'aPager'=>[
                    'nextAjaxUrl'=> $iPage+1,
                    'sParamsAjax'=>'&quiz_id='. $aQuiz['quiz_id'],
                ],
                'aQuiz'   => $aQuiz,
                'iPage'   => $iPage,
                'hasMore' => count($aQuiz['aTakenBy']) == $iPageSize,
            ]);

        return 'block';
    }
}