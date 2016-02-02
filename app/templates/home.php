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
<meta name="description" content="free web cms">
<head>
	<title>Welcome to MyApp. Login, sign-up or learn more</title>
	<?php echo "<script src=" . Config::$configuration['rootpath'] . "/js/forms.js></script>";?>	
</head>
<body>
	<strong><p>Welcome to MyApp.</p></strong>
	<br />
	<br />
	<br />
	<p>If you have already registered, please login using your username or email.</p>
	<!--Login, username and password-->
	<script type="text/javascript">
	var rootPath = '<?php echo Config::$configuration["rootpath"]; ?>';
	var existingUsers = makeForm('post', rootPath + '/index.php?controller=user&action=login', ['ident', 'pw'], ['text', 'password'], ['Username: ', 'Password: '], 'Login');
	var body = document.body;
	body.appendChild(existingUsers);
	</script>
	<p>Or sign up below!</p>
	<!--New user sign-up-->
	<script type="text/javascript">
	var rootPath = '<?php echo Config::$configuration["rootpath"]; ?>';
	var newUsers = makeForm('post', rootPath + '/index.php?controller=user&action=sign_up', ['username', 'email_address', 'new_pw'], ['text', 'text', 'password'], ['Username: ', 'Email address: ', 'New password: '], 'Sign up');
	body.appendChild(newUsers);
	</script>
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<p>Latest posts</p>
	<?php 
	if (isset($last_five_posts)) {
		foreach ($last_five_posts as $post) {
			$root_path = Config::$configuration["rootpath"];
			echo "<a href='{$root_path}/index.php?controller=post&action=select&id={$post['id']}'>{$post['title']}</a></br>";
		}
	}
	?>
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<p>All posts</p>
	<?php 
	if (isset($all_posts)) {
		foreach ($all_posts as $post) {
			$root_path = Config::$configuration["rootpath"];
			echo "<a href='{$root_path}/index.php?controller=post&action=select&id={$post['id']}'>{$post['title']}</a></br>";
		}
	}
	?>
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />	
	<br />
	<br />
	<br />
</body>
</html>
