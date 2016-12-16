<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $uninstall}
	<div class="error_message">
		{_p var='to_continue_with_uninstalling__please_enter_your_admin_login_details'}.
	</div>
	<form method="post" action="{url link='current'}" class="ajax_post">
		<div class="table form-group">
			<div class="table_left">
                {_p var='email'}:
			</div>
			<div class="table_right">
				<input type="text" name="val[email]" class="form-control">
			</div>
		</div>
		<div class="table form-group">
			<div class="table_left">
                {_p var='password'}:
			</div>
			<div class="table_right">
				<input type="password" name="val[password]" autocomplete="off" class="form-control">
			</div>
		</div>
		<div style="display:none;">
        <div class="error_message">
            {_p var='please_re_type_your_ftp_account'}
        </div>
        <div class="session_ftp_account">
            <div class="table">
                <div class="table_left">
                    {_p var='file_upload_method'}:
                </div>
                <div class="table_right">
                    <select name="val[method]"
                            onchange="if (this.value=='file_system') $('.hide_file_system_items').hide(); else $('.hide_file_system_items').show();">
                        {foreach from=$listMethod key=sKey value=sMethod}
                        <option value="{$sKey}" {if $sKey==$currentUploadMethod} selected {/if}>
                        {$sMethod}
                        </option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="hide_file_system_items" {if 'file_system'==$currentUploadMethod} style="display: none" {/if}>
            <div class="table">
                <div class="table_left">
                    {_p var='ftp_host_name'}:
                </div>
                <div class="table_right">
                    <input type="text" class="form-control" placeholder="{_p var='ftp_host_name'}"
                           value="{$currentHostName}" name="val[host_name]"/>
                </div>
            </div>

            <div class="table">
                <div class="table_left">
                    {_p var="Port"}:
                </div>
                <div class="table_right">
                    <input type="text" class="form-control" placeholder="Port" value="{$currentPort}" name="val[port]"/>
                </div>
            </div>

            <div class="table">
                <div class="table_left">
                    {_p var='ftp_user_name'}:
                </div>
                <div class="table_right">
                    <input type="text" class="form-control" placeholder="{_p var='ftp_user_name'}"
                           value="{$currentUsername}" name="val[user_name]"/>
                </div>
            </div>

            <div class="table">
                <div class="table_left">
                    {_p var='ftp_password'}:
                </div>
                <div class="table_right">
                    <input type="text" class="form-control" placeholder="{_p var='ftp_password'}"
                           value="{$currentPassword}" name="val[ftp_password]"/>
                </div>
            </div>
        </div>
        </div>
        </div>
		<div class="table_clear">
			<input type="submit" class="button btn-primary" value="Submit">
		</div>
	</form>
{else}
    {if !PHPFOX_IS_AJAX_PAGE}
    <div id="app-custom-holder" style="display:none; min-height:400px;"></div>
    <div id="app-content-holder">
    {/if}
		{if $customContent}
		<div id="custom-app-content"><i class="fa fa-circle-o-notch fa-spin"></i></div>
		<script>
			var customContent = '{$customContent}', contentIsLoaded = false, extraParams = {$extraParams}, appUrl = '{$appUrl}';
		{literal}
			$Ready(function() {
				if (contentIsLoaded) {
					return;
				}

				contentIsLoaded = true;
				$('.apps_menu a[href="#"]').addClass('active');
				if (extraParams == 1) {
					$('.apps_menu a[href="#"]').attr('href', appUrl).addClass('no_ajax');
				}
				$.ajax({
					url: customContent,
					contentType: 'application/json',
					success: function(e) {
						$('#custom-app-content').html(e.content).show();
						$Core.loadInit();
					}
				});
			});
		{/literal}
		</script>
		{/if}
    {if !PHPFOX_IS_AJAX_PAGE}
	</div>
    <div id="app-details">
        {if (!$ActiveApp.is_core)}
        <ul>
            <li><a {if $App.is_module}class="sJsConfirm" data-message="{_p var='are_you_sure' phpfox_squote=true}"{/if} href="{$uninstallUrl}">{_p var='uninstall'}</a></li>
            {if $export_path && defined('PHPFOX_IS_TECHIE') && PHPFOX_IS_TECHIE}
            <li><a href="{$export_path}">{_p var="Export"}</a></li>
            {/if}
        </ul>
        {/if}
        <div class="app-copyright">
            {if $ActiveApp.vendor}
            ©{$ActiveApp.vendor}
            {/if}
            {if $ActiveApp.credits}
            <div class="app-credits">
                <div>{_p var="Credits"}</div>
                {foreach from=$ActiveApp.credits item=url key=name}
                <ul>
                    <li><a href="{$url}">{$name|clean}</a></li>
                </ul>
                {/foreach}
            </div>
            {/if}
        </div>
    </div>
    {/if}
{/if}