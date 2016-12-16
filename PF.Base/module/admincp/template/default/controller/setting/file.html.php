<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Admincp
 * @version 		$Id: file.html.php 225 2009-02-13 13:24:59Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.setting.file'}">
	<div class="table_header">
		{_p var='export'}
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='product'}:
		</div>
		<div class="table_right">
			<select name="export">
			{foreach from=$aProducts item=aProduct}
				<option value="{$aProduct.product_id}">{$aProduct.title}</option>
			{/foreach}
			</select>
			{help var='admincp.setting_file_product'}
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='download_file_format'}:
		</div>
		<div class="table_right">
			<select name="file_extension">
			{foreach from=$aArchives item=aArchives}
				<option value="{$aArchives}">.{$aArchives}</option>
			{/foreach}
			</select>
			{help var='admincp.setting_file_extension'}
		</div>
		<div class="clear"></div>
	</div>
	<div class="table_clear">
		<input type="submit" value="{_p var='download'}" class="button btn-primary" />
	</div>
</form>

<br />

<form method="post" action="{url link='admincp.setting.file'}" enctype="multipart/form-data">
	<div class="table_header">
		{_p var='import'}
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='select_file'}:
		</div>
		<div class="table_right">
			<input type="file" name="import" />
			<div class="p_4">
				{_p var='valid_file_extensions'}: {$sSupported}
			</div>
			{help var='admincp.setting_file_import'}
		</div>
		<div class="clear"></div>
	</div>
	<div class="table_clear">
		<input type="submit" value="{_p var='upload'}" class="button btn-primary" />
	</div>
</form>