<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: list.html.php 1339 2009-12-19 00:37:55Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bIsSearching && !count($aPurchases)}
<div class="message">
	{_p var='could_not_find_any_purchase_orders_with_your_search_criteria'}
</div>
{/if}
<form method="post" action="{url link='admincp.subscribe.list'}">
	<div class="table_header">
		{_p var='filter'}
	</div>	
	<div class="table form-group">
		<div class="table_left">
			{_p var='status'}:
		</div>
		<div class="table_right">
			{filter key='status'}	
		</div>
		<div class="clear"></div>
	</div>		
	<div class="table form-group">
		<div class="table_left">
			{_p var='sort_results_by'}:
		</div>
		<div class="table_right">
			{filter key='sort'}	
		</div>
		<div class="clear"></div>
	</div>			
	<div class="table_clear">		
		<input type="submit" value="{_p var='update'}" class="button btn-primary" />
		{if $bIsSearching}
		<input type="submit" value="{_p var='reset'}" class="button btn-danger" name="search[reset]" />
		{/if}
	</div>	
</form>
{if count($aPurchases)}
<br />

{pager}
<div class="table_header">
	{_p var='orders'}
</div>
<table cellpadding="0" cellspacing="0">
<tr>
	<th style="width:20px;"></th>
	<th class="t_center" style="width:100px;">{_p var='order_id'}</th>
	<th>{_p var='package'}</th>
	<th>{_p var='user'}</th>
	<th class="t_center" style="width:100px;">{_p var='price'}</th>
	<th style="width:300px;">{_p var='status'}</th>
	<th>{_p var='time'}</th>
</tr>
{foreach from=$aPurchases key=iKey item=aPurchase}
<tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
	<td class="t_center">
		<a href="#" class="js_drop_down_link" title="{_p var='manage'}">{img theme='misc/bullet_arrow_down.png' alt=''}</a>
		<div class="link_menu">
			<ul>
				<li><a href="{url link='admincp.subscribe.list' delete={$aPurchase.purchase_id}" class="sJsConfirm" data-message="{_p var='are_you_sure' phpfox_squote=true}">{_p var='delete_order'}</a></li>
			</ul>
		</div>		
	</td>	
	<td class="t_center">{$aPurchase.purchase_id}</td>
	<td><a href="{url link='admincp.subscribe.add' id=$aPurchase.package_id}">{$aPurchase.title|convert|clean}</a></td>
	<td>{$aPurchase|user}</td>
	<td class="t_center">
		{if isset($aPurchase.default_cost) && $aPurchase.default_cost != '0.00'}			
			{if isset($aPurchase.default_recurring_cost)}
				{$aPurchase.default_recurring_currency_id|currency_symbol}{$aPurchase.default_recurring_cost}
			{else}
			{$aPurchase.default_currency_id|currency_symbol}{$aPurchase.default_cost|number_format}
			{/if}
		{else}
        {_p var='free'}
		{/if}	
	</td>
	<td>
		<a href="#" class="form_select_active">
			{if $aPurchase.status == 'completed'}
			{_p var='active'}
			{elseif $aPurchase.status == 'cancel'}
			{_p var='canceled'}
			{elseif $aPurchase.status == 'pending'}
			{_p var='pending_payment'}
			{else}
			{_p var='pending_action'}
			{/if}
		</a>
		<ul class="form_select">
			<li><a href="#?call=subscribe.updatePurchase&amp;status=completed&amp;purchase_id={$aPurchase.purchase_id}">{_p var='active'}</a></li>
			<li><a href="#?call=subscribe.updatePurchase&amp;status=cancel&amp;purchase_id={$aPurchase.purchase_id}">{_p var='canceled'}</a></li>
			<li><a href="#?call=subscribe.updatePurchase&amp;status=pending&amp;purchase_id={$aPurchase.purchase_id}">{_p var='pending_payment'}</a></li>
			<li><a href="#?call=subscribe.updatePurchase&amp;status=&amp;purchase_id={$aPurchase.purchase_id}">{_p var='pending_action'}</a></li>
		</ul>
	</td>
	<td>{$aPurchase.time_stamp|date}</td>
</tr>
{/foreach}
</table>
{pager}
{else}
{if !$bIsSearching}
<div class="extra_info">
	{_p var='no_purchase_orders'}
</div>
{/if}
{/if}