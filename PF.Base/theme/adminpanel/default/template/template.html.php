<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr{*$sLocaleDirection*}" lang="{$sLocaleCode}">
	<head>
		<title>{title}</title>
	{header}
	</head>
	<body>
		<div id="admincp_base"></div>
		<div id="global_ajax_message"></div>

		<div id="header">
			<a href="#" class="header_logo">{_p var='module_admincp'}</a>
		</div>

		<div id="top">
			<div class="nano">
				<div class="main_menu_holder nano-content">

					<div class="admincp_user">
						<div class="admincp_user_image">
							{img user=$aUserDetails suffix='_50_square'}
						</div>
						<div class="admincp_user_content">
							{$aUserDetails|user}
							<div>
								<a href="{url link=''}">{_p var='view_site'}</a>
							</div>
						</div>
					</div>

					<ul class="main_menu">
						{foreach from=$aAdminMenus key=sPhrase item=sLink}
						{if is_array($sLink)}
						{if count($sLink)}
						<li class="main_menu_link_li"><a class="main_menu_link" href="#">{$sPhrase}</a>
							<div class="main_sub_menu">
								{if strpos($sPhrase, 'fa-cog')}
								<div class="admincp_search_settings">
									<span class="remove"><i class="fa fa-remove"></i></span>
									<input type="text" name="setting" placeholder="{_p var='search_settings_dot'}" autocomplete="off">
									<div class="admincp_search_settings_results"></div>
								</div>
								{/if}
								<ul>
								{foreach from=$sLink key=sPhrase2 item=sLink2}
									{if is_array($sLink2)}
									<li class="{if isset($sLink2.highlight) && $sLink2.highlight} focus{/if}">
										<a href="{$sLink2.url}" class="popup">
											{$sPhrase2}{if isset($sLink2.message)}<span>{$sLink2.message}</span>{/if}
										</a>
									</li>
									{elseif is_numeric($sPhrase2)}
									<li class="separator">{$sLink2}</li>
									{else}
									<li><a href="{url link=$sLink2}">{$sPhrase2}</a></li>
									{/if}
								{/foreach}
								</ul>
							</div>
						</li>
						{/if}
						{elseif is_numeric($sPhrase)}
						<li class="separator">{$sLink}</li>
						{else}
						<li><a href="{url link=''$sLink''}" class="main_menu_link">{$sPhrase}</a></li>
						{/if}
						{/foreach}
					</ul>
				</div>
			</div>
		</div>
	</div>

			<div class="main_title_holder">
                {if isset($aAdmincpBreadCrumb) && is_array($aAdmincpBreadCrumb)}
                <div class="btn-group">
                    {foreach from=$aAdmincpBreadCrumb key=sUrl item=sPhrase}
                        <a class="" href="{$sUrl}">{$sPhrase}</a>
                        {if $sPhrase != end($aAdmincpBreadCrumb)}
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                        {/if}
                    {/foreach}
                </div>
                {else}
                {$sSectionTitle}
                {/if}
				{if isset($aActionMenu)}
				<div class="admin_action_menu">
					<ul>
						{foreach from=$aActionMenu key=sPhrase item=sUrl}
						<li>
							{if is_array($sUrl)}
							<a href="{$sUrl.url}" class="{if isset($sUrl.class)}{$sUrl.class}{/if}"{if isset($sUrl.custom)} {$sUrl.custom}{/if}>{$sPhrase}</a>
							{else}
							<a href="{$sUrl}">{$sPhrase}</a>
							{/if}
						</li>
						{/foreach}
					</ul>
				</div>

				{/if}
				<div id="admincp_settings_control" style="display: none">
					<div class="js_item_is_active"{if !$bAutoSaveSettings} style="display:none;"{/if}>
						<a title="{_p var="Turn off Auto-save"}" onclick="changeAutoSave(false)" href="#?call=admincp.updateAutoSaveSettings&amp;active=0" class="js_item_active_link">{img theme='misc/bullet_green.png' alt=''}</a>
					</div>
					<div class="js_item_is_not_active"{if $bAutoSaveSettings} style="display:none;"{/if}>
						<a title="{_p var="Turn on Auto-save"}" onclick="changeAutoSave(true)" href="#?call=admincp.updateAutoSaveSettings&amp;active=1" class="js_item_active_link">{img theme='misc/bullet_red.png' alt=''}</a>
					</div>
				</div>
			</div>

			<div class="main_holder">					
				<div id="js_content_container">					
					<div id="main">

						{if isset($aSectionAppMenus)}
						<div class="apps_menu {if $$ActiveApp.admin_cp_menu_ajax} ajax_menu {/if}">
							{if !$ActiveApp.is_module}
							<div class="active_app" data-app-id="{$ActiveApp.id}"></div>
							{/if}
                            <div class="app_title">
							{$ActiveApp.icon}
                            <span class="app_name">{$ActiveApp.name|clean}</span>
                            </div>
							<ul>
							{foreach from=$aSectionAppMenus key=sPhrase item=aMenu}
								<li><a href="{if (substr($aMenu.url, 0, 1) == '#')}{$aMenu.url}{else}{url link=$aMenu.url}{/if}" {if isset($aMenu.is_active) && $aMenu.is_active} class="active"{/if}>{$sPhrase}</a></li>
							{/foreach}
							</ul>

							{if (isset($has_upgrade) && $has_upgrade)}
							<div class="upgrade_product_holder">
								<p>{_p var="There is an update available for this product."}</p>
								<a href="{$store.install_url}">{_p var="Update Now"}</a>
							</div>
							{/if}

							<div class="apps_version">
								v{$ActiveApp.version}
							</div>
						</div>
						<div class="apps_content">
						{/if}

							{error}
							<div class="_block_content">
								{content}
							</div>

						{if isset($aSectionAppMenus)}
						</div>
						{/if}

					</div>
				</div>		
				
				<div id="copyright">
					{param var='core.site_copyright'} {product_branding}
				</div>		
						
			</div>		
		{plugin call='theme_template_body__end'}	
        {loadjs}
	</body>
</html>