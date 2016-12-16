<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Poll
 * @version 		$Id: design.html.php 5978 2013-05-29 08:36:19Z Miguel_Espinoza $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{template file='poll.block.entry'}

{if isset($bDesign) && $bDesign}
<h3>{_p var='poll_designer'}</h3>
	<form action="{url link='poll.design' id=$aPoll.poll_id}" method="post">
	<div><input type="hidden" name="val[poll_id]" id="iPoll" value="{$aPoll.poll_id}" /></div>
	<div id="js_poll_design_wrapper">
			<div class="table form-group">
				<div class="table_left">
					{_p var='background'}:
				</div>
				<div class="table_right label_hover">
					<input type="hidden" name="val[js_poll_background]" value="{$aPoll.background}" data-rel="backgroundChooser" class="_colorpicker" />
					<div class="_colorpicker_holder"></div>
				</div>
				<div class="clear"></div>
			</div>

			<div class="table form-group">
				<div class="table_left">
					{_p var='percent'}:
					</div>
				<div class="table_right label_hover">
					<input type="hidden" name="val[js_poll_percentage]" value="{$aPoll.percentage}" data-rel="percentageChooser" class="_colorpicker" />
					<div class="_colorpicker_holder"></div>
				</div>
				<div class="clear"></div>
			</div>

			<div class="table form-group">
				<div class="table_left">
					{_p var='border'}:
				</div>
				<div class="table_right label_hover">
					<input type="hidden" name="val[js_poll_border]" value="{$aPoll.border}" data-rel="borderChooser" class="_colorpicker" />
					<div class="_colorpicker_holder"></div>
				</div>
				<div class="clear"></div>
			</div>

			<div class="table_clear">
				<ul class="table_clear_button">
					<li><input type="submit" value="{_p var='save'}" class="button btn-primary" /></li>
					<li><input type="button" class="button button_off" onclick="window.location.href='{permalink module='poll' id=$aPoll.poll_id title=$aPoll.question}';" value="{_p var='cancel'}" /></li>
				</ul>
				<div class="clear"></div>
			</div>
		</div>
	</form>
{/if}