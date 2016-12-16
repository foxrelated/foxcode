<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Language
 * @version 		$Id: phrase.html.php 7195 2014-03-17 15:54:31Z Fern $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="block_search">
	<form method="post" action="{url link="admincp.language.phrase"}">
		{token}
		<div class="table_header">
			{_p var='filter'}
		</div>

	<div class="form-group table">
		<label>{_p var='search_for_text'}:</label>
		{$aFilters.search}
		<span class="help-block">{_p var='search'}...</span>
			{$aFilters.search_type}
	</div>

		<div id="js_admincp_search_options" style="display:none;">
			<div class="table form-group">
				<div class="table_left">
					{_p var='language_packages'}:
				</div>
				<div class="table_right">
					{$aFilters.language_id}
				</div>
				<div class="clear"></div>
			</div>
			<div class="table form-group">
				<div class="table_left">
					{_p var='phrases'}:
				</div>
				<div class="table_right">
					{$aFilters.translate_type}
				</div>
				<div class="clear"></div>
			</div>
			<div class="table form-group">
				<div class="table_left">
					{_p var='display'}:
				</div>
				<div class="table_right">
					{$aFilters.display}
				</div>
				<div class="clear"></div>
			</div>
			<div class="table form-group">
				<div class="table_left">
					{_p var='sort'}:
				</div>
				<div class="table_right">
					{$aFilters.sort} {$aFilters.sort_by}
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<div class="table_clear">
			<div class="table_clear_more_options">
				<a href="#" rel="{_p var='view_less_search_options'}" onclick="$('#js_admincp_search_options').toggle(); var text = $(this).text(); $(this).text($(this).attr('rel')); $(this).attr('rel', text); return false;">{_p var='view_more_search_options'}</a>
			</div>
			<input type="submit" name="search[submit]" value="{_p var='submit'}" class="button" />
		</div>
	</form>
</div>

<div class="block_content">
	{if count($aRows)}
	<form method="post" action="{if $bIsForceLanguagePackage}{url link='admincp.language.phrase' search-id=$sSearchIdNormal search-rid=$sSearchId page=$iPage lang-id=$iLangId}{else}{url link='admincp.language.phrase' search-id=$sSearchIdNormal search-rid=$sSearchId page=$iPage}{/if}">
		<table cellpadding="0" cellspacing="0">
		<tr>
			<th style="width:10px;"><input type="checkbox" name="val[id]" value="" id="js_check_box_all" /></th>
			<th style="width:20%;">{_p var='variable'}</th>
			{if !$iLangId}<th style="width:10%;">{_p var='language'}</th>{/if}
			<th style="width:30%;">{_p var='original'}</th>
			<th style="width:90%;">{_p var='text'}</th>
		</tr>
		{foreach from=$aRows name=rows item=aRow}
		<tr id="js_row{$aRow.phrase_id}" class="checkRow{if is_int($phpfox.iteration.rows/2)} tr{else}{/if}">
			<td><input type="checkbox" name="id[]" class="checkbox" value="{$aRow.phrase_id}" id="js_id_row{$aRow.phrase_id}" /></td>
			<td title="{$aRow.var_name}">
				<input type="text" name="null" value="{$aRow.var_name}" size="25" style="width:95%;" onfocus="tb_show('{_p var='phrase_variables' phpfox_squote=true}', $.ajaxBox('language.sample', 'height=240&width=600&phrase={$aRow.var_name}'));" />
			</td>
			{if !$iLangId}<td>{$aRow.title}</td>{/if}
			<td>{$aRow.sample_text}</td>
			<td class="t_center{if $aRow.is_translated} is_translated{/if}"><textarea cols="30" rows="6" name="text[{$aRow.phrase_id}]" class="text" style="width:95%;">{$aRow.text|htmlspecialchars}</textarea></td>
		</tr>
		{/foreach}
		</table>
		<div class="table_bottom table_hover_action">
			<input type="submit" name="save_selected" value="{_p var='save_selected'}" class="button disabled sJsCheckBoxButton" disabled="true" />
			<input type="submit" name="delete" value="{_p var='delete_selected'}" class="button sJsConfirm disabled sJsCheckBoxButton" disabled="true" />
			<input type="submit" name="revert_selected" value="{_p var='revert_selected_default'}" class="button sJsConfirm disabled sJsCheckBoxButton" disabled="true" />
			<input type="submit" name="save" value="{_p var='save_all'}" class="button" />
		</div>
	</form>
	{pager}
	{else}
	<div class="p_4 t_center">
		{_p var='phrases_found'}
	</div>
	{/if}
</div>
