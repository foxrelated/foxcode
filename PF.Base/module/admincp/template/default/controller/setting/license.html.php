<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $bCanWrite}
    <div id="client_details">
        <div class="table_header">
            {_p var='Enter your license ID & Key:'}
        </div>
        <form method="post" action="{url link='current'}" id="js_form">
            <div><input type="hidden" id="license_trial" name="val[is_trial]" value="0"></div>
            <div class="table">
                <div class="table_right">
                    <input autocomplete="off" type="text" name="val[license_id]" id="license_id" value="{value type='input' id='license_id'}" size="30" placeholder="{_p var='License ID'}" />
                </div>
            </div>
            <div class="table">
                <div class="table_right">
                    <input autocomplete="off" type="text" name="val[license_key]" id="license_key" value="" size="30" placeholder="{_p var='License Key'}" />
                </div>
            </div>
            <div class="table_clear">
                <input type="submit" value="{_p var='submit'}" class="button" />
            </div>
        </form>
    </div>
{else}
    <div class="error error_message">
        {_p var="Do not permission to edit file 'PF.Base/settings/license.sett.php'. Please change its permission or use ftp to edit it"}
    </div>
{/if}
