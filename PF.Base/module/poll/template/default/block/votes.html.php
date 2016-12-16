<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $page == 1 }
<div class="label_flow voted-members-list">
	{/if}
	{foreach from=$aVotes name=votes item=aResult}
	<div class="{if is_int($phpfox.iteration.votes/2)}row1{else}row2{/if}{if $phpfox.iteration.votes == 1} row_first{/if} clearfix">
		<div class="poll_user_image pull-left">
			{img user=$aResult suffix='_50_square' max_width=50 max_height=50}	
		</div>
		<div class="poll-info">
			<div>
				{_p var='user_info_voted_answer' user_info=$aResult|user answer=$aResult.answer|clean}
			</div>
			<div class="time-info">
				{$aResult.time_stamp|date:'poll.poll_view_time_stamp'}
			</div>
		</div>
	</div>
	{/foreach}
	{if $hasMore}
	{pager}
	{/if}
	{if $page == 1 }
</div>
{/if}