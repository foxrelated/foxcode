<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if !empty($bShowCategories)}
{if count($aCategories)}
{foreach from=$aCategories item=aCategory}
{if $aCategory.pages}
<div class="block_clear">
    <div class="title"><a href="{$aCategory.link}">
            {softPhrase var=$aCategory.name}
        </a></div>
    <div class="content clearfix">
        <div class="wrapper-items">
            {foreach from=$aCategory.pages item=aPage}
            <div class="pages_item">
                <a class="pages_photo" href="{$aPage.link}">{img server_id=$aPage.profile_server_id
                    title=$aPage.title path='pages.url_image' file=$aPage.image_path suffix='_200' max_width='200'
                    max_height='200' is_page_image=true}</a>
                <div class="pages_info">
                    <div>
                        <a href="{$aPage.link}" class="link pages_title fw-600">{$aPage.title|clean}</a>
                        {if $aPage.category_name}
                        <div class="txt-time-color">
                            <i class="fa fa-folder"></i> {if Phpfox::isPhrase($this->_aVars['aPage']['category_name'])}{_p var=$aPage.category_name}{else}{$aPage.category_name}{/if}
                        </div>
                        {/if}
                        <div class="txt-time-color"><i class="fa fa-users"></i>
                            {if $aPage.total_like != 1}
                            {_p var='groups_total_members', total=$aPage.total_like}
                            {else}
                            {_p var='groups_total_member', total=$aPage.total_like}
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
            {/foreach}
        </div>
    </div>
</div>
{/if}
{/foreach}
{/if}
{if $iCountPage == 0}
<div class="extra_info">
    {_p('No groups found.')}
</div>
{/if}
{else}

{if count($aPages)}
{if $sView == 'my' && Phpfox::getUserBy('profile_page_id')}
<div class="message">
    {_p var='Note that Groups displayed here are groups created by the Group (!<< global_full_name >>!) and not by the parent user (!<< profile_full_name >>!).' global_full_name=$sGlobalUserFullName|clean profile_full_name=$aGlobalProfilePageLogin.full_name|clean}
</div>
{/if}
{if !PHPFOX_IS_AJAX }
<div class="wrapper-items">
{/if}
    {foreach from=$aPages item=aPage}
    <div class="pages_item">
        <a class="pages_photo" href="{$aPage.link}">{img server_id=$aPage.profile_server_id title=$aPage.title
            path='pages.url_image' file=$aPage.image_path suffix='_200' max_width='200' max_height='200'
            is_page_image=true}</a>
        <div class="pages_info">
            <div>
                <a href="{$aPage.link}" class="link pages_title fw-600">{$aPage.title|clean}</a>
                {if $aPage.category_name}
                <div class="txt-time-color">
                    <i class="fa fa-folder"></i> {if Phpfox::isPhrase($this->_aVars['aPage']['category_name'])}{_p var=$aPage.category_name}{else}{$aPage.category_name}{/if}
                </div>
                {/if}
                <div class="txt-time-color"><i class="fa fa-users"></i>
                    {if $aPage.total_like != 1}
                    {_p var='groups_total_members', total=$aPage.total_like}
                    {else}
                    {_p var='groups_total_member', total=$aPage.total_like}
                    {/if}
                </div>
            </div>
        </div>
    </div>
    {/foreach}
    {pager}
{if !PHPFOX_IS_AJAX }
</div>
{/if}

{if user('pf_group_moderate', '0') == '1' }
{moderation}
{/if}

{/if}

{/if}