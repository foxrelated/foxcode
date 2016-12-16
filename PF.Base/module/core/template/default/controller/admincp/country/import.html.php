<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: import.html.php 1931 2010-10-25 11:58:06Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !Phpfox::getParam('core.phpfox_is_hosted')}
<form method="post" action="{url link='admincp.core.country.import'}" enctype="multipart/form-data">
	<div class="table_header">
		{_p var='import_country_package'}
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='file'}:
		</div>
		<div class="table_right">
			<input type="file" name="import" size="40" />
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group-follow">
		<div class="table_left">
			{_p var='overwrite'}:
		</div>
		<div class="table_right">	
			<div class="item_is_active_holder">		
				<span class="js_item_active item_is_active"><input type="radio" name="overwrite" value="1" /> {_p var='yes'}</span>
				<span class="js_item_active item_is_not_active"><input type="radio" name="overwrite" value="0" checked="checked" /> {_p var='no'}</span>
			</div>			
		</div>
		<div class="clear"></div>		
	</div>		
	<div class="table_clear">
		<input type="submit" value="{_p var='import'}" class="button btn-primary" />
	</div>
</form>
<br />
{/if}
<form method="post" action="{url link='admincp.core.country.import'}" enctype="multipart/form-data">
	<div class="table_header">
		{_p var='import_text_file'}
	</div>	
	<div class="table form-group">
		<div class="table_left">
			{_p var='country'}:
		</div>
		<div class="table_right">
			{select_location}
		</div>
		<div class="clear"></div>
	</div>		
	<div class="table form-group">
		<div class="table_left">
			{_p var='file'}:
		</div>
		<div class="table_right">
			<input type="file" name="file_import" size="40" />
			<div class="extra_info">
				{_p var='you_can_upload_a_text_file_with_a_list'}
			</div>
		</div>
		<div class="clear"></div>
	</div>	
	<div class="table form-group-follow">
		<div class="table_left">
			{_p var='enable_utf_encoding'}:
		</div>
		<div class="table_right">
			<div class="item_is_active_holder">		
				<span class="js_item_active item_is_active"><input type="radio" name="val[utf_encoding]" value="1" /> {_p var='yes'}</span>
				<span class="js_item_active item_is_not_active"><input type="radio" name="val[utf_encoding]" value="0" checked="checked" /> {_p var='no'}</span>
			</div>	
		</div>
		<div class="clear"></div>
	</div>		
	<div class="table_clear">
		<input type="submit" value="{_p var='import'}" class="button btn-primary" />
	</div>
</form>