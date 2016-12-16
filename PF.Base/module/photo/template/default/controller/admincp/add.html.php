<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{$sCreateJs}
{if $bIsEdit}
<form method="post" action="{url link='admincp.photo.add' id=$iEditId}" id="js_form" onsubmit="{$sGetJsForm}">
{else}
<form method="post" action="{url link='admincp.photo.add'}" id="js_form" onsubmit="{$sGetJsForm}">
{/if}
    <div id="js_photo_hidden">
        {if $bIsEdit}
            <div><input type="hidden" name="val[name]" value="{$aForms.name}"></div>
            <div><input type="hidden" name="val[edit_id]" value="{$aForms.category_id}"></div>
        {/if}
    </div>
    <div class="table_header" id="js_photo_table_header">
        {if $bIsEdit}
            {_p var="Edit a photo category"}
        {else}
            {_p var='add_a_photo_category'}
        {/if}
    </div>
    <div class="table form-group" id="js_photo_parent">
        <div class="table_left">
            {_p var='parent'}:
        </div>
        <div class="table_right">
            {module name='photo.drop-down' multiple=false}
        </div>
    </div>
    {foreach from=$aLanguages item=aLanguage}
    <div class="table form-group">
        <div class="table_left">
            {_p var='name'}&nbsp;<strong>{$aLanguage.title}</strong>:
        </div>
        <div class="table_right">
            {assign var='value_name' value="name_"$aLanguage.language_id}
            <input type="text" name="val[name_{$aLanguage.language_id}]" value="{value id=$value_name type='input'}" size="30" />
        </div>
        <div class="clear"></div>
    </div>
    {/foreach}

    <div class="table_clear">
        <input type="submit" value="{_p var='submit'}" class="button btn-primary" /><span id="js_photo_extra_button"></span>
    </div>
</form>