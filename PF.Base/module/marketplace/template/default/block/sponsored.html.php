<?php 
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: sponsored.html.php 1559 2010-05-04 13:06:56Z Miguel_Espinoza $
 */

defined('PHPFOX') or exit('NO DICE!'); 

?>
{foreach from=$aSponsorListings name=listings item=aListing}
<div class="{if is_int($phpfox.iteration.listings/2)}row1{else}row2{/if}{if $phpfox.iteration.listings == 1} row_first{/if}">

    <div style="width:55px; position:absolute; text-align:center;">
	<a class="mp_listing_image" href="{url link='ad.sponsor' view=$aListing.sponsor_id}">{img server_id=$aListing.server_id title=$aListing.title path='marketplace.url_image' file=$aListing.image_path suffix='_50' max_width='50' max_height='50'}</a>
    </div>

    <div style="margin-left:60px; height:auto !important;min-height: 50px;">
	<div class="mp_item_price" style="font-size:10pt; font-weight:bold;">
		{if $aListing.price == '0.00'}
			{_p var='free'}
		{else}
			{$aListing.currency_id|currency_symbol}{$aListing.price|number_format:2}
		{/if}
	</div>
	<a href="{url link='ad.sponsor' view=$aListing.sponsor_id}" class="one_line">{$aListing.title|clean}</a>
		{if !empty($aListing.short_description)}
	<div class="extra_info">
			{$aListing.short_description|clean|shorten:30:'...'|split:20}
	</div>
		{/if}
    </div>
</div>
{/foreach}