<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: album.html.php 3990 2012-03-09 15:28:08Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aAlbums)}
{foreach from=$aAlbums name=albums item=aAlbum}
	{template file='music.block.album-rows'}
{/foreach}
{if Phpfox::getUserParam('music.can_delete_other_music_albums') || Phpfox::getUserParam('music.can_feature_music_albums')}
	{moderation}
{/if}
{pager}
{else}
{if ! PHPFOX_IS_AJAX }
<div class="extra_info">
	{_p var='no_albums_found'}
</div>
{/if}
{/if}
