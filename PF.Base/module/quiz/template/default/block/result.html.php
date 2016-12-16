<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: result.html.php 3193 2011-09-27 11:44:08Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<h3 class="txt-success uppercase">
	<b>
{if isset($aQuiz.takerInfo)}
	{_p var='quiz_results_for_full_name_percentage_100' full_name=$aQuiz.takerInfo.userinfo.full_name percentage=$aQuiz.iSuccessPercentage}
{else}
	{_p var='quiz_results_percentage_100' percentage=$aQuiz.iSuccessPercentage}
{/if}
	</b>
</h3>
	{if isset($aQuiz.takerInfo)}	
		<div style="width:55px; position:absolute;">
			{img user=$aQuiz.takerInfo.userinfo suffix='_50' max_width=50 max_height=50}
		</div>
		<div class="p_2" style="margin-left:60px; height:55px;">
			<div class="extra_info">
				{_p var='taken_on_time_stamp' time_stamp=$aQuiz.takerInfo.time_stamp|date:'quiz.quiz_view_time_stamp'}
			</div>
		</div>
	{/if}
<div>
{foreach from=$aQuiz.results name=questions item=aQuestion}	
	<div class="clearfix quiz_result_item {if is_int($phpfox.iteration.questions/2)}row1{else}row2{/if}{if $phpfox.iteration.questions == 1} row_first{/if}{if $aQuestion.userAnswer == $aQuestion.correctAnswer} row_is_correct{else} row_is_incorrect{/if}">
		<div class="quiz_result_left">
			{$phpfox.iteration.questions}
		</div>
		<div class="quiz_result_right">
			<div class="txt-dark quiz_result_question">{$aQuestion.questionText}</div>
			<div>
				<span class="txt-time-color">{_p var='full_name_s_answer' full_name=$aQuestion.full_name}:</span>
				<span class="fw-500">{$aQuestion.userAnswerText}</span>
			</div>
			<div>
				<span class="txt-time-color">{_p var='correct_answer'}:</span>
				<span class="fw-500">{$aQuestion.correctAnswerText}</span>
			</div>
		</div>
	</div>
{/foreach}	
</div>
