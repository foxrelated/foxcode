<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: track.html.php 6307 2013-07-19 06:29:55Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div id="js_block_track_player"></div>
<ul class="block_listing block_listing_hover" id="js_store_album_track">
	{foreach from=$aTracks name=songs item=aSong}
	{template file='music.block.track-entry'}
	{/foreach}
</ul>
{if !count($aTracks)}
<div id="js_track_song_no_tracks" class="extra_info">
	{_p var='no_tracks_have_been_added'}
</div>
{/if}