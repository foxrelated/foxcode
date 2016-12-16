<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $hasRewrite}
<div class="message">
	{_p var='short_urls_are_up_and_running_dot_good_job'}!
</div>
{else}

<div class="message">
	{if $hasHtaccess}
	{_p var='add_the_following_at_the_start_of_your_strong_dothtaccess_strong_file'}:
	{else}
	{_p var='create_a_new_file_called_strong_dothtaccess_strong_and_add_the_following'}:
	{/if}
</div>
<pre style="margin-bottom:30px; background:#0c0c0c; text-indent:0px; color:#fff; padding:10px; font-family:monospace; font-size:13px;">
# START phpFox Rewrite
    Options -Indexes
&lt;IfModule mod_rewrite.c&gt;
	RewriteEngine On
	RewriteBase {param var='core.folder_original'}
	{literal}
	RewriteCond %{REQUEST_FILENAME} !-f
	{/literal}
    RewriteRule ^(file)/(.*) PF.Base/$1/$2

	RewriteRule ^themes/default/(.*) PF.Base/theme/default/$1
	RewriteRule ^(static|theme|module)/(.*) PF.Base/$1/$2
	RewriteRule ^(Apps|themes)/(.*) PF.Site/$1/$2

	{literal}
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	{/literal}
	RewriteRule ^(.*) index.php/$1
&lt;/IfModule&gt;
# END phpFox Rewrite
</pre>

<form method="post" action="#" class="ajax_post">
	<div class="message">
		{_p var='continue_below_if_your_strong_dothtaccess_strong_file_is_ready'}.
	</div>
	<div class="table_clear">
		<input type="submit" class="button btn-primary" value="{_p var='enable_short_urls'}">
	</div>
</form>

{/if}