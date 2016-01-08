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

class User {
	private static $table = 'users'; // property declared static so that both static and non-static methods can access it
	
	/*
	* Properties as per database table
	*/
	
	private $id;
	private $email;
	private $pw; // hashed 
	private $username; 
	
	/*
	* Other properties
	*/
	
	private $auth = false; // is User object authenticated
	private $nested_categories; 
	
	public function __construct() {}
		
	/*
	* Methods
	*/
	
	/**
	* @param string <code>$setter</code>
	*/
	private function setter_err($setter) {
		echo 'Property not set; incorrect type. Setter: ' . $setter . ' in ' . __FILE__ . PHP_EOL;
	}
	/**
	* @return integer or null
	*/
	public function get_id() {
		return $this->id;
	}
	/**
	* @param integer <code>$id</code>
	*/
	public function set_id($id) {
		if (gettype($id) == 'integer') {
			$this->id = $id;
		}
		else {
			$this->setter_err(__FUNCTION__);
		}
	}
	/**
	* @return string or null
	*/
	public function get_email() {
		return $this->email;
	}
	/**
	* @param string <code>$email</code>
	*/
	public function set_email($email) {
		if (gettype($email) == 'string') {
			$this->email = $email;
		}
		else {
			$this->setter_err(__FUNCTION__);
		}
	}
	/**
	* @return string or null
	*/
	public function get_pw() {
		return $this->pw;
	}
	/**
	* @param string <code>$pw</code>
	*/
	public function set_pw($pw) {
		$type = gettype($pw);
		if ($type == 'string' || $type == 'NULL') {
			$this->pw = $pw;
		}
		else {
			$this->setter_err(__FUNCTION__);
		}
	}
	/**
	* @return string or null
	*/
	public function get_username() {
		return $this->username;
	}
	/**
	* @param string <code>$username</code>
	*/
	public function set_username($username) {
		if (gettype($username) == 'string') {
			$this->username = $username;
		}
		else {
			$this->setter_err(__FUNCTION__);
		}
	}
	/**
	* @return boolean or null
	*/
	public function get_auth() {
		return $this->auth;
	}
	/**
	* Sets value of <code>$auth</code> property
	* @param string <code>$pw_no_hash</code>
	*/
	public function auth($pw_no_hash) {
		$this->auth = false;
		if (isset($this->pw)) {
			$pw_hash = $this->pw;
			if (password_verify($pw_no_hash, $pw_hash)) {
				$this->auth = true;
			}
		}
	}
	/**
	* @return null or array of associative arrays; keys 'name', 'id', 'parent_id', 'gauche', 'droite'
	*/
	public function get_nested_categories() {
		return $this->nested_categories;
	}
	/**
	* Attemps to set value of <code>$nested_categories</code> property
	* @param Db object <code>$db</code>
	*/
	public function nested_categories(Db $db) {
		if (isset($this->id)) {
			$this->nested_categories = NestedCategory::find($db, ['name', 'id', 'parent_id', 'gauche', 'droite'], "user_id = {$this->id}");
		}
	}
	/**
	* Attempts to set value of <code>$id</code> from a specified email
	* @param Db object <code>$db</code>, string <code>$email</code>
	*/
	public function id_from_email(Db $db, $email) {
		$db_result = self::find($db, ['id'], "email = '{$email}'"); 
		if (isset($db_result)) {
			$this->id = (int)$db_result[0]['id']; 
		}
	}
	/**
	* Attempts to set value of <code>$id</code> from a specified username
	* @param Db object <code>$db</code>, string <code>$username</code>
	*/
	public function id_from_username(Db $db, $username) {
		$db_result = self::find($db, ['id'], "username = '{$username}'"); 
		if (isset($db_result)) {
			$this->id = (int)$db_result[0]['id']; 
		}
	}
	/**
	* Checks whether given email exists already in database
	* @param Db object <code>$db</code>, string <code>$email</code>
	* @return boolean
	*/
	public static function email_already_exists(Db $db, $email) {
		return null !== self::find($db, ['id'], "email = '{$email}'");
	}
	/**
	* Checks whether given username exists already in database
	* @param Db object <code>$db</code>, string <code>$username</code>
	* @return boolean
	*/
	public static function username_already_exists(Db $db, $username) {
		return null !== self::find($db, ['id'], "username = '{$username}'");
	}
	/**
	* Returns whether a post (specified by id) belongs to User object
	* @param Db object <code>$db</code>, integer <code>$post_id</code>
	* @return boolean
	*/
	public function post_belong_to_user(Db $db, $post_id) {
		if (isset($this->id)) {
			$db_result = Post::find($db, ['user_id'], "id = {$post_id}"); // an array
			if (isset($db_result)) {
				return $db_result[0]['user_id'] == $this->id;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	/**
	* @param Db object <code>$db</code>, array of strings <code>$cols</code>, string <code>$order</code>, integer <code>$nb_results</code>, <code>boolean</code> $desc
	* @return array of associative arrays; keys are column headings or null if zero rows returned
	*/
	public static function retrieve(Db $db, $cols, $order, $nb_results, $desc) {
		return $db->retrieve($cols, $order, $nb_results, self::table, $desc);
	}
	/**
	* @param Db object <code>$db</code>, array of strings <code>$cols</code>, string <code>$cond</code>
	* @return array of associative arrays; keys are column headings or null if zero rows returned
	*/
	public static function find(Db $db, $cols, $cond) {
		return $db->find($cols, $cond, self::$table);
	}
	/**
	* If User object has corresponding record in database, deletes that record
	* @param Db object <code>$db</code>
	*/
	public function delete(Db $db) {
		if (isset($this->id)) {
			$db->delete("id = {$this->id}", self::$table);			
		}
	}
	/**
	* If User object has corresponding record in database, updates that record. Otherwise creates a new record (requries <code>$username</code>, <code>$email</code>, <code>$pw</code> properties to be set)
	* Ensures values in database not overwritten by null
	* @param Db object <code>$db</code>
	*/
	public function save(Db $db) {
		if (isset($this->id)) {
			$cond = "id = {$this->id}";
			if (isset($this->username)) {
				$db->edit($this->username, 'username', $cond, self::$table);
			}
			if (isset($this->email)) {
				$db->edit($this->email, 'email', $cond, self::$table);
			}
			if (isset($this->pw)) {
				$db->edit($this->pw, 'pw', $cond, self::$table);
			}
		}
		else {
			$db->add([$this->username, $this->email, $this->pw], ['username', 'email', 'pw'], self::$table);
		}
	}
	/**
	* Attempts to set values of database properties using <code>$id</code> property
	* Assumes property names are same as in database 
	* @param Db object <code>$db</code>
	*/
	public function db_props(Db $db) {
		if (isset($this->id)) {
			$db_result = self::find($db, ['email', 'username', 'pw'], "id = {$this->id}");
			if (isset($db_result)) {
				$prop_vals = $db_result[0];
				foreach ($prop_vals as $key => $val) {
					$this->{$key} = $val;
				}
			}
		}
	}
}
?>
