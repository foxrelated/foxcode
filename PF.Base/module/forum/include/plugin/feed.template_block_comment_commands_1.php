<?php
defined('PHPFOX') or exit('NO DICE!');

if ((Phpfox_Module::instance()->getFullControllerName() == 'forum.thread' || (PHPFOX_IS_AJAX && isset($_POST['core']) && $_POST['core']['call'] == 'forum.addReply')) && Phpfox::isUser()) {
    $aPost = $this->getVar('aPost');
    $aThread = (array)$this->getVar('aThread');
    $iTotalPosts = (int)$this->getVar('iTotalPosts');

    if ((Phpfox::getUserParam('forum.can_reply_to_own_thread')
            && $aThread['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('forum.can_reply_on_other_threads')
        || Forum_Service_Moderate_Moderate::instance()->hasAccess($aThread['forum_id'], 'can_reply')
    ) {
        echo '<div class="forum_quote_holder">
        <a role="button" class="forum_quote" onclick="$Core.box(\'forum.reply\', 800, \'id=' . $aPost['thread_id'] . '&amp;quote=' . $aPost['post_id'] . '&amp;total_post=' . $iTotalPosts . '\'); return false;"><span>' . _p('quote') . '</span></a></div>';

    }

    if (Phpfox::getParam('forum.enable_thanks_on_posts') && (Phpfox::getUserParam('forum.can_thank_on_forum_posts')
            && ($aPost['user_id'] != Phpfox::getUserId()) && !User_Service_Block_Block::instance()->isBlocked(null, $aPost['user_id']))
    ) {
        if (empty($aPost['thank_id']))
         echo '<div class="forum_thanks_holder">
            <a role="button" id="forum_thanks_btn_' . $aPost['post_id'] . '" class="forum_thanks" onclick="$.ajaxCall(\'forum.thanks\', \'post_id=' . $aPost['post_id'] . '\');return false;" title="' . _p('thanks') . '"></a></div>';
        else
            echo '<div class="forum_thanks_holder">
            <a role="button" id="forum_thanks_btn_' . $aPost['post_id'] . '" class="forum_thanks thanked" onclick="$.ajaxCall(\'forum.removeThanks\', \'thank_id=' . $aPost['thank_id'] . '\');return false;" title="' . _p('delete_thanks') . '"></a></div>';
    }
}
?>