<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Forum
 * @version 		$Id: $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
	{if isset($isReplies)}
		{foreach from=$aThread.posts name=posts item=aPost}
			{plugin call='forum.template_controller_post_1'}
			{template file='forum.block.post'}
			{plugin call='forum.template_controller_post_2'}
		{/foreach}
	{else}
		{if $sPermaView === null}
		{if $aThread.view_id}
		<div class="message">
			{_p var='thread_is_pending_approval'}
		</div>
		{/if}
		{if !$aThread.is_announcement}
		<div class="forum_header_menu">
			<ul class="sub_menu_bar">
				{if Phpfox::isModule('share')}
				{module name='share.link' type='feed' sharefeedid=$aThread.thread_id url=$sCurrentThreadLink title=$aThread.title display='menu' sharemodule='forum'}
				{/if}
				{if Phpfox::isUser()}
				<li class="sub_menu_bar_li dropdown">
					<a role="button" data-toggle="dropdown" class="sJsDropMenu drop_down_link">{_p var='thread_tools'}</a>
					<ul class="dropdown-menu dropdown-menu-right">
						{if $aThread.view_id && (Phpfox::getUserParam('forum.can_approve_forum_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess('' . $aThread.forum_id . '', 'approve_thread'))}
						<li><a href="{url link='current' approve='true'}">{_p var='approve_thread'}</a></li>
						{/if}
						{if $bCanEditThread}
						<li><a href="{if $aCallback === null}{url link='forum.post.thread' edit=$aThread.thread_id}{else}{url link='forum.post.thread' module=$aCallback.module item=$aCallback.group_id edit=$aThread.thread_id}{/if}">{phrase var='forum.edit_thread'}</a></li>
						{/if}
						{if $aCallback === null}
						{if Phpfox::getUserParam('forum.can_move_forum_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess('' . $aThread.forum_id . '', 'move_thread')}
						<li><a href="#" onclick="tb_show('{_p var='move_thread' phpfox_squote=true}', $.ajaxBox('forum.move', 'height=200&amp;width=550&amp;thread_id={$aThread.thread_id}')); return false;">{_p var='move_thread'}</a></li>
						{/if}
						{if Phpfox::getUserParam('forum.can_copy_forum_thread') || Forum_Service_Moderate_Moderate::instance()->hasAccess('' . $aThread.forum_id . '', 'copy_thread')}
						<li><a href="#" onclick="tb_show('{_p var='copy_thread' phpfox_squote=true}', $.ajaxBox('forum.copy', 'height=200&amp;width=550&amp;thread_id={$aThread.thread_id}')); return false;">{_p var='copy_thread'}</a></li>
						{/if}
						{/if}
						{if $bCanDeleteThread}
						<li><a href="#" onclick="return $Core.forum.deleteThread('{$aThread.thread_id}');">{_p var='delete_thread'}</a></li>
						{/if}
						{if $bCanStickThread}
						{if $aThread.order_id == 1}
						<li id="js_stick_thread"><a href="#" onclick="return $Core.forum.stickThread('{$aThread.thread_id}', 0);">{_p var='unstick_thread'}</a></li>
						{else}
						<li id="js_stick_thread"><a href="#" onclick="return $Core.forum.stickThread('{$aThread.thread_id}', 1);">{_p var='stick_thread'}</a></li>
						{/if}
						{/if}
						{if $bCanCloseThread}
						{if $aThread.is_closed}
						<li id="js_close_thread"><a href="#" onclick="return $Core.forum.closeThread('{$aThread.thread_id}', 0);">{_p var='open_thread'}</a></li>
						{else}
						<li id="js_close_thread"><a href="#" onclick="return $Core.forum.closeThread('{$aThread.thread_id}', 1);">{_p var='close_thread'}</a></li>
						{/if}
						{/if}
						{if $bCanMergeThread}
						<li><a href="#" onclick="tb_show('{_p var='merge_threads' phpfox_squote=true}', $.ajaxBox('forum.merge', 'height=200&amp;width=550&amp;thread_id={$aThread.thread_id}')); return false;">{_p var='merge_threads'}</a></li>
						{/if}
						<li id="js_subscribe"{if $aThread.is_subscribed} style="display:none;"{/if}><a href="#" onclick="$(this).parent().hide(); $('#js_unsubscribe').show(); $.ajaxCall('forum.subscribe', 'thread_id={$aThread.thread_id}&amp;subscribe=1'); return false;">{_p var='subscribe'}</a></li>
						<li id="js_unsubscribe"{if !$aThread.is_subscribed} style="display:none;"{/if}><a href="#" onclick="$(this).parent().hide(); $('#js_subscribe').show(); $.ajaxCall('forum.subscribe', 'thread_id={$aThread.thread_id}&amp;subscribe=0'); return false;">{_p var='unsubscribe'}</a></li>

						{if $bCanPurchaseSponsor}
						{if Phpfox::getUserParam('forum.can_sponsor_thread')}
<!--                        {* 2 = sponsored *}-->
						<li>
							<span id="js_sponsor_thread_{$aThread.thread_id}" {if $aThread.order_id == 2}style="display:none;"{/if}>
							<a href="#" onclick="$.ajaxCall('forum.sponsor','thread_id={$aThread.thread_id}&type=2');return false;">
								{_p var='sponsor'}
							</a>
							</span>
							    <span id="js_unsponsor_thread_{$aThread.thread_id}" {if $aThread.order_id != 2}style="display:none;"{/if}>
								  <a href="#" onclick="$.ajaxCall('forum.sponsor','thread_id={$aThread.thread_id}&type=0');return false;">
									  {_p var='unsponsor'}
								  </a>
							    </span>
						</li>

						{elseif Phpfox::getUserParam('forum.can_purchase_sponsor') && Forum_Service_Forum::instance()->getSponsorPrice()}
						<li>
							<a href="{permalink module='ad.sponsor' id=$aThread.thread_id}section_forum-thread/">{_p var='sponsor'}</a>
						</li>
						{/if}
						{/if}
					</ul>
				</li>
				{/if}
				{if Phpfox::getParam('forum.enable_rss_on_threads') && Phpfox::isModule('rss')}
				<li class="sub_menu_bar_li">
					<a href="{url link='forum.rss' thread=$aThread.thread_id}" title="{_p var='subscribe_to_this_thread'}" class="no_ajax_link rss_link">
						<i class="fa fa-rss-square"></i>
					</a>
				</li>
				{/if}
			</ul>
			<div class="clear"></div>
		</div>

		{if !empty($aPoll.question)}
		<div class="table_info">
			{_p var='poll'}: {$aPoll.question|clean}
		</div>
		<div class="forum_poll_content">
			{template file='poll.block.entry'}
		</div>
		{/if}

		{/if}
		{/if}

		{if $sPermaView !== null}
		<div class="table_info">
			<div class="go_left">
				{_p var='viewing_single_post'}
			</div>
			<div class="t_right" style="padding-right:5px;">
				{_p var='thread'}: <a href="{permalink module='forum.thread' id=$aThread.thread_id title=$aThread.title}" title="{$aThread.title|clean}">{$aThread.title|clean|shorten:50:'...'}</a>
			</div>
			<div class="clear"></div>
		</div>
		{/if}

		<div class="forum_thread_view_holder">
			<div id="js_thread_start"></div>
			<meta itemprop="dateCreated" content="{$aThread.time_stamp|micro_time}" />
			<meta itemprop="dateModified" content="{$aThread.time_update|micro_time}" />
			<meta itemprop="interactionCount" content="Posts:{$iTotalPosts}" />
			{if isset($aThread.post_starter)}
			<div class="thread_view_holder">
				<section class="thread_starter">
					{plugin call='forum.template_controller_post_1'}
					{template file='forum.block.post'}
					{plugin call='forum.template_controller_post_2'}
				</section>
				<section class="thread_replies">
					{if ($iTotalPosts > Phpfox::getParam('forum.total_posts_per_thread'))}
					<div class="tr_view_all">
						<a href="{permalink module='forum.thread' id=$aThread.thread_id title=$aThread.title view=all}" class="ajax view_all_previous" data-add-class="is-clicked" data-add-spin="true">View All Previous Posts</a>
					</div>
					{/if}
			{/if}
					<div class="tr_content">
						{foreach from=$aThread.posts name=posts item=aPost}
							{plugin call='forum.template_controller_post_1'}
							{template file='forum.block.post'}
							{plugin call='forum.template_controller_post_2'}
						{/foreach}
					</div>

			{if isset($aThread.post_starter)}
					<div id="js_post_new_thread"></div>
					{if !PHPFOX_IS_AJAX && (Phpfox::getUserParam('forum.can_approve_forum_thread') || Phpfox::getUserParam('forum.can_delete_other_posts'))}
					{moderation}
					{/if}
				</section>
			</div>
			{/if}
		</div>
	{/if}