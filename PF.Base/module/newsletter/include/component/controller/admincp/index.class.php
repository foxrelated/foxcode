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
 * @version 		$Id: index.class.php 1168 2009-10-09 14:20:37Z Raymond_Benc $
 */
class Newsletter_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		// Check if there is any task to handle
		if ($iId = $this->request()->get('delete'))
		{
			if(Newsletter_Service_Process::instance()->delete($iId)) // purge users
			{
				$this->url()->send('admincp.newsletter', null, _p('newsletter_successfully_deleted'));
			}
		}

		// check if there is any pending job or any user pending their newsletter.
		if ($sLink = Newsletter_Service_Newsletter::instance()->checkPending())
		{
			$this->template()->assign(array(
					'sError' => $sLink
				)
			);
		}
		$aNewsletters = Newsletter_Service_Newsletter::instance()->get();
		
		$this->template()->assign(array(
				'aNewsletters' => $aNewsletters
			)
		)
		->setTitle(_p('newsletter'))
		->setPhrase(array(
				'min_age_cannot_be_higher_than_max_age',
				'max_age_cannot_be_lower_than_the_min_age'
			)
		)
        ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
		->setBreadCrumb(_p('manage_newsletters'), null)
		->setEditor(array(
				)
			)
		->setHeader(array('add.js' => 'module_newsletter'));
	}
}