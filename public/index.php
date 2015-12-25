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

session_start();

// <code>$paths</code> contains filepaths to all directories containing class definitions
function autoloader($class_name) {
	$paths = [dirname(dirname(__FILE__)) . '/app/lib/', 
			  dirname(dirname(__FILE__)) . '/app/models/',
		  	  dirname(dirname(__FILE__)) . '/app/controllers/',
		  	  dirname(dirname(__FILE__)) . '/app/views/User/',
			  dirname(dirname(__FILE__)) . '/app/views/NestedCategory/',
		  	  dirname(dirname(__FILE__)) . '/app/views/Post/',
			  dirname(dirname(__FILE__)) . '/app/views/',
		      dirname(dirname(__FILE__)) . '/app/config/'];
	foreach ($paths as $path) {
		$file = $path . $class_name . '.php';
		if (file_exists($file)) {
			require_once $file;
		}
	}
}

spl_autoload_register('autoloader');

$db = new Db();
$db->connect(Config::$configuration['host'], Config::$configuration['db'], Config::$configuration['user'], Config::$configuration['password'], Config::$configuration['charset']);

if (isset($_SESSION['user'])) { 
	if (isset($_GET['controller']) && isset($_GET['action'])) {
		$controller = $_GET['controller'];
		$action = $_GET['action'];
	}
	else { // default response (user logged in) - show user profile homepage without reverifying credentials (see routes.php, UserLoginView.php)
		$controller = 'user';
		$action = 'login'; 
	}
}
elseif (isset($_GET['controller']) && isset($_GET['action'])) {
	$controller = $_GET['controller'];
	$action = $_GET['action'];
}
else { // default response (user not logged in)
	$controller = 'post';
	$action = 'index';
}

require_once dirname(dirname(__FILE__)) . '/app/routes.php';
?>