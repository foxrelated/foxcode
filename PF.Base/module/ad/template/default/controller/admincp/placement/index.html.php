<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 1651 2010-06-15 13:36:12Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aPlacements)}
<div class="table_header">
	{_p var='ad_placements'}
</div>
<table cellpadding="0" cellspacing="0">
	<tr>
		<th style="width:20px;"></th>
		<th>{_p var='title'}</th>
		<th>{_p var='campaigns'}</th>
		<th style="width:50px;">{_p var='active'}</th>
	</tr>
{foreach from=$aPlacements key=iKey item=aPlacement}
	<tr class="{if is_int($iKey/2)} tr{else}{/if}">
		<td class="t_center">
			<a href="#" class="js_drop_down_link" title="{_p var='manage'}">{img theme='misc/bullet_arrow_down.png' alt=''}</a>
			<div class="link_menu">
				<ul>
					<li><a href="{url link='admincp.ad.placement.add' id=$aPlacement.plan_id}">{_p var='edit'}</a></li>
					<li><a href="{url link='admincp.ad.placement' delete=$aPlacement.plan_id}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete'}</a></li>
				</ul>
			</div>		
		</td>		
		<td>{$aPlacement.title|clean}</td>
		<td class="t_center">{if $aPlacement.total_campaigns > 0}<a href="{url link='admincp.ad' location=$aPlacement.block_id}">{/if}{$aPlacement.total_campaigns}{if $aPlacement.total_campaigns > 0}</a>{/if}</td>
		<td class="t_center">
			<div class="js_item_is_active"{if !$aPlacement.is_active} style="display:none;"{/if}>
				<a href="#?call=ad.updateAdPlacementActivity&amp;id={$aPlacement.plan_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}">{img theme='misc/bullet_green.png' alt=''}</a>
			</div>
			<div class="js_item_is_not_active"{if $aPlacement.is_active} style="display:none;"{/if}>
				<a href="#?call=ad.updateAdPlacementActivity&amp;id={$aPlacement.plan_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}">{img theme='misc/bullet_red.png' alt=''}</a>
			</div>		
		</td>		
	</tr>
{/foreach}
</table>
<div class="table_clear"></div>
{else}
<div class="extra_info">
	{_p var='no_placements_found'}
</div>
{/if}