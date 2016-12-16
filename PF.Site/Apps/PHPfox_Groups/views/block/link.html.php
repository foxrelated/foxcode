<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<li><a href="{url link='groups.add' id=$aPage.page_id}">{_p('Manage')}</a></li>
{if user('pf_group_add_cover_photo')}
<li>
    <a href="#"
       onclick="$(this).closest('ul').find('.cover_section_menu_item').toggleClass('hidden'); event.cancelBubble = true; if (event.stopPropagation) event.stopPropagation();return false;">
        {if empty($aPage.cover_photo_id)}
        {_p var='add_a_cover'}
        {else}
        {_p var='change_cover'}
        {/if}
    </a>
</li>
<li class="cover_section_menu_item hidden">
    <a href="{url link='groups.'$aPage.page_id}photo">
        {_p var='choose_from_photos'}
    </a>
</li>
<li class="cover_section_menu_item hidden">
    <a href="#"
       onclick="$(this).closest('ul').find('.cover_section_menu_item').addClass('hidden'); $Core.box('profile.logo', 500, 'groups_id={$aPage.page_id}'); return false;">
        {_p('Upload photo')}
    </a>
</li>
{if !empty($aPage.cover_photo_id)}
<li class="cover_section_menu_item hidden hidden-sm hidden-md hidden-xs">
    <a role="button" onclick="repositionCoverPhoto('groups',{$aPage.page_id})">
        {_p('Reposition')}
    </a>
</li>
<li class="cover_section_menu_item hidden">
    <a href="#"
       onclick="$(this).closest('ul').find('.cover_section_menu_item').addClass('hidden'); $.ajaxCall('groups.removeLogo', 'page_id={$aPage.page_id}'); return false;">
        {_p('Remove Cover')}
    </a>
</li>
{/if}
{/if}
{if user('pf_group_moderate', 0) || $aPage.user_id == Phpfox::getUserId()}
<li class="item_delete">
    <a href="{url link='groups' delete=$aPage.page_id}" class="sJsConfirm"
       class="no_ajax_link">
        {_p('Delete this Group')}
    </a>
</li>
{/if}