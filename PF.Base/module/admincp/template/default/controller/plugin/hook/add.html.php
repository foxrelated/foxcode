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
<form method="post" action="{url link="admincp.plugin.hook.add"}" id="js_form" onsubmit="{$sGetJsForm}">
	{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.product_id}" /></div>
	{/if}
	<div class="table_header">
		{_p var='hook_details'}
	</div>
	{if Phpfox::getUserParam('admincp.can_view_product_options')}
	<div class="table form-group">
		<div class="table_left">
			{_p var='product'}:
		</div>
		<div class="table_right">
			<select name="val[product_id]" id="product_id">
			{foreach from=$aProducts item=aProduct}
				<option value="{$aProduct.product_id}"{value type='select' id='product_id' default=$aProduct.product_id}>{$aProduct.title}</option>
			{/foreach}
			</select>
		</div>
		<div class="clear"></div>
	</div>
	{/if}
	<div class="table form-group">
		<div class="table_left">
			{_p var='module'}:
		</div>
		<div class="table_right">
			<select name="val[module_id]" id="module_id">
				<option value="">{_p var='select'}:</option>
			{foreach from=$aModules key=sModule item=iModuleId}
				<option value="{$iModuleId}"{value type='select' id='module_id' default=$iModuleId}>{translate var=$sModule prefix='module'}</option>
			{/foreach}
			</select>
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='type'}:
		</div>
		<div class="table_right">
			<select name="val[hook_type]" id="hook_type">
				<option value="">{_p var='select'}:</option>
			{foreach from=$aHookTypes item=sHookType}
				<option value="{$sHookType}"{value type='select' id='hook_type' default=$sHookType}>{$sHookType}</option>
			{/foreach}
			</select>
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='call'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[call_name]" value="{value type='input' id='call_name'}" size="30" />
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='active'}:
		</div>
		<div class="table_right">
			<label><input type="radio" name="val[is_active]" style="vertical-align:bottom;" value="1" {value type='radio' id='is_active' default='1' selected=true} />{_p var='yes'}</label>
			<label><input type="radio" name="val[is_active]" style="vertical-align:bottom;" value="0" {value type='radio' id='is_active' default='0'} />{_p var='no'}</label>
		</div>
		<div class="clear"></div>
	</div>	
	<div class="table_clear">
		<input type="submit" value="{_p var='save'}" class="button btn-primary" />
	</div>	
</form>