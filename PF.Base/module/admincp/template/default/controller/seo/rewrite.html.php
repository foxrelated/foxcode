<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: nofollow.html.php 4165 2012-05-14 10:43:25Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="table_header2">
	{_p var='rewrite_url'}
</div>
	
<table class="" cellpadding="0" cellspacing="0">
	<tr id="tblHeader">
		<th id="thActions">
			
		</th>
		<th>
			{_p var='this_url'}
		</th>
		<th>
			{_p var='will_show_this_page'}
		</th>
	</tr>
	
	<tr id="trAddNew">
		<td colspan="3" id="tdAddNew" onclick="$Core.AdminCP.Rewrite.addNew();">
			{_p var='add_new_rewrite'}
		</td>
	</tr>
	
	<tr id="templateEntry">
		<td>
            <a title="{_p var='Remove'}" href="javascript:void(0)" onclick="$Core.AdminCP.Rewrite.remove(this);"><i class="fa fa-times" aria-hidden="true"></i></a>
		</td>
		<td>
			<input type="text" value="{_p var='original_url'}" class="sOriginal" onblur="$Core.AdminCP.Rewrite.checkOriginal(this)" />
			<span class="invalidOriginal">
				{img alt='Invalid Original URL' theme='misc/flag_red.png' style='vertical-align: middle;'}
			</span>
		</td>
		<td>
			<input type="text" value="{_p var='replacement_url'}" onblur="$Core.AdminCP.Rewrite.checkReplacement(this)" class="sReplacement" />
		</td>
	</tr>
</table>

<div class="clear"></div>

<div id="feedback">
	<div class="left">
		<div id="processing">
			{img theme='ajax/small.gif'}
		</div>
		<div id="message"></div>
	</div>
	<div class="right">
		<input type="button" class="button btn-primary" value="{_p var='save'}" onclick="$Core.AdminCP.Rewrite.save();" />
	</div>
</div>

<script type="text/javascript">
	$Behavior.initRewrites = function()
	{l}
		$Core.AdminCP.Rewrite.init('{$jRewrites}');
	{r};
</script>