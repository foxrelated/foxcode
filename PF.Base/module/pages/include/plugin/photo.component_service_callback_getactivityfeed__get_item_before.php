<?php
if (defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && PHPFOX_PAGES_ITEM_TYPE == 'pages')
{
    $sFeedTable = 'pages_feed';
}

else if ($iFeedId && $cache = storage()->get('feed_callback_' . $iFeedId))
{
    $module = $cache->value->module;
    if ($module == 'pages')
    {
        $sFeedTable = 'pages_feed';
    }
}