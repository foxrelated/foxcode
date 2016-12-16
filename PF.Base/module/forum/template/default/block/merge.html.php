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
<div id="js_error_message"></div>
<div class="main_break"></div>
<form method="post" action="#" onsubmit="$(this).ajaxCall('forum.processMerge'); return false;">
	<div><input type="hidden" name="thread_id" value="{$aThread.thread_id}" /></div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='url'}:
		</div>
		<div class="table_right">
			<input type="text" name="url" value="" size="30" class="form-control" />
		</div>
	</div>
	{if !$bIsGroup}
	<div class="table form-group">
		<div class="table_left">
			{_p var='destination_forum'}:
		</div>
		<div class="table_right">
			<select name="forum_id" class="form-group">
				{$sForums}
			</select>
		</div>
	</div>	
	{/if}
	<div class="table_clear">
		<input type="submit" value="{_p var='merge_threads'}" class="button btn-primary"/>
	</div>
</form>