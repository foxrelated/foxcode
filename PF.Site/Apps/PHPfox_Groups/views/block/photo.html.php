<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="profiles_banner {if $aCoverPhoto !== false}has_cover{/if}" {if
     $sCoverDefaultUrl}style="background-image:url({$sCoverDefaultUrl})" {/if}>
<div class="breadcrumbs_menu"></div>
<div class="profiles_banner_bg">
    {if $aCoverPhoto !== false}
    <div class="cover_bg"></div>
    <div class="cover" id="cover_bg_container">
        {img server_id=$aCoverPhoto.server_id path='photo.url_photo' file=$aCoverPhoto.destination suffix='_1024'
        title=$aCoverPhoto.title class="hidden-xs cover_photo"}
        {img server_id=$aCoverPhoto.server_id path='photo.url_photo' file=$aCoverPhoto.destination suffix='_1024'
        title=$aCoverPhoto.title class="visible-xs"}
    </div>
    {/if}
    <div class="cover_shadown"></div>
    <div class="profiles_info groups_profile">
        <h1 title="{$aPage.title|clean}"><a>{$aPage.title|clean}</a></h1>
        <div class="profiles_extra_info">
            <span>
                {if $aPage.reg_method == 0}
                <i class="fa fa-privacy fa-privacy-0"></i>&nbsp;{_p var='Public Group'}
                {elseif $aPage.reg_method == 1}
                <i class="fa fa-unlock-alt" aria-hidden="true"></i>&nbsp;{_p var='Closed Group'}
                {elseif $aPage.reg_method == 2}
                <i class="fa fa-lock" aria-hidden="true"></i>&nbsp;{_p var='Secret Group'}
                {/if}
            </span>
            <span>
                {if Phpfox::isPhrase($this->_aVars['aPage']['category_name'])}
                    {_p var=$aPage.category_name}
                {else}
                    {$aPage.category_name|convert}
                {/if}
            </span>
            <span class="fw-bold">
                {$aPage.total_like} {if $aPage.total_like != 1} {_p('Members')}{else}{_p('Member')}{/if}
            </span>
        </div>
    </div>
    {if (user('pf_group_moderate', 0) || $aPage.is_admin)}
    {if $aPage.view_id == '1' && user('pf_group_moderate', 0)}
    <div class="profiles_admin_actions">
        <div>
            <a href="#" class="button btn-primary btn-approved btn-lg"
               onclick="$(this).hide(); $('#js_item_bar_approve_image').show(); $.ajaxCall('groups.approve', 'page_id={$aPage.page_id}'); return false;">
                {_p('Approve')}
            </a>
        </div>
    </div>
    {/if}
    <div class="profiles_owner_actions">
        <div class="dropdown">
            <a class="icon_btn" role="button" data-toggle="dropdown">
                <i class="fa fa-cog"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
                {template file='groups.block.link'}
            </ul>
        </div>

        {if isset($aSubPagesMenus) && count($aSubPagesMenus)}
        {foreach from=$aSubPagesMenus key=iKey name=submenu item=aSubMenu}
        <a href="{url link=$aSubMenu.url)}" class="btn btn-success">
            {if (isset($aSubMenu.title))}
            {$aSubMenu.title}
            {else}
            {_p var=$aSubMenu.var_name}
            {/if}
        </a>
        {/foreach}
        {/if}

    </div>
    {/if}
    <div class="profile_viewer_actions">
        {template file='groups.block.joinpage'}
    </div>
</div>
</div>
<div class="profiles_menu hidden-xs">
    <ul class="container-fluid">
        {foreach from=$aPageMenus item=aPageMenu name=link}
        {if $phpfox.iteration.link == 8}
        <li class="dropdown">
            <a role="button" data-toggle="dropdown" class="explore"><i class="fa fa-ellipsis-h"></i></a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a href="{$aPageMenu.url}">{$aPageMenu.phrase}</a>
                </li>
                {else}
                <li><a href="{$aPageMenu.url}">{$aPageMenu.phrase}</a></li>
                {/if}
                {/foreach}
            </ul>
        </li>
    </ul>
</div>
<div class="profiles_menu visible-xs">
    <ul class="container-fluid">
        {foreach from=$aPageMenus item=aPageMenu name=link}
        {if $phpfox.iteration.link == 4 }
        <li class="pull-right dropdown">
            <a role="button" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a href="{$aPageMenu.url}">{$aPageMenu.phrase}</a>

                </li>
                {else}
                <li>
                    <a href="{$aPageMenu.url}">{$aPageMenu.phrase}</a>

                </li>
                {/if}
                {/foreach}
            </ul>
        </li>
    </ul>
</div>

<style type="text/css">
    .profiles_banner_bg .cover img.cover_photo {l}
    position: relative;
    left:0;
    top: { $iConverPhotoPosition }px;{r}
</style>