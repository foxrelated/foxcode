<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="content_attachment_{$aRow.attachment_id}" class="is-active content_attachment">
    <div id="file-icon-wrapper" class="icon-wrapper">
        {if $aRow.is_image}
            <i class="fa fa-file-image-o" aria-hidden="true"></i>
        {elseif $aRow.is_video}
            <i class="fa fa-file-video-o" aria-hidden="true"></i>
        {else}
            <i class="fa fa-file" aria-hidden="true"></i>
        {/if}
    </div>

    <div id="title">
        <span id="title-area">
            <a id="file-link" class="no_ajax" tabindex="0" role="link" href="{url link='attachment.download' id= $aRow.attachment_id}">
                {$aRow.file_name}
            </a>
        </span>

        <div id="description">{$aRow.description}</div>
    </div>

    <div id="using">
        {$aRow.using}
    </div>

    <div id="remove-wrapper" class="icon-wrapper">
        <span onclick="tb_show('{_p var='Notice'}', $.ajaxBox('attachment.deleteAttachment', 'height=400&amp;width=600&amp;TB_inline=1&amp;call=attachment.deleteAttachment&amp;type=delete&amp;item_id={$aRow.attachment_id}'));" id="remove" title="{_p var='Remove from list'}" role="button">
            <i class="fa fa-trash"></i></span>
    </div>

</div>