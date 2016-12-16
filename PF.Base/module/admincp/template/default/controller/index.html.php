<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if Phpfox::demoModeActive()}
<div class="message">
    AdminCP is set to "Demo Mode". Certain actions are limited when in this mode and acts as a Read Only control panel.
</div>
{/if}
{if isset($aNewProducts)}
<div class="dashboard clearfix mosaicflow_load" data-width="300">
    {if ($is_trial_mode)}
    <div class="block">
        <div class="title"{if ($expires <= 2)} style="background:red; color:#fff;" {/if}>
            phpFox Trial
            <a href="https://www.phpfox.com/" target="_blank" class="purchase_trial">Purchase</a>
        </div>
        <div class="content">
            <div class="info">
                <div class="info_left">
                    Expires:
                </div>
                <div class="info_right">
                    {if $expires == 0}
                    Today
                    {else}
                    {$expires} {if ($expires == '1')}day{else}days{/if}
                    {/if}
                </div>
            </div>
        </div>
    </div>
    {/if}
	{foreach from=$aNewProducts item=product}
		{template file='admincp.block.product.install'}
	{/foreach}
	{block location='2'}
	{block location='3'}
	{block location='1'}
</div>
{else}

{/if}