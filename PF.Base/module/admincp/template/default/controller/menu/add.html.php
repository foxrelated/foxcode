<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Admincp
 * @version 		$Id: add.html.php 7230 2014-03-26 21:14:12Z Fern $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form enctype="multipart/form-data" method="post" action="{if $bIsEdit}{url link="admincp.menu.add" id=$aForms.menu_id}{else}{url link="admincp.menu.add"}{/if}">
	{if $bIsEdit}
	<div><input type="hidden" name="menu_id" value="{$aForms.menu_id}" /></div>
	{/if}
	{if $bIsPage}
	<div><input type="hidden" name="val[page_id]" value="{$aPage.page_id}" /></div>
	<div><input type="hidden" name="val[product_id]" value="{$aPage.product_id}" /></div>
	<div><input type="hidden" name="val[module_id]" value="{$sModuleValue}" /></div>
	<div><input type="hidden" name="val[url_value]" value="{$aPage.title_url}" /></div>
	<div><input type="hidden" name="val[is_page]" value="true" /></div>
	{/if}
	{if !$bIsPage}
	<div class="table form-group"{if !PHPFOX_IS_TECHIE} style="display:none;"{/if}>
		<div class="table_left">
			{_p var='product'}:
		</div>
		<div class="table_right">
			<select name="val[product_id]">
			{foreach from=$aProducts item=aProduct}
				<option value="{$aProduct.product_id}"{value type='select' id='product_id' default=$aProduct.product_id}>{$aProduct.title}</option>
			{/foreach}
			</select>
			{help var='admincp.menu_add_product'}
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group"{if !PHPFOX_IS_TECHIE} style="display:none;"{/if}>
		<div class="table_left">
			{_p var='module'}:
		</div>
		<div class="table_right">			
			<select name="val[module_id]">
			<option value="">{_p var='select'}:</option>
			{foreach from=$aModules key=sModule item=iModuleId}
				<option value="{$iModuleId}|{$sModule}"{value type='select' id='module_id' default=$iModuleId}>{translate var=$sModule prefix='module'}</option>
			{/foreach}
			</select>
			{help var='admincp.menu_add_module'}
		</div>
		<div class="clear"></div>
	</div>
	{/if}
	<div class="table form-group">
		<div class="table_left">
            {_p var='placement'}:
		</div>
		<div class="table_right">
			<select name="val[m_connection]">
			<option value="">{_p var='select'}:</option>
			<optgroup label="{_p var='menu_block'}">
			{foreach from=$aTypes item=sType}
				<option value="{$sType}"{value type='select' id='m_connection' default=$sType}>{$sType}</option>
			{/foreach}
			</optgroup>
			</select>
			{help var='admincp.menu_add_connection'}
		</div>
		<div class="clear"></div>
	</div>
	{if !$bIsPage}
	<div class="table form-group">
		<div class="table_left">
			{_p var='url'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[url_value]" id="url_value" value="{value type='input' id='url_value'}" size="40" maxlength="250" />
			{if !$bIsEdit && count($aPages)}
			<div class="p_4" style="display:none;">
			{_p var='or_select_a_page'}
			<select name="val[url_value_page]" onchange="$('#url_value').val(this.value);">
				<option value="">{_p var='select'}:</option>
			{foreach from=$aPages key=sPage item=iId}		
				<option value="{$sPage}"{value type='select' id='m_connection' default=$sType}>{$sPage}</option>
			{/foreach}
			</select>
			</div>
			{/if}
			{help var='admincp.menu_add_url'}
		</div>
		<div class="clear"></div>
	</div>
	{/if}

	<div class="table form-group">
		<div class="table_left">
			{_p var='font_awesome_icon'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[mobile_icon]" value="{value type='input' id='mobile_icon'}" />
		</div>
	</div>

	<div class="table form-group">
		<div class="table_left">
			{_p var='menu'}:
		</div>
		<div class="table_right_text">
			<div class="lang_table">
			{foreach from=$aLanguages item=aLanguage}
				<div class="lang_value">
					<textarea cols="50" rows="5" name="val[text][{if isset($aLanguage.phrase_id)}{$aLanguage.phrase_id}{else}{$aLanguage.language_id}{/if}]">{if isset($aLanguage.text)}{$aLanguage.text|htmlspecialchars}{/if}</textarea>
				</div>
				<div class="lang_title">{$aLanguage.title}</div>
			{/foreach}
			</div>
		</div>
		<div class="clear"></div>
	</div>


	<div style="display:none;">
		<div class="table_header">
			{_p var='user_group_access'}
		</div>
		<div class="table form-group">
			<div class="table_left">
				{_p var='allow_access'}:
			</div>
			<div class="table_right">
			{foreach from=$aUserGroups item=aUserGroup}
				<div class="p_4">
					<label><input type="checkbox" name="val[allow_access][]" value="{$aUserGroup.user_group_id}"{if isset($aAccess) && is_array($aAccess)}{if !in_array($aUserGroup.user_group_id, $aAccess)} checked="checked" {/if}{else} checked="checked" {/if}/> {$aUserGroup.title|convert|clean}</label>
				</div>
			{/foreach}
				{help var='admincp.menu_add_access'}
			</div>
			<div class="clear"></div>
		</div>
	</div>

	<div class="table_clear">
		<div><input type="hidden" name="send_path" value="{url link='admincp.menu'}" /></div>
		{if $bIsEdit}
		<input type="submit" value="{_p var='save'}" class="button btn-primary" />
		{else}
		<input type="submit" value="{_p var='submit'}" class="button btn-primary" />
		{/if}
	</div>
</form>