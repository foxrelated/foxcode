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
 * @package  		Module_Music
 * @version 		$Id: ajax.class.php 5422 2013-02-25 13:13:56Z Raymond_Benc $
 */
class Music_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function deleteImage()
	{
		if (Music_Service_Album_Process::instance()->deleteImage($this->get('id')))
		{
			
		}
	}
	
	public function play()
	{
		Music_Service_Process::instance()->play($this->get('id'));
		
		$this->removeClass('.js_music_track', 'isSelected')
			->addClass('#js_music_track_' . $this->get('id'), 'isSelected');
	}
	
	public function playInFeed()
	{
		$aSong = Music_Service_Music::instance()->getSong($this->get('id'));

        if (!isset($aSong['song_id'])) {
            $this->alert(_p('unable_to_find_the_song_you_are_trying_to_play'));
            return false;
        }
		
		Music_Service_Process::instance()->play($aSong['song_id']);
		
		$sSongPath = $aSong['song_path'];
		
		$sWidth = '425px';
        if ($this->get('track')) {
            $sWidth = '100%';
        }

        if ($this->get('is_player')) {
            $sDivId = 'js_music_player_all';
        } else {
            $sDivId = 'js_tmp_music_player_' . $aSong['song_id'];
            if ($this->get('feed_id') && $this->get('id')) {
                $this->call('$(\'#js_play_music_song_' . $this->get('feed_id') . $aSong['song_id'] . '\').find(\'.activity_feed_content_link:first\').html(\'<div id="' . $sDivId . '" style="width:425px; height:30px;"></div>\');');
            } elseif ($this->get('feed_id')) {
                $this->call('$(\'#js_item_feed_' . $this->get('feed_id') . '\').find(\'.activity_feed_content_link:first\').html(\'<div id="' . $sDivId . '" style="width:425px; height:30px;"></div>\');');
            } else {
                $this->call('$(\'#' . ($this->get('track') ? $this->get('track') : 'js_controller_music_play_' . $this->get('id') . '') . '\').html(\'<div id="' . $sDivId . '" style="width:' . $sWidth . '; height:30px;"></div>\');');
            }
        }

		$this->call('var iWait = 250;if (typeof $Core.player == "undefined"){iWait = 2000;}setTimeout(function(){$Core.player.load({id: \'' . $sDivId . '\', auto: true, type: \'music\', play: \'' . $sSongPath . '\'});$("#'. $sDivId .'").css({height: "50px", width: "100%"});}, iWait);');		
	}
	
	public function userProfile()
	{
		if (Music_Service_Process::instance()->addForProfile($this->get('id'), $this->get('type')))
		{
			if ($this->get('type'))
			{
				$this->show('#js_music_profile_remove_' . $this->get('id'))->hide('#js_music_profile_add_' . $this->get('id'))->alert(_p('this_song_has_been_added_to_your_profile'));
			}
			else 
			{
				if ($this->get('remove'))
				{
					$this->remove('#js_music_track_' . $this->get('id'));
				}
				
				$this->show('#js_music_profile_add_' . $this->get('id'))->hide('#js_music_profile_remove_' . $this->get('id'))->alert(_p('this_song_has_been_removed_from_your_profile'));
			}
		}
	}
	
	public function featureSong()
	{
		if (Music_Service_Process::instance()->feature($this->get('song_id'), $this->get('type')))
		{
			if ($this->get('type'))
			{
				$this->addClass('#js_controller_music_track_' . $this->get('song_id'), 'row_featured');
				$this->alert(_p('song_successfully_featured'), _p('feature'), 300, 150, true);
			}
			else 
			{
				$this->removeClass('#js_controller_music_track_' . $this->get('song_id'), 'row_featured');
				$this->alert(_p('song_successfully_un_featured'), _p('un_feature'), 300, 150, true);
			}				
		}
	}	
	
	public function featureAlbum()
	{
		if (Music_Service_Album_Process::instance()->feature($this->get('album_id'), $this->get('type')))
		{
			if ($this->get('type'))
			{
				$this->addClass('#js_album_' . $this->get('album_id'), 'row_featured');
				$this->alert(_p('album_successfully_featured'), _p('feature'), 300, 150, true);
			}
			else 
			{
				$this->removeClass('#js_album_' . $this->get('album_id'), 'row_featured');
				$this->alert(_p('album_successfully_un_featured'), _p('un_feature'), 300, 150, true);
			}			
		}
	}
	
	public function sponsorSong()
	{
	    Phpfox::isUser(true);
	    if (Music_Service_Process::instance()->sponsorSong($this->get('song_id'), $this->get('type')))
	    {
		if ($this->get('type') == '1')
		{
			Phpfox::getService('ad.process')->addSponsor(array('module' => 'music', 'section' => 'song', 'item_id' => $this->get('song_id')));
		    // image was sponsored
		    $sHtml = '<a href="#" title="' . _p('unsponsor_this_song') . '" onclick="$.ajaxCall(\'music.sponsorSong\', \'song_id=' . $this->get('song_id') . '&amp;type=0\'); return false;"><img src="' . $this->template()->getStyle('image', 'misc/medal_gold_delete.png') . '" class="v_middle" alt="'._p('unsponsor_this_song').'" width="16" height="16" /></a>';
		}
		else
		{
			Phpfox::getService('ad.process')->deleteAdminSponsor('music-song', $this->get('song_id'));
		    $sHtml = '<a href="#" title="' . _p('sponsor_this_song') . '" onclick="$.ajaxCall(\'music.sponsorSong\', \'song_id=' . $this->get('song_id') . '&amp;type=1\'); return false;"><img src="' . $this->template()->getStyle('image', 'misc/medal_gold_add.png') . '" class="v_middle" alt="'._p('sponsor_this_song').'" width="16" height="16" /></a>';
		}
		$this->html('#js_song_sponsor_' . $this->get('song_id'), $sHtml)
			->alert($this->get('type') == '1' ? _p('song_successfully_sponsored') : _p('song_successfully_un_sponsored'));
		if($this->get('type') == '1')
		{
		    $this->call('$("#js_controller_music_track_'.$this->get('song_id').'").addClass("row_sponsored");');
		}
		else
		{
		    $this->call('$("#js_controller_music_track_'.$this->get('song_id').'").removeClass("row_sponsored");');
		}
	    }

	}

	public function sponsorAlbum()
	{
	    Phpfox::isUser(true);
	    
	    if (true == Music_Service_Process::instance()->sponsorAlbum($this->get('album_id'), $this->get('type')))
	    {
            if ($this->get('type') == '1')
            {
                Phpfox::getService('ad.process')->addSponsor(array('module' => 'music', 'section' => 'album', 'item_id' => $this->get('album_id')));
                //item was sponsored
                $sHtml = '<a href="#" title="' . _p('unsponsor_this_album') . '" onclick="$.ajaxCall(\'music.sponsorAlbum\', \'album_id=' . $this->get('album_id') . '&amp;type=0\'); return false;">'._p('unsponsor_this_album').'</a>';
            }
            else
            {
                Phpfox::getService('ad.process')->deleteAdminSponsor('music-album', $this->get('album_id'));
                $sHtml = '<a href="#" title="' . _p('sponsor_this_album') . '" onclick="$.ajaxCall(\'music.sponsorAlbum\', \'album_id=' . $this->get('album_id') . '&amp;type=1\'); return false;">'._p('sponsor_this_album').'</a>';
            }
            $this->html('#js_sponsor_album_' . $this->get('album_id'), $sHtml)
                ->alert($this->get('type') == '1' ? _p('album_successfully_sponsored') : _p('album_successfully_un_sponsored'));
            if($this->get('type') == '1')
            {
                $this->addClass('#js_album_' . $this->get('album_id'), 'row_sponsored');
            }
            else
            {
                $this->removeClass('#js_album_' . $this->get('album_id'), 'row_sponsored');
            }
	    }
	}

	public function approveSong()
	{
		if (Music_Service_Process::instance()->approve($this->get('id')))
		{
			$this->alert(_p('song_has_been_approved'), _p('song_approved'), 300, 100, true);
			$this->hide('#js_item_bar_approve_image');
			$this->hide('.js_moderation_off'); 
			$this->show('.js_moderation_on');				
		}
	}

	public function setName()
	{
		$sName = $this->get('sTitle');
		$iSong = (int)$this->get('iSong');
		$sTitle = Music_Service_Song_Process::instance()->setName($iSong, $sName, true);
		if (!empty($sTitle))
		{			
			Phpfox::addMessage(_p('your_song_was_named_successfully'));
			$this->call('location.href = "'.Phpfox_Url::instance()->makeUrl('music.' . $iSong . '.' . $sTitle).'";');
		}
	}
	
	public function moderation()
	{
		Phpfox::isUser(true);	
		
		switch ($this->get('action'))
		{
			case 'approve':
				Phpfox::getUserParam('music.can_approve_songs', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					Music_Service_Process::instance()->approve($iId);
					$this->call('$("#js_controller_music_track_' . $iId . '").prev().remove();');
					$this->remove('#js_controller_music_track_' . $iId);
				}
				$this->updateCount();
				$sMessage = _p('songs_s_successfully_approved');
				break;
			case 'delete':
				Phpfox::getUserParam('music.can_delete_other_tracks', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					Music_Service_Process::instance()->delete($iId);
					$this->call('$("#js_controller_music_track_' . $iId . '").prev().remove();');
					$this->remove('#js_controller_music_track_' . $iId);
				}
				$sMessage = _p('songs_s_successfully_deleted');
				break;
			case 'feature':
				Phpfox::getUserParam('music.can_feature_songs', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					Music_Service_Process::instance()->feature($iId, 1);
					$this->addClass('#js_controller_music_track_' . $iId, 'row_featured');
				}
				$sMessage = _p('songs_s_successfully_featured');
				break;
			case 'un-feature':
				Phpfox::getUserParam('music.can_feature_songs', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					Music_Service_Process::instance()->feature($iId, 0);
					$this->removeClass('#js_controller_music_track_' . $iId, 'row_featured');
				}
				$sMessage = _p('songs_s_successfully_un_featured');
				break;
            default:
                $sMessage = '';
                break;
		}

		$this->alert($sMessage, _p('moderation'), 300, 150, true);
		$this->hide('.moderation_process');
	}

	public function moderationAlbum()
	{
		Phpfox::isUser(true);

		switch ($this->get('action'))
		{
			case 'delete':
				Phpfox::getUserParam('music.can_delete_other_music_albums', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
                    Music_Service_Album_Process::instance()->delete($iId);
					$this->call('$("#js_album_' . $iId . '").prev().remove();');
					$this->remove('#js_album_' . $iId);
				}
				$sMessage = _p('albums_s_successfully_deleted');
				break;
			case 'feature':
				Phpfox::getUserParam('music.can_feature_music_albums', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
                    Music_Service_Album_Process::instance()->feature($iId, 1);
					$this->addClass('#js_album_' . $iId, 'row_featured');
				}
				$sMessage = _p('albums_s_successfully_featured');
				break;
			case 'un-feature':
				Phpfox::getUserParam('music.can_feature_music_albums', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
                    Music_Service_Album_Process::instance()->feature($iId, 0);
					$this->removeClass('#js_album_' . $iId, 'row_featured');
				}
				$sMessage = _p('albums_s_successfully_un_featured');
				break;
            default:
                $sMessage = '';
                break;
		}

		$this->alert($sMessage, _p('moderation'), 300, 150, true);
		$this->hide('.moderation_process');
	}

	public function displayFeed()
	{
		Feed_Service_Feed::instance()->processAjax($this->get('id'));
	}
    
    /**
     * be used on adminCP.
     * For ordering Genre
     */
	public function genreOrdering()
    {
        Phpfox::isAdmin(true);
    
        $aVals = $this->get('val');
        Core_Service_Process::instance()->updateOrdering([
            'table'  => 'music_genre',
            'key'    => 'genre_id',
            'values' => $aVals['ordering']
        ]);
    
        Phpfox::getLib('cache')->remove('music_genre_', 'substr');
    }
    
    /**
     * Be used on adminCP
     * Toggle Genre
     */
    public function toggleGenre()
    {
        $iGenreID = $this->get('id');
        $iActive = $this->get('active');
        Music_Service_Genre_Process::instance()->toggleGenre($iGenreID, $iActive);
    }
}