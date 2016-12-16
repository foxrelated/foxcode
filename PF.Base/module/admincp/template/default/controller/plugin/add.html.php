<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Admincp
 * @version 		$Id: add.html.php 979 2009-09-14 14:05:38Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!');

?>
{$sCreateJs}
<form method="post" action="{url link="admincp.plugin.add"}" id="js_form" onsubmit="{$sGetJsForm}">
	{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.plugin_id}" /></div>
	{/if}
	{module name='admincp.product.form'}
	{module name='admincp.module.form'}
	<div class="table form-group">
		<div class="table_left">
			{_p var='title'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[title]" value="{value type='input' id='title'}" size="30" id="title" />
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='hook'}:
		</div>
		<div class="table_right">
			<select name="val[call_name]" id="call_name">
				<option value="">{_p var='select'}:</option>
			{foreach from=$aHooks key=hook_type item=aHook1}
				<optgroup label="{$hook_type}">
				{foreach from=$aHook1 key=module_name item=aHook2}
				{if $hook_type != 'library'}
					<option value="" style="font-weight:bold;">{$module_name|translate:'module'}</option>
				{/if}
					{foreach from=$aHook2 item=aHook3}
						<option value="{$aHook3.call_name}"{value type='select' id='call_name' default=$aHook3.call_name}>{if $hook_type != 'library'}--- {/if}{$aHook3.call_name}</option>
					{/foreach}					
				{/foreach}
				</optgroup>
			{/foreach}
			</select>
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group-follow">
		<div class="table_left">
			{_p var='active'}:
		</div>
		<div class="table_right">
			<div class="item_is_active_holder">		
				<span class="js_item_active item_is_active"><input type="radio" name="val[is_active]" value="1" {value type='radio' id='is_active' default='1' selected='true'}/> {_p var='yes'}</span>
				<span class="js_item_active item_is_not_active"><input type="radio" name="val[is_active]" value="0" {value type='radio' id='is_active' default='0'}/> {_p var='no'}</span>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='php_code'}
		</div>
		<div class="table_right">
			<textarea name="val[php_code]" rows="20" cols="50" id="php_code">{value type='textarea' id='php_code'}</textarea>
		</div>
	</div>
	<div class="table_clear">
		<input type="submit" value="{_p var='save'}" class="button btn-primary" />
	</div>	
</form>