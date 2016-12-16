<?php
/**
 * Date: 5/12/16
 * Time: 16:02
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="block_search">
    <form method="post" action="{url link='admincp.attachment.manage'}">
        <div class="table_header">
            {_p var="Attachment filter"}
        </div>
        <div class="table form-group">
            <div class="table_left">
                {_p var="Display"}:
            </div>
            <div class="table_right">
                {$aFilters.display}
            </div>
            <div class="clear"></div>
        </div>
        <div class="table form-group">
            <div class="table_left">
                {_p var="Sort by"}:
            </div>
            <div class="table_right">
                {$aFilters.sort} {$aFilters.sort_by}
            </div>
        </div>
        <div class="table form-group">
            <div class="table_left">
                {_p var="Attachment name"}:
            </div>
            <div class="table_right">
                {$aFilters.name}
            </div>
            <div class="clear"></div>
        </div>
        <div class="table_clear">
            <input type="submit" name="search[submit]" value="{_p var='submit'}" class="button btn-primary" />
        </div>
    </form>
</div>
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
    {template file='attachment.block.item-admin'}
    {/foreach}
    {if $close_div}
</div>
{/if}
{pager}
</div>
{elseif !PHPFOX_IS_AJAX}
{_p var="No attachments found"}
{/if}