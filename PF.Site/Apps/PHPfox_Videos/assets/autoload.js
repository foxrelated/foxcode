
var videoCheck = function(url) {
	setTimeout(function() {
		$.ajax({
			url: url,
			data: 'check=1',
			success: function (obj) {
				eval(obj.run);
			}
		});
	}, 2000);
};

var videoUpload = function(e) {
	// $('body').prepend('<div id="pf-video-process-main"><i class="fa fa-spin fa-circle-o-notch"></i></div>');
	$('#activity_feed_submit').addClass('button_not_active').attr('disabled', true).val('Uploading...');
	$('.pf_v_video_url').hide();
	$('#pf_video_id_temp').remove();
	$('.pf_v_video_url').prepend('<div><input id="pf_video_id_temp" type="hidden" name="pf_video_id" value=""></div>');

	if (!$('.pf_is_popup').length) {
		$('.pf_upload_form').hide();
	}
	else {
		$('.pf_select_video').hide();
		$('.pf_v_video_info').hide();
		$('.pf_v_video_info > .table').show();
	}
	$('.pf_process_form').show();
	$('.pf_upload_form .error_message').remove();
	$('.activity_feed_form_button_status_info').hide();
	var files = e.target.files || e.dataTransfer.files;
	for (var i = 0, f; f = files[i]; i++) {

		var file = f;
		var data = new FormData();
		data.append('ajax_upload', file);

		$.ajax({
			url: PF.url.make('/v/upload'),
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			xhrFields: {
				onprogress: function (e) {
					if (e.lengthComputable) {
						var percent = (e.loaded / e.total * 100) + '%';
						console.log('upload: ' + percent);
						$('.pf_process_form > span').width(percent);
					}
				}
			},
			headers: {
				'X-File-Name': file.name,
				'X-File-Size': file.size,
				'X-File-Type': file.type,
				'X-Post-Form': $(($('#video_page_upload').length ? '#video_page_upload' : '#js_activity_feed_form')).getForm()
			},
			type: 'POST',
			error: function(error) {
				var e = $.parseJSON(error.responseText);
				$('#pf_video_id_temp').remove();
				$('.pf_select_video').show();
				$('.pf_process_form').hide();
				var f = $('.pf_select_video').parents('form:first');
				f.find('.error_message').remove();
				$('.pf_upload_form').show();
				f.prepend('<div class="error_message">' + e.error + '</div>');
				$('.select-video', '#global_attachment_videos').val('');
				$('.pf_process_form > span').width('2%');
				$('.pf_select_video .extra_info').removeClass('hide_it');
			},
			success: function(data) {
				if (typeof(data.skip) == 'string') {
					return;
				}

				$('.activity_feed_form_button_status_info').show();
				add_video_button();
				$('.select-video', '#global_attachment_videos').val('');
				$('.pf_process_form > span').width('2%');
				$('#pf_video_id_temp').val(data.id);
				$('.pf_video_caption').show();
				$('.pf_video_caption > div.table').show();
				$('.pf_v_video_info').show();
				$('.pf_process_form').hide();
				$('.pf_select_video').hide();
				$('.pf_video_message').show();
				$('#activity_feed_submit').removeClass('button_not_active').attr('disabled', false).val(v_phrases.share).hide();
			}
		});
	}
};

var add_video_button = function() {
	$('.process-video-upload').remove();
	$('#activity_feed_submit').before('<a href="#" class="button btn-lg btn-primary process-video-upload">'+v_phrases.save+'</a>');
	$Core.loadInit();
};

var load_videos = function() {
	if ($('#page_route_v #content-holder').outerWidth() < 600) {
		$('#page_route_v #content > ._block_content').addClass('_video_resize');
	} else {
		$('#page_route_v #content > ._block_content').removeClass('_video_resize');
	}
	$('#page_route_v #content').show();
};

$Event(function() {
	if ($('#page_route_v').length) {
		load_videos();
	}
});

