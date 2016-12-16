<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="client_details">
    <div class="table_header">
        Check Apps compatible
    </div>
    <form method="post" action="#apps" id="js_form" enctype="multipart/form-data">
        <table style="width:100%; text-align: center;">
            <tr>
                <th><strong>App Name</strong></th>
                <th><strong>Compatible</strong></th>
            </tr>
        {foreach from=$apps key=sKey item=app}
            <tr>
                <td>{$sKey}</td>
                <td>{$app}</td>
            </tr>
        {/foreach}
        </table>
        <div class="table_header">
            <h2>Incompatible apps might break your site</h2>
        </div>
        <input type="hidden" name="val[app]" value="apps">
        <div class="table_clear">
            <input type="submit" value="Continue" class="button" name="val[submit]"/>
        </div>
    </form>
</div>