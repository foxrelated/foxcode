<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Notification_Component_Controller_Panel
 */
class Notification_Component_Controller_Panel extends Phpfox_Component {

	public function process() {
		Phpfox::isUser(true);
        $iNumberNotification = Notification_Service_Notification::instance()->getUnseenTotal();
        $aNotifications = Notification_Service_Notification::instance()->get();
        $iRemainingNotification = 0;
        foreach ($aNotifications as $aNotification){
            if ($aNotification['is_seen'] == 1) {
                continue;
            }
            $iRemainingNotification++;
        }
        $iRemainingNotification = $iNumberNotification - $iRemainingNotification;
        if ($iRemainingNotification){
            $sScript = '$("span#js_total_new_notifications").html("'.$iRemainingNotification.'");';
        } else {
            $sScript = '$("span#js_total_new_notifications").hide();';
        }
        $sScript = '<script>$Behavior.resetNotificationCount = function() {'. $sScript . '};</script>';
		$this->template()->assign([
			'aNotifications' => $aNotifications,
            'sScript' => $sScript
		]);
	}
}