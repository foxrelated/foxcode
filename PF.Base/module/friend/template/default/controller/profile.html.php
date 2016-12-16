<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Friend
 * @version 		$Id: profile.html.php 6041 2013-06-10 18:50:19Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !PHPFOX_IS_AJAX}
<form method="post" action="{url link='profile.friend'}">
    <div class="table form-group">
        <div class="table_right">
            {$aFilters.search}
        </div>
        <div class="clear"></div>
    </div>
</form>
<br/>
{/if}
{if ($aFriends)}
{if !PHPFOX_IS_AJAX}
<div class="wrapper-items">
{/if}
{foreach from=$aFriends name=friend item=aUser}
    {template file='user.block.rows_wide'}
{/foreach}
{pager}
{if !PHPFOX_IS_AJAX}
</div>
{/if}
{elseif !PHPFOX_IS_AJAX}
<div class="extra_info">
    {_p var='No friends found.'}
</div>
{/if}