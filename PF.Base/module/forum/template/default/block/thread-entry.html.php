<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Forum
 * @version 		$Id: thread-entry.html.php 5844 2013-05-09 08:00:59Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{item name='CreativeWork'}
	<meta itemprop="dateCreated" content="{$aThread.time_stamp|micro_time}" />
	 <meta itemprop="interactionCount" content="Replies:{$aThread.total_post|number_format}" />
	 <meta itemprop="interactionCount" content="Views:{$aThread.total_view|number_format}" />
	<div class="forum_row js_selector_class_{$aThread.thread_id} checkRow table_row{if $aThread.is_announcement} forum_announcement{/if}{if $aThread.order_id == 1} forum_sticky {elseif $aThread.order_id == 2 && !defined('PHPFOX_IS_GROUP_VIEW')} forum_sponsor {/if}{if $aThread.view_id} row_moderate{/if}">
		<div class="forum_image">
			<div class="forum_image_holder">
				<div class="forum_mini_{$aThread.css_class} js_hover_title"><div class="js_hover_info">{$aThread.css_class_phrase}</div></div>
				{img user=$aThread suffix='_50_square'}			
			</div>		
			{if Phpfox::getUserParam('forum.can_approve_forum_thread') || Phpfox::getUserParam('forum.can_delete_other_posts')}<a href="#{$aThread.thread_id}" class="moderate_link" rel="forum" data-id="mod">{_p var='moderate'}</a>{/if}
		</div>
		<div class="forum_title">
			<div class="forum_title_inner_holder">
				<header>
					{if isset($aThread.cache_name) && $aThread.cache_name}
						<span class="user_profile_link_span"><a href="#">{$aThread.cache_name|clean}</a></span>
					{else}
						{$aThread|user}
					{/if}
					<h1 itemprop="name">
						<a href="{permalink module='forum.thread' id=$aThread.thread_id title=$aThread.title}" class="forum_thread_link{if $aThread.css_class == 'new'} forum_thread_link_new{/if}" itemprop="url">
							{$aThread.title|clean|split:40|shorten:100:'...'}
						</a>
					</h1>

					<div class="forum_user_details">
						<time>{$aThread.time_stamp|convert_time:'forum.forum_user_time_stamp'}</time>
					</div>
					<div class="forum_user_details thread_update_time hidden">
						<time>{_p var="Last updated"}: {$aThread.time_update|convert_time:'forum.forum_user_time_stamp'}</time>
					</div>

				</header>
			</div>
		</div>

		<div class="forum_totals">
			<span>{$aThread.total_post|number_format}<em>{_p var='replies'}</em></span>
		</div>

	</div>
{/item}