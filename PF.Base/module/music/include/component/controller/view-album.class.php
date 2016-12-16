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
 * @package 		Phpfox_Component
 * @version 		$Id: view-album.class.php 6881 2013-11-12 15:08:13Z Fern $
 */
class Music_Component_Controller_View_Album extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::getUserParam('music.can_access_music', true);

		if (Phpfox::isUser() && Phpfox::isModule('notification'))
		{
			Notification_Service_Process::instance()->delete('comment_music_album', $this->request()->getInt('req3'), Phpfox::getUserId());
			Notification_Service_Process::instance()->delete('music_song_album', $this->request()->getInt('req3'), Phpfox::getUserId());
			Notification_Service_Process::instance()->delete('music_album_like', $this->request()->getInt('req3'), Phpfox::getUserId());
		}

		$aAlbum = Music_Service_Album_Album::instance()->getAlbum($this->request()->getInt('req3'));

		if (!isset($aAlbum['album_id']))
		{
			return Phpfox_Error::display(_p('unable_to_find_the_album_you_are_looking_for'));
		}

        if (Phpfox::isUser() && User_Service_Block_Block::instance()->isBlocked(null, $aAlbum['user_id']))
        {
            return Phpfox_Module::instance()->setController('error.invalid');
        }

		$aCallback = false;
		if (!empty($aAlbum['module_id']))
		{
			if ($aCallback = Phpfox::callback($aAlbum['module_id'] . '.getMusicDetails', $aAlbum))
			{
				$this->template()->setBreadCrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
				$this->template()->setBreadCrumb($aCallback['title'], $aCallback['url_home']);
				if ($aAlbum['module_id'] == 'pages' && !Pages_Service_Pages::instance()->hasPerm($aCallback['item_id'], 'music.view_browse_music'))
				{
					return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
				}
			}
		}

		if (Phpfox::isModule('privacy'))
		{
			Privacy_Service_Privacy::instance()->check('music_album', $aAlbum['album_id'], $aAlbum['user_id'], $aAlbum['privacy'], $aAlbum['is_friend']);
		}

		$this->setParam('aAlbum', $aAlbum);
		$this->setParam('aRatingCallback', array(
				'type' => 'music_album',
				'total_rating' => _p('total_rating_ratings', array('total_rating' => $aAlbum['total_rating'])),
				'default_rating' => $aAlbum['total_score'],
				'item_id' => $aAlbum['album_id'],
				'stars' => array(
					'2' => _p('poor'),
					'4' => _p('nothing_special'),
					'6' => _p('worth_listening_too'),
					'8' => _p('pretty_cool'),
					'10' => _p('awesome')
				)
			)
		);

		$this->setParam('aFeed', array(
				'comment_type_id' => 'music_album',
				'privacy' => $aAlbum['privacy'],
				'comment_privacy' => $aAlbum['privacy_comment'],
				'like_type_id' => 'music_album',
				'feed_is_liked' => $aAlbum['is_liked'],
				'feed_is_friend' => $aAlbum['is_friend'],
				'item_id' => $aAlbum['album_id'],
				'user_id' => $aAlbum['user_id'],
				'total_comment' => $aAlbum['total_comment'],
				'total_like' => $aAlbum['total_like'],
				'feed_link' => $this->url()->permalink('music.album', $aAlbum['album_id'], $aAlbum['name']),
				'feed_title' => $aAlbum['name'],
				'feed_display' => 'view',
				'feed_total_like' => $aAlbum['total_like'],
				'report_module' => 'music_album',
				'report_phrase' => _p('report_this_album')
			)
		);

		$this->setParam(array(
				'album_user_id' => $aAlbum['user_id'],
				'album_id' => $aAlbum['album_id']
			)
		);

		$this->template()->setBreadCrumb(_p('music'), ($aCallback === false ? $this->url()->makeUrl('music') : $aCallback['url_home_photo']));

		if ($aCallback === false)
		{
			$this->template()->setBreadCrumb(_p('albums'), $this->url()->makeUrl('music.browse.album'));
		}

		define('PHPFOX_IS_ALBUM_VIEW', true);

		if (Phpfox::isModule('rate'))
		{
			$this->template()
				->setHeader('cache',array('rate.js' => 'module_rate',
					'<script type="text/javascript">$Behavior.rateMusicAlbum = function() { $Core.rate.init({module: \'music_album\', display: ' . ($aAlbum['has_rated'] ? 'false' : ($aAlbum['user_id'] == Phpfox::getUserId() ? 'false' : 'true')) . ', error_message: \'' . ($aAlbum['has_rated'] ? _p('you_have_already_voted', array('phpfox_squote' => true)) : _p('you_cannot_rate_your_own_album', array('phpfox_squote' => true))) . '\'}); }</script>'));
		}

		$this->template()->setTitle($aAlbum['name']);
		if (!empty($aAlbum['image_path']))
		{
			$this->template()->setMeta('og:image', Phpfox::getLib('image.helper')->display(array(
						'server_id' => $aAlbum['server_id'],
						'path' => 'music.url_image',
						'file' => $aAlbum['image_path'],
						'suffix' => '_200',
						'return_url' => true
					)
				)
			);
		}
		$this->template()->setBreadCrumb($aAlbum['name'], $this->url()->permalink('music.album', $aAlbum['album_id'], $aAlbum['name']), true)
			->setMeta('description', $aAlbum['name'])
			->setHeader('cache', array(
					'jquery/plugin/star/jquery.rating.js' => 'static_script',
					'jquery/plugin/jquery.highlightFade.js' => 'static_script',
					'jquery/plugin/jquery.scrollTo.js' => 'static_script',
					'album.js' => 'module_music'
				)
			)
			->setEditor(array(
					'load' => 'simple'
				)
			)
			->assign(array(
					'aAlbum' => $aAlbum
				)
			);
        
        Music_Service_Music::instance()->getSectionMenu();
        return null;
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('music.component_controller_view_album_clean')) ? eval($sPlugin) : false);
	}
}