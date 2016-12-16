<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Forum
 * @version 		$Id: index.html.php 140 2009-01-30 13:04:09Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $aCallback === null && count($aForums)}

	{if isset($isSubForumList)}
	<div class="content">
	{/if}

		{foreach from=$aForums name=forums item=aForum}
			{if $aForum.is_category}
			<div class="block forum_holder{if isset($aForum.toggle_class)} {$aForum.toggle_class}{/if}" data-forum-id="{$aForum.forum_id}">
				<div class="title">
					<a href="{permalink module='forum' id=$aForum.forum_id title=$aForum.name}"{if !empty($aForum.description)} title="{softPhrase var=$aForum.description}" {/if}>{softPhrase var=$aForum.name}</a>
					<span class="toggle">
						<i class="fa fa-angle-down"></i>
					</span>
				</div>
				<div class="content">
					{foreach from=$aForum.sub_forum item=aForum}
					{template file='forum.block.forum'}
					{/foreach}
				</div>
			</div>
			{else}

				{if count($aForum.sub_forum)}
                    {template file='forum.block.forum'}
				{else}
				{template file='forum.block.forum'}
				{/if}
			{/if}

		{/foreach}

	{if isset($isSubForumList)}
	</div>
	{/if}

{/if}