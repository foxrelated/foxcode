<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="block">
    <div class="title">
        {_p('Group Info')}
    </div>
    <div class="content group_about">
        <div class="founder">
            <div class="user_rows_image">
                {img user=$aUser}
            </div>
            {$aUser|user}
            <div class="txt-time-color">{_p var='Founder'}</div>
        </div>
        {if !empty($about)}
        <div class="about">
        {$about}
        </div>
        {/if}
    </div>
</div>