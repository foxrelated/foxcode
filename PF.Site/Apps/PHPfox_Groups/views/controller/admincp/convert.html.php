<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="table">
    {if $iConvertedUserId}
        {_p var="Groups are converting. Remaining groups now is:"} {$iNumberGroups}
    {else}
        {if $iNumberGroups}
        <div class="well well-lg">
            <h2>
                {_p var="You have !<< number >>! old groups (from pages)", number=$iNumberGroups}.
                <br/>
                {_p var="Do you want to convert to new groups system?"}:
            </h2>
            <div class="well-sm">
                <a class="btn-success btn" href="{url link='admincp.groups.convert', convert=true}">{_p var="Yes, convert directly"}</a>
                <a class="btn-danger btn" href="{url link='admincp.groups.convert', convert=true  cron=true}">{_p var="Yes, convert via cron job"}</a>
            </div>
            </div>
        <div class="well well-sm">
            {_p var="Learn more about this"} <a target="_blank" href="https://docs.phpfox.com/display/FOX4MAN/Enabling+and+Managing+the+Groups+App">{_p var="Visit here"}</a>
        </div>
        {else}
        {_p var="There is no old group to convert"}.
        {/if}
    {/if}
</div>