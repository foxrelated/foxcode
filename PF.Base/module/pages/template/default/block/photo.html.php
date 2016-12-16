<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="profiles_banner {if $aCoverPhoto !== false}has_cover{/if}" {if $sCoverDefaultUrl}style="background-image:url({$sCoverDefaultUrl})"{/if}>
	<div class="breadcrumbs_menu"></div>
	<div class="profiles_banner_bg">
		{if $aCoverPhoto !== false}
		<div class="cover_bg"></div>
		<div class="cover" id="cover_bg_container">
			{img server_id=$aCoverPhoto.server_id path='photo.url_photo' file=$aCoverPhoto.destination suffix='_1024' title=$aCoverPhoto.title class="hidden-xs cover_photo"}
			{img server_id=$aCoverPhoto.server_id path='photo.url_photo' file=$aCoverPhoto.destination suffix='_1024' title=$aCoverPhoto.title class="visible-xs"}
		</div>
		{/if}
		<div class="cover_shadown"></div>
		<div class="profiles_info">
			<h1 title="{$aPage.title|clean}"><a>{$aPage.title|clean}</a></h1>
			<div class="profiles_extra_info">
				<span>
                    {if Phpfox::isPhrase($this->_aVars['aPage']['category_name'])}
                    {_p var=$aPage.category_name}
                    {else}
                    {$aPage.category_name|convert}
                    {/if}
                </span>
				<span class="fw-bold">
                    {$aPage.total_like} {if $aPage.total_like >= 2} {_p var='followers'}{else}{_p var='follower'}{/if}
                </span>
			</div>
		</div>
		<div class="profile_image">
			<div class="profile_image_holder">
				<a>
					{img thickbox=true server_id=$aPage.image_server_id title=$aPage.title path='pages.url_image' file=$aPage.pages_image_path suffix='_200_square' no_default=false max_width=200}
				</a>
			</div>
		</div>
		{if (Phpfox::getUserParam('pages.can_moderate_pages') || $aPage.is_admin)}
		{if $aPage.view_id == '1' && Phpfox::getUserParam('pages.can_moderate_pages')}
		<div class="profiles_admin_actions">
			<div>
				<a href="#" class="button btn-primary btn-approved btn-lg" onclick="$(this).hide(); $('#js_item_bar_approve_image').show(); $.ajaxCall('pages.approve', 'page_id={$aPage.page_id}'); return false;">
					{_p var='approve'}
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
					{template file='pages.block.link'}
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
			{template file='pages.block.joinpage'}
			{if !$aPage.is_admin && Phpfox::getUserParam('pages.can_claim_page') && empty($aPage.claim_id)}
			<a href="#?call=contact.showQuickContact&amp;height=600&amp;width=600&amp;page_id={$aPage.page_id}" class="inlinePopup js_claim_page" title="{_p var='claim_page'}">
				{_p var='claim_page'}
			</a>
			{/if}
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
		<li><a href="{$aPageMenu.url}">{if (isset($aPageMenu.favicon))}<i class="fa fa-{$aPageMenu.favicon}"></i> {/if}{$aPageMenu.phrase}</a></li>
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
	.profiles_banner_bg .cover img.cover_photo
	{l}
	position: relative;
	left: 0;
	top: {$iConverPhotoPosition}px;
	{r}
</style>