$Ready(function() {
	if (pf_video_enabled != '1') {
		return;
	}

	if ($('#page_route_v').length) {
		load_videos();
	}

	// Upload routine for videos
	var m = $('#page_core_index-member .activity_feed_form_attach, #panel .activity_feed_form_attach'), p = $('#page_pages_view .activity_feed_form_attach'), g = $('#page_groups_view .activity_feed_form_attach'), v = $('.select-video-upload'), b = $('#pf_upload_form_input');
	if (m.length && !v.length) {
		var html = '<li><a href="#" class="select-video-upload" rel="custom">' + v_phrases.video + '</a></li>';

		m.append(html);
		// $('.select-video')[0].addEventListener("change", videoUpload);
	}

	if (p.length && !v.length && can_post_video_on_page == 1) {
		var html = '<li><a href="#" class="select-video-upload" rel="custom">' + v_phrases.video + '</a></li>';
		p.append(html);
	}

	if (g.length && !v.length && can_post_video_on_group == 1) {
		var html = '<li><a href="#" class="select-video-upload" rel="custom">' + v_phrases.video + '</a></li>';
		g.append(html);
	}

	$('.activity_feed_form_attach a:not(.select-video-upload)').click(function() {
		$('.process-video-upload').remove();
		$('.activity_feed_form .error_message').remove();
	});

	$('.select-video-upload').click(function() {

		$('.activity_feed_form_attach a.active').removeClass('active');
		$(this).addClass('active');
		$('.global_attachment_holder_section').hide().removeClass('active');
		$('.activity_feed_form_button').show();
		$('#activity_feed_submit').hide();

		$('#activity_feed_textarea_status_info').attr('placeholder', $('<div />').html(v_phrases.say).text());

		var l = $('#global_attachment_videos');
		if (l.length == 0) {
			var m = $('<div id="global_attachment_videos" class="global_attachment_holder_section" style="display:block;"><div style="text-align:center;"><i class="fa fa-spin fa-circle-o-notch"></i></div></div>');
			$('.activity_feed_form_holder').prepend(m);
			// $('#js_activity_feed_form').attr('action', PF.url.make('/v/url')).addClass('ajax_post');
			// $sFormAjaxRequest = PF.url.make('/v/url');

			$.ajax({
				url: PF.url.make('/v/share'),
				contentType: 'application/json',
				data: 'is_ajax_browsing=1',
				success: function(e) {
					m.html(e.content);
					m.find('._block').remove();

					$('.process-video-upload').remove();
					$Core.loadInit();
				}
			});
		}
		else {
			l.show();
		}

		if($('.pf_select_video').length == 0 || $('.pf_select_video').is(':visible'))
		{
			$('.activity_feed_form_button_status_info').hide();
		}

		return false;
	});

	$('.process-video-upload').click(function() {
		var t = $(this);

		t.hide();
		t.before('<span class="form-spin-it"><i class="fa fa-spin fa-circle-o-notch"></i></span>');

		var f = $(this).parents('form:first');

		t.addClass('in_process');
		f.find('.error_message').remove();
		$.ajax({
			url: PF.url.make('/v/url'),
			type: 'POST',
			data: f.serialize() + '&is_ajax_post=true',
			success: function(e) {
				if (typeof(e.error) == 'string') {
					f.prepend(e.error);
					t.show();
					t.parent().find('.form-spin-it').remove();
					return;
				}
        $('.form-spin-it').remove();
				eval(e.run);
			}
		});

		return false;
	});

	if (b.length && !b.hasClass('built')) {
		b.addClass('built');
		b.prepend('<input id="divFileInput" type="file" class="select-video feed-attach-form-file">');

		$('#divFileInput.select-video')[0].addEventListener("change", videoUpload);
	}

	// Video process form
	var f = $('.pf-video-form')
	if (f.length && !f.data('is_checked')) {
		f.data('is_checked');
		videoCheck(f.data('url'));
	}

	// Set the default image poster
	var f = $('.pf-video-frames');
	if (!f.hasClass('set')) {
		f.addClass('set');
		// if (f.data('default-poster') != '') {
			f.find('img.active').removeClass('active');
			f.find('img[data-frame="' + f.data('default-poster') + '"]').addClass('active');
		// }
	}

	// Manage default icon
	$('.pf-video-frames img').click(function() {
		var t = $(this);

		$('.pf-video-frames img.active').removeClass('active');
		t.addClass('active');
		$.ajax({
			url: PF.url.make('/v/poster'),
			type: 'POST',
			data: 'frame=' + t.data('frame') + '&id=' + t.parents('form:first').find('input[name="id"]').val(),
			success: function(e) {
				console.log(e);
			}
		});
	});

	// Find all our feeds
	// github #1703
	// $('._app_PHPfox_Videos:not(.v_built)').each(function() {
	// 	var t = $(this), d = t.find('.action_delete');
	// 	t.addClass('v_built');
	// 	if (d.length) {
	// 		d.attr('onclick', '');
	// 		d.attr('href', PF.url.make('/v/delete/' + t.data('feed-id')));
	// 	}
	// });

	var url_changed = function() {
		console.log('changed!');
		$('.pf_v_video_info').show();
		$('.pf_v_video_url .extra_info').removeClass('hide_it');
		$('.pf_select_video').slideUp();
		$('#activity_feed_submit').removeClass('button_not_active'); // .attr('disabled', false);
		$('#__form_caption').parent().parent().hide();
		$('.activity_feed_form_button_status_info').show();
		add_video_button();
	};

	$('#__form_url').focus(url_changed);

	$('.pf_v_url_cancel').click(function() {
		$(this).parent().addClass('hide_it');
		$('.pf_select_video').slideDown();
		$('.pf_v_video_url #__form_url').val('');
		var f = $(this).parents('form:first');
		f.find('.error_message').remove();
		$('.process-video-upload').hide();
		$('.activity_feed_form_button_status_info').hide();
		// $('.pf_v_video_info').hide();

		return false;
	});

	$('.pf_v_upload_cancel').click(function() {
		$(this).parent().addClass('hide_it');
		$('.pf_v_video_url').slideDown();
		var f = $(this).parents('form:first');
		f.find('.error_message').remove();
		$('.activity_feed_form_button_status_info').hide();

		return false;
	});

	$('.pf_v_message_cancel').click(function() {
		$(this).parent().addClass('hide_it');
		$('.pf_v_video_url').show();
		$('.pf_select_video').show();
		$('.pf_upload_form').slideDown();
		$('.pf_video_message').hide();
		$('.pf_upload_form .message').remove();
		$('.process-video-upload').remove();
		$('#pf_video_id_temp').remove();
		$('.pf_video_caption input').val('');

		return false;
	});

	$('#js_activity_feed_form, .pf_is_popup').submit(function() {
		// console.log('form submit...');
		if ($('.select-video-upload').hasClass('active')) {
			console.log('form submit...');
			// $(this).find('.btn-primary').attr('disabled', true);
		}
	});
});