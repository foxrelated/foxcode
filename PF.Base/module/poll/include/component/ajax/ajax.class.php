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
 * @package          Module_Poll
 * @version          $Id: ajax.class.php 6472 2013-08-20 06:11:44Z Raymond_Benc $
 */
class Poll_Component_Ajax_Ajax extends Phpfox_Ajax
{
    /**
     * Deletes the image in a poll by calling the process service's deleteImage function
     */
    public function deleteImage()
    {
        Phpfox::isUser(true);
        (($sPlugin = Phpfox_Plugin::get('poll.component_ajax_deleteimage_start')) ? eval($sPlugin) : false);
        $iPoll = (int)$this->get('iPoll');
        if (Poll_Service_Process::instance()->deleteImage($iPoll, Phpfox::getUserId())) {
            $this->call('$("#js_submit_upload_image").show();');
            $this->call('$("#js_event_current_image").remove();');
        } else {
            $this->call('$("#js_event_current_image").after("' . _p('an_error_occured_and_your_image_could_not_be_deleted_please_try_again') . '");');
        }
        (($sPlugin = Phpfox_Plugin::get('poll.component_ajax_deleteimage_end')) ? eval($sPlugin) : false);
    }

    /**
     * Adds a vote to a specific poll and sets the message to show according
     * it also may show the poll result if the userParam is set to show it
     */
    public function addVote()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_ajax_addvote_start')) ? eval($sPlugin) : false);

        Phpfox::isUser(true);

        $aVals = $this->get('val');

        // check if the poll is being moderated
        $bModerated = Poll_Service_Poll::instance()->isModerated((int)$aVals['poll_id']);

        if ($bModerated) {
            $this->call('$("#poll_holder_' . (int)$aVals['poll_id'] . '").html("' . _p('this_poll_is_being_moderated_and_no_votes_can_be_added_yet') . '");');
        } else {
            if (Poll_Service_Process::instance()->addVote(Phpfox::getUserId(), (int)$aVals['poll_id'], (int)$aVals['answer'])) {
                if (Phpfox::getUserParam('poll.view_poll_results_after_vote')) {
                    Phpfox::getBlock('poll.vote', ['iPoll' => (int)$aVals['poll_id']]);
                    $this->call('$(\'#js_poll_results_' . $aVals['poll_id'] . '\').empty();');
                    $this->html('#js_poll_results_' . $aVals['poll_id'], $this->getContent(false));

                    Phpfox::getBlock('poll.votes', ['iPoll' => (int)$aVals['poll_id'], 'page' => 0]);
                    $this->call('if($(\'#js_votes\')) {$(\'#js_votes\').html(\'' . $this->getContent(false) . '\');$Core.loadInit();}');
                } else {
                    if (!empty($aVals['vote_again'])) {
                        $this->alert(_p('poll_vote_updated'));
                    } else {
                        $this->alert(_p('your_vote_has_successfully_been_cast'));
                    }
                }
            } else {
                $this->alert(implode(' ', Phpfox_Error::get()));
            }
        }

