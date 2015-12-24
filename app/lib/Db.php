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

/**
 * Database wrapper class; implements SCRUD methods. Should be only file containing SQL instructions
 */
class Db {
	private static $conn; // ensure connection made at most once
	private $no_conn_msg = 'No database connection.';
	
	public function __construct() {}
		
	/*
	* Methods
	*/
	
	/**
	* Prints error message
	* @param PDOException object <code>$e</code>
	*/
	private function error_msg($e) {
		echo 'MySQL error: ' . $e->getMessage() . PHP_EOL;
	}
		
	/**
	* Attempts to establish a database connection if one does not already exist
	* @param string <code>$host</code>, string <code>$db</code>, string <code>$user</code>, string <code>$pw</code>, string <code>$charset</code>
	*/
	public function connect($host, $db, $user, $pw, $charset) {
		if (!isset(self::$conn)) {
			try {
				self::$conn = new PDO("mysql:host={$host}; dbname={$db}", $user, $pw);
				self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				self::$conn->exec("SET NAMES '{$charset}'");
			}
			catch (PDOException $e) {
				$this->error_msg($e);
				exit();
			}
		}
	}
	/**
	* Executes SQL statement
	* @param string <code>$sql</code>
	*/
	public function exec($sql) {
		try {
			$result = self::$conn->exec($sql);
			return $result;
		}
		catch (PDOException $e) {
			$this->error_msg($e);
			exit();
		}
	}
	/**
	* Executes SQL query
	* @param string <code>$sql</code>
	*/
	public function query($sql) {
		try {
			$result = self::$conn->query($sql);
			return $result;
		}
		catch (PDOException $e) {
			$this->error_msg($e);
			exit();
		}
	}
	/**
	* Gets number of rows in table matching criteria
	* @param array <code>$values</code>, array of strings <code>$cols</code>, string <code>$table</code>. Order in <code>$values</code> matches that in $cols
	* @return integer
	*/
	public function nb_occ($values, $cols, $table) {
		if (isset(self::$conn)) {
			try {
				$sql = "SELECT COUNT(*) AS x FROM {$table} WHERE ";
				foreach ($cols as $col) {
					$A[] = "{$col} = ?"; 
				}
				$sql .= implode(" AND ", $A);
				$stmt = self::$conn->prepare($sql);
				$stmt->execute($values);
				foreach ($stmt as $row) {
					$result = $row['x']; // only one row returned as count is an aggregate function
				}
				return $result;
			}
			catch (PDOException $e) {
				$this->error_msg($e);
				exit();
			}
		}
		else {
			echo $this->no_conn_msg;
		}
	}
	/**
	* Sets new value in field for rows matching criteria
	* @param array of strings <code>$cols</code>, string <code>$cond</code>, string <code>$table</code>
	*/
	public function edit($new_value, $col, $cond, $table) {
		if (isset(self::$conn)) {
			try {
				$stmt = self::$conn->prepare("UPDATE {$table} SET {$col} = ? WHERE {$cond}");
				$stmt->execute([$new_value]);
			}
			catch (PDOException $e) {
				$this->error_msg($e);
				exit();
			}
		}
		else {
			echo $this->no_conn_msg;
		}
	}
	/**
	* Adds a new row 
	* @param array <code>$values</code>, array of strings <code>$cols</code>, string <code>$table</code>. Order in <code>$values</code> matches that in <code>$cols</code>
	*/
	public function add($values, $cols, $table) {
		if (isset(self::$conn)) {
			try {
				$sql = "INSERT INTO {$table} ";
				$sql .= "(" . implode(",", $cols) . ')';
				foreach ($cols as $col) {
					$question_marks[] = "?";
				}
				$sql .= "VALUES (" . implode(",", $question_marks) . ")";
				$stmt = self::$conn->prepare($sql); 
				$stmt->execute($values);
			}
			catch (PDOException $e) {
				$this->error_msg($e);
				exit();
			}
		}
		else {
			echo $this->no_conn_msg;
		}
	}
	/**
	* Deletes rows
	* @param string <code>$cond</code>, string <code>$table</code>
	*/
	public function delete($cond, $table) {
		if (isset(self::$conn)) {
			try {
				self::$conn->exec("DELETE FROM {$table} WHERE {$cond}");
			}
			catch (PDOException $e) {
				$this->error_msg($e);
				exit();
			}
		}
		else {
			echo $this->no_conn_msg;
		}
	}
	/**
	* Retrieves a certain number of rows in a certain order
	* @param array of strings <code>$cols</code>, string <code>$order</code>, integer <code>$nb_results</code>, <code>string</code> $table, <code>boolean</code> $desc
	* Set <code>$order</code> = 'rand()' for no order; set <code>$nb_results</code> = 18446744073709551615 for all results
	* @return array of associative arrays; keys are column headings or null if zero rows returned
	*/
	public function retrieve($cols, $order, $nb_results, $table, $desc) {
		if (isset(self::$conn)) {
			try {
				if ($desc) {
					$sql = "SELECT " . implode(",", $cols) . " FROM {$table} ORDER BY {$order} DESC LIMIT {$nb_results}";
				}
				else {
					$sql = "SELECT " . implode(",", $cols) . " FROM {$table} ORDER BY {$order} LIMIT {$nb_results}";
				}
				$stmt = self::$conn->query($sql);
				foreach ($stmt as $row) {
					foreach ($cols as $col) {
						$A[$col] = $row[$col];
					}
					$result[] = $A;
					unset($A);
				}
				if (isset($result)) {
					return $result;
				}
			}
			catch (PDOException $e) {
				$this->error_msg($e);
				exit();
			}
		}
		else {
			echo $this->no_conn_msg;
		}
	}
	/**
	* Retrieves rows matching criteria
	* @param array of strings <code>$cols</code>, string <code>$cond</code>, string <code>$table</code>
	* @return array of associative arrays; keys are column headings or null if zero rows returned
	*/
	public function find($cols, $cond, $table) {
		if (isset(self::$conn)) {
			try {
				$sql = "SELECT ";
				$sql .= (implode(",", $cols) . " FROM {$table} WHERE ");
				$sql .= $cond;
				$stmt = self::$conn->query($sql);
				foreach ($stmt as $row) {
					foreach ($cols as $col) {
						$A[$col] = $row[$col];
					}
					$result[] = $A;
					unset($A);
				}
				if (isset($result)) {
					return $result;
				}
			}
			catch (PDOException $e) {
				$this->error_msg($e);
				exit();
			}
		}
		else {
			echo $this->no_conn_msg;
		}
	}
}	
?>

