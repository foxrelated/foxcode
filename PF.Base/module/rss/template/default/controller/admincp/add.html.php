<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 1224 2009-10-27 19:03:46Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.rss.add'}">
{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.feed_id}" /></div>
{/if}
	<div class="table_header">
		{_p var='feed_details'}
	</div>
	{if !$bIsEdit}
	{module name='admincp.product.form'}
	{module name='admincp.module.form'}
	{/if}
	<div class="table form-group">
		<div class="table_left">
			{required}{_p var='group'}:
		</div>
		<div class="table_right">
			<select name="val[group_id]">
				<option value="">{_p var='select'}:</option>
			{foreach from=$aGroups item=aGroup}
				<option value="{$aGroup.group_id}"{value type='select' id='group_id' default=$aGroup.group_id}>{_p var=$aGroup.name_var}</option>
			{/foreach}
			</select>
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{required}{_p var='title'}:
		</div>
		<div class="table_right">
			{if $bIsEdit}
			{module name='language.admincp.form' type='text' id='title_var' var_name=$aForms.title_var}
			{else}
			{module name='language.admincp.form' type='text' id='title_var'}
			{/if}
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{required}{_p var='description'}:
		</div>
		<div class="table_right">
			{if $bIsEdit}
			{module name='language.admincp.form' type='textarea' id='description_var' var_name=$aForms.description_var}
			{else}
			{module name='language.admincp.form' type='textarea' id='description_var'}
			{/if}
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{required}{_p var='link'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[feed_link]" id="feed_link" value="{value id='feed_link' type='input'}" size="40" />
		</div>
		<div class="clear"></div>
	</div>		
	<div class="table form-group">
		<div class="table_left">
			{_p var='php_group_code'}:
		</div>
		<div class="table_right">
			<textarea cols="60" rows="15" name="val[php_group_code]" id="php_group_code" style="width:99%;">{value id='php_group_code' type='textarea'}</textarea>
		</div>
		<div class="clear"></div>
	</div>		
	<div class="table form-group">
		<div class="table_left">
			{required}{_p var='php_view_code'}:
		</div>
		<div class="table_right">
			<textarea cols="60" rows="15" name="val[php_view_code]" id="php_view_code" style="width:99%;">{value id='php_view_code' type='textarea'}</textarea>
		</div>
		<div class="clear"></div>
	</div>	
	<div class="table form-group-follow">
		<div class="table_left">
			{required}{_p var='site_wide'}:
		</div>
		<div class="table_right">	
			<div class="item_is_active_holder">		
				<span class="js_item_active item_is_active"><input type="radio" name="val[is_site_wide]" value="1" {value type='radio' id='is_site_wide' default='1'}/> {_p var='yes'}</span>
				<span class="js_item_active item_is_not_active"><input type="radio" name="val[is_site_wide]" value="0" {value type='radio' id='is_site_wide' default='0' selected='true'}/> {_p var='no'}</span>
			</div>
		</div>
		<div class="clear"></div>		
	</div>		
	<div class="table form-group-follow">
		<div class="table_left">
			{required}{_p var='is_active'}:
		</div>
		<div class="table_right">	
			<div class="item_is_active_holder">		
				<span class="js_item_active item_is_active"><input type="radio" name="val[is_active]" value="1" {value type='radio' id='is_active' default='1' selected='true'}/> {_p var='yes'}</span>
				<span class="js_item_active item_is_not_active"><input type="radio" name="val[is_active]" value="0" {value type='radio' id='is_active' default='0'}/> {_p var='no'}</span>
			</div>
		</div>
		<div class="clear"></div>		
	</div>		
	<div class="table_clear">
		<input type="submit" value="{_p var='submit'}" class="button btn-primary" />
	</div>
</form>