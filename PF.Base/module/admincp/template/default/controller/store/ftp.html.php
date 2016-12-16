<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $type != 'module'}
<i class="fa fa-spin fa-circle-o-notch"></i>
{/if}
<div id="admincp_install_ftp_information"{if $type != 'module'} style="display:none;"{/if}>
    <form method="post" action="{url link='admincp.store.ftp' productName=$productName type=$type productId=$productId extraBase64=$extraBase64}" id="form_store_ftp" enctype="multipart/form-data">
        <div>
            <input type="hidden" name="val[type]" value="{$type}"/>
            <input type="hidden" name="val[productName]" value="{$productName}"/>
            <input type="hidden" name="val[productId]" value="{$productId}"/>
            <input type="hidden" name="val[extraBase64]" value="{$extraBase64}"/>
            <input type="hidden" name="val[targetDirectory]" value="{$targetDirectory}">
        </div>
        <div class="table">
            <div class="table_left">
                {_p var='file_upload_method'}:
            </div>
            <div class="table_right">
                <select id="ftp_upload_method" name="val[method]"
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
                {_p var='port'}:
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
                       value="" name="val[password]"/>
            </div>
        </div>
    </div>
    <div class="table">
        <div class="table_left"></div>
        <div class="table_right">
            <input type="submit" class="btn button" value="Check permission and finalize" name="val[submit]"/>
        </div>
    </div>
    </form>
</div>
{if $type != 'module'}
{literal}
<script>
	$Ready(function() {
		var f = $('#form_store_ftp');
		$('#ftp_upload_method').val('file_system');
		f.trigger('submit');
	});
</script>
{/literal}
{/if}