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
 * @package 		Phpfox_Component
 * @version 		$Id: add.class.php 6858 2013-11-06 17:27:45Z Fern $
 */ 
class Photo_Component_Controller_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if ($this->request()->get('picup') == '1')
		{
			// This redirects the user when Picup has finished uploading the photo
			if ($this->request()->isIOS())
			{
				die("<script type='text/javascript'>window.location.href = '" . $this->url()->makeUrl('photo.converting') . "'; </script> ");
			}
			else
			{
				die("<script type='text/javascript'>window.open('" . $this->url()->makeUrl('photo.converting') . "', 'my_form'); </script> ");
			}
		}
		// Make sure the user is allowed to upload an image
		Phpfox::isUser(true);
		Phpfox::getUserParam('photo.can_upload_photos', true);		

		$sModule = $this->request()->get('module', false);
		$iItem =  $this->request()->getInt('item', false);	

		$bCantUploadMore = false;
		$iMaxFileSize = (Phpfox::getUserParam('photo.photo_max_upload_size') === 0 ? null : ((Phpfox::getUserParam('photo.photo_max_upload_size') / 1024) * 1048576));
		$sMethod = 'simple';
		$sMethodUrl = str_replace(array('method_simple/','method_massuploader/'), '',$this->url()->getFullUrl()) . 'method_' . ($sMethod == 'simple' ? 'massuploader' : 'simple') . '/';
		
		if ($this->request()->isIOS())
		{
			$sMethod = 'simple';
		}
		$this->template()->setPhrase(array(
			'select_a_file_to_upload'
		));
		if ($sMethod == 'massuploader')
		{			
			$this->template()->setPhrase(array(							
						'you_can_upload_a_jpg_gif_or_png_file',
						'name',
						'status',
						'in_queue',
						'upload_failed_your_file_size_is_larger_then_our_limit_file_size',
						'more_queued_than_allowed',
					)
				)
				->setHeader(array(
				'massuploader/swfupload.js' => 'static_script',
				'massuploader/upload.js' => 'static_script',
				'<script type="text/javascript">
						// test for Firebug Lite (when preset it reloads the page so the user hash is not valid)
						if (typeof window.Firebug !="undefined" && window.Firebug.Lite != "undefined")
						{
							alert("You are using Firebug Lite which is known to have problems with our mass uploader. Please use the basic uploader or disable Firebug Lite and reload this page.");
						}
					$oSWF_settings =
					{
						object_holder: function()
						{
							return \'swf_photo_upload_button_holder\';
						},
						
						div_holder: function()
						{
							return \'swf_photo_upload_button\';
						},
						
						get_settings: function()
						{		
							swfu.setUploadURL("' . $this->url()->makeUrl('photo.frame') . '");
							swfu.setFileSizeLimit("'.$iMaxFileSize .' B");
							swfu.setFileUploadLimit('.Phpfox::getUserParam('photo.max_images_per_upload').');								
							swfu.setFileQueueLimit('.Phpfox::getUserParam('photo.max_images_per_upload').');
							swfu.customSettings.flash_user_id = '.Phpfox::getUserId() .';
							swfu.customSettings.sHash = "'.Core_Service_Core::instance()->getHashForUpload().'";
							swfu.customSettings.sAjaxCall = "photo.process";
							swfu.customSettings.sAjaxCallParams = "' . ($sModule !== false ? '&callback_module=' . $sModule . '&callback_item_id=' . $iItem . '&parent_user_id=' . $iItem . '': '') . '";
							swfu.customSettings.sAjaxCallAction = function(iTotalImages){								
								tb_show(\'\', \'\', null, \'' . Phpfox::getLib('image.helper')->display(array('theme' => 'ajax/add.gif', 'class' => 'v_middle')) . ' ' . _p('please_hold_while_your_images_are_being_processed_processing_image') . ' <span id="js_photo_upload_process_cnt">1</span> ' . _p('out_of') . ' \' + iTotalImages + \'.\', true);
								$Core.loadInit();
							}
							swfu.atFileQueue = function()
							{
								$(\'#js_photo_form :input\').each(function(iKey, oObject)
								{
									swfu.addPostParam($(oObject).attr(\'name\'), $(oObject).val());
								});
							}
						}
					}
				</script>',
				)
			);			
		}

		$this->template()->setPhrase([
            'maximum_number_of_images_you_can_upload_each_time_is'
        ])
        ->setHeader('<script type="text/javascript">$Behavior.photoProgressBarSettings = function(){ if ($Core.exists(\'#js_photo_form_holder\')) { oProgressBar = {html5upload: ' . (Phpfox::getParam('photo.html5_upload_photo') ? 'true' : 'false') . ', holder: \'#js_photo_form_holder\', progress_id: \'#js_progress_bar\', uploader: \'#js_photo_upload_input\', add_more: ' . ($bCantUploadMore ? 'false' : 'true') . ', max_upload: ' . Phpfox::getUserParam('photo.max_images_per_upload') . ', total: 1, frame_id: \'js_upload_frame\', file_id: \'image[]\', valid_file_ext: new Array(\'gif\', \'png\', \'jpg\', \'jpeg\')}; $Core.progressBarInit(); } }</script>');
        $aCallback = false;
		if ($sModule !== false && $iItem !== false && Phpfox::hasCallback($sModule, 'getPhotoDetails'))
		{
			if (($aCallback = Phpfox::callback($sModule . '.getPhotoDetails', array('group_id' => $iItem))))
			{
				$this->template()->setBreadCrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
				$this->template()->setBreadCrumb($aCallback['title'], $aCallback['url_home']);	
				if ($sModule == 'pages' && !Pages_Service_Pages::instance()->hasPerm($iItem, 'photo.share_photos'))
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
		
		$aPhotoAlbums = Photo_Service_Album_Album::instance()->getAll(Phpfox::getUserId(), $sModule, $iItem);
		foreach ($aPhotoAlbums as $iAlbumKey => $aPhotoAlbum)
		{
			if ($aPhotoAlbum['profile_id'] > 0)
			{
				unset($aPhotoAlbums[$iAlbumKey]);
			}
            if ($aPhotoAlbum['cover_id'] > 0)
			{
				unset($aPhotoAlbums[$iAlbumKey]);
			}
		}
		
		$this->template()->setTitle(_p('upload_photos'))
			->setBreadCrumb(_p('upload_photos'), $this->url()->makeUrl('photo.add'))
			->setHeader('cache', array(
					'progress.js' => 'static_script'
				)
			)
			->setPhrase(array(
					'not_a_valid_file_extension_we_only_allow_ext',
					'photo_uploads',
					'upload_complete_we_are_currently_processing_the_photos'
				)
			)
			->assign(array(
					'iMaxFileSize' => $iMaxFileSize,
					'iAlbumId' => $this->request()->getInt('album'),
					'aAlbums' => $aPhotoAlbums, // Get all the photo albums for this specific user
					'sModuleContainer' => $sModule,
					'iItem' => $iItem,
					'sMethod' => $sMethod,
					'sMethodUrl' => $sMethodUrl,
					'sCategories' => Photo_Service_Category_Category::instance()->get(false, true),
					'iTimestamp' => PHPFOX_TIME
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
		(($sPlugin = Phpfox_Plugin::get('photo.component_controller_add_clean')) ? eval($sPlugin) : false);
	}
}