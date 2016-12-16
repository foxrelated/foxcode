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
 * @version 		$Id: add.class.php 1522 2010-03-11 17:56:49Z Miguel_Espinoza $
 */
class Forum_Component_Controller_Admincp_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
        if ($iDeleteId = $this->request()->getInt('delete')) {
            Phpfox::getUserParam('forum.can_delete_forum', true);
            if (Forum_Service_Process::instance()->delete($iDeleteId)) {
                $this->url()->send('admincp.forum', null, _p('forum_successfully_deleted'));
            }
        }
        $aLanguages = Language_Service_Language::instance()->getAll();
		$bIsEdit = false;
		if ($iId = $this->request()->getInt('id')) {
			$bIsEdit = true;
			Phpfox::getUserParam('forum.can_edit_forum', true);
			$aForum = Forum_Service_Forum::instance()->getForEdit($iId);
			$this->template()->assign([
                'aForms'=> $aForum,
                'iId' => $iId
            ]);
            $sTitle = _p('editing_forum') . ': ' . Phpfox::getSoftPhrase($aForum['name']);
            $sForumParents = Forum_Service_Forum::instance()->active($aForum['parent_id'])->edit($aForum['forum_id'])->getJumpTool(true, $bIsEdit);
		} else {
			Phpfox::getUserParam('forum.can_add_new_forum', true);
            $sTitle = _p('create_new_form');
            $sForumParents = Forum_Service_Forum::instance()->active($this->request()->getInt('child'))->edit(0)->getJumpTool(true, $bIsEdit);
		}
		
		if ($aVals = $this->request()->getArray('val')) {
            if ($bIsEdit) {
                if (Forum_Service_Process::instance()->update($aVals)) {
                    $this->url()->send('admincp.forum', null, _p('forum_successfully_updated'));
                }
            } else {
                if (Forum_Service_Process::instance()->add($aVals)) {
                    $this->url()->send('admincp.forum.add', null, _p('forum_successfully_added'));
                }
            }
		}

		$this->template()->setTitle($sTitle)
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
			->setBreadCrumb(_p("Forum"), $this->url()->makeUrl('admincp.forum'))
			->setBreadCrumb($sTitle, $this->url()->makeUrl('admincp.forum.add'))
			->assign(array(
                'aLanguages' => $aLanguages,
				'sForumParents' => $sForumParents,
                'bIsEdit' => $bIsEdit
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('forum.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
	}
}