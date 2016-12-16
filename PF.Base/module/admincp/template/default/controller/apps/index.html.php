<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if isset($vendorCreated)}
	<i class="fa fa-spin fa-circle-o-notch"></i>
	{literal}
		<script>
			$Ready(function() {
				$Behavior.addDraggableToBoxes();
				$('.admin_action_menu .popup').trigger('click');
			});
		</script>
	{/literal}
{else}

	<div class="admincp_apps_holder">
        {if isset($warning) && $warning}
        <section class="apps">
            <div class="text-danger text-center">{$warning}</div>
        </section>
        {/if}

		<section class="apps">
            <h1>{_p var="Manage Apps"}</h1>
			<div class="admincp_apps_installed">
                <table class="table table-striped table-hover table-middle">
                    <thead>
                    <tr>
                        <th style="width: 30px;"></th>
                        <th>{_p var="name"}</th>
                        <th style="width: 120px;">{_p var="version"}</th>
                        <th style="width: 120px;">{_p var="latest"}</th>
                        <th>{_p var="author"}</th>
                        <th style="width: 80px;">{_p var="Active"}</th>
                    </tr>
                    </thead>
                    <tbody>
                        {foreach from=$apps item=app}
                        <tr>
                            <td><a href="{url link='admincp.app' id=$app.id}">{$app.icon}</a></td>
                            <td>
                                {if $app.is_active}<a href="{url link='admincp.app' id=$app.id}">{/if}
                                    {$app.name|clean}
                                {if $app.is_active}</a>{/if}
                            </td>
                            <td>
                                {if $app.is_phpfox_default}
                                {_p var='core'}
                                {else}
                                {$app.version}
                                {/if}
                            </td>
                            <td>
                                {if $app.is_phpfox_default}
                                {_p var='core'}
                                {else}
                                {$app.latest_version}
                                {/if}
                                {if $app.have_new_version}
                                <br />
                                <a href="{$app.have_new_version}" target="_blank">
                                    {_p var='upgrade_now'}
                                </a>
                                {/if}
                            </td>
                            <td>
                                {if !empty($app.publisher_url)}
                                <a href="{$app.publisher_url}" target="_blank">
                                    {/if}
                                    {$app.publisher}
                                    {if !empty($app.publisher_url)}
                                </a>
                                {/if}
                            </td>
                            <td>
                                {if $app.allow_disable}
                                <div class="js_item_is_active"{if !$app.is_active} style="display:none;"{/if}>
                                    <a href="#?call=admincp.updateModuleActivity&amp;id={$app.id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}">{img theme='misc/bullet_green.png' alt=''}</a>
                                </div>
                                <div class="js_item_is_not_active"{if $app.is_active} style="display:none;"{/if}>
                                     <a href="#?call=admincp.updateModuleActivity&amp;id={$app.id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}">{img theme='misc/bullet_red.png' alt=''}</a>
                                </div>
                                {/if}
                            </td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
			</div>
		</section>
		<section class="preview">
			<h1>{_p var='featured_apps'}</h1>
			<div class="phpfox_store_featured" data-type="apps" data-parent="{url link='admincp.store' load='apps'}">
			</div>
		</section>
	</div>

{/if}