<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 1558 2010-05-04 12:51:22Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<table id="js_drag_drop" cellpadding="0" cellspacing="0">
	<tr>
		<th></th>
		<th style="width:20px;"></th>
		<th style="width:40px;" class="t_center">{_p var='id'}</th>
		<th style="width:60px;" class="t_center">{_p var='symbol'}</th>
		<th>{_p var='currency'}</th>
		<th class="t_center" style="width:80px;">{_p var='default'}</th>
		<th class="t_center" style="width:60px;">{_p var='active'}</th>
	</tr>
{foreach from=$aCurrencies name=currencies item=aCurrency}
	<tr class="checkRow{if is_int($phpfox.iteration.currencies/2)} tr{else}{/if}">
		<td class="drag_handle"><input type="hidden" name="val[ordering][{$aCurrency.currency_id}]" value="{$aCurrency.ordering}" /></td>
		<td class="t_center">
			<a href="#" class="js_drop_down_link" title="{_p var='manage'}">{img theme='misc/bullet_arrow_down.png'}</a>
			<div class="link_menu">
				<ul>
					<li><a href="{url link='admincp.core.currency.add' id={$aCurrency.currency_id}">{_p var='edit'}</a></li>
					<li><a href="{url link='admincp.core.currency' delete={$aCurrency.currency_id}" class="sJsConfirm" data-message="{_p var='are_you_sure' phpfox_squote=true}">{_p var='delete'}</a></li>
				</ul>
			</div>
		</td>
		<td class="t_center">{$aCurrency.currency_id}</td>
		<td class="t_center">{$aCurrency.symbol}</td>
		<td>{_p var=$aCurrency.phrase_var}</td>
		<td class="t_center">
			<div class="js_item_is_active"{if !$aCurrency.is_default} style="display:none;"{/if}>
				{img theme='misc/bullet_green.png' alt=''}
			</div>
			<div class="js_item_is_not_active"{if $aCurrency.is_default} style="display:none;"{/if}>
				<a href="#?call=core.updateCurrencyDefault&amp;id={$aCurrency.currency_id}&amp;active=1" class="js_item_active_link js_remove_default" title="{_p var='set_as_default'}">{img theme='misc/bullet_red.png' alt=''}</a>
			</div>		
		</td>
		<td class="t_center">
			<div class="js_item_is_active"{if !$aCurrency.is_active} style="display:none;"{/if}>
				<a href="#?call=core.updateCurrencyActivity&amp;id={$aCurrency.currency_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}">{img theme='misc/bullet_green.png' alt=''}</a>
			</div>
			<div class="js_item_is_not_active"{if $aCurrency.is_active} style="display:none;"{/if}>
				<a href="#?call=core.updateCurrencyActivity&amp;id={$aCurrency.currency_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}">{img theme='misc/bullet_red.png' alt=''}</a>
			</div>		
		</td>		
	</tr>
{/foreach}
</table>
<div class="table_clear"></div>