<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Mail
 * @version 		$Id: view.html.php 4443 2012-07-02 08:51:49Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if Phpfox::getParam('mail.threaded_mail_conversation')}
<div class="message">
	{_p var='you_are_viewing_a_message_that_is_from_our_legacy_inbox'}
</div>	
{/if}
<div class="item_info">
	<ul class="extra_info_middot">
		<li>{$aMail.time_stamp|convert_time}</li>
		<li>&middot;</li>
		<li>
			<span class="js_hover_title">
		{if $aMail.owner_user_id == Phpfox::getUserId()}
			{if $aMail.owner_user_id == $aMail.viewer_user_id && $aMail.owner_user_id == Phpfox::getUserId()}
			{_p var='you'} <span class="js_hover_info">{_p var='this_message_was_sent_from_you'}</span>
			{else}
			{$aMail|user:'viewer_'}<span class="js_hover_info">{_p var='this_message_was_sent_to_full_name' full_name=$aMail|user:'viewer_'}</span>
			{/if}	
		{elseif $aMail.owner_user_id != 0}
			{$aMail|user:'owner_'}<span class="js_hover_info">{_p var='this_message_was_sent_from_full_name' full_name={$aMail|user:'owner_'}</span>
		{else}
			{_p var='site_sent_you_a_message' site=$sSite}<span class="js_hover_info">{_p var='this_message_was_sent_from_full_name' full_name=$sSite}</span>
		{/if}		
			</span>
		</li>
	</ul>
</div>

	<div class="item_bar">
		<div class="mail_next_prev">
			<ul>
				{if $iNextId != ""}
					<li class="previous_message"><a href="{url link='mail.view' id=$iNextId}">{_p var='previous'}</a></li>
				{/if}
				{if $iPrevId != ""}
					<li class="next_message"><a href="{url link='mail.view' id=$iPrevId}">{_p var='next'}</a></li>
				{/if}
			</ul>
			<div class="clear"></div>
		</div>
		{if !Phpfox::getParam('mail.threaded_mail_conversation')}
		<div class="item_bar_action_holder">
			<a role="button" data-toggle="dropdown" class="item_bar_action"><span>{_p var='actions'}</span></a>
			<ul class="dropdown-menu dropdown-menu-right">
				{if Phpfox::isModule('report') && $aMail.owner_user_id != Phpfox::getUserId()}
				<li><a href="#?call=report.add&amp;height=210&amp;width=400&amp;type=mail&amp;id={$aMail.mail_id}" class="inlinePopup" title="{_p var='report_this_message'}">{_p var='report'}</a></li>
				{/if}			
				<li class="item_delete"><a href="{url link='mail' action='delete' id=$aMail.mail_id}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete'}</a></li>
			</ul>				
		</div>		
		{/if}
	</div>

	{if isset($bMass)}
	<div class="p_top_8">
		{_p var='mass_message_to'}:
		{foreach from=$aMails name=mass item=aMass}{if $phpfox.iteration.mass != 1}, {/if}{$aMass|user}{/foreach}
	</div>
	{/if}
	<div>
		{$aMail.text|parse|split:100}
		{if $aMail.parent_id && $aMail.text_reply}
		<div class="quote">
			<div class="quote_body">
				{$aMail.text_reply|parse|split:80}
			</div>
		</div>
		{/if}
	</div>

	{if isset($aAttachments)}
	{module name='attachment.list' sType='mail' attachments=$aAttachments}
	{/if}

	{if Phpfox::getParam('mail.threaded_mail_conversation')}
		
	{else}
	{if $aMail.viewer_user_id == Phpfox::getUserId() && $aMail.owner_user_id != 0}
	<br />
	{$sCreateJs}
	<form method="post" action="{url link='mail.view' id=$aMail.mail_id}" id="js_form_mail" onsubmit="{$sGetJsForm}">
		<div><input type="hidden" name="val[parent_id]" value="{$aMail.mail_id}" /></div>
		<div><input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" /></div>
		<div class="table form-group">
			<div class="table_left">
				<label for="message">{_p var='reply'}:</label>
			</div>
			<div class="table_right" id="js_mail_textarea">
				{editor id='message' rows='8'}
			</div>
		</div>
		<div class="table_clear">
			<input type="submit" value="{_p var='send'}" class="button" id="js_mail_submit btn-primary" />
		</div>
	</form>
	{/if}
	{/if}