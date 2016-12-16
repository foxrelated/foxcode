<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $aRows && count($aRows)}
    <div id="attachment_manage">
        {assign var='close_div' value=0}
        {foreach from=$aRows key=iKey item=aRow}
            {if isset($aRow.time_name)}
                {if $close_div}
                    </div>
                    {assign var='close_div' value=0}
                {/if}
                <div class="attachment_time_same_block">
                <h3 class="time">{$aRow.time_name}</h3>
                {assign var='close_div' value=1}
            {/if}
            {template file='attachment.block.item'}
        {/foreach}
        {if $close_div}
            </div>
        {/if}
        {pager}
    </div>
{elseif !PHPFOX_IS_AJAX}
    {_p var="No attachments found"}
{/if}