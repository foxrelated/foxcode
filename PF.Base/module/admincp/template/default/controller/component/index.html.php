<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 1344 2009-12-21 19:50:14Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="table_header">
	{_p var='components'}
</div>
<table cellpadding="0" cellspacing="0">
{foreach from=$aComponents key=sModule item=aRows}
	<tr>
		<td colspan="5" class="table_header2">
			{$sModule|translate:'module'}
		</td>
	</tr>
	<tr>
		<th style="width:20px;"></th>
		<th>{_p var='component'}</th>
		<th>{_p var='connection'}</th>
		<th class="t_center">{_p var='controller'}</th>
		<th style="width: 60px;">{_p var='active'}</th>
	</tr>	
	{foreach from=$aRows key=iKey item=aRow}
	<tr{if is_int($iKey/2)} class="tr"{/if}>
		<td class="t_center">
			<a href="#" class="js_drop_down_link" title="{_p var='manage'}">{img theme='misc/bullet_arrow_down.png' alt=''}</a>
			<div class="link_menu">
				<ul>
					<li><a href="{url link='admincp.component.add' id=$aRow.component_id}">{_p var='edit'}</a></li>
					<li><a href="{url link='admincp.component' delete=$aRow.component_id}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete'}</a></li>
				</ul>
			</div>		
		</td>	
		<td>{$aRow.component}</td>
		<td>{if empty($aRow.m_connection)}N/A{else}{$aRow.m_connection}{/if}</td>
		<td class="t_center">
			{if $aRow.is_controller}
			{_p var='yes'}
			{else}
			{_p var='no'}
			{/if}
		</td>
		<td class="t_center">
			<div class="js_item_is_active"{if !$aRow.is_active} style="display:none;"{/if}>
				<a href="#?call=admincp.componentFeedActivity&amp;id={$aRow.component_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}">{img theme='misc/bullet_green.png' alt=''}</a>
			</div>
			<div class="js_item_is_not_active"{if $aRow.is_active} style="display:none;"{/if}>
				<a href="#?call=admincp.componentFeedActivity&amp;id={$aRow.component_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}">{img theme='misc/bullet_red.png' alt=''}</a>
			</div>			
		</td>
	</tr>
	{/foreach}
{/foreach}
</table>