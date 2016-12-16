
var socket;
var socket_built = false;
var cache_content;
var emoji_support = false;
var thread_cnt = 0;
var thread_total = 0;
var users = '';

// Override main compose function
if (pf_im_enabled == '1') {
	$Core.composeMessage = function (param) {
		$('._pf_im_friend_search').remove();
		$('.pf-im-main').removeClass('_is_friends_list');

		var f = $('.pf-im-menu a[data-type="2"]');
		if (f.hasClass('active')) {
			f.removeClass('active');
			$('.pf-im-menu a[data-type="1"]').addClass('active');
			$('.pf-im-main').html(cache_content);
			cache_content = null;
		}

		$('#pf-im').show();
		$('body').addClass('im-is-active');

		var is_listing = (typeof(param.listing_id) == 'number' ? true : false);
		$.ajax({
			url: PF.url.make('/im/conversation') + '?user_id=' + param.user_id + '&listing_id=' + (is_listing ? param.listing_id : '0'),
			contentType: 'application/json',
			success: function (resp) {
				if (typeof(resp.error) == 'string') {
					imFailed();
					return;
				}

				var e = resp.user;
				var thread_ids = [getUser().id, e.id];
				var thread_id = thread_ids.sort(sortNumber).join(':');

				var m = $('.pf-im-panel[data-thread-id="' + thread_id + '"]');
				// var m2 = $('.pf-im-panel[data-thread-id="' + getUser().id + ':' + e.id + '"]');
				if (!m.length) {
					var html = '<div class="pf-im-panel" data-user-id="' + e.id + '" data-thread-id="' + thread_id + '">' +
						'<div class="pf-im-panel-image">' + e.photo_link + '</div>' +
						'<div class="pf-im-panel-content">' + e.name + '</div>' +
						'</div>';
					$('.pf-im-main').prepend(html);
				}

				$Core.loadInit();

				m = $('.pf-im-panel[data-thread-id="' + thread_id + '"]');
				m.removeClass('active');
				if (is_listing) {
					m.data('listing-id', param.listing_id);
				}
				m.trigger('click');
			}
		});

		return false;
	};
}

var sortNumber = function(a ,b) {
	return a - b;
};

var imFailed = function() {
	$('.chat-row-close').trigger('click');
	var popup = $('<a class="popup" href="' + PF.url.make('/im/failed') + '"></a>');
	tb_show('', PF.url.make('/im/failed'), popup);
	$('#pf-im').hide();
};

var getUser = function() {
	var u = $('#auth-user');

	return {
		id: u.data('id'),
		name: u.data('name'),
		photo_link: u.data('image')
	};
};

