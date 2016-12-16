
$Core.forum = 
{
	aParams: {},	
	
	init: function(aParams)
	{
		this.aParams = aParams;
	},
	
	action: function(oObj, sAction)
	{
		aParams = $.getParams(oObj.href);
		
		this.aParams['id'] = aParams['id'];
		
		$('.dropContent').hide();
		
		switch (sAction)
		{
			case 'permission':
				window.location.href = this.aParams['url'] + 'permission/id_' + aParams['id'] + '/';
				break;			
			case 'moderator':
				$('#js_form_actual_content').hide();
				$('#js_forum_edit_content').show();
				$('#js_forum_edit_content').html($.ajaxProcess(oTranslations['loading'], 'large'));
			//	window.location.href = '#moderator/';
				$.ajaxCall('forum.getModerators', 'id=' + aParams['id']);				
				break;
			case 'delete':
				var url = this.aParams['url'];
				$Core.jsConfirm({message: oTranslations['are_you_sure_notice_this_will_delete_all_child_forums_and_any_threads_posts_announcements']}, function(){
					window.location.href = url + 'add/delete_' + aParams['id'] + '/';
				}, function(){});

				break;
			case 'view':
				window.location.href = this.aParams['url'] + 'view_' + aParams['id'] + '/';
				break;
			case 'edit':
				window.location.href = this.aParams['url'] + 'add/id_' + aParams['id'] + '/';
				break;
			case 'add':
				window.location.href = this.aParams['url'] + 'add/child_' + aParams['id'] + '/';
				break;
			default:
			
				break;
		}
		
		return false;
	},
	
	getParam: function(sParam)
	{
		return this.aParams[sParam];
	},
	
	cancel: function()
	{
		$('.js_cached_user_name').removeClass('row_focus');
		$('#js_actual_user_id').val('');
		$('#js_perm_title').html(oTranslations['global_moderator_permissions']);
		
		return false;
	},
	
	build: function(aParams)
	{
		$('.js_radio_true').attr('checked', false);
		$('.js_radio_false').attr('checked', true);
		
		for (sVar in aParams)
		{
			$('#js_true_' + sVar).attr('checked', true);
		}
	}
}

function plugin_userLinkClick(oObj)
{
	if ($(oObj).parents('.js_cached_user_name:first').hasClass('row1'))
	{		
		var iUserId = $(oObj).parents('.js_cached_user_name:first').get(0).id.replace('js_user_id_', '');
		
		$('.js_cached_user_name').removeClass('row_focus');
		$(oObj).parents('.js_cached_user_name:first').addClass('row_focus');		
		$.ajaxCall('forum.getModerator', 'user_id=' + iUserId + '&forum_id=' + $Core.forum.getParam('id'));
		$('#js_actual_user_id').val(iUserId);
		$('#js_perm_title').html(oTranslations['moderator_permissions'] + ': ' + $(oObj).html() + ' - <a href="#" onclick="return $Core.forum.cancel();">' + oTranslations['cancel'] + '</a>');
	}
	else
	{
		window.location.href = oObj.href;
	}
	
	return false;
}

$Behavior.forum_admin = function()
{
	$('.js_drop_down').click(function()
	{
		eleOffset = $(this).offset();
		aParams = $.getParams(this.href);
		$('#js_cache_menu').remove();

		$('body').prepend('<div id="js_cache_menu" style="position:absolute; left:' + eleOffset.left + 'px; top:' + (eleOffset.top + 15) + 'px; z-index:100; background:red;">' + $('#js_menu_drop_down').html() + '</div>');

		$('#js_cache_menu .link_menu li a').each(function()
		{
			this.href = '#?id=' + aParams['id'];
		});

		$('.dropContent').show();

		$('.dropContent').hover(function()
				{

				},
				function()
				{
					$('.dropContent').hide();
					$('.sJsDropMenu').removeClass('is_already_open');
				});

		return false;
	});

	$('.sortable ul').sortable({
			axis: 'y',
			update: function(element, ui)
			{
				var iCnt = 0;		
				$('.sortable li input').each(function()
				{
					iCnt++;			
					$(this).val(iCnt);			
				});				
			},
			opacity: 0.4
		}
	);	
};