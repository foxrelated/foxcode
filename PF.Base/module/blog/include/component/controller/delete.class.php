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
 * @version 		$Id: delete.class.php 5840 2013-05-09 06:14:35Z Raymond_Benc $
 */
class Blog_Component_Controller_Delete extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{	
		Phpfox::isUser(true);		
		
		if ($iId = $this->request()->getInt('id'))
		{
			$mReturn = Blog_Service_Process::instance()->deleteInline($iId);
			if (isset($mReturn['module_id']) && $mReturn['module_id'] == 'pages')
			{
				$this->url()->send($mReturn['module_id'] . '.' . $mReturn['item_id'] . '.blog', array(), _p('blog_successfully_deleted'));
			}
			
			$this->url()->send('blog', array(), _p('blog_successfully_deleted'));
		}
	}
}