<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Quiz
 * @version 		$Id: index.html.php 3342 2011-10-21 12:59:32Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aQuizzes)}
{if !PHPFOX_IS_AJAX}<div class="collection-stage">{/if}
{foreach from=$aQuizzes name=quizzes item=aQuiz}
	<div class="collection-item-stage">
		<div id="js_quiz_{$aQuiz.quiz_id}" class="quizz_large_item image_load moderation_row">
			<header>
				<h1 itemprop="name"><a href="{permalink module='quiz' id=$aQuiz.quiz_id title=$aQuiz.title}" class="link" itemprop="url">{$aQuiz.title|clean}</a></h1>
				<div>
					<span>{$aQuiz.time_stamp|convert_time:'quiz.quiz_view_time_stamp'}</span>
					<span>&middot;</span>
					<span>{_p var='by'} {$aQuiz|user}</span>
				</div>
			</header>

			<div class="quizz_large_image" {if $aQuiz.image_path} style="background-image:url({img server_id=$aQuiz.server_id path='quiz.url_image' file=$aQuiz.image_path suffix='' return_url=true})" {/if} itemprop='image'>
		</div>

		{if Phpfox::getUserParam('quiz.can_approve_quizzes')
		|| Phpfox::getUserParam('quiz.can_delete_others_quizzes')
		|| ( ($aQuiz.user_id == Phpfox::getUserId()) && Phpfox::getUserParam('quiz.can_delete_own_quiz') )
		|| ($aQuiz.user_id == Phpfox::getUserId())
		|| ( (Phpfox::getUserId() == $aQuiz.user_id && (Phpfox::getUserParam('quiz.can_edit_own_questions') || Phpfox::getUserParam('quiz.can_edit_own_title'))) || (Phpfox::getUserId() != $aQuiz.user_id && (Phpfox::getUserParam('quiz.can_edit_others_questions') || Phpfox::getUserParam('quiz.can_edit_others_title'))))
		}
		<div class="row_edit_bar_parent">
			<div class="row_edit_bar">
				<a role="button" class="row_edit_bar_action" data-toggle="dropdown">
					<i class="fa fa-action"></i>
				</a>
				<ul class="dropdown-menu dropdown-menu-right">
					{template file='quiz.block.link'}
				</ul>
			</div>
		</div>
		{/if}

		{if Phpfox::getUserParam('quiz.can_approve_quizzes') || Phpfox::getUserId() == $aQuiz.user_id}
		<a href="#{$aQuiz.quiz_id}" class="moderate_link" {if Phpfox::getUserParam('quiz.can_approve_quizzes')}data-id="mod"{else}data-id="user"{/if} rel="quiz"></a>
		{/if}
	</div>
	</div>
{/foreach}
{if !PHPFOX_IS_AJAX}</div>{/if}
{if Phpfox::getUserParam('quiz.can_approve_quizzes')}
{moderation}
{/if}
{pager}
{else}
{if !PHPFOX_IS_AJAX}
<div class="extra_info">
	{_p var='no_quizzes_found'}
</div>
{/if}
{/if}
