<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !count($aInvoices)}
<div class="extra_info">
	{_p var='you_do_not_have_any_invoices'}
</div>
{else}
<table class="default_table" cellpadding="0" cellspacing="0">
	<tr>
		<th>{_p var='id'}</th>
		<th>{_p var='status'}</th>
		<th>{_p var='price'}</th>
		<th>{_p var='created'}</th>
		<th>{_p var='paid'}</th>
		<th>{_p var='sent_to'}</th>
		<th>{_p var='sent_from'}</th>
	</tr>
	{foreach from=$aInvoices item=aInvoice}
	<tr>
		<td class="t_center">{$aInvoice.invoice_id}</td>
		<td>{$aInvoice.status}</td>
		<td>{$aInvoice.price|currency:$aInvoice.currency_id}</td>
		<td>{$aInvoice.time_stamp_created|date:'core.extended_global_time_stamp'}</td>
		<td>{if $aInvoice.time_stamp_paid > 0 }{$aInvoice.time_stamp_paid|date:'core.extended_global_time_stamp'}{/if}</td>
		<td>{$aInvoice.to|user}</td>
		<td>{$aInvoice.from|user}</td>
	</tr>
	{/foreach}
</table>
{/if}