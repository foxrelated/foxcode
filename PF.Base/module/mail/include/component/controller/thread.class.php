<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Mail
 */
class Mail_Component_Controller_Thread extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		if (!Phpfox::getParam('mail.threaded_mail_conversation'))
		{
			$this->url()->send('mail');
		}

		$aVals = $this->request()->get('val');
		if ($aVals && ($iNewId = Mail_Service_Process::instance()->add($aVals)))
		{
			list($aCon, $aMessages) = Mail_Service_Mail::instance()->getThreadedMail($iNewId);
			$aMessages = array_reverse($aMessages);

			Phpfox_Template::instance()->assign(array(
					'aMail' => $aMessages[0],
					'aCon' => $aCon,
					'bIsLastMessage' => true
				)
			)->getTemplate('mail.block.entry');

			$content = ob_get_contents();
			ob_clean();
			return [
				'append' => [
					'to' => '#mail_threaded_new_message',
					'with' => $content
				]
			];
		}
		
		$iThreadId = $this->request()->getInt('id');
		
		list($aThread, $aMessages) = Mail_Service_Mail::instance()->getThreadedMail($iThreadId);
		
		if ($aThread === false)
		{
			return Phpfox_Error::display(_p('unable_to_find_a_conversation_history_with_this_user'));
		}		
		
		$aValidation = array(
			'message' => _p('add_reply')
		);		
		
		$oValid = Phpfox_Validator::instance()->set(array(
				'sFormName' => 'js_form', 
				'aParams' => $aValidation
			)
		);			
		
		if ($aThread['user_is_archive'])
		{
			$this->request()->set('view', 'trash');
		}
		
		Mail_Service_Mail::instance()->buildMenu();
		
		Mail_Service_Process::instance()->threadIsRead($aThread['thread_id']);

		$iUserCnt = 0;
		$sUsers = '';	
		$bCanViewThread = false;
		$bCanReplyThread = false;
		foreach ($aThread['users'] as $aUser)
		{	
			if ($aUser['user_id'] == Phpfox::getUserId())
			{
				$bCanViewThread = true;
			}
			
			if ($aUser['user_id'] == Phpfox::getUserId())
			{
				continue;
			}			
			
			$iUserCnt++;
			
			if ($iUserCnt == (count($aThread['users']) - 1) && (count($aThread['users']) - 1) > 1)
			{
				$sUsers .= ' &amp; ';
			}	
			else
			{
				if ($iUserCnt != '1')
				{
					$sUsers .= ', ';
				}
			}
			$sUsers .= $aUser['full_name'];

			if (User_Service_Privacy_Privacy::instance()->hasAccess('' . $aUser['user_id'] . '', 'mail.send_message')) {
				$bCanReplyThread = true;
			}
		}
		
		if (!$bCanViewThread)
		{			
			return Phpfox_Error::display('Unable to view this thread.');
		}
		else
		{
			$this->template()->setBreadCrumb(_p('mail'), $this->url()->makeUrl('mail'))->setBreadCrumb($sUsers, $this->url()->makeUrl('mail.thread', array('id' => $iThreadId)), true);
		}
		
		$this->template()->setTitle($sUsers)
			->setTitle(_p('mail'))
			->setHeader('cache', array(
					'mail.js' => 'module_mail',
					'jquery/plugin/jquery.scrollTo.js' => 'static_script'
				)
			)
            ->setEditor()
			->assign(array(
					'sCreateJs' => $oValid->createJS(),
					'sGetJsForm' => $oValid->getJsForm(false),				
					'aMessages' => $aMessages,
					'aThread' => $aThread,
					'sCurrentPageCnt' => ($this->request()->getInt('page', 0) + 1),
					'bCanReplyThread' => $bCanReplyThread
				)
			);
		
		$this->setParam('global_moderation', array(
				'name' => 'mail',
				'custom_fields' => '<div><input type="hidden" name="forward_thread_id" value="' . $aThread['thread_id'] . '" id="js_forward_thread_id" /></div>',
				'menu' => array(
					array(
						'phrase' => _p('forward'),
						'action' => 'forward'
					)			
				)
			)
		);
        if (!Phpfox::getUserParam('mail.can_add_attachment_on_mail')) {
            $this->template()->assign('bNoAttachaFile', true);
        }

        $this->setParam('attachment_share', array(
                'type' => 'mail',
                'inline' => true,
                'id' => 'js_form_mail'
            )
        );
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('mail.component_controller_thread_clean')) ? eval($sPlugin) : false);
	}	
}