<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel_Espinoza
 * @package 		Phpfox
 * @version 		$Id: sponsor.html.php 7151 2014-02-24 15:21:24Z Fern $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="block_search">
	<form method="post" action="{url link='admincp.ad.sponsor'}">
		<div class="table_header">
			{_p var='ad_filter'}
		</div>
		<div class="table form-group">
			<div class="table_left">
				{_p var='type'}:
			</div>
			<div class="table_right">
				{$aFilters.status}
			</div>
			<div class="clear"></div>
		</div>
		<div class="table form-group">
			<div class="table_left">
				{_p var='display'}:
			</div>
			<div class="table_right">
				{$aFilters.display}
			</div>
			<div class="clear"></div>
		</div>
		<div class="table form-group">
			<div class="table_left">
				{_p var='sort_by'}:
			</div>
			<div class="table_right">
				{$aFilters.sort} {$aFilters.sort_by}
			</div>
			<div class="clear"></div>
		</div>
		<div class="table_clear">
			<input type="submit" name="search[submit]" value="{_p var='submit'}" class="button btn-primary" />
			<input type="submit" name="search[reset]" value="{_p var='reset'}" class="button btn-danger" />
		</div>
	</form>
</div>

<div class="block_content">
	{if $iPendingCount > 0 && $sView != 'pending'}
	<div class="message">
		{_p var='there_are_pending_ads_that_require_your_attention_view_all_pending_ads_a_href_link_here_a' link=$sPendingLink}
	</div>
	{/if}

	{if count($aAds)}
	<div class="table_header">
		{_p var='sponsorships'}
	</div>
	<form method="post" action="{url link='admincp.ad'}">
	<table>
	<tr>
		<th style="width:20px;"></th>
		<th style="width:30px;">{_p var='id'}</th>
		<th>{_p var='campaign_name'}</th>
		<th>{_p var='user'}</th>
		<th>{_p var='status'}</th>
		<th>{_p var='views'}</th>
		<th>{_p var='clicks'}</th>
		<th style="width:50px;">{_p var='active'}</th>
	</tr>
	{foreach from=$aAds key=iKey item=aAd}
	<tr class="{if is_int($iKey/2)} tr{else}{/if}{if $aAd.is_custom && $aAd.is_custom == '2'} is_checked{/if}">
		<td class="t_center">
			<a href="#" class="js_drop_down_link" title="{_p var='manage'}">{img theme='misc/bullet_arrow_down.png' alt=''}</a>
			<div class="link_menu">
				<ul>
					{if $aAd.is_custom == '2'}
					<li><a href="{url link='admincp.ad.sponsor' approve=$aAd.sponsor_id}">{_p var='approve'}</a></li>
					<li><a href="{url link='admincp.ad.sponsor' deny=$aAd.sponsor_id}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='deny'}</a></li>
					{/if}
					<li><a href="{url link='admincp.ad.sponsor' delete=$aAd.sponsor_id}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete'}</a></li>
				</ul>
			</div>
		</td>
		<td class="t_center">{$aAd.sponsor_id}</td>
		<td><a href="{url link='ad.sponsor' view=$aAd.sponsor_id}">{$aAd.campaign_name|clean|convert}</a></td>
		<td><a href="{url link=''$aAd.user_name'}">{$aAd|user}</a></td>
		<td>{$aAd.status}</td>
		<td class="t_center">{if $aAd.is_custom == '2' || $aAd.is_custom == '1'}N/A{else}{$aAd.count_view}{/if}</td>
		<td class="t_center">{if $aAd.is_custom == '2' || $aAd.is_custom == '1'}N/A{else}{$aAd.count_click}{/if}</td>
		<td class="t_center">
			{if $aAd.is_custom == '2' || $aAd.is_custom == '1'}
			{_p var='n_a'}
			{else}
			<div class="js_item_is_active"{if !$aAd.is_active} style="display:none;"{/if}>
				<a href="#?call=ad.updateSponsorActivity&amp;id={$aAd.sponsor_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}">{img theme='misc/bullet_green.png' alt=''}</a>
			</div>
			<div class="js_item_is_not_active"{if $aAd.is_active} style="display:none;"{/if}>
				<a href="#?call=ad.updateSponsorActivity&amp;id={$aAd.sponsor_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}">{img theme='misc/bullet_red.png' alt=''}</a>
			</div>
			{/if}
		</td>
	</tr>
	{/foreach}
	</table>
	</form>
	{pager}

	{else}
	<div class="extra_info">
	{if $bIsSearch}
		{_p var='no_search_results_were_found'}
	{else}
		{_p var='no_ads_have_been_created'}
	{/if}
	</div>
	{/if}
</div>