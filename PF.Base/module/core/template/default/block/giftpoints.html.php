<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: activity.html.php 3326 2011-10-20 09:12:45Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="div_show_gift_points">
	<script type="text/javascript">
		function doGiftPoints()
		{l}
			var iAmount = {if $iCurrentAvailable == 1}1{else}$('#txt_amount_points').val(){/if};
			if ($.isNumeric(iAmount) == false || (Math.floor(iAmount) != iAmount))
			{l}
				alert('Please enter only numbers. ' + (Math.floor(iAmount) == iAmount));
			{r}
			else
			{l}
				$.ajaxCall('core.doGiftPoints', 'user_id={$iTrgUserId}&amount=' + iAmount);
			{r}
		{r}
	</script>
	<div class="extra_info">
		{_p var='you_are_about_to_gift_activity_points' full_name=$aUser.full_name current=$iCurrentAvailable}
	</div>
	{if $iCurrentAvailable > 1}
		<div class="p_top_8">
			{if $iCurrentAvailable == 1}
				{_p var='you_only_have_one_point_available' full_name=$aUser.full_name}
				<div class="p_top_8">
					<input type="button" value="{_p var='yes'}" class="button btn-primary" onclick="doGiftPoints();" />
				<div class="p_top_8">
			{else}
				{_p var='how_many_points_do_you_want_to_gift_away'}
				<div class="p_top_8">
					<input type="text" id="txt_amount_points" value="" size="8" /> <input type="button" value="{_p var='gift_points'}" class="button btn-primary" onclick="doGiftPoints();" />
				<div class="p_top_8">
			{/if}
		</div>
	{else}
		<div class="extra_info">
			{_p var='unfortunately_you_do_not_have_enough_points_to_gift_away'}
		</div>
	{/if}
</div>