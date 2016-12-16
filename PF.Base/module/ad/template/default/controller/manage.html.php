<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: manage.html.php 4073 2012-03-28 13:25:57Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="page_section_menu page_section_menu_header">
	<div>
		<ul class="nav nav-tabs nav-justified">
			<li{if empty($sView)} class="active"{/if}><a href="{url link='ad.manage'}">{_p var='approved'}</a></li>
			<li{if $sView == 'pending'} class="active"{/if}><a href="{url link='ad.manage' view='pending'}">{_p var='pending_approval'}</a></li>
			<li{if $sView == 'payment'} class="active"{/if}><a href="{url link='ad.manage' view='payment'}">{_p var='pending_payment'}</a></li>
			<li class="last{if $sView == 'denied'} active{/if}"><a href="{url link='ad.manage' view='denied'}">{_p var='denied'}</a></li>
		</ul>
	</div>
	<div class="clear"></div>
</div>

{if $bNewPurchase}
<div class="message">
	{_p var='thank_you_for_your_purchase_your_ad_is_currently_pending_approval'}
</div>
{/if}

<table class="default_table" cellpadding="0" cellspacing="0">
	<tr>
		<th>{_p var='campaign'}</th>
		<th>{_p var='status'}</th>
		<th>{_p var='impressions'}</th>
		<th>{_p var='clicks'}</th>
		<th style="width:50px;">{_p var='active'}</th>
	</tr>
{foreach from=$aAllAds name=ads item=aAd}
	<tr{if is_int($phpfox.iteration.ads/2)} class="on"{/if}>
		<td><a href="{if $aAd.is_custom == '1'}{url link='ad.add.completed' id=$aAd.ad_id}{else}{url link='ad.add' id=$aAd.ad_id}{/if}">{$aAd.name|clean}</a></td>
		<td class="t_center">{$aAd.status}</td>
		<td class="t_center">{$aAd.count_view}</td>
		<td class="t_center">{$aAd.count_click}</td>	
		<td class="t_center">	
			{if empty($sView)}
			<div class="js_item_is_active"{if !$aAd.is_active} style="display:none;"{/if}>
				<a href="#?call=ad.updateAdActivityUser&amp;id={$aAd.ad_id}&amp;active=0" class="js_item_active_link" title="{_p var='pause_this_campaign'}">{img theme='misc/bullet_green.png' alt=''}</a>
			</div>
			<div class="js_item_is_not_active"{if $aAd.is_active} style="display:none;"{/if}>
				<a href="#?call=ad.updateAdActivityUser&amp;id={$aAd.ad_id}&amp;active=1" class="js_item_active_link" title="{_p var='continue_this_campaign'}">{img theme='misc/bullet_red.png' alt=''}</a>
			</div>
			{else}
			{_p var='n_a'}
			{/if}
		</td>			
	</tr>
{foreachelse}	
	<tr>
		<td colspan="5">
			<div class="extra_info">
				{_p var='no_ads_found'}
			</div>
		</td>
	</tr>
{/foreach}
</table>