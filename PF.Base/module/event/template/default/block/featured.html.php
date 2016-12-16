<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{foreach from=$aFeatured item=aFeature name=featured}
<div class="simple_listing_row featured_listing_row">
	<div class="listing-header">
		<a href="{permalink module='event' id=$aFeature.event_id title=$aFeature.title}" title="{$aFeature.title|clean}">
			<div id="event_date_calendar">
				<span class="month">{$aFeature.start_time_short_month}</span>
				<span class="day">{$aFeature.start_time_short_day}</span>
			</div>
		</a>
	</div>
	<div class="listing-info">
		<a href="{permalink module='event' id=$aFeature.event_id title=$aFeature.title}" title="{$aFeature.title|clean}">{$aFeature.title|clean}</a>
		<div class="extra_info_link">
			{_p var='by'} {$aFeature|user}
		</div>
		<div class="listing-time">
			{$aFeature.start_time_phrase_stamp}
		</div>
	</div>
	<div class="clear"></div>
</div>
{/foreach}