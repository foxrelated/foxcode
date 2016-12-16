<?php
defined('PHPFOX') or exit('NO DICE!');

class Mail_Component_Controller_Panel extends Phpfox_Component {
	public function process() {
		Phpfox::isUser(true);

		$iPageSize = 30;
		$this->search()->set(array(
            'type' => 'mail',
            'field' => 'mail.mail_id',
            'search_tool' => array(
                'table_alias' => 'm',
                'search' => array(
                    'action' => $this->url()->makeUrl('mail', array('view' => $this->request()->get('view'), 'id' => $this->request()->get('id'))),
                    'default_value' => _p('search_messages'),
                    'name' => 'search',
                    'field' => array('m.subject', 'm.preview')
                ),
                'sort' => array(
                    'latest' => array('m.time_stamp', _p('latest')),
                    'most-viewed' => array('m.viewer_is_new', _p('unread_first'))
                ),
                'show' => array(30)
            ))
		);
		$this->search()->setCondition('AND m.viewer_user_id = ' . Phpfox::getUserId() . ' AND m.is_archive = 0');

		list($iCnt, $aMessages, $aInputs) = Mail_Service_Mail::instance()->get($this->search()->getConditions(), $this->search()->getSort(), $this->search()->getPage(), $iPageSize);
        $iNumberUnReadMail = 0;
        foreach ($aMessages as $aMessage){
            if ($aMessage['is_read'] == 1){
                continue;
            }
            $iNumberUnReadMail++;
        }
        if ($iNumberUnReadMail){
            $sScript = '$("span#js_total_new_messages").html("'.$iNumberUnReadMail.'");';
        } else {
            $sScript = '$("span#js_total_new_messages").hide();';
        }
        $sScript = '<script>$Behavior.resetMailCount = function() {'. $sScript . '};</script>';
		$this->template()->assign(array(
				'aMessages' => $aMessages,
				'sScript' => $sScript
			)
		);

        if (Phpfox::getParam('mail.update_message_notification_preview'))
        {
            $sIds = '';
            foreach ($aMessages as $aRow)
            {
                $sIds .= $aRow['thread_id'] . ',';
            }
            $sIds = rtrim($sIds, ',');
            if(!empty($sIds))
                Phpfox_Database::instance()->update(Phpfox::getT('mail_thread_user'), array('is_read' => '1'), 'thread_id IN(' . $sIds . ') AND user_id = ' . Phpfox::getUserId());
        }
	}
}