var iMaxAnswers = 0;
var iMinAnswers = 0;

$Behavior.buildSortableAnswers = function() {
	$('.js_answers').each(function(){
		var sVal = $(this).val();
		var sOriginal= $(this).val();
		sVal = (sVal.replace(/\D/g,""));
		// dummy check			
		if ("Answer " + sVal + "..." == sOriginal)
		{
			// this is a default answer
			$(this).addClass('default_value');
			$(this).focus(function(){
				if ($(this).val() == sOriginal)
				{
					$(this).val('');
					$(this).removeClass('default_value');
				}
			});
			$(this).blur(function(){
				if ($(this).val() == '')
				{
					$(this).val(sOriginal);
					$(this).addClass('default_value');
				}
			});
		}
		
	});
}


function appendAnswer(sId)
{
	iCnt = 0;
	$('.js_answers').each(function()
	{
		if ($(this).parents('.placeholder:visible').length)
		iCnt++;
	});		
	if (iCnt >= iMaxAnswers)
	{
		alert(oTranslations['you_have_reached_your_limit']);
		return false;
	}
	
	
	//iCnt++;
	var oCloned = $('.placeholder:first').clone();
	oCloned.find('.js_answers').val(oTranslations['answer'] + ' ' + iCnt + '...');
	oCloned.find('.js_answers').addClass('default_value');
	oCloned.find('.hdnAnswerId').remove();
	
	var sInput = '<input type="text" class="form-control js_answers" size="30" value="" name="val[answer][][answer]"/>';
	oCloned.find('.class_answer').html(sInput);
	oCloned.find('.js_answers').attr('name', 'val[answer][' + (iCnt + 1) +  '][answer]');
	var oFirst = oCloned.clone();

	var firstAnswer = oFirst.html();

	$(sId).parents('.js_prev_block').parents('.placeholder').after('<div class="placeholder">' + firstAnswer + '</div><div class="js_next_block"></div>')
	return false;
}

/**
 * Uses JQuery to count the answers and validate if user is allowed one less answer
 * Effect used fadeOut(1200)
 */
function removeAnswer(sId)
{
	/* Take in count hidden input */
	iCnt = -1;
		
	$('.js_answers').each(function()
	{
		iCnt++;
	});
		
	if (iCnt == iMinAnswers)
	{
		alert(oTranslations['you_must_have_a_minimum_of_total_answers'].replace('{total}', iMinAnswers));
		return false;
	}
	
	$(sId).parents('.placeholder').remove();
		
	return false;
}

$Behavior.poll_poll_appendClick = function()
{
	$('.append_answer').click(function(){
		return false;
	});
};


$Core.poll =
{
	aParams: {},
	iTotalQuestions : 1,

	init: function(aParams)
	{
		this.aParams = aParams;		
	},

	build: function()
	{},
	
	deleteImage : function(iPoll)
	{
		$Core.jsConfirm({message: oTranslations['are_you_sure']}, function() {
			$.ajaxCall('poll.deleteImage', 'iPoll=' + iPoll);
		}, function(){});
		return false;
	},

	showFormForEditAgain: function($answerId, iPollId) {
		$('.poll_question input.js_poll_answer').each(function() {
			if ($(this).val() == $answerId)
				$(this).prop('checked', true);
			else
				$(this).prop('checked', false);
		});
		if ($('#vote_list_'+iPollId)) {
			$('#vote_list_'+iPollId).slideUp();
		}
		if ($('#vote_'+iPollId)) {
			$('#vote_'+iPollId).slideDown();
		}
	},

	hideFormForEditAgain: function(iPollId) {
		if ($('#vote_'+iPollId)) {
			$('#vote_'+iPollId).slideUp();
		}
		if ($('#vote_list_'+iPollId)) {
			$('#vote_list_'+iPollId).slideDown();
		}
	}
};