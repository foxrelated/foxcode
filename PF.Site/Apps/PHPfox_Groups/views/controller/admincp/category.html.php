<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="table_header">
	{_p('Categories')}
</div>
<table id="_sort" data-sort-url="{if $bSubCategory}{url link='groups.admincp.category.order' type='sub'}{else}{url link='groups.admincp.category.order' type='main'}{/if}">
	<tr>
		<th style="width:20px"></th>
		<th style="width:20px"></th>
		<th>{_p('Name')}</th>
		<th class="t_center" style="width:60px;">{_p var='Active'}</th>
	</tr>
	{foreach from=$aCategories key=iKey item=aCategory}
	<tr class="{if is_int($iKey/2)} tr{else}{/if}" data-sort-id="{if $bSubCategory}{$aCategory.category_id}{else}{$aCategory.type_id}{/if}">
		<td class="t_center">
			<i class="fa fa-sort"></i>
		</td>
		<td class="t_center">
			<a href="#" class="js_drop_down_link" title="Manage">{img theme='misc/bullet_arrow_down.png' alt=''}</a>
			<div class="link_menu">
				<ul>
					<li><a class="popup" href="{if $bSubCategory}{url link='groups.admincp.add-category' sub=$aCategory.category_id}{else}{url link='groups.admincp.add-category' id=$aCategory.type_id}{/if}">{_p var='Edit'}</a></li>
					{if isset($aCategory.categories) && ($iTotalSub = count($aCategory.categories))}
					<li><a href="{url link='admincp.app' id='PHPfox_Groups' val[sub]={$aCategory.type_id}">{_p var='Manage Sub-Categories (!<< total >>!)' total=$iTotalSub}</a></li>
					{/if}
					<li><a href="{if $bSubCategory}{url link='groups.admincp' sub=$aCategory.type_id delete=$aCategory.category_id}{else}{url link='groups.admincp' delete=$aCategory.type_id}{/if}" class="sJsConfirm">{_p var='Delete'}</a></li>
				</ul>
			</div>		
		</td>	
		<td>
            {softPhrase var=$aCategory.name}
        </td>
		<td class="t_center">
			<div class="js_item_is_active"{if !$aCategory.is_active} style="display:none;"{/if}>
				<a href="#?call=groups.updateActivity&amp;id={if $bSubCategory}{$aCategory.category_id}{else}{$aCategory.type_id}{/if}&amp;active=0&amp;sub={if $bSubCategory}1{else}0{/if}" class="js_item_active_link" title="{_p var='Deactivate'}">{img theme='misc/bullet_green.png' alt=''}</a>
			</div>
			<div class="js_item_is_not_active"{if $aCategory.is_active} style="display:none;"{/if}>
				<a href="#?call=groups.updateActivity&amp;id={if $bSubCategory}{$aCategory.category_id}{else}{$aCategory.type_id}{/if}&amp;active=1&amp;sub={if $bSubCategory}1{else}0{/if}" class="js_item_active_link" title="{_p var='Activate'}">{img theme='misc/bullet_red.png' alt=''}</a>
			</div>		
		</td>		
	</tr>
	{/foreach}
</table>