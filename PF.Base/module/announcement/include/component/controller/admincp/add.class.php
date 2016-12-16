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
 * @version 		$Id: add.class.php 1274 2009-11-25 15:22:46Z Miguel_Espinoza $
 */
class Announcement_Component_Controller_Admincp_Add extends Phpfox_Component
{
/**
 * Controller
 */
    public function process()
    {
        $this->template()->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('announcements'), $this->url()->makeUrl('admincp.announcement'));
	$bIsEdit = false;
	$aLanguages = Language_Service_Language::instance()->get();
	
	if ($iEdit = $this->request()->getInt('id'))
	{
	    if ($aAnnouncement = Announcement_Service_Announcement::instance()->getAnnouncementById($iEdit))
	    {
	    // set the access user groups
		$this->template()->assign(array(
		    'aAnnouncement' => $aAnnouncement,
		    'aForms' => $aAnnouncement,
		    'aAccess' => $aAnnouncement['user_group']
		    ))
		    ->setBreadCrumb(_p('edit_an_announcement'), null, true);

		$bIsEdit = true;
	    }
	}
	else
	{
	    $this->template()->setBreadCrumb(_p('add_an_announcement'), null, true);
	    $aAnnouncement = array();
	    foreach ($aLanguages as $aLanguage)
	    {
		$aAnnouncement['language'][$aLanguage['language_id']] = array(
		    'subject' => '',
		    'intro' => '',
		    'content' => '',
		    'language_id' => $aLanguage['language_id'],
		    'title' => $aLanguage['title'],
		    'is_default' => $aLanguage['is_default']
		);
	    }
	}

	// Is user submitting a form?
	if ($aVal = $this->request()->get('val'))
	{

	// security check
	    if (!empty($aVal))
	    {
		if ($bIsEdit === false)
		{ // user is adding
		    if ($bAdd = Announcement_Service_Process::instance()->add($aVal))
		    {
				Phpfox::getLib('cache')->remove();
			$this->url()->send('admincp.announcement', null, _p('announcement_successfully_added'));
		    }
		}
		else
		{
		    if ($bEdit = Announcement_Service_Process::instance()->editAnnouncement($aAnnouncement['announcement_id'], $aVal))
		    {
				Phpfox::getLib('cache')->remove();
			$this->url()->send('admincp.announcement.add', array('id' => $aAnnouncement['announcement_id']), _p('announcement_successfully_updated'));
		    }
		}
	    }

	}

	// get the languages and pass them on to the template
	
	$aAge = array();
	for ($i = 18; $i <= 68; $i++)
	{
	    $aAge[$i] = $i;
	}
    
    
        $this->template()->setTitle(_p('add_an_announcement'))->setSectionTitle(_p('announcements'))->setEditor()
            ->setPhrase([
                    'min_age_cannot_be_higher_than_max_age',
                    'max_age_cannot_be_lower_than_the_min_age'
                ])->assign([
                'aLanguages'    => $aLanguages,
                'aAnnouncement' => $aAnnouncement,
                'aUserGroups'   => User_Service_Group_Group::instance()->get(),
                'aAge'          => $aAge,
                'iUser'         => Phpfox::getUserId()
            ])->setHeader(['admin_manage.js' => 'module_announcement']);
    }
    
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
	(($sPlugin = Phpfox_Plugin::get('announcement.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}