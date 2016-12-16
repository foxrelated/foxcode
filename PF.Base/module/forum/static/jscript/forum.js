
$Core.forum = 
{
	quickReply: function()
	{		
		if (function_exists('__callBackForumAddReply'))
		{
			__callBackForumAddReply();
		}
				
		$('#js_reply_process').html($.ajaxProcess(oTranslations['adding_your_reply']));
		$('#js_quick_reply_form .button').attr('disabled', true).addClass('disabled');
		
		$('#js_quick_reply_form').ajaxCall('forum.addReply');
		
		return false;
	},
	
	goAdvanced: function()
	{
		$('#js_advance_reply_textarea').val(Editor.getContent());
		$('#js_advance_reply_form').submit();
	},
	
	processReply: function(iPostId)
	{		
		tb_remove();
		
		$.scrollTo('#post' + iPostId, 800);
	},
	
	deletePost: function(iPostId)
	{
		$Core.jsConfirm({message: oTranslations['are_you_sure']}, function() {
			$.ajaxCall('forum.deletePost', 'id=' + iPostId);
			$('#post' + iPostId).parent().html('<div class="valid_message" style="margin:0px;">' + oTranslations['post_successfully_deleted'] + '</div>').fadeOut(5000);

			var iCnt = 0;
			$('.js_post_count').each(function()
			{
				iCnt++;
			});
		}, function(){});

		
		return false;
	},
	
	deleteThread: function(iThread)
	{
		$Core.jsConfirm({message: oTranslations['are_you_sure']}, function() {
			$.ajaxCall('forum.deleteThread', 'thread_id=' + iThread);
		}, function(){});

		return false;
	},
	
	stickThread: function(iThread, iType)
	{
		$('.dropContent').hide();
		
		$.ajaxCall('forum.stickThread', 'thread_id=' + iThread + '&type_id=' + iType);
		
		return false;
	},
	
	closeThread: function(iThread, iType)
	{
		$('.dropContent').hide();
		
		$.ajaxCall('forum.closeThread', 'thread_id=' + iThread + '&type_id=' + iType);
		
		return false;
	},
	
	selected: function(oObj, iPostId)
	{	
		if ($(oObj).hasClass('selected'))
		{
			var sCookie = getCookie('forum_quote');
			
			setCookie('forum_quote', sCookie.replace(iPostId + ',', ''));
			if ($('selected').length < 1)
			{
				$('#btnGoAdvanced').val(this.sGoAdvanced);
			}
			$(oObj).removeClass('selected');
		}
		else
		{
			$(oObj).addClass('selected');
			this.sGoAdvanced = $('#btnGoAdvanced').val();
			$('#btnGoAdvanced').val(oTranslations['reply_multi_quoting']);
			setCookie('forum_quote', getCookie('forum_quote') + iPostId + ',');
		}		
		
		return false;
	},
	
	processQuotes: function()
	{
		var sValue = getCookie('forum_quote');
		
		if (!empty(sValue))
		{
			var aParts = explode(',', sValue);
			
			for (i in aParts)
			{
				if (empty(aParts[i]))
				{
					continue;
				}				
				
				$('#js_forum_quote_' + aParts[i]).addClass('selected');
			}
		}
	}
}

$Behavior.videoAttachment = function()
{
	if ($('.forum_holder').length) {

	}

	$('#page_forum_index .toggle').click(function() {
		var t = $(this), parent = t.parents('.forum_holder:first');

		// p('Forum: ' + 'forum_toggle_' + parent.data('forum-id'));

		if (parent.hasClass('is_toggled')) {
			deleteCookie('forum_toggle_' + parent.data('forum-id'));
			parent.removeClass('is_toggled'); // .find('.content').show();

			return;
		}

		parent.addClass('is_toggled'); // .find('.content').hide();
		setCookie('forum_toggle_' + parent.data('forum-id'), 1);
	});

	var oVideoAttachments = $('span[id^=js_attachment_id_]');
	$.each( oVideoAttachments, function( i, selector )
	{
		sId = $(selector).attr('id');
		rId = /[0-9]+/;
		iId = rId.exec(sId).toString();
		$('#' + sId + ' a').attr('onClick', "$.ajaxCall('attachment.playVideo', 'attachment_id=" + iId + "', 'GET'); return false;");
	});
};
