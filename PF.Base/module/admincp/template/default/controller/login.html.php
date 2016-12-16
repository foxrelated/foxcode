<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="admincp_login">
	<form method="post" action="{url link='current'}">
		<div class="adminp_login_body">
			<div class="table_header">
				{_p var='admincp_login'}
			</div>
			{error}
			<div class="table form-group">
				<div class="table_right">
					<input id="admincp_login_email" type="text" name="val[email]" value="{value id='email' type='input'}" placeholder="{_p var='email'}" size="40" />
				</div>
			</div>
			<div class="table form-group">
				<div class="table_right">
					<input type="password" name="val[password]" value="{value id='password' type='input'}" placeholder="{_p var='password'}" size="40" autocomplete="off"/>
				</div>
				<div class="clear"></div>
			</div>			
			<div class="table_clear">
				<input id="admincp_btn_login" type="submit" value="{_p var='login'}" class="button btn-primary" />
				<div id="admincp_site_link">
					<a href="{url link=''}" class="no_ajax">{_p var='back_to_site'}</a>
				</div>                                                                                            				
			</div>			
		</div>
	</form>
</div>