<?php
  defined('PHPFOX') or exit('NO DICE!');
?>
<div class="wrapper-items">
  {foreach from=$aPagesList item=aPage}
  <div class="pages_item">
    <a class="pages_photo" href="{$aPage.url}">{img server_id=$aPage.server_id title=$aPage.title path='pages.url_image' file=$aPage.image_path suffix='_200' max_width='200' max_height='200' is_page_image=true}</a>
    <div class="pages_info">
      <div>
        <a href="{$aPage.url}" class="link pages_title fw-600">{$aPage.title|clean}</a>
        <div class="txt-time-color"><i class="fa fa-users"></i>
          {if $aPage.total_like != 1}
          {_p var='pages_total_followers', total=$aPage.total_like}
          {else}
          {_p var='pages_total_follower', total=$aPage.total_like}
          {/if}
        </div>
      </div>
    </div>
  </div>
  {/foreach}
</div>