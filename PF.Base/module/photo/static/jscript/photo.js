
function deleteNewPhoto(iId)
{
	$Core.jsConfirm({}, function(){
		$('#js_photo_' + iId).remove();
		$('#js_uploaded_photo_' + iId).remove();

		iCnt = 0;
		$('.js_uploaded_image').each(function()
		{
			iCnt++;
		});

		if (!iCnt)
		{
			$('#js_uploaded_images').hide();
		}

		$.ajaxCall('photo.deleteNewPhoto', 'id=' + iId);
	}, function(){});
	
	return false;
}

function plugin_addFriendToSelectList()
{
	$('#js_allow_list_input').show();
}

function plugin_cancelFriendSelection()
{
	$('#js_allow_list_input').hide();
}

$Behavior.photoCategoryDropDownBuild = function()
{
	if ($('.js_photo_active_items').length > 0)
	{
		$('.js_photo_active_items').each(function()
		{
			var aParts = explode(',', $(this).html());
			for (i in aParts)
			{			
				if (empty(aParts[i]))
				{
					continue;
				}		
				
				$(this).parents('.js_category_list_holder:first').find('.js_photo_category_' + aParts[i] + ':first').attr('selected', true);
			}
		});
	}	
};

$Behavior.photoCategoryDropDown = function()
{
	$('.js_photo_category').click(function()
	{
		iId = this.id.replace('js_photo_category_', '');
		iItemId = $(this).parents('div:first').parent().parent().parent().find('.js_photo_item_id').html();
						
		if ($(this).hasClass('selected'))
		{
			$(this).removeClass('selected');
			$('#js_photo_category_id_' + (iItemId === null ? '' : iItemId) + iId).remove();		
		}
		else
		{
			$(this).addClass('selected');
			$(this).prepend('<div id="js_photo_category_id_' + (iItemId === null ? '' : iItemId) + iId + '"><input type="hidden" name="val' + (iItemId === null ? '' : '[' + iItemId + ']') + '[category_id][]" value="' + iId + '" /></div>');		
		}		
		
		return false;
	});
	
	$('.js_photo_category_done').click(function()
	{
		$('.select_clone').hide();
		
		return false;			
	});
	
	$('.select_clone_select').click(function()
	{
		$(this).next('.select_clone').toggle();	
		
		return false;	
	});
	
	$(document).click(function()
	{
		$('.select_clone').hide();
	});	
	
	$('.hover_action').each(function()
	{
		$(this).parents('.js_outer_photo_div:first').css('width', this.width + 'px');
	});
	
	$('#js_photo_album_select').change(function()
	{		
		if (empty(this.value))
		{


			$('#js_photo_view_this_album').remove();
		}
		else
		{
			if ($('#js_photo_view_this_album').length > 0)
			{
				$('#js_photo_view_this_album').show();
			}
			else
			{
				$('#js_photo_action').append('<option value="view_album" id="js_photo_view_this_album">' + oTranslations['view_this_album'] + '</option>');
			}
		}		
	});
};

if (typeof $Core.Photo == 'undefined') $Core.Photo = {};

$Core.Photo.setCoverPhoto = function(iPhotoId, iItemId, sModuleId)
{
	$.ajaxCall(sModuleId+'.setCoverPhoto', 'photo_id=' + iPhotoId + '&page_id=' + iItemId);
};

$Core.Photo.slidePhotoStream = function(obj) {
	var stream = $(obj).parent('.photos_stream');
	if (!stream) return false;
	stream = stream.find('div.photos').first();
	var streamWidth = stream.width();
	var screenWidth = window.innerWidth;
	if (streamWidth <= screenWidth) return;
	var streamLeft = stream.outerWidth(true)  - stream.outerWidth();
	if (streamLeft < 0) streamLeft = streamLeft*(-1);
	var id = $(obj).attr('id');
	if (id == 'prev_photos') {
		streamLeft = streamLeft - screenWidth;
		if (streamLeft < 0) streamLeft = 0;
	}
	else {
		streamLeft = streamLeft + screenWidth;
		if (streamLeft > streamWidth)
			return;
	}
	stream.animate({
		marginLeft: '-'+streamLeft
	}, 500);
}