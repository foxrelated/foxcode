<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 3990 2012-03-09 15:28:08Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 
//update phpfox_feed set time_update = time_update- (FLOOR(1 + RAND() * 7776000));
?>
{if $bShowCategories}
	{if count($aCategories)}
        {foreach from=$aCategories item=aCategory}
            {if $aCategory.pages}
            <div class="block_clear">
                <div class="title"><a href="{$aCategory.link}">
                        {if Phpfox::isPhrase($this->_aVars['aCategory']['name'])}
                        {_p var=$aCategory.name}
                        {else}
                        {$aCategory.name|convert}
                        {/if}
                    </a></div>
                <div class="content clearfix">
                    <div class="wrapper-items">
                        {foreach from=$aCategory.pages item=aPage}
                        <div class="pages_item">
                            <a class="pages_photo" href="{$aPage.link}">{img server_id=$aPage.profile_server_id title=$aPage.title path='pages.url_image' file=$aPage.image_path suffix='_200' max_width='200' max_height='200' is_page_image=true}</a>
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
                                        {_p var='pages_total_followers', total=$aPage.total_like}
                                        {else}
                                        {_p var='pages_total_follower', total=$aPage.total_like}
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
        {_p var='no_pages_found'}
	</div>
	{/if}
{else}

{if count($aPages)}
{if $sView == 'my' && Phpfox::getUserBy('profile_page_id')}
<div class="message">
	{_p var='note_that_pages_displayed_here_are_pages_created_by_the_page' global_full_name=$sGlobalUserFullName|clean profile_full_name=$aGlobalProfilePageLogin.full_name|clean}
</div>
{/if}
{if !PHPFOX_IS_AJAX }
<div class="wrapper-items">
{/if}
	{foreach from=$aPages item=aPage}
    <div class="pages_item">
        <a class="pages_photo" href="{$aPage.link}">{img server_id=$aPage.profile_server_id title=$aPage.title path='pages.url_image' file=$aPage.image_path suffix='_200' max_width='200' max_height='200' is_page_image=true}</a>
        <div class="pages_info">
            <div>
                <a href="{$aPage.link}" class="link pages_title fw-600">{$aPage.title|clean}</a>
                {if $aPage.category_name}
                <div class="txt-time-color">
                    <i class="fa fa-folder"></i> {if Phpfox::isPhrase($this->_aVars['aPage']['category_name'])}
                    {_p var=$aPage.category_name}
                    {else}
                    {$aPage.category_name}
                    {/if}
                </div>
                {/if}
                <div class="txt-time-color"><i class="fa fa-users"></i>
                    {if $aPage.total_like != 1}
                    {_p var='pages_total_followers', total=$aPage.total_like}
                    {else}
                    {_p var='pages_total_follower', total=$aPage.total_like}
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

{if Phpfox::getUserParam('pages.can_moderate_pages')}
{moderation}
{/if}

{else}
{if !PHPFOX_IS_AJAX }
<div class="extra_info">
	{_p var='no_pages_found'}
</div>
{/if}
{/if}

{/if}