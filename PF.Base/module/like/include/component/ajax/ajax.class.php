<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Ajax
 * @version 		$Id: ajax.class.php 6409 2013-08-01 14:54:51Z Raymond_Benc $
 */
class Like_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function add()
	{
		Phpfox::isUser(true);
		if (Like_Service_Process::instance()->add($this->get('type_id'), $this->get('item_id'), null, $this->get('custom_app_id', null), [], $this->get('table_prefix', '')))
		{
			if ($this->get('type_id') == 'pages') {
				$this->call('window.location.reload();');
				return;
			}

			if ($this->get('reload')) {
				$this->call('window.location.reload();');
				return;
			}

			if ($this->get('type_id') == 'feed_mini' && $this->get('custom_inline'))
			{
				$this->_loadCommentLikes();
			}
			else
			{
				/* When clicking "Like" from the Feed */
				$this->_loadLikes();
			}
			if (!$this->get('counterholder'))
			{
			    $this->call('$Core.loadInit();');
			}
		}
	}

	public function delete()
	{
		Phpfox::isUser(true);

		if (Like_Service_Process::instance()->delete($this->get('type_id'), $this->get('item_id'), (int) $this->get('force_user_id'), false, $this->get('table_prefix', '')))
		{
			if ($this->get('reload')) {
				$this->call('window.location.reload();');
				return;
			}

			if ($this->get('delete_inline', false) && (int) $this->get('force_user_id') > 0)
			{
				$this->remove('#js_row_like_' . (int) $this->get('force_user_id'));
			}
			else
			{
				if ($this->get('type_id') == 'feed_mini' && $this->get('custom_inline'))
				{
					$this->_loadCommentLikes();
				}
				else
				{
					$this->_loadLikes();
				}
			}
		}
	}

	public function browse()
	{
		$this->error(false);
		Phpfox::getBlock('like.browse');
		$sTitle = (($this->get('type_id') == 'pages' && $this->get('force_like') == '') ? _p('members') : _p('followers'));
		if ($this->get('block_title')) {
			$sTitle = $this->get('block_title');
		}
		$this->setTitle($sTitle);
		$this->call('<script>$Core.loadInit();</script>');
	}

	private function _loadCommentLikes()
	{
        $aComment = Comment_Service_Comment::instance()->getComment($this->get('item_id'));
        if ($this->get('counterholder'))
        {
            $this->call('$("#' . $this->get('counterholder') . '_counter_' . $this->get('item_id') . '").html(' . $aComment['total_like'] . ');');
            return;
        }
        if ($aComment['total_like'] > 0)
        {
            $sPhrase = _p('1_person');
            if ($aComment['total_like'] > 1)
            {
                $sPhrase = _p('total_people', array('total' => $aComment['total_like']));
            }
            $this->call('$(\'#js_comment_' . $this->get('item_id') . '\').find(\'.comment_mini_action:first\').find(\'.js_like_link_holder\').show();');
            $this->call('$(\'#js_comment_' . $this->get('item_id') . '\').find(\'.comment_mini_action:first\').find(\'.js_like_link_holder_info\').html(\'' . $sPhrase . '\');');
        }
        else
        {
            $this->call('$(\'#js_comment_' . $this->get('item_id') . '\').find(\'.comment_mini_action:first\').find(\'.js_like_link_holder\').hide();');
        }
	}

	private function _loadLikes()
	{
		$sType = $this->get('type_id');
		if (empty($sType))
		{
			$sType = $this->get('item_type_id');
		}

		if (Phpfox::getParam('like.show_user_photos'))
		{
			// The block like.block.display works very different if this setting is enabled
			$aLikes = Like_Service_Like::instance()->getLikesForFeed($sType, $this->get('item_id'), false, Phpfox::getParam('feed.total_likes_to_display'), true, $this->get('table_prefix', ''));

			$aFeed = array(
				'like_type_id' => $sType,
				'item_id' => $this->get('item_id'),
				'likes' => $aLikes,
				'feed_total_like' => Like_Service_Like::instance()->getTotalLikeCount(),
				'call_displayactions' => true,
				'feed_id' => $this->get('parent_id')
			);
		}
		else
		{
			$aFeed = Like_Service_Like::instance()->getAll( $sType, $this->get('item_id'), $this->get('table_prefix', ''));

			// Fix for likes
			$aFeed['feed_like_phrase'] = $aFeed['likes']['phrase'];
			$aFeed['feed_id'] = $this->get('parent_id');

			$aFeed['feed_total_like'] = isset($aFeed['likes']['total']) ? $aFeed['likes']['total'] : 0;
			$aFeed['like_type_id'] = $sType;
			$aFeed['item_id'] = $this->get('item_id');
		}

		$sType = ($sType == 'app' ? $this->get('custom_app_id') : $sType);
		$this->template()->assign(array('aFeed' => $aFeed, 'ajaxLoadLike' => true));
		$this->template()->getTemplate('like.block.display');
		$sId = $this->get('item_id');
		$sContent = $this->getContent(false);
		$sContent = str_replace("'", "\'", $sContent);

		$sType = str_replace('-', '_', $sType);
		$sCall = ' $("#js_feed_like_holder_' . $sType . '_' . $sId . '").find(\'.js_comment_like_holder:first\').html(\'' . $sContent . '\');';
		$this->call($sCall);
		$this->call('$("#js_feed_like_holder_' . $sType . '_' . $sId . '").show();');

		if (Phpfox::getParam('photo.show_info_on_mouseover') && $this->get('item_type_id') == 'photo' && $this->get('item_id') > 0)
		{
			$iTotal = 0;
			if (isset($aFeed['feed_total_like']))
			{
				$iTotal = $aFeed['feed_total_like'];
			}
			else if (isset($aFeed['likes']['total']))
			{
				$iTotal = $aFeed['likes']['total'];
			}
			$this->call('$("#js_like_counter_' . $this->get('item_id') . '").html('. $iTotal .');');
		}
	}
}