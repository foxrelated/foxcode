<?php 
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 3662 2011-12-06 06:02:03Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!'); 
?>
{if isset($aQuiz.quiz_id)}
<div class="view_item_link">
	<a class="page_section_menu_link" href="{permalink module='quiz' id=$aQuiz.quiz_id title=$aQuiz.title}" title="{_p var='view_quiz'}"></a>
</div>
{/if}
{$sCreateJs}

<div class="main_break"></div>
<div style="display:none;" id="hiddenQuestion">
	<div id="js_quiz_layout_default">
		{template file="quiz.block.question"}
	</div>
</div>
<form method="post" action="{$sFormAction}" id="js_form" name="js_form" {if $bShowTitle}onsubmit="{$sGetJsForm}"{/if} {if Phpfox::getUserParam('quiz.can_upload_picture')}enctype="multipart/form-data"{/if}>
	<div id="js_custom_privacy_input_holder">
	{if isset($aQuiz.quiz_id)}
		{module name='privacy.build' privacy_item_id=$aQuiz.quiz_id privacy_module_id='quiz'}	
	{/if}
	</div>	
	{if isset($aQuiz.quiz_id)}
	  <input type="hidden" name="val[quiz_id]"  value="{$aQuiz.quiz_id}" />
	{/if}
	{if !$bShowTitle}<div style="display:none;">{/if}
		<div class="table form-group">
			<div class="table_left">
                {required}{_p var='title'}:
			</div>
			<div class="table_right">
				{if isset($aQuiz.title) && ( ($aQuiz.user_id == Phpfox::getUserId() && Phpfox::getUserParam('quiz.can_edit_own_title')) ||
				($aQuiz.user_id != Phpfox::getUserId() && Phpfox::getUserParam('quiz.can_edit_others_title')))}
				<input class="form-control close_warning" type="text" name="val[title]" value="{$aQuiz.title}" id="title" maxlength="150" size="40" />
				{else}
				<input class="form-control close_warning" type="text" name="val[title]" value="{value type='input' id='title'}" id="title" maxlength="150" size="40" />
				{/if}
			</div>
		</div>
		<div class="table form-group">
			<div class="table_left">
                {required}{_p var='description'}:
			</div>
			<div class="table_right">
				{if isset($aQuiz.description)}
				<textarea class="form-control close_warning" cols="45" rows="6" name="val[description]" id="description" onkeyup="limitChars('description', 255, 'js_limit_info');">{$aQuiz.description}</textarea>
				<div id="js_limit_info" class="extra_info">{_p var='255_character_limit'}</div>
				{else}
				<textarea class="form-control" cols="45" rows="6" name="val[description]" id="description" onkeyup="limitChars('description', 255, 'js_limit_info');">{value type='textarea' id='description'}</textarea>
				<div id="js_limit_info" class="extra_info">{_p var='255_character_limit'}</div>
				{/if}
			</div>
		</div>
		
	{if !$bShowTitle}<div style="display:none;">{/if}
		{if Phpfox::getUserParam('quiz.can_upload_picture')}
		<div class="table form-group-follow">
			<div class="table_left">
                {if Phpfox::getUserParam('quiz.is_picture_upload_required')}{required}{/if} {_p var='banner'}:
			</div>

			<div class="table_right" id="js_event_current_image" {if !isset($aQuiz.image_path) || $aQuiz.image_path == ''} style="display: none;"{/if}>
				 {if isset($aQuiz) && isset($aQuiz.title) && isset($aQuiz.image_path)}
				 <div class="image_content_holder">
					{img thickbox=true title=$aQuiz.title path='quiz.url_image' file=$aQuiz.image_path suffix='' server_id=$aQuiz.server_id}
				</div>
				<div class="extra_info">
					<a href="#" onclick="$Core.quiz.deleteImage({$aQuiz.quiz_id});return false;">{_p var='click_here_to_delete_this_image_and_upload_a_new_one_in_its_place'}</a>
				</div>
				{/if}
			</div>

			<div class="table_right" id="js_submit_upload_image" {if isset($aQuiz.image_path) && $aQuiz.image_path != ''} style="display: none;"{/if}>
				 <input type="file" id='image' accept="image/*" name="image" />
				<div class="extra_info">
					{_p var='you_can_upload_a_jpg_gif_or_png_file'}
				</div>
			</div>
		</div>
		{/if}

		{if !$bShowTitle}
	</div>
	{/if}
<!--{* end of IF bShowTitle *}		-->
		
	{if !$bShowTitle}</div>{/if}
	{if !$bShowQuestions}<div style="display:none">{/if}
		<h3>{_p var='quiz_questions'}</h3>
		<div id="js_quiz_container">
			{if isset($aQuiz.questions)}
			{foreach from=$aQuiz.questions item=Question name=question}
			{template file="quiz.block.question"}
			{/foreach}
			{else}
<!--			{* Not Editing *}-->
			{/if}			
		</div>
		
		<div class="quiz_add_new_question">
			<a href="#" id="js_add_question" class="btn btn-block btn-primary">{_p var='add_another_question'}</a>
		</div>		
		
	{if !$bShowQuestions}</div>{/if}
	
		{if Phpfox::isModule('privacy')}
		<div class="table form-group-follow">
			<div class="table_left">
				{_p var='privacy'}:
			</div>
			<div class="table_right">	
				{module name='privacy.form' privacy_name='privacy' privacy_info='quiz.control_who_can_see_this_quiz' default_privacy='quiz.default_privacy_setting'}
			</div>			
		</div>
		{if Phpfox::isModule('comment')}
		<div class="table form-group-follow hidden">
			<div class="table_left">
				{_p var='comment_privacy'}:
			</div>
			<div class="table_right">	
				{module name='privacy.form' privacy_name='privacy_comment' privacy_info='quiz.control_who_can_comment_on_this_quiz' privacy_no_custom=true}
			</div>			
		</div>
		{/if}				
		{/if}	
	
<div class="table_clear">
			<ul class="table_clear_button">
				<li><input type="submit" value="{if isset($aQuiz.quiz_id)}{_p var='update'}{else}{_p var='submit'}{/if}" class="button btn-primary" /></li>
			</ul>
			<div class="clear"></div>
		</div>
</form>
