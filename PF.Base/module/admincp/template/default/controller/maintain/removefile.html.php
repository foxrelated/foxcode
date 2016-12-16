<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aFiles)}
    <div class="table_header">
        {_p var="Select a way to delete your old files"}
    </div>
    
    <div id="maintain_remove_files" class="container">
        <ul class="nav nav-tabs">
            <li>
                <a  href="#manual" data-toggle="tab">{_p var="Manual"}</a>
            </li>
            <li class="active"><a href="#ftp" data-toggle="tab">{_p var="FTP"}</a>
            </li>
            <li><a href="#ssh" data-toggle="tab">{_p var="SSH"}</a>
            </li>
        </ul>

        <div class="tab-content ">
            <div class="tab-pane" id="manual">
                <h2 class="table">
                    {_p var="Please find below files on your server and delete them"}:
                </h2>
                <ul class="list-group">
                    {foreach from=$aFiles item=sFile}
                    <li class="list-group-item">{$site_path}{$sFile}</li>
                    {/foreach}
                </ul>
            </div>
            <div class="tab-pane active" id="ftp">
                <h2 class="table">{_p var="Enter your ftp account to delete files"}:</h2>
                <form method="post" action="{url link='admincp.maintain.removefile'}"  id="form_remove_file_ftp" enctype="multipart/form-data">
                    <div class="table">
                        <div class="table_left">
                            {_p var="Protocol"}:
                        </div>
                        <div class="table_right">
                            <label><input type="radio" id="ftp_upload_method" name="val[method]" checked value="1"/>&nbsp;{_p var="FTP"}</label>&nbsp;
                            <label><input type="radio" id="ftp_upload_method" name="val[method]" value="2"/>&nbsp;{_p var="sFTP"}</label>
                        </div>
                    </div>
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
                    <div class="table">
                        <div class="table_left"></div>
                        <div class="table_right">
                            <input type="submit" class="btn button" value="{_p var='Check permission and remove files'}" name="val[submit]"/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane" id="ssh">
                <h2 class="table">{_p var="Please download below ssh file, upload to your server (root path) and execute it"}:</h2>
                <div class="table">
                    <a href="{url link="admincp.maintain.removefile", ssh=1}">{_p var="Download"}</a>
                </div>
            </div>
        </div>
    </div>
{else}
    <div class="table_header">
        {_p var="All old files were deleted"}.
    </div>
{/if}