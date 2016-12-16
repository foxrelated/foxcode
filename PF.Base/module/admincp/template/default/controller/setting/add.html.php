<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Admincp
 * @version 		$Id: add.html.php 6092 2013-06-20 13:24:17Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{literal}
<script type="text/javascript">
<!--
function changeFormValue(sValue)
{
	switch(sValue)
	{
		{/literal}
		case 'boolean':
			sHtml = '<select name="val[value]" id="value"><option value="1" {value type='select' id='value' default='1'}>{_p var='true' phpfox_squote=true}</option><option value="0" {value type='select' id='value' default='0'}>{_p var='false' phpfox_squote=true}</option></select>';
			sClass = 'table_right';
			break;
		case 'password':
			sHtml = '<input type="password" name="val[value]" value="{value type='input' id='value'}" size="40" id="value" autocomplete="off"/>';
			sClass = 'table_right';		
			break;
		case 'array':
			sHtml = '<textarea cols="50" rows="8" name="val[value]" id="value">{value type='textarea' id='value'}</textarea>';
			sHtml += '<div class="p_4">{_p var='setting_array_example' phpfox_squote=true}</div>';
			sClass = 'table_right_text';	
			break;	
		case 'drop':
			sHtml = '<textarea cols="50" rows="8" name="val[value]" id="value">{value type='textarea' id='value'}</textarea>';
			sHtml += '<div class="p_4">{_p var='setting_drop_down_example' phpfox_squote=true}</div>';
			sClass = 'table_right_text';	
			break;	
		{plugin call='admincp.template_controller_setting_add_js_form_value'}
		case 'large_string':
			sHtml = '<textarea cols="50" rows="8" name="val[value]" id="value">{value type='textarea' id='value'}</textarea>';
			sClass = 'table_right_text';
			break;
		default:
			sHtml = '<input type="text" name="val[value]" value="{value type='input' id='value'}" size="40" id="value" />';			
			sClass = 'table_right';			
			break;		
		{literal}
	}
	$('#js_form_value_class').removeClass('table_right');
	$('#js_form_value_class').removeClass('table_right_text');
	$('#js_form_value_class').addClass(sClass);
	$('#js_form_value').html(sHtml);
}
-->
</script>
{/literal}
{$sCreateJs}
<form method="post" action="{url link="admincp.setting.add"}" id="js_setting_form" onsubmit="{$sGetJsForm}">
	{if $bEdit}
	<div><input type="hidden" name="id" value="{$aForms.setting_id}" /></div>
	<div><input type="hidden" name="val[var_name]" value="{$aForms.var_name}" /></div>
	{/if}
	<div class="table_header">
		{_p var='setting_details'}
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='product'}:
		</div>
		<div class="table_right">
			<select name="val[product_id]">
			{foreach from=$aProducts item=aProduct}
				<option value="{$aProduct.product_id}">{$aProduct.title}</option>
			{/foreach}
			</select>
			{help var='admincp.setting_add_product'}
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='module'}:
		</div>
		<div class="table_right">
			<select name="val[module_id]">
				{foreach from=$aModules key=sModule item=iModuleId}
					<option value="{$iModuleId}" {value type='select' id='module_id' default=$iModuleId}>{$sModule}</option>
				{/foreach}
			</select>		
			{help var='admincp.setting_add_module_id'}
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='group'}:
		</div>
		<div class="table_right">
			<select name="val[group_id]">
				<option value="">{_p var='select'}:</option>
				{foreach from=$aGroups item=aGroup}
					<option value="{$aGroup.group_id}" {value type='select' id='group_id' default=$aGroup.group_id}>{$aGroup.var_name}</option>
				{/foreach}
			</select>	
			{help var='admincp.setting_add_group'}
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='variable'}:
		</div>
		<div class="table_right">
			{if $bEdit}
			{$aForms.var_name}
			{else}
			<input type="text" name="val[var_name]" value="{value type='input' id='var_name'}" size="40" id="var_name" maxlength="100" />
			{/if}
			{help var='admincp.setting_add_var'}
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='type'}:
		</div>
		<div class="table_right">
			<select id="js_form_value_actual" name="val[type]" onchange="changeFormValue(this.value);">
				<option value="string" {value type='select' id='type' default='string'}>{_p var='string'}</option>
				<option value="large_string" {value type='select' id='type' default='large_string'}>{_p var='large_string'}</option>
				<option value="password" {value type='select' id='type' default='password'}>{_p var='password'}</option>
				<option value="boolean" {value type='select' id='type' default='boolean'}>{_p var='boolean'}</option>
				<option value="integer" {value type='select' id='type' default='integer'}>{_p var='integer'}</option>
				<option value="array" {value type='select' id='type' default='array'}>{_p var='array'}</option>
				<option value="drop" {value type='select' id='type' default='drop'}>{_p var='defined_drop_down'}</option>
				{plugin call='admincp.template_controller_setting_add_type_drop_down'}
			</select>
			{help var='admincp.setting_add_type'}
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='value'}:
		</div>
		<div id="js_form_value_class" class="table_right_text">
			<div id="js_form_value">
				<textarea cols="60" rows="8" name="val[value]" id="value">{value type='textarea' id='value'}</textarea>			
			</div>
			{help var='admincp.setting_add_value'}
		</div>
		<div class="clear"></div>
	</div>
	<div class="table_header">
		{_p var='language_package_details'}
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='title'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[title]" value="{value type='input' id='title'}" size="40" id="title" maxlength="250" />
			{help var='admincp.setting_add_title'}
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='info'}:
		</div>
		<div class="table_right_text">
			<textarea cols="50" rows="8" name="val[info]" id="info">{value type='textarea' id='info'}</textarea>
			{help var='admincp.setting_add_info'}
		</div>
		<div class="clear"></div>
	</div>
	<div class="table_clear">
		<input type="submit" value="{_p var='submit'}" class="button btn-primary" />
	</div>
</form>
<script type="text/javascript">
$Behavior.loadCustomFormValues = function(){l}
	var oSelected = document.getElementById('js_form_value_actual');	
	changeFormValue(oSelected.options[oSelected.selectedIndex].value);
{r}
</script>