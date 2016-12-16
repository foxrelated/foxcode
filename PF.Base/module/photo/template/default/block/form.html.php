<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: form.html.php 5477 2013-03-11 07:15:40Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
		{if isset($aForms.view_id) && $aForms.view_id == 1}
		<div class="message" style="width:85%;">
			{_p var='image_is_pending_approval'}
		</div>
		{/if}
		{if isset($aForms.server_id)}
		<div><input type="hidden" name="val{if isset($aForms.photo_id)}[{$aForms.photo_id}]{/if}[server_id]" value="{$aForms.server_id}" /></div>
		{/if}
		<div class="table form-group">
			<div class="table_left">
				<label for="title">{_p var='title'}</label>:
			</div>
			<div class="table_right">
				<input type="text" name="val{if isset($aForms.photo_id)}[{$aForms.photo_id}]{/if}[title]" value="{if isset($aForms.title)}{$aForms.title|clean}{else}{value type='input' id='title'}{/if}" size="30" maxlength="150" onfocus="this.select();" class="form-control" />
			</div>			
		</div>
		<div class="table form-group">
			<div class="table_left">
				{_p var='description'}:
			</div>
			<div class="table_right">
				<textarea cols="30" rows="4" name="val{if isset($aForms.photo_id)}[{$aForms.photo_id}]{/if}[description]" class="form-control">{if isset($aForms.description)}{$aForms.description|clean}{else}{value type='input' id='description'}{/if}</textarea>
			</div>			
		</div>		
		
		{if isset($aForms.group_id) && $aForms.group_id != '0'}
		
		{else}
		{if Photo_Service_Category_Category::instance()->hasCategories()}
		<div class="table form-group">
			<div class="table_left">
				{_p var='category'}:
			</div>
			<div class="table_right js_category_list_holder">
				{if isset($aForms.photo_id)}<div class="js_photo_item_id" style="display:none;">{$aForms.photo_id}</div>{/if}				
				{if isset($aForms.category_list)}<div class="js_photo_active_items" style="display:none;">{$aForms.category_list}</div>{/if}
				{module name='photo.drop-down'}
			</div>			
		</div>	
		{/if}
		{/if}

		{if isset($aForms.group_id) && $aForms.group_id != '0'}
		
		{else}		
			{if Phpfox::isModule('tag') && Phpfox::getUserParam('photo.can_add_tags_on_photos')}{if isset($aForms.photo_id)}{module name='tag.add' sType='photo' separate=false id=$aForms.photo_id}{else}{module name='tag.add' sType='photo' separate=false}{/if}{/if}
		{/if}
			{if Phpfox::getUserParam('photo.can_add_mature_images')}
			<div class="table form-group">
				<div class="table_left">
					{_p var='mature_content'}:
				</div>
				<div class="table_right">
					<div class="radio">
						<label><input type="radio" name="val{if isset($aForms.photo_id)}[{$aForms.photo_id}]{/if}[mature]" value="2" style="vertical-align:middle;" class="checkbox"{value type='radio' id='mature' default='2'}/> {_p var='yes_strict'}</label>
					</div>
					<div class="radio">
						<label><input type="radio" name="val{if isset($aForms.photo_id)}[{$aForms.photo_id}]{/if}[mature]" value="1" style="vertical-align:middle;" class="checkbox"{value type='radio' id='mature' default='1'}/> {_p var='yes_warning'}</label>
					</div>
					<div class="radio">
						<label><input type="radio" name="val{if isset($aForms.photo_id)}[{$aForms.photo_id}]{/if}[mature]" value="0" style="vertical-align:middle;" class="checkbox"{value type='radio' id='mature' default='0' selected=true}/> {_p var='no'}</label>
					</div>
				</div>			
			</div>
			{/if}
			
			<div class="table form-group">
				<div class="table_left">
					{_p var='download_enabled'}:
				</div>
				<div class="table_right">
					<div class="radio-wrapper">
						<div class="radio">
						<label><input type="radio" name="val{if isset($aForms.photo_id)}[{$aForms.photo_id}]{/if}[allow_download]" value="1" style="vertical-align:middle;" class="checkbox"{value type='radio' id='allow_download' default='1' selected=true}/> {_p var='yes'}</label>
						</div>
						<div class="radio">
						<label><input type="radio" name="val{if isset($aForms.photo_id)}[{$aForms.photo_id}]{/if}[allow_download]" value="0" style="vertical-align:middle;" class="checkbox"{value type='radio' id='allow_download' default='0'}/> {_p var='no'}</label>
						</div>
					</div>
					<div class="extra_info" style="padding-left: 0; padding-right: 0">
						{_p var='enabling_this_option_will_allow_others_the_rights_to_download_this_photo'}
					</div>				
				</div>
			</div>