var fixChatMessage = function(text) {
	if (text === null) {
		return '';
	}

	var map = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#039;'
	};

	return text.replace(/[&<>"']/g, function(m) { return map[m]; });
};

var build_attachment = function(a) {
	attachment = '<article class="_im_a" data-url="' + a.link + '">';
	attachment += '<div class="_im_a_image"><img src="' + a.default_image + '"></div>';
	attachment += '<div class="_im_a_body"><h1>' + a.title + '</h1><p>' + a.description + '</p></div>';
	attachment += '</article>';

	return attachment;
};

var buildMessage = function(message, do_scroll, force) {
	if (typeof(message) == 'string') {
		message = JSON.parse(message);
	}

	var attachment = '';
	if (typeof(message.attachment_id) == 'object') {
		var a = message.attachment_id;

		attachment = build_attachment(a);
	}

	var icon = '';
	if (message.user.id == getUser().id) {
		icon = '<a href="#" class="pf_chat_delete_message" data-key="' + message.time_stamp + '"><i class="fa fa-trash"></i></a>';
	}
	var html = '<div class="pf-chat-message' + (message.user.id == getUser().id ? ' pf-chat-owner' : '') + '" ' + (force === true ? '' : 'style="display:none;"') + ' data-user-id="' + message.user.id + '">' +
		'<div class="pf-chat-image">' + (force ? message.user.photo_link : '') + '</div>' +
		'<div class="pf-chat-body">' + fixChatMessage(message.text) + attachment + icon + '' +
		'<time class="set-moment" data-time="' + message.time_stamp + '"></time>' +
		'</div>' +
		'</div>';

	if (do_scroll === true) {
		$('.chat-row').scrollTop($('.chat-row')[0].scrollHeight);
	}

	return html;
};

var is_mobile = false;
if( /iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
	is_mobile = true;
}
var chatTextArea = function() {
	$('.chat-form input').unbind().focus(function() {
		if (is_mobile) {
			$('body > *:not(#pf-im):visible').addClass('im_temp_hide').hide();
			$('#pf-im').css({
				'margin-top': '300px'
			});
		}
	}).blur(function() {
		if (is_mobile) {
			$('.im_temp_hide').show().removeClass('im_temp_hide');
			$('#pf-im').css({
				'margin-top': 'auto'
			});
		}
	}).keydown(function(e) {

		if (e.which == 13) {
			var t = $(this), c = $('.chat-row'), timeNow = Math.floor(Date.now() / 1000);
			e.preventDefault();
			if (t.val().length <= 0) {
				return;
			}

			console.log('Submit...');

			$('.chat-form').attr('style', '');

			c.append(buildMessage({
				text: t.val(),
				time_stamp: timeNow,
				attachment_id: t.data('attachment-id'),
				user: {
					photo_link: getUser().photo_link,
					id: getUser().id
				}
			}, false, true));

			updateChatTime();
			$('.chat-row').scrollTop($('.chat-row')[0].scrollHeight);

			socket.emit('chat', {
				text: t.val(),
				user: getUser(),
				time_stamp: timeNow,
				thread_id: c.data('thread-id'),
				attachment_id: t.data('attachment-id'),
				listing_id: ($('#pf_im_listing_id').length ? $('#pf_im_listing_id').val() : 0)
			});

			t.data('attachment-id', '');
			t.val('').focus();

			$Core.loadInit();
		}
	});
};

var chatWithUser = function() {

	$('._im_a').click(function() {
		var t = $(this);

		window.open(t.data('url'), '_blank');
	});

	$('.pf-im-panel').click(function() {
		var c = $('#pf-chat-window');
		// <span class="chat-row-back"><i class="fa fa-chevron-left"></i></span>
		var html = '<div class="chat-row-title"><span class="chat-row-back"><i class="fa fa-chevron-left"></i></span><span class="chat-row-users"></span><span class="chat-row-close"><i class="fa fa-trash"></i></span></div><div class="chat-row"></div><div class="chat-form-actions-close"><i class="fa fa-toggle-down"></i></div><div class="chat-form-actions"></div><div class="chat-form"><span class="chat-row-action"><i class="fa fa-smile-o" data-action="' + PF.url.make('/emojis') + '"></i><i class="fa fa-external-link" data-action="' + PF.url.make('/im/link') + '"></i></span><div><input type="text" autocomplete="off" name="chat" maxlength="200"></div></div>';
		var t = $(this);

		if (t.hasClass('open_it')) {
			t.removeClass('open_it');
			t.find('.chat_panel_remove').remove();

			return false;
		}

		c.css('opacity', '1');
		t.removeClass('new');
		t.removeClass('is_hidden');
		$('.pf-im-empty').remove();

		if (t.hasClass('active')) {
			t.removeClass('active');
			c.hide();
			$('#pf-chat-window-active').hide();

			return false;
		}

		$('.pf-im-panel.active').removeClass('active');
		t.addClass('active');

		if (!t.data('thread-id')) {
			function get_thread_id(numArray) {
				numArray = numArray.sort(function (a, b) {
					return a - b;
				});

				return numArray.join(':');
			};

			t.data('thread-id', get_thread_id([t.data('user-id'), getUser().id]));
		}

		/*
		var friends = {};
		for (var i in $Cache.friends) {
			var e = $Cache.friends[i];

			friends[e.user_id] = true;
		}
		*/
		/*
		if (!total) {
			// socket.emit('failed', {type: 'not_friends', thread: conversation.thread_id});
			console.log('Message user(s) that are not friends.');
			return;
		}
		*/

		socket.emit('loadConversation', {
			user_id: getUser().id,
			thread_id: t.data('thread-id')
		});

		if (c.length) {
			c.html(html).show();
		}
		else {
			$('#pf-im').prepend('<span id="pf-chat-window-active"></span><div id="pf-chat-window">' + html + '</div>');
		}

		$('#pf-chat-window-active').css('top', ((t.offset().top - $(window).scrollTop()) + (t.height() / 2)) - 5).show();
		$('.chat-row').attr('data-thread-id', t.data('thread-id'));
		// $('.chat-form textarea').focus();

		if (t.data('listing-id')) {
			$('.chat-form input').before('<div><input type="hidden" name="listing_id" id="pf_im_listing_id" value="' + t.data('listing-id') + '">');
		}

		var l = $('#pf-chat-window .fa-external-link');
		l.data('action', l.data('action') + '?thread_id=' + t.data('thread-id'));

		chatTextArea();
		$Core.loadInit();
		if (!is_mobile) {
			$('.chat-form input').focus();
		}
	});

	chatTextArea();
};

var updateChatTime = function() {
	$('.set-moment:not(.built)').each(function() {
		var t = $(this);
		t.addClass('built');
		t.html(moment(t.data('time'), 'X').toNow(true));
	});
};

var build_thread = function(message, users) {
	var is_new = '', is_hidden = '';
	if (typeof(message.is_new) == 'string' && message.is_new == '1' && (message.user.id != getUser().id)) {
		is_new = ' new';
	}

	if (typeof(message.is_hidden) == 'string' && message.is_hidden == '1') {
		is_hidden = ' is_hidden';
	}

	console.log(message);

	var html = '<div class="pf-im-panel' + is_new + is_hidden + '" data-thread-id="' + message.thread_id + '" style="display:none;">';
		html += '<div class="pf-im-panel-image"><span class="__thread-image" data-users="' + users + '"></span></div>';
		html += '<div class="pf-im-panel-content">';
		html += '<span class="__thread-name" data-users="' + users + '"></span>';
		html += '<div class="pf-im-panel-preview">' + fixChatMessage(message.preview) + '</div>';
		html += '</div>';
		html += '<div class="pf-im-panel-info">';
		html += '<span class="pf-im-count"></span><time data-time="' + message.updated + '"></time>';
		html += '</div>';
		html += '</div>';

	return html;
};

var start_im = function() {
	if ($('#admincp_base').length || pf_im_enabled != '1' || !$('#auth-user').length) {
		return;
	}

	if (socket_built === false) {
		socket_built = true;
		$.ajaxSetup({cache: true});
		$.getScript('https://cdn.socket.io/socket.io-1.2.0.js', function () {

			socket = io(pf_im_node_server);
			if ($('#pf-im-host').length) {
				socket.emit('host', {
					token: $('#pf-im-host').data('token')
				});

				socket.on('host_failed', function() {
					$('#pf-im').html('<div class="error_message">Unable to load IM due to not having a valid hosting account.</div>');

					socket.on = function() {

					};
				});
			}

			socket.emit('loadThreads', getUser().id);
			socket.on('total_threads', function (total) {
				if (!total) {
					$('#pf-im > i').remove();
					$('.pf-im-main').html('<div class="pf-im-empty">No conversations</div>');
				}
				thread_total = total;
				console.log('total: ' + total);
			});

			var cache = {};
			socket.on('loadThreads', function (thread) {
				var threadUsers = '';
				thread_cnt++;
				thread = $.parseJSON(thread);

				// console.log('loadThreads2');
				// console.log(thread);

				if (isset(thread.is_hidden) && thread.is_hidden == '1') {
					console.log('no new count...');
				}
				else {
					/*
					if (isset(thread.is_new) && thread.is_new == '1') {
						var n = $('#js_total_new_messages'), l = n.html().length, t = (l ? parseInt(n.html()) : 0);
						t++;
						console.log(t);
						$('span#js_total_new_messages').html(t).show();
					}
					*/
				}

				for (var i in thread.users) {
					if (typeof(cache[thread.users[i]]) == 'string') {
						continue;
					}
					cache[thread.users[i]] = '1';
					users += thread.users[i] + ',';
					threadUsers += thread.users[i] + ',';
				}

				if (thread_cnt === 1) {
					$('.pf-im-main').html('');
				}

				$('.pf-im-main').append(build_thread(thread, threadUsers));

				if (thread_cnt === thread_total) {
					var total_new = $('.pf-im-panel.new').length;
					if (total_new) {
						$('#js_total_new_messages').html(total_new).show();
					}

					$.ajax({
						url: PF.url.make('/im/panel'),
						data: 'users=' + users,
						contentType: 'application/json',
						'success': function (e) {

							$('.pf-im-panel').each(function () {
								var t = $(this);
								var names = t.find('.__thread-name').data('users').split(',');
								for (var i in names) {
									var n = names[i];
									if (!n) {
										continue;
									}

									if (typeof(e[n]) == 'object') {
										var u = e[n];
										if (u.id == getUser().id) {
											continue;
										}

										t.find('.pf-im-panel-image').html(u.photo_link);
										t.find('.__thread-name').html(u.name);
									} else {
										t.remove();
										var total_new = parseInt($('#js_total_new_messages').html());
										if (total_new) {
											total_new--;
											$('#js_total_new_messages').html(total_new).show();
											if (!parseInt($('#js_total_new_messages').html())) {
												$('#js_total_new_messages').hide();
											}

											socket.emit('deleteUser', n);
										}
									}
								}

								t.show();
							});

							$('#pf-im > i').remove();
							updateChatTime();
							// chatWithUser();
							$Core.loadInit();
						}
					});
				}
			});

			socket.on('failed', function(data) {
				$('.chat-row-close').trigger('click');
				$('.pf-im-panel[data-thread-id="' + data.thread + '"]').remove();
				var popup = $('<a class="popup" href="' + PF.url.make('/im/failed') + '"></a>');
				tb_show('', PF.url.make('/im/failed'), popup);
				$('#pf-im').hide();
			});

			socket.on('loadConversation', function (threads) {
				var u = '';
				var c = $('.chat-row');
				var cache = {};
				var iteration = false;

				for (var i in threads) {
					var thread = $.parseJSON(threads[i]);

					/*
					 if (typeof(cache[thread.user.id]) != 'string') {
					 cache[thread.user.id] = '1';
					 u += thread.user.id + ',';
					 }
					 */

					if (!iteration) {
						iteration = true;
						var k = thread.thread_id.split(':');
						for (var i2 in k) {
							if (typeof(cache[k[i2]]) != 'string') {
								cache[k[i2]] = '1';
								u += k[i2] + ',';
							}
						}
					}

					c.append(buildMessage(thread));
				}

				$.ajax({
					url: PF.url.make('/im/panel'),
					data: 'users=' + u,
					contentType: 'application/json',
					'success': function (e) {
						$('.pf-chat-message').each(function () {
							var t = $(this), id = t.data('user-id'), u = e[id];

							t.find('.pf-chat-image').html(u.photo_link);
							updateChatTime();

							t.show();
						});

						for (var i in e) {
							var u = e[i];
							$('.chat-row-users').prepend('<span>' + u.photo_link + '</span>');
						}

						$('.chat-row').scrollTop($('.chat-row')[0].scrollHeight);

						reloadImages();
						$Core.loadInit();
					}
				});
			});
			
			socket.on('chat_delete', function(key) {
				$('.pf-chat-message').each(function() {
					var t = $(this);

					if (t.find('.pf_chat_delete_message[data-key="' + key + '"]').length) {
						t.find('.pf-chat-body').html('<div class="pf_chat_message_deleted">Message deleted...</div>');
					}
				});
			});

			socket.on('chat', function (chat) {
				var sameUser = chat.user.id == getUser().id;

				var users = chat.thread_id.split(':'), total_friends = 0;
				for (var i in users) {
					if (getUser().id == users[i]) {
						total_friends++;
					}
				}

				if (!total_friends) {
					console.log('Unable to chat with this user.');
					return;
				}

				if (!$('#pf-im').is(':visible')) {
					console.log('not visible...');

					/*
					var t = $('#js_total_new_messages');
					var l = t.html().length;
					var total = (l ? parseInt(l) : 0);
					total++;
					$('span#js_total_new_messages').html(total).show();
					*/

					// return;
				}

				if ((!$('.chat-row[data-thread-id="' + chat.thread_id + '"]').length) || ($('.chat-row[data-thread-id="' + chat.thread_id + '"]').length && !$('.chat-row[data-thread-id="' + chat.thread_id + '"]').is(':visible'))) {
					console.log('thread does not exist: ' + chat.thread_id);
					if (!$('.pf-im-panel[data-thread-id="' + chat.thread_id + '"]').length) {
						console.log('does not exist in panel either: ' + chat.thread_id);

						var html = '<div class="pf-im-panel ' + (sameUser ? '' : 'new') +'" data-user-id="' + chat.user.id + '" data-thread-id="' + chat.thread_id + '">' +
							'<div class="pf-im-panel-image">' + chat.user.photo_link + '</div>' +
							'<div class="pf-im-panel-content">' + chat.user.name + '<div class="pf-im-panel-preview">' + fixChatMessage(chat.text) + '</div></div>' +
							'<div class="pf-im-panel-info"><span class="pf-im-count"></span><time class="set-moment" data-time="' + chat.time_stamp + '"></time></div>' +
							'</div>';
						$('.pf-im-main').prepend(html);
						updateChatTime();
					}

					var t = $('.pf-im-panel[data-thread-id="' + chat.thread_id + '"]').clone();

					$('.pf-im-panel[data-thread-id="' + chat.thread_id + '"]').remove();

					$('.pf-im-empty').remove();
					t.prependTo('.pf-im-main');
					if (!sameUser)
					{
						t.addClass('new');
					}
					t.find('.pf-im-panel-preview').html(fixChatMessage(chat.text));
					$Core.loadInit();

					var total_new = $('.pf-im-panel.new').length;
					if (!sameUser && total_new) {
						$('span#js_total_new_messages').html(total_new).show();
					}

					return;
				}

				$('.chat-row').append(buildMessage(chat, false, true));
				updateChatTime();
				$('.chat-row').scrollTop($('.chat-row')[0].scrollHeight);
				$Core.loadInit();
				var total_new = $('.pf-im-panel.new').length;
				if (total_new) {
					$('span#js_total_new_messages').html(total_new).show();
				}
			});
		});

		$.ajaxSetup({cache: false});
		$Core.loadInit();
	}
};

var reloadImages = function() {
	$('.image_deferred:not(.built)').each(function() {
		var t = $(this),
			src = t.data('src'),
			i = new Image();

		t.addClass('built');
		if (!src) {
			t.addClass('no_image');
			return;
		}

		t.addClass('has_image');
		i.onerror = function(e, u) {
			t.replaceWith('');
		};
		i.onload = function(e) {
			t.attr('src', src);
		};
		i.src = src;
	});
};

function im_attachment(id, object) {
	$('.chat-form-actions').hide();
	$('.chat-form-actions-close').hide();
	$('.chat-form input').data('attachment-id', object);
	$('._im_a', '.chat-form').remove();
	$('.chat-form').height('auto').append(build_attachment(object));
	$('.chat-form').find('.form-control:first').focus();
}

// var checked_friends = false;
var active_friends = {};

$Ready(function() {
	if ($('#admincp_base').length || pf_im_enabled != '1' || !$('#auth-user').length || socket_built === false) {
		return;
	}

	thread_cnt = 0;
	thread_total = 0;
	users = '';

	$('.pf-im-panel').bind('contextmenu', function(e) {
		e.preventDefault();
		var t = $(this);
		if (t.hasClass('open_it')) {
			t.removeClass('open_it');
			$('.chat_panel_remove').remove();
			return;
		}

		t.addClass('open_it');
		t.prepend('<span class="chat_panel_remove"><i class="fa fa-trash"></i></span>');
		$('.chat_panel_remove').unbind().click(function() {
			var t = $(this).parent().data('thread-id');
			$(this).parent().remove();
			$('.pf-im-panel[data-thread-id="' + t + '"]').remove();
			socket.emit('hideThread', {
				id: t,
				user_id: getUser().id
			});
			console.log('remove!');
		});
	});
	
	$('.pf_chat_delete_message').click(function() {
		var t = $(this);

		socket.emit('chat_delete', t.parents('.chat-row:first').data('thread-id'), t.data('key'));
		t.parents('.pf-chat-message:first').remove();

		return false;
	});

	if (typeof twemoji_selectors != 'undefined') {
		emoji_support = true;
		twemoji_selectors += ', .pf-chat-body, .pf-im-panel-preview';
	}

	$('.chat-row-title').click(function() {
		$('#pf-chat-window').hide();
		$('.pf-im-panel.active').removeClass('active');
		$('#pf-chat-window-active').hide();
	});

	$('.chat-row-close').click(function() {
		$('#pf-chat-window').hide();
		$('.pf-im-panel.active').removeClass('active');
		$('#pf-chat-window-active').hide();
		var t = $('#pf-chat-window .chat-row').data('thread-id');
		$('.pf-im-panel[data-thread-id="' + t + '"]').remove();
		socket.emit('hideThread', {
			id: t,
			user_id: getUser().id
		});
	});

	/*
	$('#main').click(function() {
		$('#pf-chat-window').css('opacity', 0.4);
		$Core.loadInit();
	});
	*/

	$('.chat-row, .chat-form').mousedown(function() {
		$('#pf-chat-window').css('opacity', 1);
	});

	$('.chat-form-actions-close').click(function() {
		$(this).hide();
		$('.chat-form-actions').hide();
	});

	var u = $('#auth-user'), im = $('#pf-im');
	if ($('#hd-message a').length || (!$('#hd-message a').length && $('span#js_total_new_messages').length)) {
		var obj = ($('#hd-message a').length ? $('#hd-message a') : $('span#js_total_new_messages').parents('a:first'));

		obj.each(function() {
			var m = $(this);
			// if (m.length && !m.hasClass('built')) {
				m.addClass('built');
				m.addClass('no_ajax');
				m.removeClass('_panel');
				m.removeAttr('data-toggle').removeAttr('data-panel');
			// }

			m.click(function () {
				console.log('click!');
				$('#pf-open-im').trigger('click');
				return false;
			});
		});
	}

	$('.pf-im-title .close-im-window').click(function() {
		// $('body').css('margin-right', '0px').removeClass('im-is-active');
		$('body').removeClass('im-is-active');
		deleteCookie('pf_im_active');
		$('#pf-im, #pf-chat-window, #pf-chat-window-active').hide();
	});

	$('.popup-im-window').click(function() {
		var newwindow = window.open(PF.url.make('/im/popup'),'name','height=400,width=800,location=false');
		if (window.focus) {
			newwindow.focus();
		}
		$('body').removeClass('im-is-active');
		deleteCookie('pf_im_active');
		$('#pf-im, #pf-chat-window, #pf-chat-window-active').hide();
	});

	$('.pf-im-menu a').click(function() {
		var t = $(this), url = '/im/panel';

		$('._pf_im_friend_search').remove();
		
		if (t.hasClass('active')) {
			return false;
		}

		if (t.data('type') == '2') {
			url = '/im/friends';
			$('.pf-im-panel.active').removeClass('active');
			cache_content = $('.pf-im-main').html();
			$('#pf-im').prepend('<i class="fa fa-spin fa-circle-o-notch"></i>');
			$('#pf-chat-window, #pf-chat-window-active').hide();
			$('.pf-im-main').addClass('_is_friends_list');
		} else {
			$('.pf-im-main').removeClass('_is_friends_list');
		}

		if (t.data('type') == '1' && !t.hasClass('active') && cache_content === null) {
			return false;
		}

		if (t.data('type') == '1' && !t.hasClass('active') && cache_content !== null) {

			$('.pf-im-menu a.active').removeClass('active');
			t.addClass('active');
			$('#pf-im .pf-im-main').html(cache_content);
			$Core.loadInit();
			cache_content = null;

			return false;
		}

		$('.pf-im-menu a.active').removeClass('active');
		t.addClass('active');

		$.ajax({
			url: PF.url.make(url),
			contentType: 'application/json',
			success: function(e) {
				var h = $('#pf-im .pf-im-main');

				if (t.data('type') == '2') {
					$('.pf-im-menu').after('<div class="_pf_im_friend_search"><i class="fa fa-search"></i><input type="text" name="user" autocomplete="off" placeholder="' + oTranslations['search_friends_dot_dot_dot']  + '"></div>');
				}

				h.html(e.content);
				h.find('._block').remove();
				$('#pf-im > i ').remove();

				$Core.loadInit();
				reloadImages();
			}
		});

		return false;
	});

	$('._pf_im_friend_search input').keyup(function() {
		var t = $(this);
		$('.search_no_result', '._is_friends_list').remove();
		var search = t.val().replace(/[^\w\s]/gi, '');
		var found = 0;
		if (active_friends) {
			$('.pf-im-friends').hide();
			for (i in active_friends) {
				var f = active_friends[i], r = new RegExp(search, 'i');
				if (i.match(r)) {
					found ++;
					$('.pf-im-friends[data-friend-id="' + f + '"]').show();
				}
			}
			 if (found == 0) {
				 $('._is_friends_list').append('<div class="search_no_result">' + v_phrases.no_friends_found + '</div>')
			 }
		}
	});

	$('.chat-row-action i').click(function() {
		var t = $(this), c = $('.chat-form-actions');

		c.html('<i class="fa fa-spin fa-circle-o-notch"></i>').css('bottom', '-60px').show().animate({
			bottom: '0px'
		});
		$.ajax({
			url: t.data('action'),
			type: 'GET',
			contentType: 'application/json',
			success: function(e) {
				$('.chat-form-actions-close').fadeIn();
				lastEmojiObject = $('.chat-form input.form-control');
				c.html(e.content);
				$Core.loadInit();
			}
		});
	});

	$('.emoji-list i').click(function() {
		$('.chat-form-actions').hide();
		$('.chat-form-actions-close').hide();
	});

	$('#pf-open-im').click(function() {
		var b = $(this);

		$('#pf-im').show();
		// $('body').css('margin-right', $('#pf-im').width() + 'px');

		if (!b.data('fake-click') || (b.data('fake-click') && b.data('fake-click') == '0')) {
			$('body').addClass('im-is-active');
		}

		setCookie('pf_im_active', 1);

		$('.pf-im-panel.active').removeClass('active');
		$('span#js_total_new_messages').html('0').hide();

		// socket.emit('loadThreads', getUser().id);
	});

	if (u.length && !im.length) {
		var menu = '<div class="pf-im-menu"><ul><li><a href="#" data-type="1" class="active no_ajax">' + PHPfox_IM_Phrases.conversations + '</a></li><li><a href="#" data-type="2" class="no_ajax">' + PHPfox_IM_Phrases.friends + '</a></li></ul></div>';
		$('body').prepend('<span id="pf-im-total-messages">0</span><div id="pf-open-im"><i class="fa fa-comments"></i></div><div id="pf-im"><i class="fa fa-spin fa-circle-o-notch"></i><div class="pf-im-title"><i class="fa fa-comments"></i>' + PHPfox_IM_Phrases.messenger + '<span class="close-im-window"></span><span class="popup-im-window"></span></div>' + menu + '<div class="pf-im-main"></div></div>');

		$Core.loadInit();
		/*
		if (getCookie('pf_im_active')) {
			$('#pf-open-im').data('fake-click', '1').trigger('click').data('fake-click', '0');
		}
		*/
	}

	var nIm = $('#pf-im');
	if (nIm.hasClass('ui-draggable')) {
		nIm.draggable('destroy');
	}
	nIm.draggable({
		handle: '.pf-im-title'
	});

	if (nIm.hasClass('ui-resizable')) {
		nIm.resizable('destroy');
	}
	nIm.resizable();

	chatWithUser();
});