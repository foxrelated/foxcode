<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{foreach from=$events item=aEvent name=event}
<div class="simple_listing_row featured_listing_row">
    <div class="listing-header">
        <a href="{permalink module='event' id=$aEvent.event_id title=$aEvent.title}" title="{$aEvent.title|clean}">
            <div id="event_date_calendar">
                <span class="month">{$aEvent.start_time_month}</span>
                <span class="day">{$aEvent.start_time_short_day}</span>
            </div>
        </a>
    </div>
    <div class="listing-info">
        <a href="{permalink module='event' id=$aEvent.event_id title=$aEvent.title}" class="row_sub_link" title="{$aEvent.title|clean}">{$aEvent.title|clean|shorten:50:'...'|split:20}</a>
        <div class="extra_info_link">
            {_p var='by'} {$aEvent|user}
        </div>
    </div>
    <div class="clear"></div>
</div>
{/foreach}