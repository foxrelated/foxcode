{item name='Event'}
<div id="js_event_item_holder_{$aEvent.event_id}" class="event_large_item image_load js_event_parent {if empty($aEvent.image_path)}no_image{else}has_image{/if}">
    <header class="clearfix">
        <div class="event_large_date">
            <div class="day">{$aEvent.start_time_short_day}</div>
            <div class="month">{$aEvent.start_time_month}</div>
        </div>
        <div class="event_large_time">
            <div class="time">{$aEvent.start_time_phrase_stamp}</div>
        </div>
        <div class="event_large_title">
            <h1 itemprop="name"><a href="{$aEvent.url}" class="link" itemprop="url">{$aEvent.title|clean}</a></h1>
            <div class="extra_info">{_p var='by'} {$aEvent|user} {_p var='at'} {$aEvent.location}</div>
        </div>
    </header>

    <div class="event_large_image" {if $aEvent.image_path}style="background-image:url({img server_id=$aEvent.server_id title=$aEvent.title path='event.url_image' file=$aEvent.image_path suffix='' return_url=true})"{/if}  itemprop='image'></div>
    {if (isset($aEvent.is_on_feed) && $aEvent.is_on_feed)}
    {if  $aEvent.description_parsed}
    <div class="item-description txt-time-color">
        {$aEvent.description_parsed|parse|split:70|shorten:100}
    </div>
    {/if}
    {else}

        {if ($aEvent.user_id == Phpfox::getUserId() && Phpfox::getUserParam('event.can_edit_own_event')) || Phpfox::getUserParam('event.can_edit_other_event')
        || ($aEvent.view_id == 0 && ($aEvent.user_id == Phpfox::getUserId() && Phpfox::getUserParam('event.can_edit_own_event')) || Phpfox::getUserParam('event.can_edit_other_event'))
        || ($aEvent.user_id == Phpfox::getUserId() && Phpfox::getUserParam('event.can_edit_own_event')) || Phpfox::getUserParam('event.can_edit_other_event')
        || ($aEvent.user_id == Phpfox::getUserId() && Phpfox::getUserParam('event.can_delete_own_event')) || Phpfox::getUserParam('event.can_delete_other_event')
        || (defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->isAdmin('' . $aPage.page_id . ''))
        }
        <div class="moderation_row">
            <div class="row_edit_bar_parent">
                <div class="row_edit_bar">
                    <a role="button" class="row_edit_bar_action" data-toggle="dropdown">
                        <i class="fa fa-action"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        {template file='event.block.menu'}
                    </ul>
                </div>
            </div>
            {if Phpfox::getUserParam('event.can_approve_events') || Phpfox::getUserParam('event.can_delete_other_event')}<a href="#{$aEvent.event_id}" class="moderate_link" rel="event" data-id="mod">{_p var='moderate'}</a>{/if}
        </div>
        {/if}
    {/if}
</div>
{/item}