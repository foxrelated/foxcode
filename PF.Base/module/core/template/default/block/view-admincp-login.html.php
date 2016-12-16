<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: view-admincp-login.html.php 1407 2010-01-21 12:35:36Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="table_header">
	{_p var='log_details'}
</div>
<div class="table form-group">
	<div class="table_left">
		{_p var='attempt'}:
	</div>
	<div class="table_right_text">
		{$aLog.attempt}
	</div>
	<div class="clear"></div>
</div>
<div class="table form-group">
	<div class="table_left">
		{_p var='user'}:
	</div>
	<div class="table_right_text">
		{$aLog|user}
	</div>
	<div class="clear"></div>
</div>
<div class="table form-group">
	<div class="table_left">
		{_p var='time_stamp'}:
	</div>
	<div class="table_right_text">
	    {if Phpfox::isModule('Mail')}
		{$aLog.time_stamp|date:'mail.mail_time_stamp'}
	{else}
	    {$aLog.time_stamp|date:'core.global_update_time'}
	   {/if}
	</div>
	<div class="clear"></div>
</div>
<div class="table form-group">
	<div class="table_left">
		{_p var='ip_address'}:
	</div>
	<div class="table_right_text">
		{$aLog.ip_address}
	</div>
	<div class="clear"></div>
</div>
<div class="table form-group">
	<div class="table_left">
		{_p var='location'}:
	</div>
	<div class="table_right_text">
		<input type="text" name="" value="{$aLog.cache_data.location}" style="width:95%;" />
	</div>
	<div class="clear"></div>
</div>
<div class="table form-group">
	<div class="table_left">
		{_p var='referrer'}:
	</div>
	<div class="table_right_text">
		<input type="text" name="" value="{$aLog.cache_data.referrer}" style="width:95%;" />
	</div>
	<div class="clear"></div>
</div>
<div class="table form-group">
	<div class="table_left">
		{_p var='user_agent'}:
	</div>
	<div class="table_right_text">
		<input type="text" name="" value="{$aLog.cache_data.user_agent}" style="width:95%;" />
	</div>
	<div class="clear"></div>
</div>
<div class="table form-group">
	<div class="table_left">
		{_p var='security_token'}:
	</div>
	<div class="table_right_text">
		<input type="text" name="" value="{$aLog.cache_data.token}" style="width:95%;" />
	</div>
	<div class="clear"></div>
</div>
<div class="table form-group">
	<div class="table_left">
		{_p var='email'}:
	</div>
	<div class="table_right_text">
		<input type="text" name="" value="{$aLog.cache_data.email}" style="width:95%;" />
	</div>
	<div class="clear"></div>
</div>
<div class="table_clear">
	<input type="button" value="{_p var='close'}" class="button btn-danger" onclick="tb_remove();" />
</div>