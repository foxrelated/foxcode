<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="item_view">
		<div class="item_info">
			<span>{$aForms.time_stamp|convert_time}</span>
			<span>{_p var='by'} {$aForms|user:'':'':35:'':'author'}</span>
			{if !empty($aForms.album_id)}<span>{_p var='in'} <a href="{$aForms.album_url}">{$aForms.album_title|convert|clean|split:45|shorten:75:'...'}</a></span>{/if}
		</div>
		{if (Phpfox::getUserParam('photo.can_edit_own_photo') && $aForms.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('photo.can_edit_other_photo')
		|| (Phpfox::getUserParam('photo.can_delete_own_photo') && $aForms.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('photo.can_delete_other_photos')
		}
		<div class="item_bar">
			<div class="dropup item_bar_action_holder">
				{if $aForms.view_id == '1' && Phpfox::getUserParam('photo.can_approve_photos')}
				<a href="#" class="item_bar_action approve btn-primary" onclick="$(this).hide(); $('#js_item_bar_approve_image').show(); $.ajaxCall('photo.approve', 'inline=true&amp;id={$aForms.photo_id}'); return false;" title="{_p var='approve'}"></a>
				{/if}
				<a role="button" data-toggle="dropdown" class="item_bar_action"><span>{_p var='actions'}</span></a>
				<ul class="dropdown-menu dropdown-menu-right">
					{template file='photo.block.menu'}
				</ul>
			</div>
		</div>
		{/if}

	<div class="photo_tag_in_photo">
		{_p var='in_this_photo'}: <span id="js_photo_in_this_photo"></span>
	</div>

	{if Phpfox::isModule('tag') && isset($aForms.tag_list)}
	{module name='tag.item' sType='photo' sTags=$aForms.tag_list iItemId=$aForms.photo_id iUserId=$aForms.user_id}
	{/if}
	<div class="item_detail_wrapper">
		<div id="js_photo_item_detail" class="info_holder">
			{module name='photo.detail'}
		</div>
	</div>
	{if $aForms.description}
	<div class="item_content">
		{$aForms.description|clean}
	</div>
	{/if}
	<div class="js_moderation_on">
		{module name='feed.comment'}
	</div>

</div>
<script type="text/javascript">
	var bChangePhoto = true;
	var aFeedPhotos = {$sFeedPhotos};
	$Behavior.tagPhoto = function() {l} $Core.photo_tag.init({l}{$sPhotoJsContent}{r}); {r};
	$Behavior.removeTagBox = function()
	{l}
	{literal}
	if ($('#noteform').length > 0)$('#noteform').hide(); if ($('#js_photo_view_image').length > 0 && typeof $('#js_photo_view_image').imgAreaSelect == 'function')$('#js_photo_view_image').imgAreaSelect({ hide: true });
	{/literal}
	{r};
	
	
	$Behavior.removeImgareaselectBox = function()
	{l}
	{literal}
	if ($('body#page_photo_view').length == 0 || ($('body#page_photo_view').length > 0 && bChangePhoto == true)) {
		bChangePhoto = false;
		$('.imgareaselect-outer').hide();
		$('.imgareaselect-selection').each(function() {
			$(this).parent().hide();
		});
	}
	{/literal}
	{r};
</script>