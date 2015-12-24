<?php
/*
Copyright (C) 2015  Guy R. King

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or any 
later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

class UserController {
	private $user; 
	private $username_updated = false;
	private $email_updated = false; 
	private $pw_updated = false; 
	
	public function __construct(User $user) {
		$this->user = $user;
	}
	
	/*
	* Methods
	*/
	
	/**
	* @return User object or null
	*/
	public function get_user() {
		return $this->user;
	}
	/**
	* @param User object <code>$user</code>
	*/
	public function set_user(User $user) {
		$this->user = $user;
	}
	/**
	* @return boolean
	*/
	public function get_username_updated() {
		return $this->username_updated;
	}
	/**
	* @return boolean
	*/
	public function get_email_updated() {
		return $this->email_updated;
	}
	/**
	* @return boolean
	*/
	public function get_pw_updated() {
		return $this->pw_updated;
	}
	/**
	* Checks credentials of user login attempt. If correct, saves User object into Session variable
	* @param Db object <code>$db</code>, string <code>$ident</code>, string <code>$pw</code>
	*/
	public function login(Db $db, $ident, $pw) {
		$ident_clean = filter_var($ident, FILTER_SANITIZE_STRING);
		$pw_clean = filter_var($pw, FILTER_SANITIZE_STRING);
		// check for presence of @ in identifier to see if identifier is username or email
		$ident_is_username = (strpos($ident_clean, '@') === false);
		if ($ident_is_username) {
			$this->user->id_from_username($db, $ident_clean);
		}
		else {
			$this->user->id_from_email($db, $ident_clean);
		}
		$this->user->db_props($db);
		$this->user->auth($db, $pw_clean);
		$pw_clean = null;
		$pw = null;
		if ($this->user->get_auth()) {
			$this->user->nested_categories($db);
			$_SESSION['user'] = serialize($this->user);
		}
	}
	/**
	* Updates <code>$user</code> property with new user details. If details are valid, saves User object into Session variable and database
	* @param Db object <code>$db</code>, string <code>$username</code>, string <code>$email</code>, string <code>$pw</code>
	*/
	public function sign_up(Db $db, $username, $email, $pw) {
		$this->username_updated = false;
		$this->email_updated = false;
		$this->pw_updated = false;
		$username_clean = filter_var($username, FILTER_SANITIZE_STRING);
		if ($username_clean == $username) { 
			if (strpos($username, '@') === false && strpos($username, '\\') === false) { // usernames containing @, \ not permitted
				if (!User::username_already_exists($db, $username)) {
					$this->user->set_username($username);
					$this->username_updated = true;
				}
			}
		}
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) { 
			if (!User::email_already_exists($db, $email)) {
				$this->user->set_email($email);
				$this->email_updated = true;
			}
		}
		$pw_clean = filter_var($pw, FILTER_SANITIZE_STRING);
		if ($pw_clean == $pw) {
			$this->user->set_pw($pw);
			$this->pw_updated = true;
		}
		if ($this->username_updated && $this->email_updated && $this->pw_updated) {
			$this->user->save($db);
			// authenticate user
			$this->user->id_from_username($db, $this->user->get_username());
			$this->user->db_props($db);
			$this->user->auth($db, $pw_clean);
			$pw_clean = null;
			$pw = null;
			$_SESSION['user'] = serialize($this->user);
		}	
	}
	/**
	* Deletes user from database matching <code>$user</code> property
	* @param Db object <code>$db</code>
	*/
	public function delete(Db $db) {
		if ($this->user->get_id() !== null) {
			$this->user->delete($db);
		}
		session_destroy();
	}
	/**
	* Attempts to update <code>$email</code> property of <code>$user</code> property. If successful, saves User object into Session variable and database
	* Checks if resubmitted password is valid
	* @param Db object <code>$db</code>, string <code>$email</code>, string <code>$pw</code>
	*/
	public function edit_email(Db $db, $email, $pw) {
		$this->email_updated = false;
		if (!User::email_already_exists($db, $email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$pw_clean = filter_var($pw, FILTER_SANITIZE_STRING);
			$this->user->auth($db, $pw_clean);
			if ($this->user->get_auth()) {
				$this->user->set_email($email); 
				$this->user->set_pw($pw_clean); // password also needs setting to avoid hashing password twice
				$pw_clean = null;
				$pw = null;
				$this->user->save($db);
				$_SESSION['user'] = serialize($this->user);
				$this->email_updated = true;
			}
		}
	}
	/**
	* Attempts to update <code>$pw</code> property of <code>$user</code> property. If successful, saves User object into Session variable and database
	* Checks if resubmitted password is valid and checks confirmation of new password
	* @param Db object <code>$db</code>, string <code>$current_pw</code>, string <code>$new_pw</code>, string <code>$confirm_new_pw</code>
	*/
	public function edit_pw(Db $db, $current_pw, $new_pw, $confirm_new_pw) {
		$this->pw_updated = false;
		$current_pw_clean = filter_var($current_pw, FILTER_SANITIZE_STRING);
		$new_pw_clean = filter_var($new_pw, FILTER_SANITIZE_STRING);
		$confirm_new_pw_clean = filter_var($confirm_new_pw, FILTER_SANITIZE_STRING);
		$this->user->auth($db, $current_pw_clean);
		if ($this->user->get_auth()) {
			if ($new_pw_clean == $confirm_new_pw_clean) {
				$this->user->set_pw($new_pw_clean); 
				$new_pw_clean = null;
				$new_pw = null;
				$this->user->save($db);
				$_SESSION['user'] = serialize($this->user);
				$this->pw_updated = true;
			}
		}
	}
	/**
	* End session
	*/
	public function logout() {
		session_destroy();
	}
}
?>