<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="main_break"></div>
<form method="post" action="#" onsubmit="$('#js_moving_forum').html($.ajaxProcess('{_p var='moving' phpfox_squote=true}')); $(this).ajaxCall('forum.processMove'); return false;">
	<div><input type="hidden" name="thread_id" value="{$aThread.thread_id}" /></div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='destination_forum'}:
		</div>
		<div class="table_right">
			<select name="forum_id" class="form-control">
				{$sForums}
			</select>
		</div>
	</div>
	<div class="table_clear">
		<input type="submit" value="{_p var='move_thread'}" class="button btn-primary" /> <span id="js_moving_forum"></span>
	</div>
</form>