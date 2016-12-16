<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{$url}" id="page_editor">
	<div class="page_editor_content">
		<div class="ace_editor" data-ace-mode="smarty">{$data|clean}</div>
	</div>
	<div class="page_editor_data">
		<div>
			<div class="table form-group">
				<div class="table_left">
                    {_p var='title'}:
				</div>
				<div class="table_right">
					<input type="text" name="val[title]" value="">
				</div>
			</div>

			<div class="table form-group">
				<div class="table_left">
					{_p var='meta_keywords'}:
				</div>
				<div class="table_right">
					<input type="text" name="val[keywords]" value="">
				</div>
			</div>

			<div class="table form-group">
				<div class="table_left">
					{_p var='meta_description'}:
				</div>
				<div class="table_right">
					<input type="text" name="val[description]" value="">
				</div>
			</div>

			<div class="table form-group">
				<div class="table_left">
					{_p var='custom_header'}:
				</div>
				<div class="table_right">
					<textarea name="val[head]"></textarea>
				</div>
			</div>

			<div class="table_clear">
				<input type="submit" class="button btn-primary" value="{_p var='save'}">
			</div>
		</div>
	</div>
</form>
{literal}
<script>
	$Ready(function() {
		if ($('.page_editor_data:not(.built)').length) {
			$('.page_editor_data').addClass('built');

			// var custom = $('#page_editor_meta').html().trim();
			$('input[name="val[title]"]').val(document.title);
			$('input[name="val[keywords]"]').val($('meta[name="keywords"]').attr('content'));
			$('input[name="val[description]"]').val($('meta[name="description"]').attr('content'));
			if (typeof(page_editor_meta) == 'string') {
				$('textarea[name="val[head]"]').val(page_editor_meta.head);
			}
		}

		$('#page_editor').submit(function() {
			var t = $(this);

			$.ajax({
				url: t.attr('action'),
				type: 'POST',
				data: t.serialize() + '&content=' + encodeURIComponent($AceEditor.obj.getSession().getValue()),
				success: function(e) {
					p(e);
				}
			});

			return false;
		});
	});
</script>
{/literal}