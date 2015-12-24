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

class NestedCategory {
	private static $table = 'nested_categories'; // property declared static so that both static and non-static methods can access it

	/*
	* Properties as per database table
	*/
	
	private $id;
	private $name;
	private $user_id;
	private $gauche;
	private $droite;
	private $parent_id;
	
	/*
	* Other properties
	*/
	
	private $children; 
	private $posts; 
	private $valid_id = false; // is <code>$id</code> a valid value
	private $parent_gauche_droite; 
	private $parent_ancestors;
	private $parent_siblings_and_parent_siblings_children;
	private $nested_cats_to_update_after_del;
	
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
	public function get_name() {
		return $this->name;
	}
	/**
	* @param string <code>$name</code>
	*/
	public function set_name($name) {
		if (gettype($name) == 'string') {
			$this->name = $name;
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
	public function get_gauche() {
		return $this->gauche;
	}
	/**
	* @param integer <code>$gauche</code>
	*/
	public function set_gauche($gauche) {
		if (gettype($gauche) == 'integer') {
			$this->gauche = $gauche;
		}
		else {
			$this->setter_err(__FUNCTION__);
		}
	}
	/**
	* @return integer or null
	*/
	public function get_droite() {
		return $this->droite;
	}
	/**
	* @param integer <code>$droite</code>
	*/
	public function set_droite($droite) {
		if (gettype($droite) == 'integer') {
			$this->droite = $droite;
		}
		else {
			$this->setter_err(__FUNCTION__);
		}
	}
	/**
	* @return integer or null
	*/
	public function get_parent_id() {
		return $this->parent_id;
	}
	/**
	* @param integer <code>$parent_id</code>
	*/
	public function set_parent_id($parent_id) {
		if (gettype($parent_id) == 'integer') {
			$this->parent_id = $parent_id;
		}
		else {
			$this->setter_err(__FUNCTION__);
		}
	}
	/**
	* @return null or array of associative arrays; keys are 'id', 'name'
	*/
	public function get_children() {
		return $this->children;
	}
	/**
	* Attempts to set value of <code>$children</code> property
	* @param Db object <code>$db</code>
	*/
	public function children(Db $db) {
		if (isset($this->id)) {
			$this->children = self::find($db, ['id', 'name'], "parent_id = {$this->id}"); 
		}
	}
	/**
	* @return null or array of associative arrays; keys are 'id', 'title'
	*/
	public function get_posts() {
		return $this->posts;
	}
	/**
	* Attempts to set value of <code>$posts</code> property
	* @param Db object <code>$db</code>
	*/
	public function posts(Db $db) {
		if (isset($this->id)) {
			$this->posts = Post::posts_in_nested_category($db, $this->id);	
		}
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
	* @return null or associative array; keys are 'gauche', 'droite'
	*/
	public function get_parent_gauche_droite() {
		return $this->parent_gauche_droite;
	}
	/**
	* Attempts to set value of <code>$parent_gauche_droite</code> property
	* @param Db object <code>$db</code>
	*/
	public function parent_gauche_droite(Db $db) {
		if (isset($this->parent_id)) {
			$db_result = self::find($db, ['gauche', 'droite'], "id = {$this->parent_id}");
			if (null !== $db_result) {
				$this->parent_gauche_droite = $db_result[0];
			}
		}
	}
	/**
	* @return null or array of associative arrays; keys are 'id', 'gauche', 'droite'
	*/
	public function get_parent_ancestors() {
		return $this->parent_ancestors;
	}
	/**
	* Attempts to set value of <code>$parent_ancestors</code> property
	* @param Db object <code>$db</code>
	*/
	public function parent_ancestors(Db $db) {
		if (isset($this->parent_gauche_droite) && isset($this->user_id)) {
			$parent_gauche = $this->parent_gauche_droite['gauche'];
			$parent_droite = $this->parent_gauche_droite['droite'];
			$user_id = $this->user_id;
			$db_result = self::find($db, ['id', 'gauche', 'droite'], "gauche < {$parent_gauche} AND droite > {$parent_droite} AND user_id = {$user_id}");
			if (isset($db_result)) {
				$this->parent_ancestors = $db_result;
			}
		}
	}
	/**
	* @return null or array of associative arrays; keys are 'id', 'gauche', 'droite'
	*/
	public function get_parent_siblings_and_parent_siblings_children() {
		return $this->parent_siblings_and_parent_siblings_children;
	}
	/**
	* Attempts to set value of <code>$parent_siblings_and_parent_siblings_children</code> property
	* @param Db object <code>$db</code>
	*/
	public function parent_siblings_and_parent_siblings_children(Db $db) {
		if (isset($this->parent_gauche_droite) && isset($this->user_id)) {
			$parent_gauche = $this->parent_gauche_droite['gauche'];
			$parent_droite = $this->parent_gauche_droite['droite'];
			$user_id = $this->user_id;
			$db_result = self::find($db, ['id', 'gauche', 'droite'], "gauche > {$parent_droite} AND user_id = {$user_id}");
			if (isset($db_result)) {
				$this->parent_siblings_and_parent_siblings_children = $db_result;
			}
		}
	}		
	/**
	* @return null or array of associative arrays; keys are 'id', 'gauche', 'droite'
	*/
	public function get_nested_cats_to_update_after_del() {
		return $this->nested_cats_to_update_after_del;
	}
	/**
	* Attempts to set value of <code>$nested_cats_to_update_after_del</code> property
	* @param Db object <code>$db</code>
	*/
	public function nested_cats_to_update_after_del(Db $db) {
		if (isset($this->gauche) && isset($this->droite) && isset($this->user_id)) {
			$db_result = self::find($db, ['id', 'gauche', 'droite'], "(gauche > {$this->droite} OR droite > {$this->droite}) AND user_id = {$this->user_id}");
			if (isset($db_result)) {
				$this->nested_cats_to_update_after_del = $db_result;
			}
		}
	}
	/**
	* Gets maximum value of <code>droite</code> in <code>nested_categories</code> table for a user (specified by id)
	* @param Db object <code>$db</code>, integer or string <code>$user_id</code>
	* @return null or string
	*/
	public static function max_droite(Db $db, $user_id) {
		$db_result = self::find($db, ['max(droite)'], "user_id = {$user_id}");
		if (null !== $db_result) {
			return $db_result[0]['max(droite)'];
		}
	}	
	/**
	* Gets maximum value of <code>id</code> in <code>nested_categories</code> table for a user (specified by id) 
	* @param Db object <code>$db</code>
	* @return null or string
	*/
	public static function max_id(Db $db, $user_id) {
		$db_result = self::find($db, ['max(id)'], "user_id = {$user_id}");
		if (null !== $db_result) {
			return $db_result[0]['max(id)'];
		}
	}
	/**
	* Recursively deletes all records in database which are descendants of NestedCategory object
	* @param Db object <code>$db</code>
	*/
	public function delete_descendants(Db $db) {
		if (isset($this->children)) {
			foreach ($this->children as $child) {
				$nested_category = new NestedCategory();
				$nested_category->set_id((int)$child['id']);
				$nested_category->db_props($db);
				if (null !== $nested_category->get_droite() && null !== $nested_category->get_gauche()) {
					$diff = $nested_category->get_droite() - $nested_category->get_gauche();
					$db->delete("id = {$child['id']}", self::$table);
					if ($diff > 1) {
						$nested_category->children($db);
						$nested_category->delete_descendants($db);
					}
				}
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
	* If NestedCategory object has corresponding record in database, deletes that record
	* @param Db object <code>$db</code>
	*/
	public function delete(Db $db) {
		if (isset($this->id)) {
			$db->delete("id = {$this->id}", self::$table);			
		}
	}
	/**
	* If NestedCategory object has corresponding record in database, updates that record. Otherwise creates a new record (requries <code>$name</code>, <code>$user_id</code>, <code>$gauche</code>, <code>$droite</code>, <code>$parent_id</code> properties to be set)
	* Ensures values in database not overwritten by null
	* @param Db object <code>$db</code>
	*/
	public function save(Db $db) {
		if (isset($this->id)) {
			$cond = "id = '{$this->id}'";
			if (isset($this->name)) {
				$db->edit($this->name, 'name', $cond, self::$table);
			}
			if (isset($this->user_id)) {
				$db->edit($this->user_id, 'user_id', $cond, self::$table);
			}
			if (isset($this->gauche)) {
				$db->edit($this->gauche, 'gauche', $cond, self::$table);
			}
			if (isset($this->droite)) {
				$db->edit($this->droite, 'droite', $cond, self::$table);
			}
			if (isset($this->parent_id)) {
				$db->edit($this->parent_id, 'parent_id', $cond, self::$table);
			}
		}
		else {
			$db->add([$this->name, $this->user_id, $this->gauche, $this->droite, $this->parent_id], ['name', 'user_id', 'gauche', 'droite', 'parent_id'], self::$table);
		}
	}
	/**
	* Attempts to set values of database properties using <code>$id</code> property
	* Assumes property names are same as in database 
	* @param Db object <code>$db</code>
	*/
	public function db_props(Db $db) {
		$db_result = self::find($db, ['name', 'user_id', 'gauche', 'droite', 'parent_id'], "id = {$this->id}");
		if (isset($db_result)) {
			$prop_vals = $db_result[0];
			foreach ($prop_vals as $key => $val) {
				$this->{$key} = $val;
			}
		}
	}	
}
?>
