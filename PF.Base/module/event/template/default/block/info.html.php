<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: info.html.php 7046 2014-01-15 20:18:25Z Fern $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="item_user_info">
	{_p var='by'} {$aEvent|user}
</div>

<div class="item_info_wrap">

	{if $aEvent.view_id == '1'}
	<div class="message js_moderation_off">
		{_p var='event_is_pending_approval'}
	</div>
	{/if}

	{if ($aEvent.user_id == Phpfox::getUserId() && Phpfox::getUserParam('event.can_edit_own_event')) || Phpfox::getUserParam('event.can_edit_other_event')
	|| ($aEvent.view_id == 0 && ($aEvent.user_id == Phpfox::getUserId() && Phpfox::getUserParam('event.can_edit_own_event')) || Phpfox::getUserParam('event.can_edit_other_event'))
	|| ($aEvent.user_id == Phpfox::getUserId() && Phpfox::getUserParam('event.can_edit_own_event')) || Phpfox::getUserParam('event.can_edit_other_event')
	|| ($aEvent.user_id == Phpfox::getUserId() && Phpfox::getUserParam('event.can_delete_own_event')) || Phpfox::getUserParam('event.can_delete_other_event')
	}
	<div class="item_bar">
		<div class="item_bar_action_holder">
			{if (Phpfox::getUserParam('event.can_approve_events') && $aEvent.view_id == '1')}
			<a href="#" class="item_bar_action approve btn-primary" onclick="$(this).hide(); $('#js_item_bar_approve_image').show(); $.ajaxCall('event.approve', 'inline=true&amp;event_id={$aEvent.event_id}'); return false;" title="{_p var='approve'}"></a>
			{/if}
			<a role="button" data-toggle="dropdown" class="item_bar_action"><span>{_p var='actions'}</span></a>
			<ul class="dropdown-menu dropdown-menu-right">
				{template file='event.block.menu'}
			</ul>
		</div>
	</div>
	{/if}

	{if $aEvent.image_path}
	<div class="item_banner image_load" data-src="{img server_id=$aEvent.server_id title=$aEvent.title path='event.url_image' file=$aEvent.image_path suffix='' itemprop='image' return_url=true}"></div>
	{/if}
	<div class="info_holder">
		<div class="info">
			<div class="info_left">
				<span itemprop="startDate" style="display:none;">{$aEvent.start_time_micro}</span>
				{_p var='time'}
			</div>
			<div class="info_right">
				{$aEvent.event_date}
			</div>
		</div>

		{if is_array($aEvent.categories) && count($aEvent.categories)}
		<div class="info">
			<div class="info_left">
				{_p var='category'}
			</div>
			<div class="info_right">
				{$aEvent.categories|category_display}
			</div>
		</div>
		{/if}

		<div class="info" itemscope itemtype="http://schema.org/Place">
			<div class="info_left">
				{_p var='location'}
			</div>
			<div class="info_right">
				<span itemprop="name">{$aEvent.location|clean|split:60}</span>
				<div itemscope itemtype="http://schema.org/PostalAddress">
					{if !empty($aEvent.address)}
					<div class="p_top_2" itemprop="streetAddress">{$aEvent.address|clean}</div>
					{/if}
					{if !empty($aEvent.city)}
					<div class="p_top_2" itemprop="addressLocality">{$aEvent.city|clean}</div>
					{/if}
					{if !empty($aEvent.postal_code)}
					<div class="p_top_2" itemprop="postalCode">{$aEvent.postal_code|clean}</div>
					{/if}
					{if !empty($aEvent.country_child_id)}
					<div class="p_top_2" itemprop="addressRegion">{$aEvent.country_child_id|location_child}</div>
					{/if}
					<div class="p_top_2" itemprop="addressCountry">{$aEvent.country_iso|location}</div>
				</div>
				{if isset($aEvent.map_location)}
				<div style="width:100%;height:170px; position:relative;">
					<div style="margin-left:-8px; margin-top:-8px; position:absolute; background:#fff; border:8px blue solid; width:12px; height:12px; left:50%; top:50%; z-index:200; overflow:hidden; text-indent:-1000px; border-radius:12px;">Marker</div>
					<a href="//maps.google.com/?q={$aEvent.map_location}" target="_blank" title="{_p var='view_this_on_google_maps'}"><img src="//maps.googleapis.com/maps/api/staticmap?center={$aEvent.map_location}&amp;zoom=16&amp;size=360x170&amp;sensor=false&amp;maptype=roadmap" alt="" /></a>
				</div>
				<div class="p_top_4">
					<a href="//maps.google.com/?q={$aEvent.map_location}" target="_blank">{_p var='view_on_google_maps'}</a>
				</div>
				{/if}
			</div>
		</div>

		<div class="event_item_content">
			{$aEvent.description|parse|split:70}
		</div>

	</div>

</div>
<div class="marvic_separator clearfix"></div>