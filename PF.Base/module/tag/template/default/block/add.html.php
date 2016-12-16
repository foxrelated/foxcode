<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="table form-group">
	<div class="table_left">
		{_p var='topics'}:
	</div>
	<div class="table_right">
		<input type="text" name="val{if $iItemId}[{$iItemId}]{/if}[tag_list]" value="{value type='input' id='tag_list'}" size="30" />
		<div class="extra_info">
			{_p var='separate_multiple_topics_with_commas'}
		</div>
	</div>
	<div class="clear"></div>
</div>