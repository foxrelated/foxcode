<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<ul class="list-group page-action">
    {if $aPage.is_admin}
    <li><a href="{url link='groups.add' id=$aPage.page_id}"><i class="fa fa-pencil-square-o"></i>{_p('Edit group')}</a></li>
    {/if}
    {if !Phpfox::getUserBy('profile_page_id')}
    <li id="js_add_pages_unlike" {if !$aPage.is_liked} style="display:none;" {/if}>
        <a href="#" onclick="$(this).parent().hide(); $('#pages_like_join_position').show(); $.ajaxCall('like.delete', 'type_id=groups&amp;item_id={$aPage.page_id}'); return false;"><i class="fa fa-sign-out"></i>{_p('Unjoin')}</a></li>
    {/if}
    {if Phpfox::isModule('report') && ($aPage.user_id != Phpfox::getUserId())}
    <li><a href="#?call=report.add&amp;height=210&amp;width=400&amp;type=groups&amp;id={$aPage.page_id}" class="inlinePopup" title="{_p var='Report this Group'}"><i class="fa fa-flag"></i>{_p var='report'}</a></li>
    {/if}
</ul>