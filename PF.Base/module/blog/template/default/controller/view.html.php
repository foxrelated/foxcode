<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Blog
 * @version 		$Id: view.html.php 5844 2013-05-09 08:00:59Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="item_view">
	<div class="item_info">
		<span>{$aItem.time_stamp|convert_time:'blog.blog_time_stamp'}</span>
		<span>{_p var='by_user' full_name=$aItem|user:'':'':50:'':'author'}</span>
	</div>
	
	{if $aItem.is_approved != 1}
	<div class="message js_moderation_off" id="js_approve_message">
		{_p var='this_blog_is_pending_an_admins_approval'}
	</div>
	{/if}	
	
	{if Phpfox::getUserParam('blog.can_approve_blogs')
		|| (Phpfox::getUserParam('blog.edit_own_blog') && Phpfox::getUserId() == $aItem.user_id) || Phpfox::getUserParam('blog.edit_user_blog')
		|| (Phpfox::getUserParam('blog.delete_own_blog') && Phpfox::getUserId() == $aItem.user_id) || Phpfox::getUserParam('blog.delete_user_blog')
	}	
	<div class="item_bar">
		<div class="item_bar_action_holder">
			{if $aItem.is_approved != 1 && Phpfox::getUserParam('blog.can_approve_blogs')}
				<a href="#" class="item_bar_action approve btn-primary" onclick="$(this).hide(); $('#js_item_bar_approve_image').show(); $.ajaxCall('blog.approve', 'inline=true&amp;id={$aItem.blog_id}'); return false;" title="{_p var='approve'}"></a>
			{/if}		
			<a role="button" data-toggle="dropdown" class="item_bar_action"><span>{_p var='actions'}</span></a>
			<ul class="dropdown-menu dropdown-menu-right">
				{template file='blog.block.link'}
			</ul>
		</div>		
	</div>
	{/if}
	
	{template file='blog.block.entry'}
	{if (isset($sCategories) && $sCategories)}
	<div class="blog-category">
		{_p var="Posted in"}: {$sCategories}
	</div>
	{/if}
	{plugin call='blog.template_controller_view_end'}
	<div {if $aItem.is_approved != 1}style="display:none;" class="js_moderation_on"{/if}>
		{module name='feed.comment'}
	</div>
</div>