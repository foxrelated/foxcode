<div class="_block_error">
	{if isset($aPageSectionMenu) && count($aPageSectionMenu)}
	<div class="page_section_menu page_section_menu_header">
		{if $aPageExtraLink !== null}
		<a href="{$aPageExtraLink.link}" class="page_section_menu_link" title="{$aPageExtraLink.phrase}">
			<span>{$aPageExtraLink.phrase}</span>
		</a>
		{/if}
		<div class="">
			<ul class="nav nav-tabs nav-justified">
				{foreach from=$aPageSectionMenu key=sPageSectionKey item=sPageSectionMenu name=pagesectionmenu}
				<li {if ($phpfox.iteration.pagesectionmenu == 1 && !$bPageIsFullLink) || ($bPageIsFullLink && $sPageSectionKey == $sPageCurrentUrl)} class="active"{/if}><a href="{if $bPageIsFullLink}{$sPageSectionKey}{else}#{$sPageSectionKey}{/if}" {if !$bPageIsFullLink}rel="{$sPageSectionMenuName}_{$sPageSectionKey}"{/if}>{$sPageSectionMenu}</a></li>
				{/foreach}
			</ul>
		</div>
		<div class="clear"></div>
	</div>
	{/if}
	{section_menu_js}
</div>

{if isset($sPublicMessage) && $sPublicMessage && !is_bool($sPublicMessage)}
<div class="public_message" id="public_message">
	{$sPublicMessage}
</div>
<script type="text/javascript">
	$Behavior.template_error = function()
	{l}
	$('#public_message').show();
	{r};
</script>
{else}
<div class="public_message" id="public_message"></div>
{/if}
<div id="pem"><a href="#"></a></div>
<div id="core_js_messages">
	{if isset($aErrors) && count($aErrors)}
	{foreach from=$aErrors item=sErrorMessage}
	<div class="error_message {if defined('PHPFOX_ERROR_AS_WARNING') && PHPFOX_ERROR_AS_WARNING}warning_message{/if}">{$sErrorMessage}
    {if defined('PHPFOX_ERROR_FORCE_LOGOUT') && PHPFOX_ERROR_FORCE_LOGOUT}
    <button class="button btn-default" onclick="$Core.reloadPage();">{_p var="ok"}</button>
    {/if}
    </div>
	{/foreach}
	{unset var=$sErrorMessage var2=$sample}

	{/if}
</div>