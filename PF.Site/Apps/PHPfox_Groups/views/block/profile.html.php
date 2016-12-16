<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="user_rows_mini pages_listing">
    {foreach from=$aPagesList name=pages item=aUser}
    <div class="user_rows">
        <div class="user_rows_image">
            {img user=$aUser suffix='_120_square'}
        </div>
        <div class="page_info">
            {$aUser|user}
        </div>
    </div>
    {/foreach}
</div>