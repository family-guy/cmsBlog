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

class Post {
	private static $table = 'posts'; // property declared static so that both static and non-static methods can access it
	
	/*
	* Properties as per database table
	*/
	
	private $id;
	private $title;
	private $date; 
	private $body;
	private $user_id;
	private $nested_category_id;
	
	/*
	* Other properties
	*/
	
	private $last_ten_posts; // PostIndexView.php
	private $valid_id = false; // is <code>$id</code> property a valid value
	private $write = false; // can post be edited
	private $username; // matching username to <code>$user_id</code> property
	
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
	public function get_title() {
		return $this->title;
	}
	/**
	* @param string <code>$title</code>
	*/
	public function set_title($title) {
		if (gettype($title) == 'string') {
			$this->title = $title;
		}
		else {
			$this->setter_err(__FUNCTION__);
		}
	}
	/**
	* @return string or null
	*/
	public function get_date() {
		return $this->date;
	}
	/**
	* @return string or null
	*/
	public function get_body() {
		return $this->body;
	}
	/**
	* @param string <code>$body</code>
	*/
	public function set_body($body) {
		if (gettype($body) == 'string') {
			$this->body = $body;
		}
		else {
			$this->setter_err(__FUNCTION__);
		}
	}
	/**
	* @return integer or null
	*/
	public function get_user_id() {
		return $this->user_id;
	}
	/**
	* @param integer <code>$user_id</code>
	*/
	public function set_user_id($user_id) {
		if (gettype($user_id) == 'integer') {
			$this->user_id = $user_id;
		}
		else {
			$this->setter_err(__FUNCTION__);
		}
	}
	/**
	* @return integer or null
	*/
	public function get_nested_category_id() {
		return $this->nested_category_id;
	}
	/**
	* @param integer <code>$nested_category_id</code>
	*/
	public function set_nested_category_id($nested_category_id) {
		if (gettype($nested_category_id) == 'integer') {
			$this->nested_category_id = $nested_category_id;
		}
		else {
			$this->setter_err(__FUNCTION__);
		}
	}
	/**
	* @return null or array of associative arrays; keys 'id', 'title'
	*/
	public function get_last_ten_posts() {
		return $this->last_ten_posts;
	}
	/**
	* Sets value of <code>$last_ten_posts</code> property
	* @param Db object <code>$db</code>
	*/
	public function last_ten_posts(Db $db) {
		$this->last_ten_posts = self::retrieve($db, ['id', 'title'], 'date', 10, true);
	}
	/**
	* @return boolean
	*/
	public function get_valid_id() {
		return $this->valid_id;
	}
	/**
	* Sets value of <code>$valid_id</code> property
	* @param Db object <code>$db</code>
	*/
	public function valid_id(Db $db) {
		$this->valid_id = false;
		if (isset($this->id)) {
			if (null !== self::find($db, ['id'], "id = {$this->id}")) {
				$this->valid_id = true;
			}
		}
	}
	/**
	* @return boolean
	*/
	public function get_write() {
		return $this->write;
	}
	/**
	* Sets value of <code>$write</code> property
	* @param Db object <code>$db</code>, UserController object <code>$user_controller</code>
	* Checks if given user can edit post
	*/
	public function write(Db $db, UserController $user_controller) {
		$this->write = false;
		$user = $user_controller->get_user();
		if ($user->get_id() !== null && isset($this->id)) {
			$this->write = $user->post_belong_to_user($db, $this->id); 
		}
	}
	/**
	* @return string or null
	*/
	public function get_username() {
		return $this->username;
	}
	/**
	* Attempts to set value of <code>$username</code> property
	* @param Db object <code>$db</code>
	*/
	public function username(Db $db) {
		if (isset($this->user_id)) {
			$db_result = User::find($db, ['username'], "id = {$this->user_id}");
			if (isset($db_result)) {
				$this->username = $db_result[0]['username']; 	
			}
		}
	}
	/**
	* @param Db object <code>$db</code>
	*/
	public static function retrieve(Db $db, $cols, $order, $nb_results, $desc) {
		return $db->retrieve($cols, $order, $nb_results, self::$table, $desc);
	}
	/**
	* @param Db object <code>$db</code>
	*/
	public static function find(Db $db, $cols, $cond) {
		return $db->find($cols, $cond, self::$table);
	}
	/**
	* If Post object has corresponding record in database, deletes that record
	* @param Db object <code>$db</code>
	*/
	public function delete(Db $db) {
		if (isset($this->id)) {
			$db->delete("id = {$this->id}", self::$table);			
		}
	}
	/**
	* If Post object has corresponding record in database, updates that record. Otherwise creates a new record (requries <code>$title</code>, <code>$body</code>, <code>$user_id</code>, <code>$nested_category_id</code> properties to be set)
	* Ensures values in database not overwritten by null
	* @param Db object <code>$db</code>
	*/
	public function save(Db $db) {
		if (isset($this->id)) {
			$cond = "id = '{$this->id}'";
			if (isset($this->title)) { 
				$db->edit($this->title, 'title', $cond, self::$table);
			}
			if (isset($this->body)) {
				$db->edit($this->body, 'body', $cond, self::$table);
			}
			if (isset($this->user_id)) {
				$db->edit($this->user_id, 'user_id', $cond, self::$table);
			}
			if (isset($this->nested_category_id)) {
				$db->edit($this->nested_category_id, 'nested_category_id', $cond, self::$table);
			}
		}
		else {
			$db->add([$this->title, $this->body, $this->user_id, $this->nested_category_id], ['title', 'body', 'user_id', 'nested_category_id'], self::$table);
		}
	}
	/**
	* Attempts to set values of database properties using <code>$id</code> property
	* Assumes property names are same as in database 
	* @param Db object <code>$db</code>
	*/
	public function db_props(Db $db) {
		if (isset($this->id)) {
			$db_result = self::find($db, ['title', 'date', 'body', 'user_id', 'nested_category_id'], "id = {$this->id}");
			if (isset($db_result)) {
				$prop_vals = $db_result[0];
				foreach ($prop_vals as $key => $val) {
					$this->{$key} = $val;
				}
			}
		}
	}
	/** 
	* Gets all posts in a nested category (specified by id)
	* @param Db object <code>$db</code>, integer or string <code>$nested_category_id</code>
	* @return null or array of associative arrays; keys are 'id', 'title'
	*/
	public static function posts_in_nested_category(Db $db, $nested_category_id) {
		return self::find($db, ['id', 'title'], "nested_category_id = {$nested_category_id}");
	}
}
?>
