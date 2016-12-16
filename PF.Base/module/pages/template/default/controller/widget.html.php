<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
		<iframe src="#" id="js_pages_frame" name="js_pages_frame" style="display:none;"></iframe>
		<div id="js_pages_widget_error"></div>
		<form method="post" action="{url link='pages.frame'}" target="js_pages_frame" enctype="multipart/form-data">
			<div><input type="hidden" name="val[page_id]" value="{$iPageId}" /></div>
			{if $bIsEdit}
			<div><input type="hidden" name="widget_id" value="{$aForms.widget_id}" /></div>
			{/if}
			<div class="table form-group">
				<div class="table_left">
					{_p var='title'}:
				</div>
				<div class="table_right">
					<input type="text" name="val[title]" value="{value type='input' id='title'}" size="30" class="form=control" />
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="table form-group">
				<div class="table_left">
					{_p var='is_a_block'}
				</div>
				<div class="table_right">
					<select name="val[is_block]" class="form-control" onchange="if (this.value == '1') {l} $('#js_pages_widget_block').slideUp(); {r} else {l} $('#js_pages_widget_block').slideDown(); {r}">
						<option value="0"{value type='select' id='is_block' default='0'}> {_p var='no'}</option>
						<option value="1"{value type='select' id='is_block' default='1'}> {_p var='yes'}</option>
					</select>
				</div>
				<div class="clear"></div>
			</div>				
			
			<div id="js_pages_widget_block"{if $bIsEdit && $aForms.is_block == '1'} style="display:none;"{/if}>
				<div class="table form-group">
					<div class="table_left">
						{_p var='menu_title'}:
					</div>
					<div class="table_right">
						<input class="form-control" type="text" name="val[menu_title]" value="{value type='input' id='menu_title'}" size="30" />
					</div>
					<div class="clear"></div>
				</div>			

				<div class="table form-group">
					<div class="table_left">
						{_p var='url_title'}:
					</div>
					<div class="table_right">
						<span class="extra_info">{$sPageUrl}</span><input onclick="this.select();" type="text" name="val[url_title]" value="{value type='input' id='url_title'}" size="15" class="form-control" /><span class="extra_info">/</span>
					</div>
					<div class="clear"></div>
				</div>	
				 
				<div class="table form-group">
					<div class="table_left">
						{_p var='icon'}:
					</div>
					<div class="table_right">
						{if $bIsEdit && !empty($aForms.image_path)}
						{img thickbox=true server_id=$aForms.image_server_id path='pages.url_image' file=$aForms.image_path suffix='_16' class='v_middle'}
						<div class="extra_info">								
							{_p var='upload_a_new_image_below_if_you_wish_to_change_this_icon'}
						</div>
						{/if}						
						<input type="file" name="image" accept="image/*"/>
						<div class="extra_info">
							{_p var='you_can_upload_a_jpg_gif_or_png_file'}
						</div>						
					</div>
					<div class="clear"></div>
				</div>				 
			</div>
			
			<div class="table form-group">
				<div class="table_left">
					{_p var='content'}:
				</div>
				<div class="table_right">
					{editor id='text'}			
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="table_clear" id="js_pages_widget_submit_button">
				<ul class="table_clear_button">
					<li><input type="submit" value="{_p var='submit'}" class="button btn-primary" /></li>
					<li class="table_clear_ajax"></li>
				</ul>		
				<div class="clear"></div>
			</div>			
		</form>