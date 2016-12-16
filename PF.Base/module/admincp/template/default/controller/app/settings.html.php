<?php
defined('PHPFOX') or exit('NO DICE!');
/**
 * @author Neil <neil@phpfox.com>
 */
?>
<script>
    var set_active = false, group_class = '';
    {if ($group_class)}
    group_class = '{$group_class}';
    {/if}
    {literal}
    $Ready(function() {
        if (set_active) {
            return;
        }
        set_active = true;
        $('._is_app_settings').show();
        $('.apps_menu a[href="#settings"]').addClass('active');
        if (group_class) {
            $('.' + group_class + ':not(.is_option_class)').show();

            var do_this = function() {
                var driver = $(this).data('option-class').split('='),
                    s_key = driver[0],
                    s_value = driver[1],
                    i = $(this),
                    t = $('.__data_option_' + s_key + '');

                if (t.length) {
                    if (t.val() == s_value && i.hasClass(group_class)) {
                        i.show();
                    } else {
                        i.hide();
                    }

                    t.change(function () {
                        $('.is_option_class').each(do_this);
                    });
                }
            };

            $('.is_option_class').each(do_this);
        }
    });
    {/literal}
</script>
{if !PHPFOX_IS_AJAX_PAGE}
<div id="app-custom-holder" style="display:none; min-height:400px;"></div>
<div id="app-content-holder">
{/if}
    {if isset($settings) && $settings}
    <section class="app_grouping _is_app_settings">
        {if (!$group_class)}
        <h1>{_p var='app_settings'}{if ($App.admincp_help)} <a href="{$App.admincp_help}" target="_blank"><i class="fa fa-info-circle"></i></a>{/if}</h1>
        {/if}
        <form class="on_change_submit" method="post" action="{url link='current'}">
            {foreach from=$settings item=setting key=var}
            <div
                {if (isset($setting.group_class) && $setting.group_class) || (isset($setting.option_class) && $setting.option_class)}
                    class="{$setting.group_class} {if (isset($setting.option_class) && $setting.option_class)} is_option_class{/if}" {if (isset($setting.option_class) && $setting.option_class)}
                            data-option-class="{$setting.option_class}"
                        {/if}
                    {if $group_class != $setting.group_class}
                    style="display:none;"
                    {/if}
                {/if}
            >
            <div class="table_header2 settings">
                {$setting.info}
            </div>
            <div class="table3 settings">
                <div class="row_right">
                    {if $setting.type == 'input:text'}
                    <input type="text" name="setting[{$var}]" value="{$setting.value|clean}" class="form-control">
                    {elseif $setting.type == 'password'}
                    <input type="password" name="setting[{$var}]" value="{$setting.value|clean}" class="form-control">
                    {elseif $setting.type == 'input:radio'}
                    <div class="item_is_active_holder">
								<span class="js_item_active item_is_active">
									<input type="radio"{if $setting.value == 1} checked="checked"{/if} name="setting[{$var}]" value="1"> {_p var='yes'}
								</span>
                        <span class="js_item_active item_is_not_active">
									<input type="radio"{if $setting.value != 1} checked="checked"{/if} name="setting[{$var}]" value="0"> {_p var='no'}
								</span>
                    </div>
                    {elseif $setting.type == 'select'}
                    <select name="setting[{$var}]" class="__data_option_{$var}">
                        {foreach item=option key=name from=$setting.options}
                        <option value="{$name}"{if ($name == $setting.value)} selected="selected"{/if}>{$option}</option>
                        {/foreach}
                    </select>
                    {/if}
                </div>
                {if !empty($setting.description)}
                <div class="extra_info">
                    { $setting.description }
                </div>
                {/if}
            </div>
</div>
{/foreach}
<div class="table_clear submit_btn" {if $bAutoSaveSettings}style="display: none;"{/if}>
<input type="submit" class="btn btn-danger" value="{_p var='Save Changes'}">
</div>
</form>
</section>

{$extra}
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