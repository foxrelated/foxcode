<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_Newsletter
 * @version 		$Id: view.html.php 2197 2010-11-22 15:26:08Z Raymond_Benc $
 */

?>
<div style="margin-left: 15px;">
	<div> 
		<div style="width:100%;"><h3> {$aNewsletter.subject} </h3> </div>
		<div style="position: absolute; right:2%; top:2%;">
			{_p var='view_in'} <select id="js_mode" onchange="$Core.Newsletter.toggleMode(this);">
				<option value="html">{_p var='html'}</option>
				<option value="plain">{_p var='plain'}</option>
			</select>
		</div>
	</div>
	<div>
	{if $aNewsletter.text_plain != '' && $aNewsletter.text_html != ''}
		<div id="js_view_newsletter_html" class="js_view_newsletter_content" {if $aNewsletter.mode != 'html'}style="display:none;"{/if}>
			{$aNewsletter.text_html}
		</div>
		<div id="js_view_newsletter_plain" class="js_view_newsletter_content" {if $aNewsletter.mode != 'plain'}style="display:none;"{/if}>
			{$aNewsletter.text_plain}
		</div>
	{else}
		{_p var='this_newsletter_is_empty'}
	{/if}
	</div>
</div>

{literal}
<script type="text/javascript">
	$Core.Newsletter = {
		toggleMode : function(obj) {
			var sMode = $(obj).val();
			$('.js_view_newsletter_content').hide();
			$('#js_view_newsletter_'+sMode).show();
		}
	}
</script>
{/literal}