<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.setting.redirection'}" class="ajax_post">
	<div class="message">
		{$info}
	</div>
	<div class="table_clear">
		<input type="submit" class="button{if ($enabled)} button_off{/if}" value="{if ($enabled)}{_p var='Disable'}{else}{_p var='Enable'}{/if}">
	</div>
</form>