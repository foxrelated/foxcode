
function photoLoaderImage()
{
	$('#site_content').html($.ajaxProcess(oTranslations['loading'], 'large'));
}

$Behavior.photoCategoryDropDown = function()
{
	if (!empty($('.js_photo_active_items').html()))
	{
		var aParts = explode(',', $('.js_photo_active_items').html());
		for (i in aParts)
		{			
			if (empty(aParts[i]))
			{
				continue;
			}		
			
			$('#js_photo_category_' + aParts[i]).attr('selected', true);
		}
	}
};