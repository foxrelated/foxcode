<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: attending.html.php 3342 2011-10-21 12:59:32Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="event_block_attending">
{if count($aInvites)}
<div class="block">
	<div class="title">{$iAttendingCnt} {_p var='attending'}</div>
	<div class="content">
		<ul class="block_listing">
		{foreach from=$aInvites name=invites item=aInvite}
			<li>{img user=$aInvite suffix='_50_square' max_width=32 max_height=32 class='v_middle'} {$aInvite|user}</li>
		{/foreach}
		</ul>
	</div>
</div>
{/if}

{if count($aMaybeInvites)}
<div class="block">
	<div class="title">
		{$iMaybeCnt} {_p var='maybe_attending'}
	</div>
	<div class="content">
		<ul class="block_listing">
		{foreach from=$aMaybeInvites name=invites item=aInvite}
			<li>{img user=$aInvite suffix='_50_square' max_width=32 max_height=32 class='v_middle'} {$aInvite|user}</li>
		{/foreach}
		</ul>
	</div>
</div>	
{/if}	

{if count($aAwaitingInvites)}
<div class="block">
	<div class="title">
		{$iAwaitingCnt} {_p var='awaiting_reply'}
	</div>
	<div class="content">
		<ul class="block_listing">
		{foreach from=$aAwaitingInvites name=invites item=aInvite}
			<li>{img user=$aInvite suffix='_50_square' max_width=32 max_height=32 class='v_middle'} {$aInvite|user}</li>
		{/foreach}
		</ul>
		<div class="clear"></div>
	</div>
</div>
{/if}	

{if count($aNotAttendingInvites)}
<div class="block">
	<div class="title">
		{$iNotAttendingCnt} {_p var='not_attending'}
	</div>
	<div class="content">
		<ul class="block_listing">
		{foreach from=$aNotAttendingInvites name=invites item=aInvite}
			<li>{img user=$aInvite suffix='_50_square' max_width=32 max_height=32 class='v_middle'} {$aInvite|user}</li>
		{/foreach}
		</ul>
	</div>
</div>
{/if}
</div>
{if !PHPFOX_IS_AJAX}
<script type="text/javascript">
var sEventId = {$aEvent.event_id};
{literal}
$Behavior.onClickEventGuestList = function()
{
	if ($Core.exists('#js_controller_event_view')){
		$('#js_controller_event_view #js_block_bottom_link_1').click(function()
		{
			$Core.box('event.browseList', '400', 'id=' + sEventId);

			return false;
		});
	}
}
{/literal}
</script>
{/if}