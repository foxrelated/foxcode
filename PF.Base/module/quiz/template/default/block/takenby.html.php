{foreach from=$aQuiz.aTakenBy name=users item=aUser}
<div style="width:55px; position:absolute;">
    {img user=$aUser.user_info suffix='_50' max_width=50 max_height=50}
</div>
<div class="p_2" style="margin-left:60px; height:55px;">
    <a href="{url link=""$aUser.user_info.user_name""}">{$aUser.user_info.full_name}</a> {if (Phpfox::getParam('quiz.show_percentage_in_results'))}{$aUser.iSuccessPercentage}%{else}{$aUser.total_correct}/{$aUser.iTotalCorrectAnswers}{/if}.
    <div class="t_right">
        <a href="{permalink module='quiz' id=$aQuiz.quiz_id title=$aQuiz.title}results/id_{$aUser.user_info.user_id}/" id="quiz_inner_title_{$aQuiz.quiz_id}">{_p var='view_results'}</a>
    </div>
</div>
{/foreach}
{if isset($hasMore) && $hasMore}
{pager}
{/if}