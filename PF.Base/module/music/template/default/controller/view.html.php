<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: view.html.php 3990 2012-03-09 15:28:08Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="item_view">
    <div class="item_info">
		<span>{$aSong.time_stamp|convert_time}</span>
		<span>{_p var='by_lowercase'} {$aSong|user:'':'':50}</span>
		{if $aSong.album_id}<span>{_p var='in'} <a href="{permalink module='music.album' id=$aSong.album_id title=$aSong.album_url}" title="{$aSong.album_url|clean}">{$aSong.album_url|clean|shorten:50:'...'|split:50}</a></span>{/if}
    </div>
    
	{if $aSong.view_id != 0}
	<div class="message js_moderation_off" id="js_approve_message">
		{_p var='song_is_pending_approval'}
	</div>
	{/if}    

	{if Phpfox::getUserParam('music.can_approve_songs')
		|| (($aSong.user_id == Phpfox::getUserId() && Phpfox::getUserParam('music.can_edit_own_song')) || Phpfox::getUserParam('music.can_edit_other_song'))
		|| ($aSong.user_id == Phpfox::getUserId() && Phpfox::getUserParam('music.can_delete_own_track')) || Phpfox::getUserParam('music.can_delete_other_tracks')
		|| (Phpfox::getUserParam('music.can_purchase_sponsor_song') && $aSong.user_id == Phpfox::getUserId())
	}
	<div class="item_bar">
		<div class="item_bar_action_holder">
			{if $aSong.view_id != 0 && Phpfox::getUserParam('music.can_approve_songs')}
				<a href="#" class="item_bar_approve item_bar_approve_image" onclick="return false;" style="display:none;" id="js_item_bar_approve_image">{img theme='ajax/add.gif'}</a>			
				<a href="#" class="item_bar_approve" onclick="$(this).hide(); $('#js_item_bar_approve_image').show(); $.ajaxCall('music.approveSong', 'inline=true&amp;id={$aSong.song_id}', 'GET'); return false;">{_p var='approve'}</a>
			{/if}		
			<a role="button" data-toggle="dropdown" class="item_bar_action"><span>{_p var='actions'}</span></a>
			<ul class="dropdown-menu dropdown-menu-right">
				{template file='music.block.menu'}
			</ul>			
		</div>		
	</div>       
	{/if}

	<div class="item_content">
		<div id="js_music_player"></div>
		<div class="music_view_total_play">
			{_p var='total_plays' total=$aSong.total_play|number_format}
		</div>
	</div>
	<div {if $aSong.view_id != 0}style="display:none;" class="js_moderation_on"{/if}>
		{module name='feed.comment'}
	</div>
</div>