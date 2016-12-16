<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<ul class="action">
    {foreach from=$aCategories item=aCategory}
    <li class="category"><a href="{$aCategory.link}">
            {if Phpfox::isPhrase($this->_aVars['aCategory']['name'])}
            {_p var=$aCategory.name}
            {else}
            {$aCategory.name|convert}
            {/if}
        </a></li>
    {/foreach}
</ul>