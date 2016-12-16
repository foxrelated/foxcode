<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_Newsletter
 * @version 		$Id: add.html.php 4264 2012-06-13 11:03:47Z Miguel_Espinoza $
 */

?>
{$sCreateJs}
{if isset($sMessage)}
<div class="message">{$sMessage}</div>
{else}
<form method="post" id="frmNewsletter" action="{url link='admincp.newsletter.add'}">
	<div class="table_header">
		{_p var='newsletter'}
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='archive'}:
		</div>
		<div class="table_right">
			<input type="checkbox" name="val[archive]" value="1" {if isset($aForms.archive) && $aForms.archive == 1}checked="checked"{/if}>
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group" id="js_privacy">
		<div class="table_left"> {_p var='override_privacy'}:</div>
		<div class="table_right">
			<input type="checkbox" name="val[privacy]" value="1" {if isset($aForms.privacy) && $aForms.privacy == 1}checked="checked"{/if}>
		</div>
		<div class="clear"></div>
	</div>
	<div class="table_header">
		{_p var='audience'}
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='user_groups'}:
		</div>
		<div class="table_right">
			<select name="val[is_user_group]" id="js_is_user_group">
				<option value="1"{value type='select' id='is_user_group' default='1'}>{_p var='all_user_groups'}</option>
				<option value="2"{value type='select' id='is_user_group' default='2'}>{_p var='selected_user_groups'}</option>
			</select>
			<div class="p_4" style="display:none;" id="js_user_group">
				{foreach from=$aUserGroups item=aUserGroup}
				<div class="p_4">
					<label><input type="checkbox" name="val[user_group][]" value="{$aUserGroup.user_group_id}"{if isset($aAccess) && is_array($aAccess)}{if in_array($aUserGroup.user_group_id, $aAccess)} checked="checked" {/if}{else} checked="checked" {/if}/> {$aUserGroup.title|convert|clean}</label>
				</div>
				{/foreach}
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='location'}:
		</div>
		<div class="table_right">
			{select_location value_title='phrase var=core.any'}
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='gender'}:
		</div>
		<div class="table_right">
			{select_gender value_title='phrase var=core.any'}
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='age_group_between'}:
		</div>
		<div class="table_right">
			<select name="val[age_from]" id="age_from">
				<option value="">{_p var='any'}</option>
				{foreach from=$aAge item=iAge}
				<option value="{$iAge}"{value type='select' id='age_from' default=$iAge}>{$iAge}</option>
				{/foreach}
			</select>
			<span id="js_age_to">
				{_p var='and'}
				<select name="val[age_to]" id="age_to">
					<option value="">{_p var='any'}</option>
					{foreach from=$aAge item=iAge}
					<option value="{$iAge}"{value type='select' id='age_to' default=$iAge}>{$iAge}</option>
					{/foreach}
				</select>
			</span>
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='how_many_per_round'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[total]" value="{value type='input' id='total' default='50'}" id="total" size="40" maxlength="150" />
		</div>
		<div class="clear"></div>
	</div>

	<div class="table_header">
		{_p var='content'}
	</div>
	<div class="table form-group">
		<div class="table_left">
			{required}{_p var='subject'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[subject]" value="{value type='input' id='subject'}" id="subject" size="40" maxlength="150" />
		</div>
		<div class="clear"></div>
	</div>
	
	<div class="table form-group">
		<div class="table_left" id="lbl_html_text">
			{_p var='html_text'}:
		</div>
		<div class="table_right">
			{editor id='text' rows='15'}
		</div>
		<div class="clear"></div>
	</div>
		
	<div class="table form-group">
		<div class="table_left js_txtPlain">
			{_p var='plain_text'}:
		</div>
		<div class="table_right js_txtPlain">
			<textarea name="val[txtPlain]" id="txtPlain" style="width:98%;" cols="50" rows="15"></textarea>
			<a href="javascript:void(0);" onclick="$Core.Newsletter.showPlain(); return false;">{_p var='get_plain_text_from_html'}</a>
		</div>
		<div class="clear"></div>
	</div>
	
	<div class="extra_info table">
		{_p var='keyword_substitutions'}:
		<ul>
			<li>{_p var='123_full_name_125_recipient_s_full_name'}</li>
			<li>{_p var='123_user_name_125_recipient_s_user_name'}</li>
			<li>{_p var='123_site_name_125_site_s_name'}</li>
		</ul>
	</div>
	<div class="table_clear">		
		<input type="button" value="{_p var='send_now'}" class="button btn-primary" onclick="$Core.Newsletter.checkText();" />
	</div>
	<div class="table_clear"></div>
</form>
{/if}