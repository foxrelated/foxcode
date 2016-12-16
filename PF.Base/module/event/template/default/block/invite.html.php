<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: invite.html.php 3533 2011-11-21 14:07:21Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<ul class="block_event_listing">
	{foreach from=$aEventInvites item=aEventInvite}
	<li>
		<div class="block_listing_title pb-base">
			<a class="fw-500 lines-base-1-forced" href="{permalink module='event' id=$aEventInvite.event_id title=$aEventInvite.title}">{$aEventInvite.title|clean}</a>
		</div>
		<div class="block_listing_image clearfix">
			<div class="mp_event_image pull-left">
				{img server_id=$aEventInvite.server_id title=$aEventInvite.title path='event.url_image' file=$aEventInvite.image_path suffix='_50' max_width='50' max_height='50'}
			</div>
			<div style="padding-left: 60px;line-height: 17px;">
				<div class="txt-danger fw-500 uppercase fs-12">{$aEventInvite.start_time_phrase}</div>
				<div>@ {$aEventInvite.start_time_phrase_stamp}</div>
				<div> at {$aEventInvite.country_iso|location}</div>
			</div>
		</div>
		<div class="pv-base clearfix">
			<div class="lines-base-3">
				{$aEventInvite.description}
			</div>
		</div>
		<ul class="event_rsvp_invite">
			<li>
				<div>
					<a class="btn btn-success btn-block" href="#" onclick="$(this).parent().parent().hide(); $('#js_event_rsvp_invite_image_{$aEventInvite.event_id}').show(); $.ajaxCall('event.addRsvp', 'id={$aEventInvite.event_id}&amp;rsvp=1&amp;inline=1'); return false;">{_p var='yes'}</a>
				</div>
				<div class="event_rsvp_btn_list clearfix">
					<div>
						<a class="btn btn-default btn-block" href="#" onclick="$(this).parent().parent().hide(); $('#js_event_rsvp_invite_image_{$aEventInvite.event_id}').show(); $.ajaxCall('event.addRsvp', 'id={$aEventInvite.event_id}&amp;rsvp=3&amp;inline=1'); return false;">{_p var='no'}</a>
					</div>
					<div class="col-sm-6">
						<a class="btn btn-primary btn-block" href="#" onclick="$(this).parent().parent().hide(); $('#js_event_rsvp_invite_image_{$aEventInvite.event_id}').show(); $.ajaxCall('event.addRsvp', 'id={$aEventInvite.event_id}&amp;rsvp=2&amp;inline=1'); return false;">{_p var='maybe'}</a>
					</div>
				</div>
			</li></ul>
		<div class="clear"></div>
	</li>
	{/foreach}
</ul>