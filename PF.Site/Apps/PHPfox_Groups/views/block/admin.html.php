<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<ul class="block_listing">
    {foreach from=$aPageAdmins name=pageadmins item=aPageAdmin}
    <li>
        <div class="block_listing_image">
            {img user=$aPageAdmin suffix='_50_square' max_width=50 max_height=50}
        </div>
        <div class="block_listing_title" style="padding-left:56px;">
            {$aPageAdmin|user:'':'':40}
            {if $phpfox.iteration.pageadmins == 1}
            <div class="extra_info">
                {_p('Founder')}
            </div>
            {/if}
        </div>
        <div class="clear"></div>
    </li>
    {/foreach}
</ul>