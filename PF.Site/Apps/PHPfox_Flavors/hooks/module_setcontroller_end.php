<?php

if ($this->_sController == 'index-member' && Phpfox::isAdmin() && request()->get('preview')) {
	$this->_sController = 'index-visitor';
	\User_Service_Auth::instance()->reset();
}