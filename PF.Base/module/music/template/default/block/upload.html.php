<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: upload.html.php 4328 2012-06-25 13:49:41Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="js_music_form_holder">
	{if !$bIsEdit}
	<div class="valid_message" id="js_music_upload_valid_message" style="display:none;">
		{_p var='successfully_uploaded_the_mp3'}
	</div>	
			
	<div class="main_break"></div>
	{/if}
	{if isset($sModule) && $sModule}
	
	{else}
	<div id="js_custom_privacy_input_holder">
	{if $bIsEdit && Phpfox::isModule('privacy')}
		{if isset($bIsEditAlbum)}
		{module name='privacy.build' privacy_item_id=$aForms.album_id privacy_module_id='music_album'}
		{else}
		{module name='privacy.build' privacy_item_id=$aForms.song_id privacy_module_id='music_song'}
		{/if}
	{/if}
	</div>	
	{/if}
	{if isset($bIsEditAlbum) && $bIsEdit}
	<div><input type="hidden" name="val[inline]" value="1" /></div>
	<div><input type="hidden" name="val[album_id]" value="{$aForms.album_id}" /></div>
	{/if}
	
	<div class="table song_name">
		<div class="table_left">
			{required}{_p var='song_name'}:
		</div>
		<div class="table_right">
			<input class="form-control close_warning" type="text" name="val[title]" value="{value type='input' id='title'}" size="30" id="title" />
		</div>
	</div>

	<div class="_form_extra">

		{if !isset($bIsEditAlbum)}
		<div class="table form-group" style="display:none;">
			<div class="table_left">
				{if isset($aUploadAlbums) && count($aUploadAlbums)}
				{_p var='album'}:
				{else}
				{_p var='album_name'}:
				{/if}
			</div>
			<div class="table_right">
				{if isset($aUploadAlbums) && count($aUploadAlbums)}
				<select class="form-control" name="val[album_id]" id="music_album_id_select" onchange="if (empty(this.value)) {l} $('#js_song_privacy_holder').slideDown(); {r} else {l} $('#js_song_privacy_holder').slideUp(); {r}">
					<option value="">{_p var='select'}:</option>
					{foreach from=$aUploadAlbums item=aAlbum}
					<option value="{$aAlbum.album_id}"{value type='select' id='album_id' default=$aAlbum.album_id}>{$aAlbum.name|clean}</option>
					{/foreach}
				</select>
				<div class="extra_info_link"><a href="#" onclick="$('#js_create_new_music_album').show(); $('#js_create_new_music_album input').focus(); return false;">{_p var='or_create_a_new_album'}</a></div>
				<div id="js_create_new_music_album" class="p_top_8" style="display:none;">
					<input class="form-control" type="text" name="val[new_album_title]" value="{value type='input' id='new_album_title'}" size="30" />
				</div>
				{else}
				<input class="form-control" type="text" name="val[new_album_title]" value="{value type='input' id='new_album_title'}" size="30" /> <span class="extra_info">{_p var='optional'}</span>
				{/if}
			</div>
		</div>
		{/if}
	
		<div class="table song_name">
			<div class="table_left">
				{_p var='genre'}:
			</div>
			<div class="table_right">
				<select class="form-control" name="val[genre_id]">
					<option value="">{_p var='select'}:</option>
				{foreach from=$aGenres item=aGenre}
					<option value="{$aGenre.genre_id}"{value type='select' id='genre_id' default=$aGenre.genre_id}>
                        {softPhrase var=$aGenre.name}
                    </option>
				{/foreach}
				</select>
			</div>
		</div>

		{if isset($sModule) && $sModule}

		{else}
		{if $bIsEdit && $aForms.album_id > 0}

		{else}
		{if !isset($bIsEditAlbum) && Phpfox::isModule('privacy')}
		<div id="js_song_privacy_holder">
			<div class="table form-group-follow">
				<div class="table_left">
					{_p var='privacy'}:
				</div>
				<div class="table_right">
					{module name='privacy.form' privacy_name='privacy' privacy_info='music.control_who_can_see_this_song' default_privacy='music.default_privacy_setting'}
				</div>
			</div>

			<div class="table form-group-follow" style="display:none;">
				<div class="table_left">
					{_p var='comment_privacy'}:
				</div>
				<div class="table_right">
					{module name='privacy.form' privacy_name='privacy_comment' privacy_info='music.control_who_can_comment_on_this_song' privacy_no_custom=true}
				</div>
			</div>
		</div>
		{/if}
		{/if}
		{/if}
	</div>
	
	{if !isset($bIsEditAlbum) && $bIsEdit}
	
	{else}	
	{if isset($sMethod) && $sMethod == 'massuploader'}
	<div class="table mass_uploader_table">
		<div id="swf_music_upload_button_holder">
			<div class="swf_upload_holder">
				<div id="swf_music_upload_button"></div>
			</div>
			
			<div class="swf_upload_text_holder">
				<div class="swf_upload_progress"></div>
				<div class="swf_upload_text">
					{_p var='select_mp3'}
				</div>
			</div>				
		</div>
		<div class="extra_info">
			{_p var='max_file_size'}: {$iUploadLimit}
		</div>			
	</div>
	<div class="mass_uploader_link">{_p var='upload_problems_try_the_basic_uploader' url=$sMethodUrl}</div>
	{else}	
	<div><input type="hidden" name="val[method]" value="simple" /></div>
	<div class="table form-group">
		<div class="table_left">
			{required}{_p var='select_mp3'}:
		</div>
		<div class="table_right">		
			<div id="js_progress_uploader"></div>
			<div class="extra_info">
				{_p var='max_file_size'}: {$iUploadLimit}
			</div>		
		</div>
	</div>	
	<div class="table_clear">
		<input type="submit" value="{_p var='upload'}" class="button btn-primary" />
	</div>
	{/if}
	{/if}
</div>