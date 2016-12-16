<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="table form-group">
  <div class="table_left">
    {required}{_p var='name'}:
  </div>
  <div class="table_right">
    {if isset($aForms.album_id) && $aForms.profile_id > 0}
    <input type="hidden" name="val[name]" id="name" value="{_p var='profile_pictures'}" size="30" maxlength="150" />
    {_p var='profile_pictures'}
    {else}
    <input class="form-control" type="text" name="val[name]" id="name" value="{value type='input' id='name'}" size="30" maxlength="150" />
    {/if}
  </div>
  <div class="clear"></div>
</div>
<div class="table form-group">
  <div class="table_left">
    {_p var='description'}:
  </div>
  <div class="table_right">
    <textarea class="form-control" name="val[description]" id="description" cols="40" rows="5">{value type='textarea' id='description'}</textarea>
  </div>
  <div class="clear"></div>
</div>
{if isset($sModule) && $sModule}

{else}
  {if Phpfox::isModule('privacy') && Phpfox::getUserParam('photo.can_use_privacy_settings')}
  <div class="table form-group-follow">
    <div class="table_left">
      {_p var='album_s_privacy'}:
    </div>
    <div class="table_right">
      {module name='privacy.form' privacy_name='privacy' privacy_info='photo.control_who_can_see_this_photo_album_and_any_photos_associated_with_it' privacy_custom_id='js_custom_privacy_input_holder_album'}
    </div>
  </div>
  </div>
  {/if}
{/if}