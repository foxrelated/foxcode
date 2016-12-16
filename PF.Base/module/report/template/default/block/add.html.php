<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Report
 * @version 		$Id: add.html.php 3533 2011-11-21 14:07:21Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bCanReport}
<div id="js_report_body">
{_p var='you_are_about_to_report_a_violation_of_our_a_href_link_target_blank_terms_of_use_a' link=$sTermsUrl}
<div class="">
	{_p var='all_reports_are_strictly_confidential'}
	<div class="p_top_8">
		<div class="table form-group">
			<div class="table_left">
				{_p var='reason'}:
			</div>
			<div class="table_right">
				<select class="form-control" name="reason" id="js_report">
				<option value="">{_p var='choose_one'}</option>
				{foreach from=$aOptions item=aOption}
					<option value="{$aOption.report_id}">{$aOption.message|convert}</option>
				{/foreach}
				</select>
			</div>
			<div class="table_left">
				{_p var='a_comment_optional'}:
			</div>
			<div class="table_right">
				<textarea class="form-control" name="feedback" id="feedback" cols="19" rows="3"></textarea>
			</div>			
		</div>
		<div class="table">
			<div class="table_right">
				<input type="button" value="{_p var='submit'}" class="button btn-block btn-danger" onclick="if ( ($('#js_report').val() != '' || $('#feedback').val() != '' ) && confirm('{_p var='are_you_sure' phpfox_squote=true}')) {left_curly} $.ajaxCall('report.insert', 'id={$iItemId}&amp;type={$sType}&amp;report=' + $('#js_report').val() + '&feedback='+$('#feedback').val()); {right_curly}" />
			</div>
		</div>
			
	</div>
</div>
</div>
{else}
{_p var='you_have_already_reported_this_item'}
{/if}