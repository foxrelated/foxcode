<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Feed
 * @version 		$Id: comment.class.php 6714 2013-10-03 08:28:06Z Miguel_Espinoza $
 */
class Feed_Component_Block_Comment extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aFeed = $this->getParam('aFeed');
		if (!isset($aFeed['feed_id'])) {
			$aFeed['feed_id'] = $aFeed['item_id'];
		}
		$aFeed['is_view_item'] = true;
		$sFeedType = (isset($aFeed['feed_display']) ? $aFeed['feed_display'] : null);
		
		if (Phpfox::isModule('comment') && Phpfox::getUserParam('comment.can_delete_comment_on_own_item') && ($iOwnerDeleteCmt = $this->request()->getInt('ownerdeletecmt')) && isset($aFeed['user_id']) && $aFeed['user_id'] == Phpfox::getUserId())
		{
			if (Comment_Service_Process::instance()->deleteInline($iOwnerDeleteCmt, $aFeed['comment_type_id'], true))
			{
				$this->url()->forward($aFeed['feed_link'], _p('comment_successfully_deleted'));
			}
		}

		$bCanPostComment = true;
		if (isset($aFeed['comment_privacy']) && $aFeed['user_id'] != Phpfox::getUserId() && (Phpfox::isModule('privacy') && !Phpfox::getUserParam('privacy.can_comment_on_all_items')))
		{
			switch ($aFeed['comment_privacy'])
			{
				case 1:					
					if ((int) $aFeed['feed_is_friend'] <= 0)
					{
						$bCanPostComment = false;						
					}
					break;
				case 2:
					if ((int) $aFeed['feed_is_friend'] > 0)
					{
						$bCanPostComment = true;
					}
					else 
					{
						if (Phpfox::isModule('friend') &&  !Friend_Service_Friend::instance()->isFriendOfFriend($aFeed['user_id']))
						{
							$bCanPostComment = false;	
						}
					}
					break;
				case 3:
					$bCanPostComment = false;
					break;
			}
		}
		$aFeed['can_post_comment'] = $bCanPostComment;

		if (isset($aFeed['total_like']) && (int) $aFeed['total_like'] > 0 && Phpfox::isModule('like'))
		{
			$aFeed['likes'] = Like_Service_Like::instance()->getLikesForFeed($aFeed['like_type_id'], $aFeed['item_id'], ((int) $aFeed['feed_is_liked'] > 0 ? true : false), Phpfox::getParam('feed.total_likes_to_display'), false, (isset($aFeed['feed_table_prefix']) ? $aFeed['feed_table_prefix'] : ''));
		}
		
		/* Quick check without the actions*/
		$aFeed['bShowEnterCommentBlock'] = false;
		
		if (Phpfox::isModule('like') && ((isset($aFeed['total_like']) && $aFeed['total_like'] > 0 && Phpfox::getParam('like.show_user_photos') == false) ||
				(isset($aFeed['total_comment']) && $aFeed['total_comment'] > 0)
			))
		{
			$aFeed['bShowEnterCommentBlock'] = true;
		}

		$iPageLimit = 2;
		$mPager = null;
		$iCommentId = null;
		$bIsViewingComments = false;
		if (Phpfox::isModule('comment') && $sFeedType != 'mini')
		{
			if ((int) $aFeed['total_comment'] > 0)
			{
				if ($sFeedType == 'view')
				{
					$iPageLimit = Phpfox::getParam('comment.comment_page_limit');
					if ($this->request()->get('stream-mode')) {
						$iPageLimit = ($iPageLimit + 1);
						if (!defined('PHPFOX_FEED_STREAM_MODE')) {
							define('PHPFOX_FEED_STREAM_MODE', true);
						}
					}
					$mPager = $aFeed['total_comment'];
				}

				if ($this->request()->getInt('comment'))
				{
					$iCommentId = $this->request()->getInt('comment');
					$bIsViewingComments = true;
				}

				$aFeed['comments'] = Comment_Service_Comment::instance()->getCommentsForFeed($aFeed['comment_type_id'], $aFeed['item_id'], $iPageLimit, $mPager, $iCommentId, (isset($aFeed['feed_table_prefix']) ? $aFeed['feed_table_prefix'] : ''));
			}
		}

		if ($sFeedType == 'view')
		{
			Phpfox_Pager::instance()->set(array(
					'ajax' => 'comment.viewMoreFeed', 
					'page' => Phpfox_Request::instance()->getInt('page'),
					'size' => $iPageLimit, 
					'count' => $mPager,
					'phrase' => Phpfox::isModule('comment') ? _p('view_previous_comments') : '',
					'icon' => 'misc/comment.png',
					'aParams' => array(
						'comment_type_id' => $aFeed['comment_type_id'],
						'item_id' => $aFeed['item_id'],
						'append' => true,
						'pagelimit' => $iPageLimit,
						'total' => $mPager,
                        'feed_table_prefix' => (isset($aFeed['feed_table_prefix']) ? $aFeed['feed_table_prefix'] : '')
					)
				)
			);
		}

		$aFeed['type_id'] = (!empty($aFeed['type_id']) ? $aFeed['type_id'] : (isset($aFeed['report_module']) ? $aFeed['report_module'] : ''));

		if ($aFeed['type_id'] == 'forum_reply') $aFeed['type_id'] = 'forum_post';
		if (!isset($aFeed['feed_like_phrase']) && Phpfox::isModule('like'))
		{
            Feed_Service_Feed::instance()->getPhraseForLikes($aFeed);
		}
		$this->template()->assign(array(
				'aFeed' => $aFeed,
				'sFeedType' => $sFeedType,
				'bIsViewingComments' => $bIsViewingComments,
				'feedJson' => json_encode($aFeed)
			)
		);
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('comment.component_block_comment_clean')) ? eval($sPlugin) : false);
	}	
}