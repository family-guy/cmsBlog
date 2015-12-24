<!--
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
-->
<!DOCTYPE html>
<html lang='en'>
<meta charset="UTF-8">
<head>
	<title>Change email</title>
	<?php echo "<script src=" . Config::$configuration['rootpath'] . "/js/forms.js></script>";?>
</head>
<body>
	<p>Current email: </p>
	<?php 
	$user_controller = $controller; 
	echo $user_controller->get_user()->get_email(); 
	?>
	<br />
	<br />
	<br />
	<p>Please enter your new email address below</p>
	<script type="text/javascript">
	var rootPath = '<?php echo Config::$configuration["rootpath"]; ?>';
	var newEmail = makeForm('post', rootPath + '/index.php?controller=user&action=edit_email', ['new_email', 'pw'], ['text', 'password'], ['New email: ', 'Enter password:'], 'Submit');
	var body = document.body;
	body.appendChild(newEmail);
	</script>
	<br />
	<br />
	<br />
	<a href="./">Back</a>
</body>
</html>