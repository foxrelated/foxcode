<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Blog
 * @version 		$Id: index.html.php 7290 2014-04-30 19:14:20Z Fern $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if isset($bSpecialMenu) && $bSpecialMenu == true}
{template file='blog.block.specialmenu'}
{/if}
{if !count($aBlogs)}
{if !PHPFOX_IS_AJAX}
<div class="extra_info">
    {_p var='no_blogs_found'}
</div>
{/if}
{else}
{if !PHPFOX_IS_AJAX}
<div class="item-collections item-collections-2">
    {/if}
    {foreach from=$aBlogs name=blog item=aItem}
    {item name='BlogPosting'}
    {template file='blog.block.entry'}
    {/item}
    {/foreach}
    {pager}

    {if !PHPFOX_IS_AJAX && (Phpfox::getUserParam('blog.can_approve_blogs') || Phpfox::getUserParam('blog.delete_user_blog'))}
    {moderation}
    {/if}
    {if !PHPFOX_IS_AJAX}
</div>
{/if}
{/if}
