<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: genre.html.php 2217 2010-11-29 12:33:01Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{for $i = 1; $i <= $iGenerCount; $i++}
	<div class="{if PHPFOX_IS_AJAX && !$bIsGlobalEdit}info{else}table{/if} js_custom_groups js_custom_group_genre">
		<div class="{if PHPFOX_IS_AJAX && !$bIsGlobalEdit}info{else}table{/if}_left">
			{_p var='genre_total' total=$i}:
		</div>
		<div class="{if PHPFOX_IS_AJAX && !$bIsGlobalEdit}info{else}table{/if}_right">
			<select class="form-control" name="custom[music_genre][{$i}]">
				<option value="">{_p var='none'}</option>
			{foreach from=$aGenres item=aGenre}
				<option value="{$aGenre.genre_id}">
                {softPhrase var=$aGenre}
                </option>
			{/foreach}
			</select>
		</div>
	</div>
{/for}