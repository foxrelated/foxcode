<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<article class="music_row" id="js_controller_music_track_{$aSong.song_id}" xmlns="http://www.w3.org/1999/html">
	<div class="music_row_content moderation_row">
		<div class="music_left">
			{if !isset($aSong.is_in_feed)}
			<div class="music_row_image width_60">{img user=$aSong suffix='_120_square'}</div>
			{/if}
			<header>
				<h1><a classs="title" href="{permalink title=$aSong.title id=$aSong.song_id module='music'}">{$aSong.title|clean}</a></h1>
				{if !isset($aSong.is_in_feed)}
				<div class="music_info txt-time-color">
					<span class="time_info">{$aSong.time_stamp|convert_time}</span>
					<span class="user_info">{_p var='by'} {$aSong|user}</span>
				</div>
				{/if}
			</header>
		</div>
		<div class="music_right pull-right">
			<div class="statistic txt-time-color">
				{if $aSong.total_play != 1}
				{_p var='music_total_plays' total=$aSong.total_play|number_format}
				{else}
				{_p var='music_total_play' total=$aSong.total_play|number_format}
				{/if}
			</div>
			<div class="play_button">
				<a href="javascript:void(0)" onclick="$Core.music_playSong(this)"><i class="fa fa-play" aria-hidden="true"></i></a>
			</div>
		</div>
		{if Phpfox::getUserParam('music.can_approve_songs')
		|| (($aSong.user_id == Phpfox::getUserId() && Phpfox::getUserParam('music.can_edit_own_song')) || Phpfox::getUserParam('music.can_edit_other_song'))
		|| ($aSong.user_id == Phpfox::getUserId() && Phpfox::getUserParam('music.can_delete_own_track')) || Phpfox::getUserParam('music.can_delete_other_tracks')
		|| (Phpfox::getUserParam('music.can_purchase_sponsor_song') && $aSong.user_id == Phpfox::getUserId())
		}
		<div class="row_edit_bar_parent">
			<div class="row_edit_bar">
				<a role="button" class="row_edit_bar_action" data-toggle="dropdown">
					<i class="fa fa-action"></i>
				</a>
				<ul class="dropdown-menu dropdown-menu-left">
					{template file='music.block.menu'}
				</ul>
			</div>
		</div>
		{/if}

		{if !isset($aSong.is_in_feed) && ($aSong.user_id == Phpfox::getUserId() || Phpfox::getUserParam('music.can_approve_songs') || Phpfox::getUserParam('music.can_delete_other_tracks') || Phpfox::getUserParam('music.can_feature_songs'))}
		<a href="#{$aSong.song_id}" class="moderate_link" rel="music" {if Phpfox::getUserParam('music.can_approve_songs') || Phpfox::getUserParam('music.can_delete_other_tracks') || Phpfox::getUserParam('music.can_feature_songs')}data-id="mod"{else}data-id="user"{/if}></a>
		{/if}
	</div>
	<div class="music_player">
		<div class="audio_player" data-src="{$aSong.song_path}" data-onplay="{url link='music.view' play=$aSong.song_id}"></div>
	</div>
</article>