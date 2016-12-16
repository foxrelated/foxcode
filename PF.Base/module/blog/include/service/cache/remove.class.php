<?php
defined('PHPFOX') or exit('NO DICE!');

class Blog_Service_Cache_Remove extends Phpfox_Service
{
    public function my()
    {
        $iUserId = Phpfox::getUserId();
        $this->user($iUserId);
    }
    
    public function user($iUserId = null)
    {
        if (!isset($iUserId)){
            $iUserId = Phpfox::getUserId();
        }
        $iUserId = (int) $iUserId;
        $this->cache()->remove('blog_draft_count_' . $iUserId);
        $this->cache()->remove('blog_draft_total_' . $iUserId);
    }
    
    public function blog($iBlogId)
    {
        $iBlogId = (int) $iBlogId;
        $this->cache()->remove('blog_detail_view_' . $iBlogId);
        $this->cache()->remove('blog_detail_edit_' . $iBlogId);
    }
}