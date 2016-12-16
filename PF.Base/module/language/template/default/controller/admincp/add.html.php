<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if isset($bImportingPhrases)}
<div class="message">
	{_p var='importing_phrases_page_current_total' current=$iCurrentPage total=$iTotalPages}
</div>
{else}
<form method="post" action="{url link='admincp.language.add'}" enctype="multipart/form-data">
{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.language_id}" /></div>
{/if}
	{if !$bIsEdit}
	<div class="table form-group">
		<div class="table_left">
		{required}{_p var='create_from'}:
		</div>
		<div class="table_right">
			<select name="val[parent_id]">
				<option value="">{_p var='select'}:</option>
			{foreach from=$aLanguages item=aLanguage}
				<option value="{$aLanguage.language_code}">{$aLanguage.title|clean}</option>
			{/foreach}
			</select>
		</div>
		<div class="clear"></div>
	</div>	
	{/if}
	<div class="table form-group">
		<div class="table_left">
			{required}{_p var='name'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[title]" value="{value type='input' id='title'}" size="40" />
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{required}{_p var='language_abbreviation_code'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[language_code]" value="{value type='input' id='language_code'}" size="2" maxlength="2" />
		</div>
		<div class="clear"></div>
	</div>	
	<div class="table form-group">
		<div class="table_left">
			{required}{_p var='text_direction'}:
		</div>
		<div class="table_right">
			<label><input type="radio" name="val[direction]" value="ltr" {value type='radio' id='direction' default='ltr' selected=true}/> {_p var='left_to_right'}</label> <br />
			<label><input type="radio" name="val[direction]" value="rtl" {value type='radio' id='direction' default='rtl'}/> {_p var='right_to_left'}</label>
		</div>
		<div class="clear"></div>
	</div>		
	<div class="table form-group-follow hidden">
		<div class="table_left">
			{required}{_p var='allow_user_selection'}:
		</div>
		<div class="table_right">	
			<div class="item_is_active_holder">		
				<span class="js_item_active item_is_active"><input type="radio" name="val[user_select]" value="1" {value type='radio' id='user_select' default='1' selected='true'}/> {_p var='yes'}</span>
				<span class="js_item_active item_is_not_active"><input type="radio" name="val[user_select]" value="0" {value type='radio' id='user_select' default='0'}/> {_p var='no'}</span>
			</div>
		</div>
		<div class="clear"></div>		
	</div>	
	<div class="table form-group" style="display:none;">
		<div class="table_left">
			{_p var='icon'}:
		</div>
		<div class="table_right">
			{if $bIsEdit && !empty($aForms.image)}
			<div id="js_current_image">
				<img src="{$aForms.image}" alt="" class="v_middle" /> - <a href="#" onclick="$('#js_current_image').hide(); $('#js_upload_new_icon').show();">{_p var='change_icon'}</a>
			</div>
			{/if}
			<div id="js_upload_new_icon"{if $bIsEdit && !empty($aForms.image)} style="display:none;"{/if}>
				<input type="file" name="icon" size="30" />
				{if $bIsEdit}
				- <a href="#" onclick="$('#js_current_image').show(); $('#js_upload_new_icon').hide();">{_p var='cancel'}</a>
				{/if}		
				<div class="extra_info">			
					{_p var='default_icon_to_represent_this_language_package_br_advised_size_is_max_16_pixels_width_height'}				
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>		
	<div class="table form-group" style="display:none;">
		<div class="table_left">
			{_p var='created_by'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[created]" value="{value type='input' id='created'}" size="40" />
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group" style="display:none;">
		<div class="table_left">
			{_p var='website'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[site]" value="{value type='input' id='site'}" size="40" />
		</div>
		<div class="clear"></div>
	</div>	
	<div class="table_clear">
		<input type="submit" value="{_p var='submit'}" class="button btn-primary" />
	</div>
</form>
{/if}