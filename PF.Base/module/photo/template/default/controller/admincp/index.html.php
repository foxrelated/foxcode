<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 1174 2009-10-11 13:56:13Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: index.html.php 3072 2011-09-12 13:23:50Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if count($aCategories)}
{module name='help.info' phrase='photo.tip_delete_category'}
<form method="post" action="{url link='admincp.photo'}">
    <table id="js_drag_drop" cellpadding="0" cellspacing="0">
        <tr>
            <th></th>
            <th style="width:20px;"></th>
            <th>{_p var='name'}</th>
            <th>{_p var='Total photos'}</th>
            <th class="t_center" style="width:60px;">{_p var='Active'}</th>
        </tr>
        {foreach from=$aCategories key=iKey item=aCategory}
        <tr id="js_row{$aCategory.category_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
            <td class="drag_handle"><input type="hidden" name="val[ordering][{$aCategory.category_id}]" value="{$aCategory.ordering}" /></td>
            <td class="t_center">
                <a href="#" class="js_drop_down_link" title="{_p var='Manage'}">{img theme='misc/bullet_arrow_down.png' alt=''}</a>
                <div class="link_menu">
                    <ul>
                        <li><a href="{url link='admincp.photo.add' id=$aCategory.category_id}">{_p var="Edit"}</a></li>
                        {if $aCategory.total_sub > 0}
                        <li><a href="{url link='admincp.photo' parent=$aCategory.category_id}">{_p var="Manage Sub-Category"} ({$aCategory.total_sub})</a></li>
                        {/if}
                        <li><a href="{url link='admincp.photo.add' delete=$aCategory.category_id}" class="sJsConfirm">{_p var='delete'}</a></li>
                    </ul>
                </div>
            </td>
            <td id="js_photo_edit_title{$aCategory.category_id}">
                {if Phpfox::isPhrase($this->_aVars['aCategory']['name'])}
                {_p var=$aCategory.name}
                {else}
                {$aCategory.name|convert|clean}
                {/if}
            </td>
            <td>{if $aCategory.used > 0}<a href="{$aCategory.link}" id="js_category_link{$aCategory.category_id}">{$aCategory.used}</a>{else}{_p var="None"}{/if}</td>
            <td class="t_center">
                <div class="js_item_is_active"{if !$aCategory.is_active} style="display:none;"{/if}>
                <a href="#?call=photo.toggleActiveCategory&amp;id={$aCategory.category_id}&amp;active=0" class="js_item_active_link" title="{_p var='Deactivate'}">{img theme='misc/bullet_green.png' alt=''}</a>
                </div>
                <div class="js_item_is_not_active"{if $aCategory.is_active} style="display:none;"{/if}>
                <a href="#?call=photo.toggleActiveCategory&amp;id={$aCategory.category_id}&amp;active=1&amp;" class="js_item_active_link" title="{_p var='Activate'}">{img theme='misc/bullet_red.png' alt=''}</a>
                </div>
            </td>
        </tr>
        {/foreach}
    </table>
    {else}
    <div class="p_4">
        {_p var='no_photo_categories_have_been_created'} <a href="{url link='admincp.photo.add'}">{_p var='create_one_now'}</a>.
    </div>
    {/if}
</form>