<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Forum
 * @version 		$Id: $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if isset($iEditId) && !PHPFOX_IS_AJAX}
	<div>
		<a class="page_section_menu_link" href="{permalink module='forum.thread' id=$aForms.thread_id title=$aForms.title}" title="{_p var='view_thread'}"></a>
	</div>
{/if}
{if Phpfox::getUserParam('forum.can_post_announcement') || Forum_Service_Moderate_Moderate::instance()->hasAccess('' . $iActualForumId . '', 'post_announcement')}
	<script type="text/javascript">
	{literal}
	function selectThreadType(oObj)
	{
		if (oObj.value == 'announcement')
		{
			$('.js_announcement_list').show();
			$('#js_forum_close').hide();
		}
		else
		{
			$('.js_announcement_list').hide();
			$('#js_forum_close').show();
		}	
	}
	{/literal}
	</script>
{/if}
{$sCreateJs}
<form method="post" action="{$sFormLink}" id="js_forum_form" onsubmit="{if PHPFOX_IS_AJAX} $('#js_forum_form_submit_btn').prop('disabled',true); if ({$sGetJsForm}) {l} $Core.processForm('#js_forum_submit_button'); {plugin call='forum.template_controller_post_ajax_onsubmit'}{if isset($iEditId)} $(this).ajaxCall('forum.updateText'); {else} $(this).ajaxCall('forum.addReply'); {/if} {r} return false;{else}{$sGetJsForm}{/if}">
	<div><input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" /></div>
	{if isset($iTotalPosts)}
		<div><input type="hidden" name="val[total_post]" value="{$iTotalPosts}" /></div>
	{/if}
{if $aCallback !== false}
	<div><input type="hidden" name="val[group_id]" value="{$aCallback.item}" /></div>
{/if}
{if isset($iForumId)}
	<div><input type="hidden" name="val[forum_id]" value="{$iForumId}" /></div>
{/if}
{if isset($iThreadId)}
	<div><input type="hidden" name="val[thread_id]" value="{$iThreadId}" /></div>
{/if}
{if isset($iEditId)}
	<div><input type="hidden" name="edit" value="{$iEditId}" /></div>
{/if}
	{if isset($iForumId)}
		<div class="table form-group">
			<div class="table_right">
				<input class="form-control close_warning" type="text" name="val[title]" placeholder="{_p var='title'}" value="{value type='input' id='title'}" size="40" id="title" />
			</div>
		</div>
	{/if}
	<div class="table form-group">
		<div class="table_right" id="js_forum_new_post">
      {if defined('PHPFOX_FORUM_REPLY_THREAD')}
        {editor id='text' placeholder='your_reply_dot_dot_dot'}
      {else}
        {editor id='text' placeholder='your_message_dot_dot_dot'}
      {/if}
			{if !isset($iForumId)}
			{literal}
			<script>
				$Ready(function() {
					if ($('#js_forum_new_post').length) {
						$('#js_forum_new_post textarea').focus();
					}
				});
			</script>
			{/literal}
			{/if}
		</div>
	</div>

	<div class="form_extra" style="display: block;">

		<div>
			{if !isset($iEditId)}
				<div class="table form-group-follow">
					<div class="table_left">
						{_p var='subscribe'}:
					</div>
					<div class="table_right">
						<div class="item_is_active_holder">
							<span class="js_item_active item_is_active"><input type="radio" name="val[is_subscribed]" value="1" class="v_middle"{value type='radio' id='is_subscribed' default='1' selected='true'}/> {_p var='yes'}</span>
							<span class="js_item_active item_is_not_active"><input type="radio" name="val[is_subscribed]" value="0" class="v_middle"{value type='radio' id='is_subscribed' default='0'}/> {_p var='no'}</span>
						</div>
					</div>
				</div>
			{/if}

			{if isset($iForumId) && $iForumId > 0 && Phpfox::isModule('poll') && Phpfox::getUserParam('poll.can_create_poll') && Phpfox::getUserParam('forum.can_add_poll_to_forum_thread')}
				<div><input type="hidden" name="val[poll_id]" value="" id="js_poll_id"></div>
				<div class="separate"></div>
				<div class="table form-group">
					<div class="table_left">
						{_p var='poll'}:
					</div>
					<div class="table_right">
					<div id="js_attach_poll_question">
					{if $bIsEdit && $aForms.poll_id > 0}
						{$aForms.poll_question|clean} - <a href="#" onclick="$.ajaxCall('forum.deletePoll', 'poll_id={$aForms.poll_id}&amp;thread_id={$aForms.thread_id}'); return false;" title="{_p var='click_to_delete_this_poll'}">{_p var='delete'}</a>
					{/if}
					</div>
					<div id="js_attach_poll"{if $bIsEdit && $aForms.poll_id > 0} style="display:none;"{/if}>
						<input type="button" name="poll" value="{_p var='attach_poll'}" class="button btn-primary" onclick="tb_show('{_p var='attach_poll'}', $.ajaxBox('poll.add', 'height=340&amp;width=550&amp;item_id={$iForumId}&amp;module_id=forum'));" />
					</div>
					</div>
				</div>
			{/if}


			{if isset($aCallback) && $aCallback !== false}
			{else}
				{if Phpfox::isModule('tag') && Phpfox::getUserParam('forum.can_add_tags_on_threads') && isset($iForumId)}
					{if isset($aCallback) && $aCallback !== false}
					{module name='tag.add' sType='forum_group'}
					{else}
					{module name='tag.add' sType='forum'}
					{/if}
				{/if}
			{/if}
			{if Phpfox::isModule('captcha') && Phpfox::getUserParam('forum.enable_captcha_on_posting')}{module name='captcha.form' sType='forum'}{/if}


			{if isset($iForumId)}

			{if Phpfox::getUserParam('forum.can_stick_thread')
				|| Phpfox::getUserParam('forum.can_close_a_thread')
				|| Phpfox::getUserParam('forum.can_post_announcement')
				|| Forum_Service_Moderate_Moderate::instance()->hasAccess('' . $iActualForumId . '', 'post_announcement')
				|| Forum_Service_Moderate_Moderate::instance()->hasAccess('' . $iActualForumId . '', 'post_sticky')

			}

			{if (($bIsEdit && $aForms.is_announcement != 1) || (!$bIsEdit))}

				<h3>{_p var='additional_options'}</h3>

				{if Phpfox::getUserParam('forum.can_stick_thread')
					|| Phpfox::getUserParam('forum.can_post_announcement')
					|| Forum_Service_Moderate_Moderate::instance()->hasAccess('' . $iActualForumId . '', 'post_announcement')
					|| Forum_Service_Moderate_Moderate::instance()->hasAccess('' . $iActualForumId . '', 'post_sticky')

				}

				{if ($bIsEdit && $aForms.is_announcement != 1) || (!$bIsEdit)}
					<div class="table form-group">
						<div class="table_left">
							{_p var='type'}:
						</div>
						<div class="table_right label_hover">
							<select class="form-control" name="val[type_id]" {if Phpfox::getUserParam('forum.can_post_announcement') || Forum_Service_Moderate_Moderate::instance()->hasAccess('' . $iActualForumId . '', 'post_announcement')} onchange="selectThreadType(this);"{/if}>
								<option value="thread"{value type='select' id='type_id' default='thread'}>{_p var='thread'}</option>
								{if Phpfox::getUserParam('forum.can_stick_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess('' . $iActualForumId . '', 'post_sticky')}
								<option value="sticky"{value type='select' id='type_id' default='sticky'}>{_p var='sticky'}</option>
								{/if}
								{if Phpfox::getUserParam('forum.can_sponsor_thread') && (!isset($bIsGroup) || $bIsGroup != '1')}
								<option value="sponsor"{value type='select' id='type_id' default='sponsor'}>{_p var='sponsor'}</option>
								{/if}
								{if (Phpfox::getUserParam('forum.can_post_announcement') || Forum_Service_Moderate_Moderate::instance()->hasAccess('' . $iActualForumId . '', 'post_announcement')) && !$bIsEdit}
								<option value="announcement"{value type='select' id='type_id' default='announcement'}>{_p var='announcement'}</option>
								{/if}
							</select>
							{if $aCallback === false}
							{if (Phpfox::getUserParam('forum.can_post_announcement') || Forum_Service_Moderate_Moderate::instance()->hasAccess('' . $iActualForumId . '', 'post_announcement')) && !$bIsEdit}
							<div style="margin-top:10px;{if !$bPosted} display:none;{/if}" class="js_announcement_list">
								{_p var='select_a_parent_forum'}:
								<div class="p_4 form-inline">
									<select class="form-control" name="val[announcement_forum_id]" style="width:300px;">
										{$sForumParents}
									</select>
									<div class="extra_info">
										{_p var='announcement_will_be_included_in_child_forums'}
									</div>
								</div>
							</div>
							{/if}
							{/if}
						</div>
					</div>
					<div class="js_announcement_list" style="display:none;">
						<div class="separate"></div>
					</div>
				{/if}

			{/if}

			{if Phpfox::getUserParam('forum.can_close_a_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess('' . $iActualForumId . '', 'close_thread')}
			{if ($bIsEdit && $aForms.is_announcement != 1) || (!$bIsEdit)}
			<div class="table form-group-follow" id="js_forum_close">
				<div class="table_left">
					{_p var='closed'}:
				</div>
				<div class="table_right">
					<div class="item_is_active_holder">
						<span class="js_item_active item_is_active"><label><input type="radio" name="val[is_closed]" value="1" class="v_middle"{value type='radio' id='is_closed' default='1'}/> {_p var='yes'}</label></span>
						<span class="js_item_active item_is_not_active"><label><input type="radio" name="val[is_closed]" value="0" class="v_middle"{value type='radio' id='is_closed' default='0' selected='true'}/> {_p var='no'}</label></span>
					</div>
				</div>
			</div>
			{/if}
			{/if}
		{/if}
		{/if}
		{/if}

		</div>
	</div>

	<div class="table_clear">
		<input type="submit" id="js_forum_form_submit_btn" value="{if isset($iEditId)}{_p var='update'}{else}{_p var='submit'}{/if}" class="button btn-primary" />
	</div>
	
</form>