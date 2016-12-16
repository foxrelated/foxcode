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
 * @version 		$Id: upload.class.php 7189 2014-03-14 16:29:42Z Fern $
 */
class Music_Component_Controller_Upload extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		
		$sModule = $this->request()->get('module', false);
		$iItem =  $this->request()->getInt('item', false);		
		
		$aCallback = false;
		
		$bIsEdit = false;
		
		$aValidation = array(			
			'title' => _p('provide_a_name_for_this_song')
		);
        
        $oValidator = Phpfox_Validator::instance()->set([
                'sFormName' => 'js_music_form',
                'aParams'   => $aValidation
            ]);
        
        if (($iId = $this->request()->getInt('id')) && ($aEditSong = Music_Service_Music::instance()->getForEdit($iId)))
		{
			if ($aEditSong['module_id'] == 'pages')
			{
				Pages_Service_Pages::instance()->setIsInPage();
			}
			
			$bIsEdit = true;
			$this->template()->assign(array(
					'aForms' => $aEditSong
				)
			);
		}

		if (!$bIsEdit)
        {
            Phpfox::getUserParam('music.can_upload_music_public', true);
        }

        if ($sModule !== false && $iItem !== false && Phpfox::hasCallback($sModule, 'getMusicDetails'))
        {
            if (($aCallback = Phpfox::callback($sModule . '.getMusicDetails', array('item_id' => $iItem))))
            {
                $this->template()->setBreadCrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
                $this->template()->setBreadCrumb($aCallback['title'], $aCallback['url_home']);
                if (!$bIsEdit && Phpfox::hasCallback($sModule, 'checkPermission') && ! Phpfox::callback($sModule .'.checkPermission', $iItem, 'music.share_music'))
                {
                    return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
                }
            }
            else
            {
                return Phpfox_Error::display(_p('Cannot find the parent item.'));
            }
        }
        else if ($sModule && $iItem && $aCallback === false)
        {
            return Phpfox_Error::display(_p('Cannot find the parent item.'));
        }

        $sMethod = Phpfox::getParam('music.music_enable_mass_uploader') && ($this->request()->get('method','massuploader') == 'massuploader') ? 'massuploader' : 'simple';
		// used to tell the template where to link for the opposite method
		$sMethodUrl = str_replace(array('method_simple/','method_massuploader/'), '',$this->url()->getFullUrl()) . 'method_' . ($sMethod == 'simple' ? 'massuploader' : 'simple') . '/';
		$aVals = $this->request()->getArray('val');
		
		if (isset($aVals['method']))
		{
			$sMethod = $aVals['method'];
		}		
		
		if ($bIsEdit && !empty($aVals) && $this->request()->get('upload_via_song'))
		{
			if ($oValidator->isValid($aVals))
			{
				if (Music_Service_Process::instance()->update($aEditSong['song_id'], $aVals))
				{
					$this->url()->permalink('music', $aEditSong['song_id'], $aEditSong['title'], true, 'Song successfully updated.');
				}
			}
		}
		else 
		{
			if ($sMethod == 'simple' && !empty($aVals))
			{
				if (isset($aVals['music_title']))
				{
					$aVals['title'] = $aVals['music_title'];	
				}
				
				if ($oValidator->isValid($aVals))
				{
					if (($aSong = Music_Service_Process::instance()->upload($aVals, (isset($aVals['album_id']) ? (int) $aVals['album_id'] : 0))))
					{
						if (isset($aVals['iframe']))
						{
							if (isset($aVals['music_title']))
							{					
								$iFeedId = Feed_Service_Process::instance()->getLastId();
								
								echo "<script type=\"text/javascript\">";
								(($sPlugin = Phpfox_Plugin::get('music.component_controller_upload_feed')) ? eval($sPlugin) : false);
						    	echo 'window.parent.$.ajaxCall(\'music.displayFeed\', \'id=' . $iFeedId . '&song_id=' . $aSong['song_id'] . '\', \'GET\');';
						    	echo "</script>";
							}
							else 
							{									
								Phpfox::addMessage(_p('song_successfully_uploaded'));
								
								echo "<script type=\"text/javascript\">";								
								echo 'window.parent.location.href = "' . $this->url()->makeUrl('music.album.track', array('id' => $aVals['album_id'],'method' => 'simple') ) . '";';
								echo '</script>';
							}
						}
						else 
						{
							Phpfox::addMessage(_p('song_successfully_uploaded'));
							
							echo "<script type=\"text/javascript\">";		
							echo 'window.parent.location.href = "' . $this->url()->permalink('music', $aSong['song_id'], $aSong['title']) . '";';
							echo '</script>';
							exit;
						}
						exit;
					}
					else 
					{
						if (isset($aVals['music_title']))
						{	
							echo "<script type=\"text/javascript\">";
							echo 'window.parent.$Core.resetActivityFeedError(\'' . implode('<br />', Phpfox_Error::get()) . '\');';
							echo "</script>";		
						}
						else
						{
							echo "<script type=\"text/javascript\">";
							echo 'window.parent.$(\'#js_music_upload_song\').show(); window.parent.$(\'.js_upload_song\').remove();';
							echo 'window.parent.alert(\'' . implode('\n', Phpfox_Error::get()) . '\');';
							echo "</script>";
							exit;
						}
					}
				}
				else 
				{
					if (isset($aVals['music_title']))
					{	
						echo "<script type=\"text/javascript\">";
						echo 'window.parent.$Core.resetActivityFeedError(\'' . implode('<br />', Phpfox_Error::get()) . '\');';
						echo "</script>";
					}
					else
					{
						echo '<script type="text/javascript">';
						echo 'window.parent.$Core.resetActivityFeedError(\'' . implode('<br />', Phpfox_Error::get()) . '\');';
						echo 'window.parent.$Core.music.resetUploadForm(\'' . implode('<br />', Phpfox_Error::get()) . '\');';
						echo '</script>';
						exit;
					}
				}
			}
			elseif ($sMethod == 'massuploader' && isset($_FILES['Filedata']))
			{	
				$_FILES['mp3'] = $_FILES['Filedata'];
				
				if (($aSong = Music_Service_Process::instance()->upload($aVals, (isset($aVals['album_id']) ? (int) $aVals['album_id'] : 0))))
				{
					if (isset($aVals['inline']))
					{
						$aSong = Music_Service_Music::instance()->getSong($aSong['song_id']);
						
						$this->template()->assign(array(
								'aSong' => $aSong
							)
						);
						$this->template()->getTemplate('music.block.track-entry');
	
						Phpfox::addMessage(_p('song_successfully_uploaded'));
						
						echo 'window.location.href = "' . $this->url()->makeUrl('music.album.track', array('id' => $aVals['album_id'])) . '";';
						
						exit();
					}
					
					echo 'window.location.href = "' . $this->url()->permalink('music', $aSong['song_id'], $aSong['title']) . '";';
					exit();
				}
				else
				{
					echo '$(\'#js_music_upload_song\').show(); $(\'.js_upload_song\').remove();';
					echo 'alert(\'' . implode('\n', Phpfox_Error::get()) . '\');';
					exit;
				}
			}
		}

		if ($sMethod == 'massuploader')
		{
			$iMaxFileSize = (Phpfox::getUserParam('music.music_max_file_size') === 0 ? null : (Phpfox::getUserParam('music.music_max_file_size') ));
			$this->template()->setHeader('cache', array(
					'massuploader/swfupload.js' => 'static_script',
					'massuploader/upload.js' => 'static_script',
					'<script type="text/javascript">
						$oSWF_settings =
						{
							object_holder: function()
							{
								return \'swf_music_upload_button_holder\';
							},
							
							div_holder: function()
							{
								return \'swf_music_upload_button\';
							},
							
							get_settings: function()
							{		
								swfu.setUploadURL("' . $this->url()->makeUrl('music.upload') . '");
								swfu.setFileSizeLimit("' . ($iMaxFileSize). ' MB");
								swfu.setFileUploadLimit(1);
								swfu.setFileQueueLimit(1);
								swfu.customSettings.flash_user_id = '.Phpfox::getUserId() .';
								swfu.customSettings.sHash = "'.Core_Service_Core::instance()->getHashForUpload().'";
								swfu.setFileTypes("*.mp3","*.mp3");
								swfu.atFileQueue = function()
								{
									$(\'#js_music_form :input\').each(function(iKey, oObject)
									{
										swfu.addPostParam($(oObject).attr(\'name\'), $(oObject).val());
									});
								}
							}
						}
					</script>',
					'upload.css' => 'module_music'
				)
			)->setPhrase([
                        'name',
                        'status',
                        'in_queue',
                        'upload_failed_your_file_size_is_larger_then_our_limit_file_size',
                        'more_queued_than_allowed'
                    ]);
        }
        
        $this->template()->setTitle(($bIsEdit ? _p('editing_song') . ': ' . $aEditSong['title'] : _p('upload_a_song')))
			->setBreadCrumb(_p('music'), ($aCallback === false ? $this->url()->makeUrl('music') : $aCallback['url_home_photo']))
			->setBreadCrumb(($bIsEdit ? _p('editing_song') . ': ' . $aEditSong['title'] : _p('upload_a_song')), ($bIsEdit) ? $this->url()->makeUrl('music.upload', array('id'=>$iId)) : $this->url()->makeUrl('music.upload'), true)
			->setFullSite()
			->setPhrase(array(
					'select_an_mp3'
				)
			)			
			->setHeader('cache', array(
					'upload.js' => 'module_music',
					'progress.css' => 'style_css',
					'progress.js' => 'static_script',
					'<script type="text/javascript">$Behavior.musicUpload = function(){ if ($Core.exists(\'#js_music_form_holder\')) { oProgressBar = {holder: \'#js_music_form_holder\', progress_id: \'#js_progress_bar\', total: 1, max_upload: 1, uploader: \'#js_progress_uploader\', frame_id: \'js_upload_frame\', file_id: \'mp3\'}; $Core.progressBarInit(); }}</script>'							
				)
			)
			->assign(array(
					'sModule' => $sModule,
					'iItem' => $iItem,					
					'bIsEdit' => $bIsEdit,
					'aUploadAlbums' => Music_Service_Album_Album::instance()->getForUpload($aCallback),
					'sCreateJs' => $oValidator->createJS(),
					'sGetJsForm' => $oValidator->getJsForm(false),
					'iUploadLimit' => Phpfox_File::instance()->getLimit(Phpfox::getUserParam('music.music_max_file_size')),
					'aGenres' => Music_Service_Genre_Genre::instance()->getList(),
					'sMethod' => $sMethod,
					'sMethodUrl' => $sMethodUrl
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
		(($sPlugin = Phpfox_Plugin::get('music.component_controller_upload_clean')) ? eval($sPlugin) : false);
	}
}