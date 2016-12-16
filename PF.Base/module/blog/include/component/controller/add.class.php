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
 * @version 		$Id: add.class.php 6313 2013-07-19 07:12:03Z Raymond_Benc $
 */
class Blog_Component_Controller_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		$bIsEdit = false;
		$bCanEditPersonalData = true;
		
		$sModule = $this->request()->get('module');
		$iItemId = $this->request()->getInt('item');
		if (($aVals = $this->request()->getArray('val')) && !empty($aVals['module_id']) && !empty($aVals['item_id']))
		{
			$sModule = $aVals['module_id'];
			$iItemId = $aVals['item_id'];
		}
        if (!empty($sModule) && !empty($iItemId)) {
            $this->template()->assign([
                'sModule' => $sModule,
                'iItem'   => $iItemId
            ]);
        }
        
        if (($iEditId = $this->request()->getInt('id')))
		{	
			$oBlog = Blog_Service_Blog::instance();
			
			$aRow = $oBlog->getBlogForEdit($iEditId);

            if (empty($aRow) || empty($aRow['blog_id']))
            {
                return Phpfox_Error::display(_p('blog_not_found'));
            }

			if ($aRow['is_approved'] != '1' && 
				($aRow['user_id'] != Phpfox::getUserId() && !Phpfox::getUserParam('blog.edit_user_blog')) )
			{
				return Phpfox_Error::display(_p('unable_to_edit_this_blog'));
			}
			
			if (Phpfox::isModule('tag'))
			{
				$aTags = Tag_Service_Tag::instance()->getTagsById('blog', $aRow['blog_id']);
				if (isset($aTags[$aRow['blog_id']]))
				{
					$aRow['tag_list'] = '';					
					foreach ($aTags[$aRow['blog_id']] as $aTag)
					{
						$aRow['tag_list'] .= ' ' . $aTag['tag_text'] . ',';	
					}
					$aRow['tag_list'] = trim(trim($aRow['tag_list'], ','));
				}
			}

			(Phpfox::getUserId() == $aRow['user_id'] ? Phpfox::getUserParam('blog.edit_own_blog', true) : Phpfox::getUserParam('blog.edit_user_blog', true));
			if (Phpfox::getUserParam('blog.edit_user_blog') && Phpfox::getUserId() != $aRow['user_id'])
			{
				$bCanEditPersonalData = false;
			}
			
			$aCategories = Blog_Service_Category_Category::instance()->getCategoriesById($aRow['blog_id']);
			$sCategories = '';
			if (isset($aCategories[$aRow['blog_id']]))
			{
				foreach ($aCategories[$aRow['blog_id']] as $aCategory)
				{
					$sCategories .= $aCategory['category_id'] . ',';	
				}
			}			
			$aRow['selected_categories'] = $sCategories;							
					
			$bIsEdit = true;
			$this->setParam('aSelectedCategories', (isset($aCategories[$aRow['blog_id']]) ? $aCategories[$aRow['blog_id']] : []));
			$this->template()->assign(array(
					'aForms' => $aRow					
				)
			);
			
			if (!empty($aRow['module_id']))
			{
				$sModule = $aRow['module_id'];
				$iItemId = $aRow['item_id'];
			}

			(($sPlugin = Phpfox_Plugin::get('blog.component_controller_add_process_edit')) ? eval($sPlugin) : false);
		}
		else 
		{
			Phpfox::getUserParam('blog.add_new_blog', true);

			http_cache()->set();
		}
		
		$aValidation = array(
			'title' => array(
				'def' => 'required',
				'title' => _p('fill_title_for_blog')
			),
			'text' => array(
				'def' => 'required',
				'title' => _p('add_content_to_blog')
			)		
		);
		
		if (Phpfox::isModule('captcha') && Phpfox::getUserParam('captcha.captcha_on_blog_add'))
		{
			$aValidation['image_verification'] = _p('complete_captcha_challenge');
		}		
		
		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_add_process_validation')) ? eval($sPlugin) : false);

		$oValid = Phpfox_Validator::instance()->set(array(
				'sFormName' => 'core_js_blog_form', 
				'aParams' => $aValidation
			)
		);

        $aCallback = null;

		if (!empty($sModule) && Phpfox::hasCallback($sModule, 'getItem'))
		{
			$aCallback = Phpfox::callback($sModule . '.getItem' , $iItemId);
            if ($aCallback === false)
            {
                return Phpfox_Error::display(_p('Cannot find the parent item.'));
            }
			$bCheckParentPrivacy = true;
			if (!$bIsEdit && Phpfox::hasCallback($sModule, 'checkPermission')) {
				$bCheckParentPrivacy = Phpfox::callback($sModule . '.checkPermission' , $iItemId, 'blog.share_blogs');
			}

			if (!$bCheckParentPrivacy)
			{
				return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
			}

			if ($bIsEdit)
			{
				$sUrl = $this->url()->makeUrl('blog', array('add', 'id' => $iEditId));
				$sCrumb = _p('editing_blog') . ': ' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Core_Service_Core::instance()->getEditTitleSize(), '...');
			}
			else
			{
				$sUrl = $this->url()->makeUrl('blog', array('add', 'module' => $aCallback['module'], 'item' => $iItemId));
				$sCrumb = _p('adding_a_new_blog');
			}
			
			$this->template()
			->setBreadCrumb(isset($aCallback['module_title']) ? $aCallback['module_title'] : _p($sModule), $this->url()->makeUrl($sModule))
			->setBreadCrumb($aCallback['title'], Phpfox::permalink($sModule, $iItemId))
			->setBreadCrumb(_p('blogs'), $this->url()->makeUrl($sModule, array($iItemId, 'blog')))
			->setBreadCrumb($sCrumb, $sUrl, true)
			;
		}
		else
		{
		    if (!empty($sModule) && !empty($iItemId) && $sModule != 'blog' && $aCallback === null)
            {
                return Phpfox_Error::display(_p('Cannot find the parent item.'));
            }

			$this->template()
			->setBreadCrumb(_p('blogs'), $this->url()->makeUrl('blog'))
			->setBreadCrumb((!empty($iEditId) ? _p('editing_blog') . ': ' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Core_Service_Core::instance()->getEditTitleSize(), '...') : _p('adding_a_new_blog')), ($iEditId > 0 ? $this->url()->makeUrl('blog', array('add', 'id' => $iEditId)) : $this->url()->makeUrl('blog', array('add'))), true);
		
		}		

		if ($aVals = $this->request()->getArray('val'))
		{		
			if ($oValid->isValid($aVals))
			{					
				// Add the new blog
				if (isset($aVals['publish']) || isset($aVals['draft']))
				{
                    if (isset($aVals['draft'])) {
                        $aVals['post_status'] = 2;
                        $sMessage = _p('blog_successfully_saved');
                    } else {
                        $sMessage = _p('your_blog_has_been_added');
                    }
					
					if (($iFlood = Phpfox::getUserParam('blog.flood_control_blog')) !== 0)
					{
						$aFlood = array(
							'action' => 'last_post', // The SPAM action
							'params' => array(
								'field' => 'time_stamp', // The time stamp field
								'table' => Phpfox::getT('blog'), // Database table we plan to check
								'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
								'time_stamp' => $iFlood * 60 // Seconds);	
							)
						);
							 			
						// actually check if flooding
						if (Phpfox::getLib('spam')->check($aFlood))
						{
							Phpfox_Error::set(_p('your_are_posting_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
						}
					}					
					
					if (Phpfox_Error::isPassed())
					{
						$iId = Blog_Service_Process::instance()->add($aVals);
					}
				}
				
				// Update a blog
				if ((isset($aVals['update']) || isset($aVals['draft_update']) || isset($aVals['draft_publish'])) && isset($aRow['blog_id']) && $bIsEdit)
				{
					if (isset($aVals['draft_publish']))
					{
						$aVals['post_status'] = 1;	
					}
					
					// Update the blog
					$iId = Blog_Service_Process::instance()->update($aRow['blog_id'], $aRow['user_id'], $aVals, $aRow);
					$sMessage = _p('blog_updated');
				}				
				
				if (isset($iId) && $iId)
				{		
					Phpfox::permalink('blog', $iId, $aVals['title'], true, $sMessage);
				}
			}
		}
		
		$this->template()
			->setTitle((!empty($iEditId) ? _p('editing_blog') . ': ' . $aRow['title'] : _p('adding_a_new_blog')))
			->setFullSite()	
			->assign(array(
					'sCreateJs' => $oValid->createJS(),
					'sGetJsForm' => $oValid->getJsForm(),
					'bIsEdit' => $bIsEdit,
					'bCanEditPersonalData' => $bCanEditPersonalData,
					'bCanCustomPrivacy' => (empty($sModule) ? true : !Phpfox::hasCallback($sModule, 'inheritPrivacy'))
				)
			)
			->setHeader('cache', array(
				'jquery/plugin/jquery.highlightFade.js' => 'static_script',
				'switch_legend.js' => 'static_script',
				'switch_menu.js' => 'static_script',
			)
		);	
		
		if (Phpfox::isModule('attachment') && Phpfox::getUserParam('attachment.can_attach_on_blog'))
		{
			$this->setParam('attachment_share', array(
					'type' => 'blog',
					'id' => 'core_js_blog_form',
					'edit_id' => ($bIsEdit ? $iEditId : 0)
				)
			);
		}
			
		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_add_process')) ? eval($sPlugin) : false);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_add_clean')) ? eval($sPlugin) : false);
	}
}