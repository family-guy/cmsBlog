<?php
/*
Copyright (C) 2015  Guy R. King

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

class UserEditPwView {
	private $user_controller;
	
	public function __construct(UserController $user_controller) {
		$this->user_controller = $user_controller;
	}
	
	/*
	* Methods
	*/
	
	public function output() {
		if ($this->user_controller->get_pw_updated()) {
			$username = $this->user_controller->get_user()->get_username();
			$nested_categories = $this->user_controller->get_user()->get_nested_categories();
			require_once dirname(dirname(dirname(__FILE__))) . '/templates/userProfileHome.php';
		}
		else {
			require_once dirname(dirname(dirname(__FILE__))) . '/templates/badNewPw.php';
		}
	}
}
?>