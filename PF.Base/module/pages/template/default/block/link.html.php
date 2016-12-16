<li><a href="{url link='pages.add' id=$aPage.page_id}">{_p var='manage'}</a></li>
{if Phpfox::getUserParam('pages.can_add_cover_photo_pages')}
<li>
	<a href="#" onclick="$(this).closest('ul').find('.cover_section_menu_item').toggleClass('hidden'); event.cancelBubble = true; if (event.stopPropagation) event.stopPropagation();return false;">
		{if empty($aPage.cover_photo_id)}
			{_p var='add_a_cover'}
		{else}
			{_p var='change_cover'}
		{/if}
	</a>
</li>
<li class="cover_section_menu_item hidden">
	<a href="{url link='pages.'$aPage.page_id}photo">
		{_p var='choose_from_photos'}
	</a>
</li>
<li class="cover_section_menu_item hidden">
	<a href="#" onclick="$(this).closest('ul').find('.cover_section_menu_item').addClass('hidden'); $Core.box('profile.logo', 500, 'page_id={$aPage.page_id}'); return false;">
		{_p var='upload_photo'}
	</a>
</li>
{if !empty($aPage.cover_photo_id)}
<li class="cover_section_menu_item hidden hidden-sm hidden-md hidden-xs">
	<a role="button" onclick="repositionCoverPhoto('pages',{$aPage.page_id})">
		{_p var='reposition'}
	</a>
</li>
<li class="cover_section_menu_item hidden">
	<a href="#" onclick="$(this).closest('ul').find('.cover_section_menu_item').addClass('hidden'); $.ajaxCall('pages.removeLogo', 'page_id={$aPage.page_id}'); return false;">
		{_p var='Remove Cover'}
	</a>
</li>
{/if}
{/if}
{if Phpfox::getUserParam('pages.can_moderate_pages') || $aPage.user_id == Phpfox::getUserId()}
<li class="item_delete">
	<a href="{url link='pages' delete=$aPage.page_id}" data-message="{_p var='are_you_sure'}" class="no_ajax_link sJsConfirm">
		{_p var='Delete this Page'}
	</a>
</li>
{/if}