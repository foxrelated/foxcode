<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: category.html.php 3917 2012-02-20 18:21:08Z Raymond_Benc $
 */
 
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