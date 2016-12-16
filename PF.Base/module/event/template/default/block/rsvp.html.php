<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: rsvp.html.php 4503 2012-07-11 14:41:02Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form id="event_rsvp" method="post" action="{url link='current'}" data-event-id="{$aEvent.event_id}">
{if isset($aCallback) && $aCallback !== false}
	<div><input type="hidden" name="module" value="{$aCallback.module}" /></div>
	<div><input type="hidden" name="item" value="{$aCallback.item}" /></div>
{/if}
	<div class="p_2 attending {if $aEvent.rsvp_id == 1} active{/if}">
		<input type="radio" name="rsvp" value="1" class="v_middle js_event_rsvp" {if $aEvent.rsvp_id == 1}checked="checked" {/if}/> {_p var='attending'}
	</div>
	<div class="p_2 maybe_attending {if $aEvent.rsvp_id == 2} active{/if}">
		<input type="radio" name="rsvp" value="2" class="v_middle js_event_rsvp" {if $aEvent.rsvp_id == 2}checked="checked" {/if}/> {_p var='maybe_attending'}
	</div>
	<div class="p_2 not_attending{if $aEvent.rsvp_id == 3} active{/if}">
		<input type="radio" name="rsvp" value="3" class="v_middle js_event_rsvp" {if $aEvent.rsvp_id == 3}checked="checked" {/if}/> {_p var='not_attending'}
	</div>
</form>
{literal}
<script>
	$Ready(function() {
		$('#event_rsvp .p_2').click(function() {
			var t = $(this), f = $(this).parents('form:first');
			$('.p_2.active').removeClass('active');
			t.addClass('active');
			t.find('input').prop('checked', true);
			f.ajaxCall('event.addRsvp', '&id=' + f.data('event-id'));
		});
	});
</script>
{/literal}