<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Blog
 * @version 		$Id: add.html.php 281 2009-03-05 12:20:08Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{$sCreateJs}
{if $bIsEdit}
<form method="post" action="{url link='admincp.blog.add' id=$iEditId}" id="js_form" onsubmit="{$sGetJsForm}">
{else}
<form method="post" action="{url link="admincp.blog.add"}" id="js_form" onsubmit="{$sGetJsForm}">
{/if}
    {if $bIsEdit}
    <div>
        <input type="hidden" value="{$aForms.name}" name="val[name]">
    </div>
    {/if}
	<div class="table_header">
	{_p var='category_details'}
	</div>
    {foreach from=$aLanguages item=aLanguage}
	<div class="table form-group">
		<div class="table_left">
			{_p var='category'}&nbsp;<strong>{$aLanguage.title}</strong>:
		</div>
		<div class="table_right">
            {assign var='value_name' value="name_"$aLanguage.language_id}
            <input type="text" name="val[name_{$aLanguage.language_id}]" value="{value id=$value_name type='input'}" size="30" />
			{help var='admincp.blog_category_add_name'}
		</div>
		<div class="clear"></div>
	</div>
    {/foreach}
	<div class="table_clear">
		<input type="submit" value="{_p var='submit'}" class="button btn-primary" />
	</div>
</form>