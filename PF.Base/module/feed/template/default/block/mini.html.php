<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Feed
 * @version 		$Id: mini.html.php 4545 2012-07-20 10:40:35Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

<div class="feed_share_holder feed_share_{$aParentFeed.type_id}">
	<div class="feed_share_header">
	{if !isset($aParentFeed.no_user_show)}
	{$aParentFeed|user:'':'':50}				
	{/if}
	{if isset($aParentFeed.parent_user)} {img theme='layout/arrow.png' class='v_middle'} {$aParentFeed.parent_user|user:'parent_':'':50} {/if}{if !empty($aParentFeed.feed_info)} {$aParentFeed.feed_info}{/if}				
	
	
			{if !empty($aParentFeed.feed_mini_content)}
			<div class="activity_feed_content_status">
				<div class="activity_feed_content_status_left">
					<img src="{$aParentFeed.feed_icon}" alt="" class="v_middle" /> {$aParentFeed.feed_mini_content} 
				</div>
				<div class="activity_feed_content_status_right">
					{template file='feed.block.link'}
				</div>
				<div class="clear"></div>
			</div>
			{/if}

			{if isset($aParentFeed.feed_status) && (!empty($aParentFeed.feed_status) || $aParentFeed.feed_status == '0')}
			<div class="activity_feed_content_status">
				{$aParentFeed.feed_status|feed_strip|shorten:200:'feed.view_more':true|split:55}				
			</div>
			{/if}
		</div>
    {if isset($aParentFeed.load_block)}
    {module name=$aParentFeed.load_block}
    {else}
			<div class="activity_feed_content_link"{if isset($aParentFeed.no_user_show)} style="margin-top:0px;"{/if}>

				{if $aParentFeed.type_id == 'friend' && isset($aParentFeed.more_feed_rows) && is_array($aParentFeed.more_feed_rows) && count($aParentFeed.more_feed_rows)}
					{foreach from=$aParentFeed.more_feed_rows item=aFriends}
						{$aFriends.feed_image}
					{/foreach}
					{$aParentFeed.feed_image}
				{else}
				{if !empty($aParentFeed.feed_image)}
				<div class="activity_feed_content_image"{if isset($aParentFeed.feed_custom_width)} style="width:{$aParentFeed.feed_custom_width};"{/if}>
					{if is_array($aParentFeed.feed_image)}
					<div class="activity_feed_multiple_image feed-img-stage-{$aParentFeed.total_image}">
						{foreach from=$aParentFeed.feed_image item=sFeedImage name=image}
						<div class="img-{$phpfox.iteration.image}">
							{$sFeedImage}
						</div>
						{/foreach}
					</div>
					<div class="clear"></div>
					{else}
						<a href="{$aParentFeed.feed_link}" target="_blank" class="{if isset($aParentFeed.custom_css)} {$aParentFeed.custom_css} {/if}{if !empty($aParentFeed.feed_image_onclick)}{if !isset($aParentFeed.feed_image_onclick_no_image)}play_link {/if} no_ajax_link{/if}"{if !empty($aParentFeed.feed_image_onclick)} onclick="{$aParentFeed.feed_image_onclick}"{/if}{if !empty($aParentFeed.custom_rel)} rel="{$aParentFeed.custom_rel}"{/if}{if isset($aParentFeed.custom_js)} {$aParentFeed.custom_js} {/if}>{if !empty($aParentFeed.feed_image_onclick)}{if !isset($aParentFeed.feed_image_onclick_no_image)}<span class="play_link_img">{_p var='play'}</span>{/if}{/if}{$aParentFeed.feed_image}</a>
					{/if}
				</div>
				{/if}
				<div class="{if (!empty($aParentFeed.feed_content) || !empty($aParentFeed.feed_custom_html)) && empty($aParentFeed.feed_image)} activity_feed_content_no_image{/if}{if !empty($aParentFeed.feed_image)} activity_feed_content_float{/if}"{if isset($aParentFeed.feed_custom_width)} style="margin-left:{$aParentFeed.feed_custom_width};"{/if}>
					{if !empty($aParentFeed.feed_title)}
					<a href="{$aParentFeed.feed_link}" class="activity_feed_content_link_title"{if isset($aParentFeed.feed_title_extra_link)} target="_blank"{/if}>{$aParentFeed.feed_title|clean|split:30}</a>
					{if !empty($aParentFeed.feed_title_extra)}
					<div class="activity_feed_content_link_title_link">
						<a href="{$aParentFeed.feed_title_extra_link}" target="_blank">{$aParentFeed.feed_title_extra|clean}</a>
					</div>
					{/if}
					{/if}
					{if !empty($aParentFeed.feed_content)}
					<div class="activity_feed_content_display">
						{if strpos($aParentFeed.feed_content, '<br />') >= 200}
						{$aParentFeed.feed_content|feed_strip|shorten:200:'feed.view_more':true|split:55|max_line}
						{else}
						{$aParentFeed.feed_content|feed_strip|split:55|max_line|shorten:200:'feed.view_more':true}
						{/if}
					</div>
					{/if}
					{if !empty($aParentFeed.feed_custom_html)}
					<div class="activity_feed_content_display_custom">
						{$aParentFeed.feed_custom_html}
					</div>
					{/if}

					{if !empty($aParentFeed.app_content)}
					{$aParentFeed.app_content}
					{/if}

				</div>
				{if !empty($aParentFeed.feed_image)}
				<div class="clear"></div>
				{/if}
				{/if}
			</div>
{/if}
			
</div>
			