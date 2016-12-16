<?php
    defined('PHPFOX') or exit('NO DICE!');
?>
{if !Phpfox::getUserBy('profile_page_id') && Phpfox::isUser() && isset($aPage)}
{if isset($aPage) && isset($aPage.is_reg) && $aPage.is_reg}
{else}
{if isset($aPage) && $aPage.is_liked}
<a href="#" class="pages_like_join pages_unlike_unjoin"
   onclick="$(this).remove(); $.ajaxCall('like.delete', 'type_id=groups&amp;item_id={$aPage.page_id}&amp;reload=1'); return false;">
    {_p('Unjoin')}
</a>
{else}

<a href="#" class="pages_like_join"
   onclick="$(this).remove(); {if $aPage.reg_method == '1' && !isset($aPage.is_invited)} $.ajaxCall('groups.signup', 'page_id={$aPage.page_id}'); {else}$.ajaxCall('like.add', 'type_id=groups&amp;item_id={$aPage.page_id}&amp;reload=1');{/if} return false;">
    {_p('Join')}
</a>

{/if}
{/if}
{/if}
