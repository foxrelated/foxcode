<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 1164 2009-10-09 09:27:09Z Anna_Eliasson $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bIsEdit}
    <form method="post" action="{url link='admincp.music.add' id=$iEditId}">
    <div><input type="hidden" name="val[edit_id]" value="{$iEditId}" /></div>
    <div><input type="hidden" name="val[name]" value="{$aForms.name}" /></div>
{else}
    <form method="post" action="{url link='admincp.music.add'}">
{/if}

        <div class="table_header">
            {_p var='genre_details'}
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
            <input type="submit" value="{if $bIsEdit}{_p var='edit_genre'}{else}{_p var='add_genre'}{/if}" class="button btn-primary" />
        </div>
    </form>