        (($sPlugin = Phpfox_Plugin::get('poll.component_ajax_addvote_end')) ? eval($sPlugin) : false);
    }

    /**
     * Process moderation on a poll
     */
    public function moderatePoll()
    {
        Phpfox::isUser(true);

        (($sPlugin = Phpfox_Plugin::get('poll.component_ajax_moderatepoll_start')) ? eval($sPlugin) : false);

        $iPoll = (int)$this->get('iPoll');
        $iResult = (int)$this->get('iResult');

        if ($iResult == 0) {
            Phpfox::getUserParam('poll.poll_can_moderate_polls', true);
    
            Poll_Service_Process::instance()->moderatePoll($iPoll, $iResult);

            if ($this->get('inline')) {
                $this->alert(_p('poll_has_been_approved'), _p('poll_approved'), 300, 100, true);
                $this->hide('#js_item_bar_approve_image');
                $this->hide('.js_moderation_off');
                $this->show('.js_moderation_on');
            } else {
                $sCall = "$('#poll_holder_" . $iPoll . "').removeClass('row_moderate'); $('#poll_holder_" . $iPoll . "').find('.js_poll_approve_link').remove();";

                $this->call($sCall)
                    ->prepend('#poll_holder_' . (int)$iPoll, '<div class="valid_message" style="display:none;">' . _p('poll_successfully_approved') . '</div>')
                    ->call('$(\'#poll_holder_' . (int)$iPoll . '\').find(\'.valid_message\').slideDown();')
                    ->call('setTimeout("$(\'#poll_holder_' . (int)$iPoll . '\').find(\'.valid_message\').slideUp();", 2000);');
            }
        } elseif ($iResult == 2) {
            if (User_Service_Auth::instance()->hasAccess('poll', 'poll_id', $iPoll, 'poll.poll_can_delete_own_polls', 'poll.poll_can_delete_others_polls') && Poll_Service_Process::instance()->moderatePoll($iPoll, $iResult)) {
                $this->call('$("#js_poll_id_' . (int)$iPoll . '").prev().remove();');
                $this->remove('#js_poll_id_' . (int)$iPoll);
            }
        } else {
            $this->call("$('#poll_holder_" . $iPoll . "').html('" . _p('there_was_a_problem_moderating_this_poll', ['phpfox_squote' => true]) . "');");
        }

        (($sPlugin = Phpfox_Plugin::get('poll.component_ajax_moderatepoll_end')) ? eval($sPlugin) : false);
    }

    /**
     * Shows the votes result in a poll
     */
    public function pageVotes()
    {
        $page = $this->get('page');

        if($page < 2)
            $this->setTitle(_p('poll_results'));

        Phpfox::getBlock('poll.votes');

        if ($page > 1) {
            $this->replaceWith('.js_box_content .js_pager_popup_view_more_link', $this->getContent(false));
        }
        $this->call('<script>$Core.loadInit();</script>');
    }

    /**
     * Shows the newest polls
     */
    public function getNew()
    {
        Phpfox::getBlock('poll.new');

        $this->html('#' . $this->get('id'), $this->getContent(false));
        $this->call('$(\'#' . $this->get('id') . '\').parents(\'.block:first\').find(\'.bottom li a\').attr(\'href\', \'' . Phpfox_Url::instance()->makeUrl('poll') . '\');');
    }

    public function add()
    {
        echo '<div style="position:relative;">';
        Phpfox::getComponent('poll.add', [], 'controller');
        echo '</div>';
        echo $this->template()->getHeader();
        echo '<script type="text/javascript">$Core.loadInit();</script>';
    }

    public function addCustom()
    {
        $this->errorSet('#js_poll_form_msg');

        $aVals = $this->get('val');

        $mErrors = Poll_Service_Poll::instance()->checkStructure($aVals);
        if (is_array($mErrors)) {
            foreach ($mErrors as $sError) {
                Phpfox_Error::set($sError);
            }
        }

        if (Phpfox_Error::isPassed()) {
            // check if question has a question mark
            if (strpos($aVals['question'], '?') === false) {
                $aVals['question'] = $aVals['question'] . '?';
            }

            if ((list($iId, $aPoll) = Poll_Service_Process::instance()->add(Phpfox::getUserId(), $aVals))) {
                $this->val('#js_poll_id', $iId);
                $this->call('tb_remove();');
                $this->html('#js_attach_poll_question', Phpfox::getLib('parse.output')->clean($aPoll['question']) . ' - <a href="#" onclick="$.ajaxCall(\'forum.deletePoll\', \'poll_id=' . $iId . '&amp;thread_id=\' + $(\'#js_poll_id\').val()); return false;" title="' . _p('click_to_delete_this_poll') . '">' . _p('delete') . '</a>');
                $this->hide('#js_attach_poll');
            }
        }
    }

    public function moderation()
    {
        Phpfox::isUser(true);
        $sMessage = '';
        switch ($this->get('action')) {
            case 'approve':
                Phpfox::getUserParam('poll.poll_can_moderate_polls', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Poll_Service_Process::instance()->moderatePoll($iId, '0');
                    $this->call('$("#js_poll_id_' . $iId . '").prev().remove();');
                    $this->remove('#js_poll_id_' . $iId);
                }
                $this->updateCount();
                $sMessage = _p('poll_s_successfully_approved');
                break;
            case 'delete':
                Phpfox::getUserParam('poll.poll_can_moderate_polls', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Poll_Service_Process::instance()->moderatePoll($iId, 2);
                    $this->call('$("#js_poll_id_' . $iId . '").prev().remove();');
                    $this->remove('#js_poll_id_' . $iId);
                }
                $sMessage = _p('poll_s_successfully_deleted');
                break;
        }

        $this->alert($sMessage, _p('moderation'), 300, 150, true);
        $this->hide('.moderation_process');
    }

    public function addViaStatusUpdate()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('poll.can_create_poll', true);

        $this->error(false);

        $aVals = (array)$this->get('val');

        $aVals['question'] = $aVals['poll_question'];

        $iFlood = Phpfox::getUserParam('poll.poll_flood_control');
        if ($iFlood != '0') {
            $aFlood = [
                'action' => 'last_post', // The SPAM action
                'params' => [
                    'field'      => 'time_stamp', // The time stamp field
                    'table'      => Phpfox::getT('poll'), // Database table we plan to check
                    'condition'  => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                    'time_stamp' => $iFlood * 60 // Seconds);
                ],
            ];
            // actually check if flooding
            if (Phpfox::getLib('spam')->check($aFlood)) {
                // Set an error
                Phpfox_Error::set(_p('poll_flood_control', ['x' => $iFlood]));
            }
        }

        $mErrors = Poll_Service_Poll::instance()->checkStructure($aVals);
        if (is_array($mErrors)) {
            foreach ($mErrors as $sError) {
                Phpfox_Error::set($sError);
            }
        }

        $bIsError = false;
        if (Phpfox_Error::isPassed()) {
            // check if question has a question mark
            if (strpos($aVals['question'], '?') === false) {
                $aVals['question'] = $aVals['question'] . '?';
            }

            if (list($iPollId, $aPoll) = Poll_Service_Process::instance()->add(Phpfox::getUserId(), $aVals)) {
                $iId = Feed_Service_Process::instance()->getLastId();

                (($sPlugin = Phpfox_Plugin::get('user.component_ajax_addviastatusupdate')) ? eval($sPlugin) : false);

                Feed_Service_Feed::instance()->processAjax($iId);
            } else {
                $bIsError = true;
            }

        } else {
            $bIsError = true;
        }

        if ($bIsError) {
            $this->call('$Core.resetActivityFeedError(\'' . implode('<br />', Phpfox_Error::get()) . '\');');
        } else {
            $this->call('$("#global_attachment_poll input:text").val(" ");');
        }
    }
}