<?php 
    defined('PHPFOX') or exit('NO DICE!');

?>
	<li>
		<div class="block_listing_image_feature">
			<a href="{permalink module='marketplace' id=$aMiniListing.listing_id title=$aMiniListing.title}" class="mp_listing_image">
            {img server_id=$aMiniListing.server_id title=$aMiniListing.title path='marketplace.url_image' file=$aMiniListing.image_path  suffix='_250_square'}</a>
            <div class="other_listing_image">
                {if isset($aMiniListing.images) && count($aMiniListing.images) > 0}
                <ul class="extra_info_middot">
                    {foreach from=$aMiniListing.images item=aMiniListingImage}
                    <li>
                        {img thickbox=true server_id=$aMiniListingImage.server_id title=$aMiniListing.title path='marketplace.url_image' file=$aMiniListingImage.image_path suffix='_50_square' max_width='32' max_height='32'}
                    </li>
                    {/foreach}
                    {if $aMiniListing.more_image}
                    <li class="more_image">
                        <span>+{$aMiniListing.more_image}</span>
                    </li>
                    {/if}
                </ul>
                {/if}
            </div>
		</div>
		<div class="block_listing_title" style="padding-top:10px; padding-left:0;">
			<a href="{permalink module='marketplace' id=$aMiniListing.listing_id title=$aMiniListing.title}" class="one_line" style="font-weight:500">{$aMiniListing.title|clean}</a>
			<div class="extra_info">
				<ul class="extra_info_middot">
                    <li class="mp_item_price">{if $aMiniListing.price == '0.00'}{_p var='free'}{else}{$aMiniListing.currency_id|currency_symbol}{$aMiniListing.price|number_format:2}{/if}</li>
                    <li>&middot;</li>
                    <li>{$aMiniListing.country_iso|location}</li></ul>
			</div>
		</div>
		<div class="clear"></div>
	</li>