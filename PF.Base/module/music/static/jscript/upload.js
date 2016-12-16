
$Core.music =
{
	upload: function(bForm)
	{
		if (bForm)
		{			

			$('#js_music_upload_song').parent().prepend('<div class="t_center js_upload_song" style="height:' + $('#js_music_upload_song').height() + 'px;">' + $.ajaxProcess('Uploading', 'large') + '</div>');
			$('#js_music_upload_song').hide();
			
			return true;
		}
		
		return false;
	},
	
	setName: function(iSong)
	{
		if ($("#title").val() != '')
		{
			$.ajaxCall('music.setName', 'sTitle='+$("#title").val() + '&iSong='+ iSong);
		}
	},
	
	/* Called from the iFrame response when the Simple Uploader fails due to the validator*/
	resetUploadForm: function(sError)
	{
		$('#js_progress_cache_holder').hide();
		$('#js_music_form_holder').show();
		if (typeof sError != 'undefined' && sError.length > 0)
		{
			$('#js_album_form').before('<div class="error_message">' + sError + ' </div>');
		}
	}
}