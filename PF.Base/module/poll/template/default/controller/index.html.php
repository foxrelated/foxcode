<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Poll
 * @version 		$Id: index.html.php 3342 2011-10-21 12:59:32Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !count($aPolls)}
{if !PHPFOX_IS_AJAX}
<div class="extra_info">
	{_p var='no_polls_found'}
</div>
{/if}
{else}
	{if !PHPFOX_IS_AJAX}<div class="collection-stage">{/if}
	{foreach from=$aPolls item=aPoll key=iKey name=polls}
		<div class="collection-item-stage">
			<div id="js_poll_id_{$aPoll.poll_id}" class="poll_large_item image_load moderation_row">
				<header>
					<h1 itemprop="name"><a href="{permalink module='poll' id=$aPoll.poll_id title=$aPoll.question}" class="link" itemprop="url">{$aPoll.question|clean}</a></h1>
					<div>
						<span>{$aPoll.time_stamp|convert_time:'poll.poll_view_time_stamp'}</span>
						<span>&middot;</span>
						<span>by {$aPoll|user}</span>
					</div>
				</header>
				<div class="poll_large_image" {if $aPoll.image_path}style="background-image: url('{img server_id=$aPoll.server_id path='poll.url_image' file=$aPoll.image_path suffix='' return_url=true}')"{/if})>
			</div>
			{if (isset($aPoll.view_id) && $aPoll.view_id == 1 && Phpfox::getUserParam('poll.poll_can_moderate_polls'))
			|| $aPoll.bCanEdit
			|| (!isset($bIsCustomPoll) && (isset($aPoll.user_id) && $aPoll.user_id == Phpfox::getUserId() && Phpfox::getUserParam('poll.poll_can_delete_own_polls')))
			}
			<div class="row_edit_bar_parent">
				<div class="row_edit_bar">
					<a role="button" class="row_edit_bar_action" data-toggle="dropdown">
						<i class="fa fa-action"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-right">
						{template file='poll.block.link'}
					</ul>
				</div>
			</div>
			{/if}

			{if !PHPFOX_IS_AJAX && (Phpfox::getUserParam('poll.poll_can_moderate_polls') || (isset($aPoll.user_id) && $aPoll.user_id == Phpfox::getUserId()))}
			<a href="#{$aPoll.poll_id}" class="moderate_link" {if Phpfox::getUserParam('poll.poll_can_moderate_polls')}data-id="mod"{else}data-id="user"{/if} rel="poll"></a>
			{/if}
		</div>
		</div>
	{/foreach}

	{pager}
	{if !PHPFOX_IS_AJAX && Phpfox::getUserParam('poll.poll_can_moderate_polls')}
	{moderation}
	{/if}
	{if !PHPFOX_IS_AJAX}</div>{/if}
{/if}