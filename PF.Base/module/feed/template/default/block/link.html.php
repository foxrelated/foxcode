<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<ul class="dropdown-menu dropdown-menu-right">
    {if Phpfox::isModule('report') && $aFeed.type_id=='user_status' && $aFeed.user_id != Phpfox::getUserId()  && !User_Service_Block_Block::instance()->isBlocked(null, $aFeed.user_id)}
    <li class=""><a href="#?call=report.add&height=210&width=400&type=user_status&id={$aFeed.item_id}" class="inlinePopup" title="{_p var='report'}">
            <i class="fa fa-flag"></i> {_p var='report'}</a></li>
    {/if}

	{if !empty($feed_entry_be) && ((defined('PHPFOX_FEED_CAN_DELETE')) || (Phpfox::getUserParam('feed.can_delete_own_feed') && $aFeed.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('feed.can_delete_other_feeds'))}
	<li class=""><a href="#" class="" onclick="tb_show('{_p var='Notice'}', $.ajaxBox('feed.delete', 'height=400&amp;width=600&amp;TB_inline=1&amp;call=feed.delete&amp;type=delete&amp;id={$aFeed.feed_id}{if isset($aFeedCallback.module)}&amp;module={$aFeedCallback.module}&amp;item={$aFeedCallback.item_id}{/if}')); return false;">
			<i class="fa fa-trash"></i> {_p var='delete_this_feed'}</a></li>
	{/if}

    {if $aFeed.type_id == "user_status" && ((Phpfox::getUserParam('feed.can_edit_own_user_status') && $aFeed.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('feed.can_edit_other_user_status'))}
	<li class=""><a href="#" class="" onclick="tb_show('{_p var='Edit your post'}', $.ajaxBox('feed.editUserStatus', 'height=400&amp;width=600&amp;id={$aFeed.feed_id}')); return false;">
			<i class="fa fa-pencil-square-o"></i> {_p var='edit'}</a></li>
	{/if}

	{assign var=empty value=true}
	
	{if Phpfox::isModule('report') && isset($sFeedType) && isset($aFeed.report_module)  && !User_Service_Block_Block::instance()->isBlocked(null, $aFeed.user_id)}
		{assign var=empty value=false}
		<li><a href="#?call=report.add&amp;height=100&amp;width=400&amp;type={$aFeed.report_module}&amp;id={$aFeed.item_id}" class="inlinePopup activity_feed_report" title="{$aFeed.report_phrase}">
				<i class="fa fa-flag"></i>
				{_p var='report'}</a>
		</li>
	{/if}

	{plugin call='feed.template_block_entry_2'}

	{plugin call='core.template_block_comment_border_new'}

</ul>
{if $empty}
<input type="hidden" class="comment_mini_link_like_empty" value="1" />
{/if}