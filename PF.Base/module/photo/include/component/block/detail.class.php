<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Display the image details when viewing an image.
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: detail.class.php 5857 2013-05-10 08:05:37Z Raymond_Benc $
 */
class Photo_Component_Block_Detail extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (!$this->getParam('bIsValidImage'))
		{
			return false;
		}

		$aPhoto = $this->getParam('aPhoto');
		$bIsInPhoto = $this->getParam('is_in_photo');

		if ($aPhoto === null)
		{
			return false;
		}
		
		$sCategories = '';
		if (isset($aPhoto['categories']) && is_array($aPhoto['categories']))
		{
			foreach ($aPhoto['categories'] as $aCategory)
			{
				$sCategories .= $aCategory[0] . ',';
			}
			$sCategories = rtrim($sCategories, ',');
		}
		
		$aInfo = array(
			_p('added') => '<span itemprop="dateCreated">' . Phpfox::getTime(Phpfox::getParam('photo.photo_image_details_time_stamp'), $aPhoto['time_stamp']) . '</span>',
			_p('category') => $sCategories,
			_p('file_size') => Phpfox_File::instance()->filesize($aPhoto['file_size']),
			_p('resolution') => $aPhoto['width'] . '×' . $aPhoto['height'],
			_p('comments') => $aPhoto['total_comment'],
			_p('views') => '<span itemprop="interactionCount">' . $aPhoto['total_view'] . '</span>',
			_p('rating') => round($aPhoto['total_rating']),
			_p('battle_wins') => round($aPhoto['total_battle']),
			_p('downloads') => $aPhoto['total_download']
		);
		
		if ($bIsInPhoto)
		{
			unset($aInfo[_p('added')]);
		}
		
		foreach ($aInfo as $sKey => $mValue)
		{
			if (empty($mValue))
			{
				unset($aInfo[$sKey]);
			}
		}

		$this->template()->assign(array(
				'sHeader' => _p('image_details'),
				'aPhotoDetails' => $aInfo,
				'bIsInPhoto' => $bIsInPhoto,
				'sUrlPath' => (preg_match("/\{file\/pic\/(.*)\/(.*)\.jpg\}/i", $aPhoto['destination'], $aMatches) ? Phpfox::getParam('core.path') . str_replace(array('{', '}'), '', $aMatches[0]) : Phpfox::getLib('cdn')->getUrl(Phpfox::getParam('photo.url_photo') . sprintf($aPhoto['destination'], '_500'), $aPhoto['server_id']))
			)
		);
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('photo.component_block_detail_clean')) ? eval($sPlugin) : false);

		$this->template()->clean(array(
				'aPhotoDetails',
				'sEmbedCode',
				'sHeader'
			)
		);
	}
}