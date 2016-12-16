<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="activity_feed_form">
    <form method="post" action="javascript:void(0);" id="js_activity_feed_edit_form" enctype="multipart/form-data">
        <div id="js_custom_privacy_input_holder"></div>
        <div><input type="hidden" name="val[feed_id]" value="{$iFeedId}" /></div>
        {if isset($aFeedCallback.module)}
        <div><input type="hidden" name="val[callback_item_id]" value="{$aFeedCallback.item_id}" /></div>
        <div><input type="hidden" name="val[callback_module]" value="{$aFeedCallback.module}" /></div>
        <div><input type="hidden" name="val[parent_user_id]" value="{$aFeedCallback.item_id}" /></div>
        {/if}
        {if isset($bFeedIsParentItem)}
        <div><input type="hidden" name="val[parent_table_change]" value="{$sFeedIsParentItemModule}" /></div>
        {/if}
        {if isset($bForceFormOnly) && $bForceFormOnly}
        <div><input type="hidden" name="force_form" value="1" /></div>
        {/if}
        <div class="activity_feed_form_holder">

            <div id="activity_feed_upload_error" style="display:none;"><div class="error_message" id="activity_feed_upload_error_message"></div></div>

            <div class="global_attachment_holder_section" id="global_attachment_status" style="display:block;">
                <div id="global_attachment_status_value" style="display:none;"></div>
                <textarea {if isset($aPage)} id="pageFeedTextarea" {else} {if isset($aEvent)} id="eventFeedTextarea" {else} {if isset($bOwnProfile) && $bOwnProfile == false}id="profileFeedTextarea" {/if}{/if}{/if} cols="60" rows="8" name="val[user_status]" placeholder="{if isset($aFeedCallback.module) || defined('PHPFOX_IS_USER_PROFILE')}{_p var='write_something'}{else}{_p var='what_s_on_your_mind'}{/if}" class="close_warning">{$aForms.feed_status}</textarea>
                {if isset($bLoadCheckIn) && $bLoadCheckIn == true}
                <script type="text/javascript">
                    oTranslations['at_location'] = "{_p var='at_location'}";
                </script>
                <div id="js_location_feedback"></div>
                {/if}
            </div>

            {if Phpfox::isModule('egift')}
            {module name='egift.display'}
            {/if}
        </div>
        <div class="activity_feed_form_button" style="display: block">
            {if $bLoadCheckIn}
            <div id="js_location_input">
                <a class="btn btn-danger" href="#" onclick="$Core.Feed.cancelCheckIn(); return false;"><i class="fa fa-times"></i></a>
                <input type="text" id="hdn_location_name">
            </div>
            {/if}

            <div class="activity_feed_form_button_status_info">
                <textarea id="activity_feed_textarea_status_info" cols="60" rows="8" name="val[status_info]">{$aForms.feed_status}</textarea>
            </div>
            <div class="activity_feed_form_button_position">

                {if (defined('PHPFOX_IS_PAGES_VIEW') && $aPage.is_admin)}

                <div id="activity_feed_share_this_one">
                    <ul class="">
                        {if defined('PHPFOX_IS_PAGES_VIEW') && $aPage.is_admin && $aPage.page_id != Phpfox::getUserBy('profile_page_id') && ($aPage.item_type == 0)}
                        <li>
                            <input type="hidden" name="custom_pages_post_as_page" value="{$aPage.page_id}">
                            <a data-toggle="dropdown" role="button" class="btn btn-lg">
                                <span class="txt-prefix">{_p var='posting_as'}: </span>
                                <span class="txt-label">{$aPage.full_name|clean|shorten:20:'...'}</span>
                                <i class="caret"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-checkmark">
                                <li>
                                    <a class="is_active_image" data-toggle="privacy_item" role="button" rel="{$aPage.page_id}">{$aPage.full_name|clean|shorten:20:'...'}</a>
                                </li>
                                <li>
                                    <a data-toggle="privacy_item" role="button" rel="0">{$sGlobalUserFullName|shorten:20:'...'}</a>
                                </li>
                            </ul>
                        </li>
                        {/if}
                        {if $bLoadCheckIn}
                        {template file='feed.block.checkin'}
                        {/if}
                    </ul>
                    <div class="clear"></div>
                </div>

                {else}
                {if $bLoadCheckIn}
                <div id="activity_feed_share_this_one">
                    <ul>
                        {template file='feed.block.checkin'}
                    </ul>
                    <div class="clear"></div>
                </div>
                {/if}
                {/if}

                <div class="activity_feed_form_button_position_button">
                    <input type="submit" value="{_p var='Update'}"  id="activity_feed_submit" class="button btn-lg btn-primary" />
                </div>
                {if isset($aFeedCallback.module)}
                {else}
                {if !isset($bFeedIsParentItem) && (!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id) && $aUser.user_id == Phpfox::getUserId()))}
                {module name='privacy.form' privacy_name='privacy' privacy_type='mini'}
                {/if}
                {/if}
                <div class="clear"></div>
            </div>

            {if Phpfox::getParam('feed.enable_check_in') && (Phpfox::getParam('core.ip_infodb_api_key') != '' || Phpfox::getParam('core.google_api_key') != '')}
            <div id="js_add_location">
                <div><input type="hidden" id="val_location_latlng" name="val[location][latlng]"></div>
                <div><input type="hidden" id="val_location_name" name="val[location][name]"></div>
                <div id="js_add_location_suggestions" style="overflow-y: auto;"></div>
                <div id="js_feed_check_in_map"></div>
            </div>
            {/if}

        </div>
    </form>
    <div class="activity_feed_form_iframe"></div>
</div>
<script>
    $Behavior.activityFeedProcess();
</script>