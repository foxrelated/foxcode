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
 * @package  		Module_Blog
 * @version 		$Id: ajax.class.php 3642 2011-12-02 10:01:15Z Miguel_Espinoza $
 */
class Blog_Component_Ajax_Ajax extends Phpfox_Ajax
{
    /**
     * Display blog preview. For preview a  blog before publish
     */
	public function preview()
	{
		Phpfox::getBlock('blog.preview', array('sText' => $this->get('text')));
	}

	public function updateCategory()
	{
		Phpfox::isAdmin(true);
        Blog_Service_Category_Process::instance()->update($this->get('category_id'), $this->get('quick_edit_input'));

		$this->call('window.location.href = \'' . Phpfox_Url::instance()->makeUrl('admincp.blog') . '\'');
	}

	public function getNew()
	{
		Phpfox::getBlock('blog.new');

		$this->html('#' . $this->get('id'), $this->getContent(false));
		$this->call('$(\'#' . $this->get('id') . '\').parents(\'.block:first\').find(\'.bottom li a\').attr(\'href\', \'' . Phpfox_Url::instance()->makeUrl('blog') . '\');');
	}

	public function quickSubmit()
	{
		$sId = $this->get('id');
		$sText = $this->get('sText');

		// get the id from the sId variable
		$iId = preg_replace('/[^0-9]/', '', $sId);

		// Only update if text is not empty
		Blog_Service_Process::instance()->updateBlogText($iId, $sText);
		$this->call('window.location.href="' . $this->get('sUrl') . '";');

	}

	public function approve()
	{
        if (Blog_Service_Process::instance()->approve($this->get('id'))) {
            if ($this->get('inline')) {
                $this->alert(_p('blog_has_been_approved'), _p('blog_approved'), 300, 100, true);
                $this->hide('#js_item_bar_approve_image');
                $this->hide('.js_moderation_off');
                $this->show('.js_moderation_on');
            }
        }
	}
	
	public function moderation()
	{
		Phpfox::isUser(true);
		$sMessage = '';
		switch ($this->get('action'))
		{
			case 'approve':
				Phpfox::getUserParam('blog.can_approve_blogs', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					Blog_Service_Process::instance()->approve($iId);
					$this->call('$("#js_blog_entry' . $iId . '").prev().remove();');
					$this->remove('#js_blog_entry' . $iId);
				}
				$this->updateCount();
				$sMessage = _p('blog_s_successfully_approved');
				break;
			case 'delete':
				Phpfox::getUserParam('blog.delete_user_blog', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
                    Blog_Service_Process::instance()->delete($iId);
					$this->call('$("#js_blog_entry' . $iId . '").prev().remove();');
					$this->remove('#js_blog_entry' . $iId);
				}
				$sMessage = _p('blog_s_successfully_deleted');
				break;
		}

		$this->alert($sMessage, _p('moderation'), 300, 150, true);
		$this->hide('.moderation_process');
	}

    public function categorySubOrdering(){
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Core_Service_Process::instance()->updateOrdering(array(
                'table' => 'blog_category',
                'key' => 'category_id',
                'values' => $aVals['ordering']
            )
        );

        Phpfox::getLib('cache')->remove('blog', 'substr');
    }
    
    public function toggleCategory(){
        $iCategoryId = $this->get('id');
        $iActive = $this->get('active');
        Blog_Service_Category_Process::instance()->toggleCategory($iCategoryId, $iActive);
    }
}