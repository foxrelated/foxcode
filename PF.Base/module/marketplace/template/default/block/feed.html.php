<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if $aListing.image_path}
<div class="activity_feed_content_image">
	<a href="{$aListing.url}" target="_blank">
		{img server_id=$aListing.server_id title=$aListing.title path='marketplace.url_image' file=$aListing.image_path suffix='_200' itemprop='image'}
	</a>
</div>
{/if}

<div class="feed_block_title_content  activity_feed_content_float">
	<a href="{$aListing.url}" class="activity_feed_content_link_title">{$aListing.title|clean|shorten:100:'...'|split:25}</a>
	<div class="activity_feed_content_display">
		<div class="extra_info">
			<span class="fw-600 txt-time-color">{_p var='category'}:</span>
			<span class="category">{$aListing.categories|category_display}</span>
		</div>
		<div class="extra_info price fw-600">
			{if $aListing.price == '0.00'}
			{_p var='free'}
			{else}
			{$aListing.currency_id|currency_symbol}{$aListing.price|number_format:2}
			{/if}
		</div>
		<div class="extra_info">
			{$aListing.mini_description|feed_strip|split:55|max_line|shorten:100}
		</div>
	</div>
</div>