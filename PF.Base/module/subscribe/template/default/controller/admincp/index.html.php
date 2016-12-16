<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aPackages)}
<div class="table_header">
	{_p var='packages'}
</div>
<table id="js_drag_drop" cellpadding="0" cellspacing="0">
<tr>
	<th></th>
	<th style="width:20px;"></th>
	<th>{_p var='title'}</th>
	<th class="t_center" style="width:120px;">{_p var='subscriptions'}</th>
	<th class="t_center" style="width:60px;">{_p var='active'}</th>
</tr>
{foreach from=$aPackages key=iKey item=aPackage}
<tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
	<td class="drag_handle"><input type="hidden" name="val[ordering][{$aPackage.package_id}]" value="{$aPackage.ordering}" /></td>
	<td class="t_center">
		<a href="#" class="js_drop_down_link" title="{_p var='manage'}">{img theme='misc/bullet_arrow_down.png' alt=''}</a>
		<div class="link_menu">
			<ul>
				<li><a href="{url link='admincp.subscribe.add' id={$aPackage.package_id}">{_p var='edit_package'}</a></li>
				<li><a href="{url link='admincp.subscribe' delete={$aPackage.package_id}" class="sJsConfirm" data-message="{_p var='are_you_sure' phpfox_squote=true}">{_p var='delete_package'}</a></li>
				{if $aPackage.total_active > 0}
				<li><a href="{url link='admincp.subscribe.list' package=$aPackage.package_id status='completed'}">{_p var='view_active_subscriptions'}</a></li>
				<li><a href="{url link='admincp.user.browse' group=$aPackage.user_group_id}">{_p var='view_active_users'}</a></li>
				{/if}
			</ul>
		</div>		
	</td>	
	<td>{$aPackage.title|convert|clean}</td>
	<td class="t_center">{if $aPackage.total_active > 0}<a href="{url link='admincp.subscribe.list' package=$aPackage.package_id status='completed'}">{/if}{$aPackage.total_active}{if $aPackage.total_active > 0}</a>{/if}</td>
	<td class="t_center">
		<div class="js_item_is_active"{if !$aPackage.is_active} style="display:none;"{/if}>
			<a href="#?call=subscribe.updateActivity&amp;package_id={$aPackage.package_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}">{img theme='misc/bullet_green.png' alt=''}</a>
		</div>
		<div class="js_item_is_not_active"{if $aPackage.is_active} style="display:none;"{/if}>
			<a href="#?call=subscribe.updateActivity&amp;package_id={$aPackage.package_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}">{img theme='misc/bullet_red.png' alt=''}</a>
		</div>		
	</td>
</tr>
{/foreach}
</table>
{else}
<div class="extra_info">
	{_p var='no_packages_have_been_added'}
	<ul class="action">
		<li><a href="{url link='admincp.subscribe.add'}">{_p var='create_a_new_package'}</a></li>
	</ul>
</div>
{/if}