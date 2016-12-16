<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: menu.html.php 7088 2014-02-04 15:37:30Z Fern $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
		{if (Phpfox::getUserParam('photo.can_edit_own_photo') && $aForms.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('photo.can_edit_other_photo')}
		<li><a href="#" onclick="if ($Core.exists('.js_box_image_holder_full')) {l} js_box_remove($('.js_box_image_holder_full').find('.js_box_content')); {r} $Core.box('photo.editPhoto', 700, 'photo_id={$aForms.photo_id}'); $('#js_tag_photo').hide();return false;">{_p var='edit_this_photo'}</a></li>
		{/if}

		{if Phpfox::isModule('ad') && Phpfox::isModule('feed') && (Phpfox::getUserParam('feed.can_purchase_sponsor') || Phpfox::getUserParam('feed.can_sponsor_feed')) && Phpfox::getUserParam('feed.feed_sponsor_price') && ($iSponsorId = Feed_Service_Feed::instance()->canSponsoredInFeed('photo', $aForms.photo_id))}
			<li>
				{if $iSponsorId === true}
				<a href="{url link='ad.sponsor' where='feed' section='photo' item=$aForms.photo_id}">
					{_p var='sponsor_in_feed'}
				</a>
				{else}
				<a href="#" onclick="$.ajaxCall('ad.removeSponsor', 'type_id=photo&item_id={$aForms.photo_id}', 'GET'); return false;">
					{_p var="Unsponsor In Feed"}
				</a>
				{/if}
			</li>
		{/if}
		
		{if Phpfox::getUserParam('photo.can_feature_photo') && !$aForms.is_sponsor}
		    <li id="js_photo_feature_{$aForms.photo_id}">
		    {if $aForms.is_featured}
			    <a href="#" title="{_p var='un_feature_this_photo'}" onclick="$.ajaxCall('photo.feature', 'photo_id={$aForms.photo_id}&amp;type=0', 'GET'); return false;">{_p var='un_feature'}</a>
		    {else}
			    <a href="#" title="{_p var='feature_this_photo'}" onclick="$.ajaxCall('photo.feature', 'photo_id={$aForms.photo_id}&amp;type=1', 'GET'); return false;">{_p var='feature'}</a>
		    {/if}
		    </li>
		{/if}		

		{if Phpfox::getUserParam('photo.can_sponsor_photo') && !defined('PHPFOX_IS_GROUP_VIEW')}     
		<li id="js_sponsor_{$aForms.photo_id}" class="" style="{if $aForms.is_sponsor}display:none;{/if}">
			    <a href="#" onclick="$('#js_sponsor_{$aForms.photo_id}').hide();$('#js_unsponsor_{$aForms.photo_id}').show();$.ajaxCall('photo.sponsor','photo_id={$aForms.photo_id}&type=1'); return false;">
				{_p var='sponsor_this_photo'}
			    </a>
		</li>		    
		<li id="js_unsponsor_{$aForms.photo_id}" class="" style="{if $aForms.is_sponsor != 1}display:none;{/if}">
			    <a href="#" onclick="$('#js_sponsor_{$aForms.photo_id}').show();$('#js_unsponsor_{$aForms.photo_id}').hide();$.ajaxCall('photo.sponsor','photo_id={$aForms.photo_id}&type=0'); return false;">
				{_p var='unsponsor_this_photo'}
			    </a>
		</li>
		{elseif Phpfox::getUserParam('photo.can_purchase_sponsor')  && !defined('PHPFOX_IS_GROUP_VIEW')
		    && $aForms.user_id == Phpfox::getUserId()
		    && $aForms.is_sponsor != 1}
		    <li>
			<a href="{permalink module='ad.sponsor' id=$aForms.photo_id}section_photo/">
				{_p var='sponsor_this_photo'}
			</a>
		    </li>
		{/if}
		
		{if PHPFOX_IS_AJAX && isset($bIsTheater) && $bIsTheater && (Phpfox::getUserParam('photo.can_edit_own_photo') && $aForms.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('photo.can_edit_other_photo')}
					<li>
						<a href="#" onclick="$('#photo_view_ajax_loader').show(); $('#menu').remove(); $('#noteform').hide(); $('#js_photo_view_image').imgAreaSelect({left_curly} hide: true {right_curly}); $('#js_photo_view_holder').hide(); $.ajaxCall('photo.rotate', 'photo_id={$aForms.photo_id}&amp;photo_cmd=right&amp;currenturl=' + $('#js_current_page_url').html()); return false;">
							{_p var='rotate_right'}
						</a>
					</li>
					<li>
						<a href="#" onclick="$('#photo_view_ajax_loader').show(); $('#menu').remove(); $('#noteform').hide(); $('#js_photo_view_image').imgAreaSelect({left_curly} hide: true {right_curly}); $('#js_photo_view_holder').hide(); $.ajaxCall('photo.rotate', 'photo_id={$aForms.photo_id}&amp;photo_cmd=left&amp;currenturl=' + $('#js_current_page_url').html()); return false;">		{_p var='rotate_left'}
						</a>
					</li>		
		{/if}
		
		{plugin call='photo.template_block_menu'}
		
		{if (Phpfox::getUserParam('photo.can_delete_own_photo') && $aForms.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('photo.can_delete_other_photos')}
		{if defined('PHPFOX_IS_THEATER_MODE')}
		<li class="item_delete"><a href="#" onclick="$Core.jsConfirm({l}{r}, function() {l} $.ajaxCall('photo.deleteTheaterPhoto', 'photo_id={$aForms.photo_id}'); {r}, function(){l}{r}); return false;">{_p var='delete_this_photo'}</a></li>
		{else}
		<li class="item_delete"><a href="{url link='photo' delete=$aForms.photo_id}" {if $iAvatarId == $aForms.photo_id}data-message="{_p var='Are you sure? This will delete your current profile picture also.'}"{/if} class="sJsConfirm">{_p var='delete_this_photo'}</a></li>
		{/if}
		{/if}		
		
		{if isset($aCallback)}
			<li>
				<a href="#" onclick="$Core.Photo.setCoverPhoto({$aForms.photo_id},{$aCallback.item_id},'{$aCallback.module_id}'); return false;" >
					{if isset($aCallback.set_default_phrase)}
					{$aCallback.set_default_phrase}
					{else}
					{_p var='set_as_page_s_cover_photo'}
					{/if}
				</a>
			</li>
		{/if}
