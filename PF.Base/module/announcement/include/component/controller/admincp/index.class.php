<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Display the image details when viewing an image.
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_Announcement
 * @version 		$Id: index.class.php 979 2009-09-14 14:05:38Z Raymond_Benc $
 */
class Announcement_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{		
		if ($iDelete = $this->request()->getInt('delete'))
		{
			if ($bDel = Announcement_Service_Process::instance()->delete((int) $iDelete))
			{
				$this->url()->send('admincp.announcement', null, _p('announcement_successfully_deleted'));
			}
		}
		
		// find the default language to pass it to the template so it can load the appropriate announcements
		// by calling the block manage only once.
		// get available languages
		$aLanguages = Language_Service_Language::instance()->get();
		$sDefLanguage = '';
		foreach ($aLanguages as $aLanguage)
		{
			if ($aLanguage['is_default']) 
			{
				$sDefLanguage = $aLanguage['language_id'];
			}
		}
		
		$aAnnouncements = Announcement_Service_Announcement::instance()->getAnnouncementsByLanguage($sDefLanguage);

		$this->template()->setTitle(_p('announcements'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
			->setBreadCrumb(_p('announcements'), $this->url()->makeUrl('admincp.announcement'))
			->setSectionTitle(_p('announcements'))
			->setActionMenu([
				_p('new_announcement') => [
					'class' => '',
					'url' => $this->url()->makeUrl('admincp.announcement.add')
				]
			])
			->assign(array(
				'aLanguages' => $aLanguages,
				'sDefaultLanguage' => $sDefLanguage,
				'aAnnouncements' => $aAnnouncements
			)
		);
	}
}