<?php

if (setting('pf_video_enabled')) {
    $val = Core\Lib::appsGroup()->hasPerm($aPage['page_id'], 'pf_video.share_videos');
    $val = ($val) ? 1 : 0;
    $this->template()->setHeader('<script>window.can_post_video_on_group = ' . $val . ';</script>');
}
