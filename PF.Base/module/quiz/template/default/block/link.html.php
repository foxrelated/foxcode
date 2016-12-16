<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        Raymond Benc
 * @package        Module_Poll
 * @version        $Id: link.html.php 3342 2011-10-21 12:59:32Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if ( (Phpfox::getUserId() == $aQuiz.user_id && (Phpfox::getUserParam('quiz.can_edit_own_questions') || Phpfox::getUserParam('quiz.can_edit_own_title'))) ||
(Phpfox::getUserId() != $aQuiz.user_id && (Phpfox::getUserParam('quiz.can_edit_others_questions') || Phpfox::getUserParam('quiz.can_edit_others_title'))))}
<li><a href="{url link='quiz.add' id=$aQuiz.quiz_id}">{_p var='edit'}</a></li>
{/if}
{if Phpfox::isModule('ad') && Phpfox::isModule('feed') && (Phpfox::getUserParam('feed.can_purchase_sponsor') || Phpfox::getUserParam('feed.can_sponsor_feed')) && Phpfox::getUserParam('feed.feed_sponsor_price') && ($iSponsorId = Feed_Service_Feed::instance()->canSponsoredInFeed('quiz', $aQuiz.quiz_id))}
<li>
    {if $iSponsorId === true}
    <a href="{url link='ad.sponsor' where='feed' section='quiz' item=$aQuiz.quiz_id}">
        {_p var='sponsor_in_feed'}
    </a>
    {else}
    <a href="#" onclick="$.ajaxCall('ad.removeSponsor', 'type_id=quiz&item_id={$aQuiz.quiz_id}', 'GET'); return false;">
        {_p var="Unsponsor In Feed"}
    </a>
    {/if}
</li>
{/if}
{if ($aQuiz.user_id == Phpfox::getUserId())}
<li><a href="{permalink module='quiz' id=$aQuiz.quiz_id title=$aQuiz.title}results/"">{_p var='view_results'}</a></li>
{/if}
{if Phpfox::getUserParam('quiz.can_delete_others_quizzes') || ( ($aQuiz.user_id == Phpfox::getUserId()) && Phpfox::getUserParam('quiz.can_delete_own_quiz') )}
<li class="item_delete"><a href="#"
                           onclick="return $Core.quiz_moderate.deleteQuiz({$aQuiz.quiz_id}, '{if isset($bIsViewingQuiz) && $bIsViewingQuiz}viewing{else}browsing{/if}')">{_p
        var='delete'}</a></li>
{/if}
{plugin call='quiz.template_block_entry_links_main'}