<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if ((count($aSubMenus) || isset($customMenu))) && Phpfox::isUser()}
<div class="page_breadcrumbs_menu">
	{if Phpfox::isUser()}
	{if (isset($customMenu))}
	<a class="btn btn-success{if (isset($customMenu.css_class))} {$customMenu.css_class}{/if}" href="{$customMenu.url}" {$customMenu.extra}>
		<span></span>{$customMenu.title}
	</a>
	{/if}
	{foreach from=$aSubMenus key=iKey name=submenu item=aSubMenu}
        {if isset($aSubMenu.module) && (isset($aSubMenu.var_name) || isset($aSubMenu.text))}
        <a href="{url link=$aSubMenu.url)}"{if (isset($aSubMenu.css_name))} class="btn btn-success {$aSubMenu.css_name} no_ajax"{else}class="btn btn-success"{/if}>
		<span></span>
		{if isset($aSubMenu.text)}
			{$aSubMenu.text}
		{else}
			{_p var=$aSubMenu.var_name}
		{/if}
        </a>
        {/if}
	{/foreach}
	{else}
	{if Phpfox::getParam('user.allow_user_registration')}
	{/if}
	{/if}
</div>
{/if}


