<?php

namespace Apps\PHPfox_Facebook\Model;

use User_Service_Auth;
use Phpfox;
use Invite_Service_Invite;
use Friend_Service_Process;
/**
 * Service class for Facebook Connect App
 *
 * @package Apps\PHPfox_Facebook\Model
 */
class Service extends \Core\Model {

	/**
	 * Create a new user or log them in if they exist
	 *
	 * @param \Facebook\GraphUser $fb
	 * @return bool
	 * @throws \Exception
	 */
	public function create(\Facebook\GraphUser $fb) {
		$email = $fb->getEmail();
		$url = null;
		$blank_email = false;

		if (!$email) {
			stream_context_set_default(
				array(
					'http' => array(
						'header' => "User-Agent: {$_SERVER['HTTP_USER_AGENT']}\r\n"
					)
				)
			);
            $headers =  array();
            $filename =  rtrim(str_replace('app_scoped_user_id/', '', $fb->getLink()), '/');

            if($filename){
                $headers = get_headers($filename);
            }


			if (isset($headers[1])) {
				$url = trim(str_replace('Location: https://www.facebook.com/', '', $headers[1]));
				$email = strtolower($url) . '@facebook.com';
				$blank_email = true;
			}
		}

		if (!$email) {
			$email = $fb->getId() . '@fb';
			$blank_email = true;
		}

		$cached = storage()->get('fb_users_' . $fb->getId());
		if ($cached) {
			$user = $this->db->select('*')->from(':user')->where(['user_id' => $cached->value->user_id])->get();
			if (isset($user['email'])) {
				$email = $user['email'];
			} else {
				storage()->del('fb_users_' . $fb->getId());
			}
		} else {
			$user = $this->db->select('*')->from(':user')->where(['email' => $email])->get();
		}

		if (isset($user['user_id'])) {
			$_password = $fb->getId() . uniqid();
			$password = (new \Core\Hash())->make($_password);

			$this->db->update(':user', ['password' => $password], ['user_id' => $user['user_id']]);
            storage()->update('fb_users_' . (int) $fb->getId(), [
				'user_id' => $user['user_id'],
				'email' => $user['email']
			]);
		}
		else {
		    if (!Phpfox::getParam('user.allow_user_registration')){
		        return false;
            }
            if (Phpfox::getParam('user.invite_only_community') && !Invite_Service_Invite::instance()->isValidInvite($user['email'])){
                return false;
            }
			$_password = $fb->getId() . uniqid();
			$password = (new \Core\Hash())->make($_password);

			$id = $this->db->insert(':user', [
				'user_group_id' => NORMAL_USER_ID,
				'email' => $email,
				'password' => $password,
				'gender' => ($fb->getGender() == 'male' ? 0 : '1'),
				'full_name' => ($fb->getFirstName() === null ? $fb->getName() : $fb->getFirstName() . ' ' . $fb->getLastName()),
				'user_name' => ($url === null ? 'fb-' . $fb->getId() : str_replace('.', '-', $url)),
				'user_image' => '{"fb":"' . $fb->getId() . '"}',
				'joined' => PHPFOX_TIME,
				'last_activity' => PHPFOX_TIME
			]);

			if (setting('m9_facebook_require_email') && $blank_email) {
				storage()->set('fb_force_email_' . $id, $fb->getId());
			}

			storage()->set('fb_users_' . $fb->getId(), [
				'user_id' => $id,
				'email' => $email
			]);

			$tables = [
				'user_activity',
				'user_field',
				'user_space',
				'user_count'
			];
			foreach ($tables as $table) {
				$this->db->insert(':' . $table, ['user_id' => $id]);
			}

            $iFriendId = (int) Phpfox::getParam('user.on_signup_new_friend');
            if ($iFriendId > 0 && Phpfox::isModule('friend'))
            {
                $iCheckFriend = db()->select('COUNT(*)')
                    ->from(Phpfox::getT('friend'))
                    ->where('user_id = ' . (int) $id . ' AND friend_user_id = ' . (int) $iFriendId)
                    ->execute('getSlaveField');

                if (!$iCheckFriend)
                {
                    db()->insert(Phpfox::getT('friend'), array(
                            'list_id' => 0,
                            'user_id' => $id,
                            'friend_user_id' => $iFriendId,
                            'time_stamp' => PHPFOX_TIME
                        )
                    );

                    db()->insert(Phpfox::getT('friend'), array(
                            'list_id' => 0,
                            'user_id' => $iFriendId,
                            'friend_user_id' => $id,
                            'time_stamp' => PHPFOX_TIME
                        )
                    );

                    if (!Phpfox::getParam('user.approve_users'))
                    {
                        Friend_Service_Process::instance()->updateFriendCount($id, $iFriendId);
                        Friend_Service_Process::instance()->updateFriendCount($iFriendId, $id);
                    }
                }
            }
		}

		User_Service_Auth::instance()->login($email, $_password, true, 'email');
		if (!\Phpfox_Error::isPassed()) {
			throw new \Exception(implode('', \Phpfox_Error::get()));
		}

		return true;
	}
}