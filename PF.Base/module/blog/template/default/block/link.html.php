<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Blog
 * @version 		$Id: entry.html.php 2232 2010-12-03 21:04:43Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if (Phpfox::getUserParam('blog.edit_own_blog') && Phpfox::getUserId() == $aItem.user_id) || Phpfox::getUserParam('blog.edit_user_blog')}
	<li><a href="{url link="blog.add" id=""$aItem.blog_id""}">{_p var='edit'}</a></li>
{/if}

{if Phpfox::isModule('ad') && Phpfox::isModule('feed') && (Phpfox::getUserParam('feed.can_purchase_sponsor') || Phpfox::getUserParam('feed.can_sponsor_feed')) && Phpfox::getUserParam('feed.feed_sponsor_price') && ($iSponsorId = Feed_Service_Feed::instance()->canSponsoredInFeed('blog', $aItem.blog_id))}
<li>
	{if $iSponsorId === true}
	<a href="{url link='ad.sponsor' where='feed' section='blog' item=$aItem.blog_id}">
		{_p var='sponsor_in_feed'}
	</a>
	{else}
	<a href="#" onclick="$.ajaxCall('ad.removeSponsor', 'type_id=blog&item_id={$aItem.blog_id}', 'GET'); return false;">
		{_p var="Unsponsor In Feed"}
	</a>
	{/if}
</li>
{/if}

{if (Phpfox::getUserParam('blog.delete_own_blog') && Phpfox::getUserId() == $aItem.user_id) || Phpfox::getUserParam('blog.delete_user_blog')}
	<li class="item_delete"><a href="{url link="blog.delete" id=""$aItem.blog_id""}" class="no_ajax_link sJsConfirm" data-message="{_p var='are_you_sure_you_want_to_delete_this_blog' phpfox_squote=true}" phpfox_squote=true}');" title="{_p var='delete_blog'}">{_p var='delete'}</a></li>
{/if}
{plugin call='blog.template_block_entry_links_main'}