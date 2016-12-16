<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 1544 2010-04-07 13:20:17Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="table_header">
	{_p var='feeds'}
</div>
<table id="js_drag_drop" cellpadding="0" cellspacing="0">
	<tr>
		<th></th>
		<th style="width:20px;"></th>
		<th>{_p var='title'}</th>
		<th class="t_center">{_p var='subscribers'}</th>
		<th class="t_center" style="width:80px;">{_p var='site_wide'}</th>
		<th class="t_center" style="width:60px;">{_p var='active'}</th>
	</tr>
	{foreach from=$aFeeds key=iKey item=aFeed}
	<tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
		<td class="drag_handle"><input type="hidden" name="val[ordering][{$aFeed.group_id}]" value="{$aFeed.ordering}" /></td>
		<td class="t_center">
			<a href="#" class="js_drop_down_link" title="Manage">{img theme='misc/bullet_arrow_down.png' alt=''}</a>
			<div class="link_menu">
				<ul>
					<li><a href="{url link='admincp.rss.add' id=$aFeed.feed_id}">{_p var='edit_feed'}</a></li>
					<li><a href="{url link='admincp.rss' delete=$aFeed.feed_id}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete_feed'}</a></li>
				</ul>
			</div>		
		</td>	
		<td>{_p var=$aFeed.title_var}</td>
		<td class="t_center">
		{if $aFeed.total_subscribed > 0}<a href="{url link='admincp.rss.log' id=$aFeed.feed_id}">{/if}{$aFeed.total_subscribed}{if $aFeed.total_subscribed > 0}</a>{/if}
		</td>
		<td class="t_center">
			<div class="js_item_is_active"{if !$aFeed.is_site_wide} style="display:none;"{/if}>
				<a href="#?call=rss.updateSiteWide&amp;id={$aFeed.feed_id}&amp;active=0" class="js_item_active_link" title="{_p var='disable'}">{img theme='misc/bullet_green.png' alt=''}</a>
			</div>
			<div class="js_item_is_not_active"{if $aFeed.is_site_wide} style="display:none;"{/if}>
				<a href="#?call=rss.updateSiteWide&amp;id={$aFeed.feed_id}&amp;active=1" class="js_item_active_link" title="{_p var='enable'}">{img theme='misc/bullet_red.png' alt=''}</a>
			</div>		
		</td>
		<td class="t_center">
			<div class="js_item_is_active"{if !$aFeed.is_active} style="display:none;"{/if}>
				<a href="#?call=rss.updateFeedActivity&amp;id={$aFeed.feed_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}">{img theme='misc/bullet_green.png' alt=''}</a>
			</div>
			<div class="js_item_is_not_active"{if $aFeed.is_active} style="display:none;"{/if}>
				<a href="#?call=rss.updateFeedActivity&amp;id={$aFeed.feed_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}">{img theme='misc/bullet_red.png' alt=''}</a>
			</div>		
		</td>
	</tr>
	{/foreach}
</table>