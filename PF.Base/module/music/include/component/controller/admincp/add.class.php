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
class Music_Component_Controller_Admincp_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
        if ($iDelete = $this->request()->getInt('delete')) {
            if (Music_Service_Genre_Process::instance()->delete($iDelete)) {
                $this->url()->send('admincp.music', null, _p('successfully_deleted_genres'));
            }
        }

        $bIsEdit = false;
        $aLanguages = Language_Service_Language::instance()->getAll();
        if ($iEditId = $this->request()->getInt('id'))  {
            $bIsEdit = true;
            $aGenre = Music_Service_Genre_Genre::instance()->getForEdit($iEditId);
            if (!isset($aGenre['genre_id'])){
                $this->url()->send('admincp.music', null, _p('not_found'));
            }
            $this->template()->assign([
                'aForms' => $aGenre,
                'iEditId' => $iEditId
            ]);
        }

        if ($aVals = $this->request()->getArray('val')) {
            if ($bIsEdit)  {
                if (Music_Service_Genre_Process::instance()->update($aVals))  {
                    $this->url()->send('admincp.music', [], _p('Genre successfully updated'));
                }
            } else {
                if (Music_Service_Genre_Process::instance()->add($aVals)) {
                    $this->url()->send('admincp.music', null, _p('genre_successfully_added'));
                }
            }
        }

        $this->template()->setTitle(($bIsEdit ? _p('Edit Genre') : _p('add_genre')))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("Music"), $this->url()->makeUrl('admincp.music'))
            ->setBreadCrumb(($bIsEdit ? _p('Edit Genre') : _p('add_genre')), $this->url()->makeUrl('admincp.music.add'))
            ->assign([
                'bIsEdit' => $bIsEdit,
                'aLanguages' => $aLanguages,
            ]);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('music.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
	}
}