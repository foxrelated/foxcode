<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="table_header">
	{_p var='add_category'}
</div>
<form action="{url link='admincp.egift.categories'}" method="post">
	{foreach from=$aLanguages item=aLang key=iKey}
		<div class="table form-group">
			<div class="table_left">
					{$aLang.title}:
			</div>
			<div class="table_right">
				<input type="text" name="cat[lang][{$aLang.language_id}]">
			</div>
		</div>
	{/foreach}
	<div class="table form-group-follow">
		<div class="table_left">
			{_p var='schedule_availability'}:
			
		</div>
		<div class="table_right">
			<div class="item_is_active_holder">
				<span class="js_item_active item_is_active" onclick="$('#availableSince, #availableUntil').show();">
					<input type="radio" name="cat[do_schedule]" value="1" {value type='radio' id='do_schedule' default='1'}/> {_p var='yes'}
				</span>
				<span class="js_item_active item_is_not_active" onclick="$('#availableSince, #availableUntil').hide();">
					<input type="radio" name="cat[do_schedule]" value="0" {value type='radio' id='do_schedule' default='0'  selected='true'}/> {_p var='no'}
				</span>
			</div>
			<div class="extra_info">{_p var='when_disabled_this_category_will_only_show_up_on_birthdays'}</div>
		</div>
	</div>
	<div class="table form-group" id="availableSince" style="display: none;">
		<div class="table_left">
			{_p var='available_since'}:
		</div>
		<div class="table_right">
			{select_date start_year='current_year' end_year=2020 field_separator=' / ' field_order='MDY' prefix='start_' bUseDatepicker=true sort_years='DESC'}
		</div>
	</div>
	<div class="table form-group" id="availableUntil" style="display: none;">
		<div class="table_left">
			{_p var='available_until'}:
		</div>
		<div class="table_right">
			{select_date start_year='current_year' end_year=2020 field_separator=' / ' field_order='MDY' prefix='end_' bUseDatepicker=true sort_years='DESC'}
		</div>
	</div>
	
	<input type="hidden" name="val[date_order]" value="MDY">
	
	<div class="table_clear">
		<input type="submit" value="{_p var='add_category'}" class="button btn-primary">
	</div>
</form>

<br />

<div class="table_header">
	{_p var='manage_categories'}
</div>
<form action="{url link='admincp.egift.categories'}" method="post">
	<table id="js_drag_drop">
		<tr>
			<th style="width:20px"></th>
			<th style="width:20px"></th>
			<th style="width:20px"></th>
			{foreach from=$aLanguages key=iLangCount item=aLang}
			<th>{$aLang.title}</th>
			{/foreach}
			<th>{_p var='since'}</th>
			<th>{_p var='until'}</th>
			<th>{_p var='use_schedule'}</th>
		</tr>
		{foreach from=$aCategories key=iKey item=aCategory}
		<tr {if is_int($iKey/2)} class="tr"{else}{/if} id="tr_{$iKey}">
			<td class="drag_handle">
				<input type="hidden" name="val[ordering][{$aCategory.category_id}]" value="{$aCategory.ordering}" />
			</td>
			<td>
				<input type="hidden" id="language_var_{$iKey}" value="{$aCategory.phrase}">
				<a href="#" onclick="showEdit({$iKey}); return false;">
					{img theme='misc/page_white_edit.png' style='vertical-align:middle;'}
				</a>
			</td>
			<td>
				<a href="{url link='admincp.egift.categories' delete=$aCategory.category_id}" class="sJsConfirm">
					{img theme='misc/delete.png' style='vertical-align:middle;'}
				</a>
			</td>
			{foreach from=$aLanguages item=aLang}				
				<td id="phraseid_{$aLang.language_id}_{$aCategory.category_id}" class="phraseid_{$aLang.language_id}_{$aCategory.category_id} tr_td_{$iKey}">
					{_p var=$aCategory.phrase language=$aLang.language_id}
				</td>
			{/foreach}
			<td class='time_holder'>
				<span style="display:none;" class="time_hidden">{$aCategory.time_start}</span>
				<span id="currentStart_{$iKey}" class="span_start_time">{if ($aCategory.time_start > 0)}{$aCategory.time_start|date}{/if}</span>
				<div id='dateStart_{$iKey}' style="display:none;">
					{select_date start_year=2012 end_year=2020 field_separator=' / ' field_order='MDY' prefix='start_' bUseDatepicker=true sort_years='DESC'}
				</div>
			</td>
			<td class='time_holder'>
				<span style="display:none;" class="time_hidden">{$aCategory.time_end}</span>
				<span id="currentEnd_{$iKey}" class="span_end_time">{if ($aCategory.time_end > 0)}{$aCategory.time_end|date}{/if}</span>
				<div id='dateEnd_{$iKey}' style="display:none;">
					{select_date start_year=2012 end_year=2020 field_separator=' / ' field_order='MDY' prefix='end_' bUseDatepicker=true sort_years='DESC'}
				</div>
			</td>
			<td>
				<input type="hidden" name="val[edit_date_order]" value="MDY">
				<input id="doSchedule{$aCategory.category_id}" type="checkbox" name="val[dates][{$aCategory.category_id}][do_schedule]" {if $aCategory.time_start > 0}checked="checked"{/if} disabled="disabled">
			</td>
		</tr>
		{foreachelse}
		<tr>
			<td colspan="{$iTotalColumns}" class="t_center"> {_p var='no_categories_found'} </td>
		</tr>
		{/foreach}
	</table>
	<div class="table_clear">
		<span id="edit_button" style="display:none;">
			<input type="submit" value="{_p var='edit_categories'}" class="button btn-primary">
		</span>
	</div>
</form>
