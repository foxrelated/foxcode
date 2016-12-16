<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: online-guest.html.php 3622 2011-11-30 12:34:24Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aGuests)}
<div class="table_header">
	{_p var='guests_bots'}
</div>
<table cellpadding="0" cellspacing="0">
	<tr>
		<th>{_p var='ip_address'}</th>
		<th>{_p var='user_agent'}</th>
		<th class="t_center" style="width:70px;">{_p var='banned'}</th>
		<th>{_p var='last_activity'}</th>
	</tr>
{foreach from=$aGuests key=iKey item=aGuest}
	<tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
		<td><a href="{url link='admincp.core.ip' search=$aGuest.ip_address_search}" title="{_p var='view_all_the_activity_from_this_ip'}">{$aGuest.ip_address|clean}</a></td>
		<td>{$aGuest.user_agent|clean}</td>
		<td class="t_center">
			<div class="js_item_is_active"{if !$aGuest.ban_id} style="display:none;"{/if}>
				<a href="#?call=ban.ip&amp;ip={$aGuest.ip_address}&amp;active=0" class="js_item_active_link" title="{_p var='unban'}">{img theme='misc/bullet_green.png' alt=''}</a>
			</div>
			<div class="js_item_is_not_active"{if $aGuest.ban_id} style="display:none;"{/if}>
				<a href="#?call=ban.ip&amp;ip={$aGuest.ip_address}&amp;active=1" class="js_item_active_link" title="{_p var='ban'}">{img theme='misc/bullet_red.png' alt=''}</a>
			</div>		
		</td>		
		<td>{$aGuest.last_activity|date:'core.global_update_time'}</td>		
	</tr>
{/foreach}
</table>
{pager}
{else}
<div class="extra_info">
	{_p var='no_guests_online'}
</div>
{/if}