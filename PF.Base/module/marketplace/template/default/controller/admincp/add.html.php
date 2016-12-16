<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 1163 2009-10-09 08:02:14Z Anna_Eliasson $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bIsEdit}
<form method="post" action="{url link='admincp.marketplace.add' id=$iEditId}">
    <div><input type="hidden" name="val[edit_id]" value="{$iEditId}" /></div>
    <div><input type="hidden" name="val[name]" value="{$aForms.name}" /></div>
    {else}
    <form method="post" action="{url link='admincp.marketplace.add'}">
        {/if}
        <div class="table_header">
            {_p var='marketplace_category_details'}
        </div>
        <div class="table form-group">
            <div class="table_left">
                {_p var='parent_category'}:
            </div>
            <div class="table_right">
                <select name="val[parent_id]" style="width:300px;">
                    <option value="0">{_p var='select'}:</option>
                    {foreach from=$aParentCategories item=aParentCategory}
                    <option {if $bIsEdit && $aForms.parent_id==$aParentCategory.category_id}selected{/if} value="{$aParentCategory.category_id}">{softPhrase var=$aParentCategory.name}</option>
                    {/foreach}
                </select>
            </div>
            <div class="clear"></div>
        </div>

        {foreach from=$aLanguages item=aLanguage}
        <div class="table form-group">
            <div class="table_left">
                {_p var='name'}&nbsp;<strong>{$aLanguage.title}</strong>:
            </div>
            <div class="table_right">
                {assign var='value_name' value="name_"$aLanguage.language_id}
                <input type="text" name="val[name_{$aLanguage.language_id}]" value="{value id=$value_name type='input'}" size="30" maxlength="100"/>
            </div>
            <div class="clear"></div>
        </div>
        {/foreach}

        <div class="table_clear">
            <input type="submit" value="{_p var='submit'}" class="button btn-primary" />
        </div>
    </form>