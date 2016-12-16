<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{foreach from=$aWidgetBlocks item=aWidgetBlock}
<div class="block">
    <div class="title">{$aWidgetBlock.title|clean}</div>
    <div class="content">
        {$aWidgetBlock.text|parse}
    </div>
</div>
{/foreach}