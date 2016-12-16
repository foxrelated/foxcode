<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if !count($aPhotos)}
<div class="extra_info">
    {_p var='no_photos_uploaded_yet'}
    {if Phpfox::getUserId() == $aUser.user_id}
    <ul class="action">
        <li><a href="{url link='photo.upload'}">{_p var='click_here_to_upload_photos'}</a></li>
    </ul>
    {/if}
</div>
{else}
<div class="collection-stage-narrow photos-{$iCount} photos-listing">
{foreach from=$aPhotos item=aPhoto}
    {if ($aPhoto.mature == 0 || (($aPhoto.mature == 1 || $aPhoto.mature == 2) && Phpfox::getUserId() && Phpfox::getUserParam('photo.photo_mature_age_limit') <= Phpfox::getUserBy('age'))) || $aPhoto.user_id == Phpfox::getUserId()}
    <a class="collection-stage-item-narrow my_photo_item" href="{$aPhoto.link}" title="{_p var='title_by_full_name' title=$aPhoto.title|clean full_name=$aPhoto.full_name|clean}">
        <span style="background-image: url({img server_id=$aPhoto.server_id path='photo.url_photo' file=$aPhoto.destination suffix='_500' max_width=500 max_height=500 class="hover_action" title=$aPhoto.title return_url=true})">

        </span>

        <div class="photo-statistic">
            <span class="like-count">{$aPhoto.total_like} {if $aPhoto.total_like == 1}{_p var='like'}{else}{_p var='likes'}{/if}</span>
            <span class="comment-count">{$aPhoto.total_comment} {if $aPhoto.total_comment == 1}{_p var='comment'}{else}{_p var='comments'}{/if}</span>
        </div>
    </a>
    {else}

    {/if}
{/foreach}
</div>
<div class="clear"></div>
{/if}