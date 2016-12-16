<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<article id="js_mp_item_holder_{$aListing.listing_id}" class="listing_row {if $aListing.is_sponsor}is_sponsored {/if}{if $aListing.is_featured}is_featured {/if}{if $aListing.view_id == 1 && !isset($bIsInPendingMode)}is_moderate {/if}">
	{if $aListing.is_featured}
	<div id="js_featured_phrase_{$aListing.listing_id}" class="row_featured_link">
		{_p var='featured'}
	</div>
	{/if}					
	{if $aListing.is_sponsor}	
	<div id="js_sponsor_phrase_{$aListing.listing_id}" class="js_sponsor_event row_sponsored_link">
		{_p var='sponsored'}
	</div>
	{/if}
	<div class="listing_image pull-left hidden-xs">
		<a href="{$aListing.url}">
			{img server_id=$aListing.server_id title=$aListing.title path='marketplace.url_image' file=$aListing.image_path suffix='_200_square' max_width='160' max_height='160' itemprop='image'}
		</a>
	</div>

	<div class="listing_image_full visible-xs" style="background-image: url({img server_id=$aListing.server_id title=$aListing.title path='marketplace.url_image' file=$aListing.image_path return_url=true})">
	</div>

	<div class="listing_content">
		<header><h1 itemprop="name"><a href="{$aListing.url}" class="link" title="{$aListing.title|clean}" itemprop="url">{$aListing.title|clean|shorten:100:'...'|split:25}</a>{if $aListing.view_id == '2'}<span class="marketplace_item_sold">({_p var='sold'})</span>{/if}</h1></header>

		{if !isset($aListing.is_in_feed)}
		<ul class="listing_info">
			{if isset($aListing.user_name)}
			<li><span class="txt-time-color">{_p var='posted_by'}:<span> {$aListing|user:'':'':30}</li>
			{/if}
			<li class="txt-time-color" itemprop="releaseDate">{$aListing.time_stamp|convert_time}</li>
		</ul>
		{/if}
		<div class="listing_category">
			<ul class="listing_info">
				<li><span class="txt-time-color">{_p var='category'}:</span> {softPhrase var=$aListing.category_name}</li>
			</ul>
		</div>
		<div class="listing_location">
			<ul class="listing_info">
				<li><span class="txt-time-color">{_p var='location'}:</span>
					<a class="js_hover_title" href="{url link='marketplace' location=$aListing.country_iso}">
						{$aListing.country_iso|location}<span class="js_hover_info">{if !empty($aListing.city)} {$aListing.city|clean} &raquo; {/if}
					{if !empty($aListing.country_child_id)} {$aListing.country_child_id|location_child} &raquo; {/if}
					{$aListing.country_iso|location}</span>
					</a>
				</li>
			</ul>
		</div>

		<div class="listing_pricing" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
		<span itemprop="price">
		    {if $aListing.price == '0.00'}
			    {_p var='free'}
		    {else}
			    {$aListing.currency_id|currency_symbol}{$aListing.price|number_format:2}
		    {/if}
		</span>
		</div>
	</div>

</article>