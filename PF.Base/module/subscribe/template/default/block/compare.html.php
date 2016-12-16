<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 
	[feature-id][title]
	[feature-id][package][package-id] = array
	[feature-id][package][package-id][radio] = [0|1|2]
	[feature-id][package][package-id][text] = text
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="subscribe_compare_plan">
{if !count($aPackages) && $bIsDisplay}
<div class="message">{_p var='there_is_nothing_to_compare_at_this_time'}</div>
{/if}
<div id="div_compare_wrapper" class="{if !$bIsDisplay}compare_admincp{/if} container">
		<div class="row">
            {foreach from=$aPackages item=aPackage}
            <div class="col-xs-12 col-sm-6 col-md-{$iBootstrapCol} col-lg-{$iBootstrapCol}">
                <div class="panel price panel-color">
                    <div class="panel-heading  text-center">
                        <h3>
                            {$aPackage.title|convert}
                        </h3>
                    </div>
                    <ul class="list-group list-group-flush text-center">
                        {foreach from=$aPackage.feature key=sKey item=aFeature}
                        <li class="list-group-item" {if !empty($aPackage.background_color)} style="background-color:{$aPackage.background_color}"{/if}> {$sKey}:&nbsp;
                            {if isset($aFeature.feature_value)}
                                {if $aFeature.feature_value == 'img_accept.png'} {img theme='misc/accept.png'} {/if}
                                {if $aFeature.feature_value == 'img_cross.png'} {img theme='misc/cross.png'} {/if}
                                {if $aFeature.feature_value != 'img_cross.png' && $aFeature.feature_value != 'img_accept.png'} {$aFeature.feature_value|convert} {/if}
                            {/if}
                        {/foreach}
                    </ul>
                    <div class="panel-body text-center">
                        <p class="lead">
                            <strong>
                                {if empty($aPackage.price_phrase)}
                                {_p var='free'}
                                {else}
                                {$aPackage.price_phrase}
                                {/if}
                            </strong>
                        </p>
                    </div>
                    <div class="panel-footer">
                        <a href="#" class="btn btn-lg btn-block btn-danger" onclick="tb_show('Select Payment', $.ajaxBox('subscribe.upgrade', 'height=400&width=400&id={$aPackage.package_id}')); return false;">
                            {_p var='purchase'}
                        </a>
                    </div>
                </div>
            </div>
            {/foreach}
		</div>
	</div>
</div>