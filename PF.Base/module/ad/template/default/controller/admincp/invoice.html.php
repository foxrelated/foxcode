<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: invoice.html.php 2029 2010-11-01 16:57:31Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.ad.invoice'}">
	<div class="table_header">
		{_p var='invoice_filter'}
	</div>	
	<div class="table form-group">
		<div class="table_left">
			{_p var='status'}:
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

<br />

{pager}

{if !count($aInvoices)}
<div class="extra_info">
	{_p var='there_are_no_invoices'}
</div>
{else}
<div class="table_header">
	{_p var='invoices'}
</div>
<table cellpadding="0" cellspacing="0">
	<tr>
		<th style="width:20px;"></th>
		<th>{_p var='id'}</th>
		<th>{_p var='status'}</th>
		<th>{_p var='price'}</th>
		<th>{_p var='date'}</th>
	</tr>
	{foreach from=$aInvoices key=iKey item=aInvoice}
	<tr {if is_int($iKey/2)} class="tr"{/if}>
		<td class="t_center">
			<a href="#" class="js_drop_down_link" title="{_p var='manage'}">{img theme='misc/bullet_arrow_down.png' alt=''}</a>
			<div class="link_menu">
				<ul>
					<li><a href="{url link='admincp.ad.invoice' delete=$aInvoice.invoice_id}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete'}</a></li>
				</ul>
			</div>		
		</td>		
		<td class="t_center">{$aInvoice.invoice_id}</td>
		<td>{$aInvoice.status_phrase}</td>
		<td>{$aInvoice.price|currency:$aInvoice.currency_id}</td>
		<td>{$aInvoice.time_stamp|date}</td>
	</tr>
	{/foreach}
</table>
<div class="table_clear"></div>

{pager}

{/if}