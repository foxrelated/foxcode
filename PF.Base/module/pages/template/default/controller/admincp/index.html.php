<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 3332 2011-10-20 12:50:29Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="table_header">
	{_p var='categories'}
</div>
<table id="js_drag_drop" cellpadding="0" cellspacing="0">
	<tr>
		<th></th>
		<th style="width:20px;"></th>
		<th>{_p var='name'}</th>
		<th class="t_center" style="width:60px;">{_p var='active'}</th>
	</tr>
	{foreach from=$aCategories key=iKey item=aCategory}
	<tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
		<td class="drag_handle"><input type="hidden" name="val[ordering][{if $bSubCategory}{$aCategory.category_id}{else}{$aCategory.type_id}{/if}]" value="{$aCategory.ordering}" /></td>
		<td class="t_center">
			<a href="#" class="js_drop_down_link" title="{_p var='Manage'}">{img theme='misc/bullet_arrow_down.png' alt=''}</a>
			<div class="link_menu">
				<ul>
					<li><a href="{if $bSubCategory}{url link='admincp.pages.add' sub=$aCategory.category_id}{else}{url link='admincp.pages.add' id=$aCategory.type_id}{/if}">{_p var='edit'}</a></li>
					{if isset($aCategory.categories) && ($iTotalSub = count($aCategory.categories))}
					<li><a href="{url link='admincp.pages' sub={$aCategory.type_id}">{_p var='manage_sub_categories_total' total=$iTotalSub}</a></li>
					{/if}
					<li><a href="{if $bSubCategory}{url link='admincp.pages' sub=$aCategory.type_id delete=$aCategory.category_id}{else}{url link='admincp.pages' delete=$aCategory.type_id}{/if}" class="sJsConfirm" data-message="{_p var='are_you_sure'}">{_p var='delete'}</a></li>
				</ul>
			</div>		
		</td>	
		<td>
            {if Phpfox::isPhrase($this->_aVars['aCategory']['name'])}
            {_p var=$aCategory.name}
            {else}
            {$aCategory.name|convert}
            {/if}
        </td>
		<td class="t_center">
			<div class="js_item_is_active"{if !$aCategory.is_active} style="display:none;"{/if}>
				<a href="#?call=pages.updateActivity&amp;id={if $bSubCategory}{$aCategory.category_id}{else}{$aCategory.type_id}{/if}&amp;active=0&amp;sub={if $bSubCategory}1{else}0{/if}" class="js_item_active_link" title="{_p var='deactivate'}">{img theme='misc/bullet_green.png' alt=''}</a>
			</div>
			<div class="js_item_is_not_active"{if $aCategory.is_active} style="display:none;"{/if}>
				<a href="#?call=pages.updateActivity&amp;id={if $bSubCategory}{$aCategory.category_id}{else}{$aCategory.type_id}{/if}&amp;active=1&amp;sub={if $bSubCategory}1{else}0{/if}" class="js_item_active_link" title="{_p var='activate'}">{img theme='misc/bullet_red.png' alt=''}</a>
			</div>		
		</td>		
	</tr>
	{/foreach}
</table>