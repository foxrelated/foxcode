<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package 		Phpfox_Component
 * @version 		$Id: index.class.php 3830 2011-12-19 12:55:57Z Miguel_Espinoza $
 */
class Announcement_Component_Controller_Index extends Phpfox_Component
{
/**
 * Controller
 */
    public function process()
    {
    	Phpfox::getUserParam('announcement.can_view_announcements', true);
			
		$iId = $this->request()->getInt('id');
		if ($iId > 0)
		{
		/**
		 * @todo assess if we can use getAnnouncementById instead of getLatest here
		 */
			$aAnnouncements = Announcement_Service_Announcement::instance()->getLatest($iId);

			if ($aAnnouncements === false)
			{
				$this->url()->send('announcement.view', null, _p('that_announcement_does_not_exist'));
			}
            if (is_array($aAnnouncements)){
                $aAnnouncements = reset($aAnnouncements);
            }
			$sSubject = $aAnnouncements['subject_var'];
			
			$this->template()
				->setBreadCrumb(_p('announcements'), $this->url()->makeUrl('announcement.view'))
				->setBreadCrumb(_p($sSubject), $this->url()->current(), true)
				->setTitle(_p($sSubject))
				->assign(array('aAnnouncements' => array($aAnnouncements)));

		}
		else
		{
			$aAnnouncements = Announcement_Service_Announcement::instance()->getLatest();
			
			$this->template()->setBreadCrumb(_p('announcements'), $this->url()->makeUrl('announcement.view'))
				->setTitle(_p('announcements'))
				->assign(array('aAnnouncements' => ($aAnnouncements)));
		}

    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
	(($sPlugin = Phpfox_Plugin::get('announcement.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}