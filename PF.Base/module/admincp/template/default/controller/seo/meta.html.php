<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: meta.html.php 6188 2013-06-29 08:36:52Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="#" onsubmit="$(this).ajaxCall('admincp.addMeta'); return false;" id="js_meta_form">
	<div class="table_header">
		{_p var='add_new_element'}
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='type'}:
		</div>
		<div class="table_right">
			<select name="val[type_id]">
				<option value="0">{_p var='keyword'}</option>
				<option value="1">{_p var='description'}</option>
				<option value="2">{_p var='title'}</option>
			</select>
		</div>
		<div class="clear"></div>
	</div>	
	
	<div class="table form-group">
		<div class="table_left">
			{_p var='url'}:
		</div>
		<div class="table_right">
			<input type="input" name="val[url]" value="" size="60" id="js_nofollow_url" />
			<div class="extra_info">
				{_p var='provide_the_full_url_to_add_your_custom_element'}
			</div>
		</div>
		<div class="clear"></div>
	</div>
	
	<div class="table form-group">
		<div class="table_left">
			{_p var='value'}:
		</div>
		<div class="table_right">
			<textarea name="val[content]" cols="60" rows="6"></textarea>		
			<div class="extra_info">
				{_p var='if_adding_keywords_make_sure_to_separate_them_with_commas'}
			</div>
		</div>
		<div class="clear"></div>
	</div>	
	
	<div class="table_clear">
		<input type="submit" value="{_p var='submit'}" class="button btn-primary" />
	</div>
</form>

<br /><br />

<div id="js_meta_holder"{if !count($aMetas)} style="display:none;"{/if}>	
	<form method="post" action="#" onsubmit="$(this).ajaxCall('admincp.deleteMeta'); return false;">	
		<div class="table_header">
			{_p var='meta_keyword_descriptions'}
		</div>
		<table cellpadding="0" cellspacing="0" id="js_meta_holder_table">
			<tr>
				<th style="width:10px;"><input type="checkbox" name="val[id]" value="" id="js_check_box_all" /></th>
				<th>{_p var='type'}</th>
				<th>{_p var='url'}</th>
				<th>{_p var='value'}</th>
				<th style="width:20%;">{_p var='added'}</th>
			</tr>
		{foreach from=$aMetas item=aMeta key=iKey}
			<tr id="js_id_row_{$aMeta.meta_id}" class="js_nofollow_row {if is_int($iKey/2)} tr{else}{/if}">
				<td><input type="checkbox" name="id[]" class="checkbox" value="{$aMeta.meta_id}" id="js_id_row{$aMeta.meta_id}" /></td>
				<td>{if $aMeta.type_id == '1'}{_p var='description'}{elseif $aMeta.type_id == '2'}Title{else}{_p var='keyword'}{/if}</td>
				<td>{$aMeta.url}</td>
				<td><textarea name="val[{$aMeta.meta_id}][content]" cols="30" rows="4" style="height:30px;">{$aMeta.content|clean}</textarea></td>
				<td>{$aMeta.time_stamp|convert_time}</td>
			</tr>
		{/foreach}
		</table>
		<div class="table_bottom">	
			<input type="submit" name="delete" value="{_p var='delete'}" class="button sJsConfirm disabled sJsCheckBoxButton" disabled="true" />
		</div>
	</form>
</div>