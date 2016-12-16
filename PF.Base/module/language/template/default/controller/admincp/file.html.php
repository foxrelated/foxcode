<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Language
 * @version 		$Id: file.html.php 225 2009-02-13 13:24:59Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link="admincp.language.file"}">
<div class="table_header">
	{_p var='export'}
</div>
<div class="table form-group">
	<div class="table_left">
		{_p var='language_package'}:
	</div>
	<div class="table_right">
		<select name="val[language_id]">
		{foreach from=$aLanguages item=aLanguage}
			<option value="{$aLanguage.language_id}">{$aLanguage.title}</option>
		{/foreach}
		</select>
		{help var='admincp.language_file_package'}
	</div>
	<div class="clear"></div>
</div>
<div class="table form-group">
	<div class="table_left">
		{_p var='product'}:
	</div>
	<div class="table_right">
		<select name="val[product_id]">
		{foreach from=$aProducts item=aProduct}
			<option value="{$aProduct.product_id}">{$aProduct.title}</option>
		{/foreach}
		</select>
		{help var='admincp.language_file_product'}
	</div>
	<div class="clear"></div>
</div>
<div class="table form-group">
	<div class="table_left">
		{_p var='download_file_format'}:
	</div>
	<div class="table_right">
		<select name="val[file_extension]">
		{foreach from=$aArchives item=aArchives}
			<option value="{$aArchives}">.{$aArchives}</option>
		{/foreach}
		</select>
		{help var='admincp.language_file_extension'}
	</div>
	<div class="clear"></div>
</div>
<div class="table_clear">
	<input type="submit" value="{_p var='download'}" class="button" />
</div>
</form>

<br />

<form method="post" action="{url link="admincp.language.file"}" enctype="multipart/form-data">
{token}
<div><input type="hidden" name="import" value="true" /></div>
<div class="table_header">
	{_p var='import'}
</div>
<div class="table form-group">
	<div class="table_left">
		{_p var='import_language_package'}:
	</div>
	<div class="table_right">
		{_p var='either_select_language_package'}:
		<div class="p_4">
		<select name="download" size="10" style="width:400px;">
			{foreach from=$aImports item=aImport}
				<option value="{$aImport.title}">{$aImport.title}</option>
			{/foreach}
		</select>
		</div>
		<br />
		{_p var='upload_one_from_your_computer'}:
		<div class="p_4">
			<input type="file" name="file" />
			<div class="p_4">
				{_p var='valid_file_extensions'}: {$sSupported}
			</div>
		</div>
		{help var='admincp.language_file_import_package'}
	</div>
	<div class="clear"></div>
</div>
<div class="table form-group">
	<div class="table_left">
		{_p var='import_missing_phrases_only'}:
	</div>
	<div class="table_right">
		<label><input type="radio" name="missing_phrases" value="0" checked="checked" />{_p var='no'}</label>
		<label><input type="radio" name="missing_phrases" value="1" />{_p var='yes'}</label>
		{help var='admincp.language_file_missing_phrases'}
	</div>
	<div class="clear"></div>
</div>
<div class="table_clear">
	<input type="submit" value="{_p var='upload'}" class="button" />
</div>
</form>