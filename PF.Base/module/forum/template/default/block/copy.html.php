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
<form method="post" action="#" onsubmit="$('#js_copying_forum').html($.ajaxProcess('{_p var='copying' phpfox_squote=true}')); $(this).ajaxCall('forum.processCopy'); return false;">
	<div><input type="hidden" name="thread_id" value="{$aThread.thread_id}" /></div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='new_title'}:
		</div>
		<div class="table_right">
			<input type="text" name="title" value="{$aThread.title|clean}" size="30" class="form-control" />
		</div>
	</div>	
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
		<input type="submit" value="{_p var='copy_thread'}" class="button btn-primary" />
		<span id="js_copying_forum"></span>
	</div>
</form>