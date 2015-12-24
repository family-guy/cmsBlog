<!-->
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
-->
<!DOCTYPE html>
<html lang='en'>
<meta charset="UTF-8">
<head>
	<title>Change password</title>
	<?php echo "<script src=" . Config::$configuration['rootpath'] . "/js/forms.js></script>";?>
</head>
<body>
	<script type="text/javascript">
	var rootPath = '<?php echo Config::$configuration["rootpath"]; ?>';
	var newPw = makeForm('post', rootPath + '/index.php?controller=user&action=edit_pw', ['current_pw', 'new_pw', 'confirm_new_pw'], ['password', 'password', 'password'], ['Current password', 'New password', 'Confirm new password'], 'Submit');
	var body = document.body;
	body.appendChild(newPw);
	</script>
	<br />
	<br />
	<br />
	<a href="./">Back</a>
</body>
</html>