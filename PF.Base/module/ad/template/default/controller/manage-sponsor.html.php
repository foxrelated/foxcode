<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: manage-sponsor.html.php 7256 2014-04-07 17:54:52Z Fern $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="page_section_menu page_section_menu_header">
	<div>
		<ul class="nav nav-tabs nav-justified">
			<li{if empty($sView)} class="active"{/if}><a href="{url link='ad.manage-sponsor'}">{_p var='approved'}</a></li>
			<li{if $sView == 'pending'} class="active"{/if}><a href="{url link='ad.manage-sponsor' view='pending'}">{_p var='pending_approval'}</a></li>
			<li{if $sView == 'payment'} class="active"{/if}><a href="{url link='ad.manage-sponsor' view='payment'}">{_p var='pending_payment'}</a></li>
			<li class="last{if $sView == 'denied'} active{/if}"><a href="{url link='ad.manage-sponsor' view='denied'}">{_p var='denied'}</a></li>
		</ul>
	</div>
	<div class="clear"></div>
</div>

<table class="default_table" cellpadding="0" cellspacing="0">
	<tr>
		<th>{_p var='campaign'}</th>
		<th>{_p var='status'}</th>
		<th>{_p var='impressions'}</th>
		<th>{_p var='clicks'}</th>
		<th style="width:50px;">{_p var='active'}</th>
	</tr>
{foreach from=$aAds name=ads item=aAd}
	<tr{if is_int($phpfox.iteration.ads/2)} class="on"{/if}>
		<td><a href="{url link='ad.sponsor' view=$aAd.sponsor_id}"> {$aAd.campaign_name|clean|convert} </a>{if $aAd.is_custom == '1'}<a href="{url link='ad.sponsor' pay=$aAd.sponsor_id}">({_p var='pay_now'})</a>{/if}</td>
		<td class="t_center">{$aAd.status}</td>
		<td class="t_center">{$aAd.total_view}</td>
		<td class="t_center">{$aAd.total_click}</td>
		<td class="t_center">	
			{if empty($sView)}
			<div class="js_item_is_active"{if !$aAd.is_active} style="display:none;"{/if}>
				{img theme='misc/bullet_green.png' alt=''}
			</div>
			<div class="js_item_is_not_active"{if $aAd.is_active} style="display:none;"{/if}>
				{img theme='misc/bullet_red.png' alt=''}
			</div>
			{else}
			{_p var='n_a'}
			{/if}
		</td>			
	</tr>
{foreachelse}	
	<tr>
		<td colspan="5" id="no_ads_found">
			{_p var='no_ads_found'}
			{if empty($sView)}
			
			{/if}
		</td>
	</tr>
{/foreach}
</table>