$Behavior.design_page = function()
{
	$('.js_cancel_change_poll_question').click(function()
	{
		if (document.getElementById('js_current_poll_question').style.display == '' || document.getElementById('js_current_poll_question').style.display == 'inline')
		{
			$('#js_current_poll_question').hide();
			$('#js_update_poll_question').show();
		}
		else
		{
			$('#js_current_poll_question').show();
			$('#js_update_poll_question').hide();
		}

		return false;
	});

	$('.js_current_poll_question').click(function()
	{
		// hide the label
		$('#js_current_poll_question').hide();
		// show the input field
		$('#js_update_poll_question').show();

		return false;
	});

	// Colorpicker
	$('#js_poll_design_wrapper ._colorpicker:not(.built)').each(function() {
		var t = $(this),
			h = t.parent().find('._colorpicker_holder');

		t.addClass('built');
		console.log(t.val());
		h.css('background-color', '#' + t.val());

		h.colpick({
			layout: 'hex',
			submit: false,
			onChange: function(hsb,hex,rgb,el,bySetColor) {
				t.val(hex);
				h.css('background-color', '#' + hex);
				var rel = t.data('rel');
				switch(rel) {
					case 'backgroundChooser':
						$('.poll_answer_container').css('backgroundColor', '#' + hex);
						break;
					case 'percentageChooser':
						$('.poll_answer_percentage').css('backgroundColor','#'+hex);
						break;
					default:
						$('.poll_answer_container').css('border', '1px solid #' + hex);
						break;
				}
			},
			onHide: function() {
				t.trigger('change');
			}
		});
	});

	// Answers
	$('.js_update_answer').click(function()
	{
		var iId = $(this).get(0).id.replace('js_text_answer_','');
		$('#js_text_answer_' + iId).hide();
		$('#js_input_answer_' + iId).show();
	});

	$('.js_cancel_change_answer').click(function()
	{
		// get the id of the answer
		var iId = $(this).get(0).id.replace('js_cancel_change_answer_','');

		// set the value of the input to the current value of the 'label', this step should not be needed
		// $('#js_input_answer_text_' + iId).val(trim($('#js_text_answer_' + iId).html()));

		// hide the input field
		$('#js_input_answer_' + iId).hide();

		// show the 'label' field
		$('#js_text_answer_' + iId).show();

		return false;
	});

	// this function cancels editing an answer
	$('.js_commit_change_answer').click(function()
	{
		// get the id of the answer
		var iId = $(this).get(0).id.replace('js_commit_change_answer_','');

		// hide the input field
		$('#js_input_answer_' + iId).hide();
		// commit the changes with a beautiful ajax call
		$.ajaxCall('poll.changeAnswer', 'iId=' + iId + '&sTxt=' + $('#js_input_answer_'+iId).val());

		// show the 'label'
		$('#js_text_answer_' + iId).html(trim($('#js_input_answer_text_' + iId).val()));
		$('#js_text_answer_' + iId).show();

		// we need nothing else because the input is still there
		return false;
	});

}