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
	<title><?php echo "{$post_title}";?></title>
	<script type="text/x-mathjax-config">MathJax.Hub.Config({tex2jax: {inlineMath: [['$','$'], ['\\(','\\)']]}});</script>
	<script type="text/javascript" src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>
	<script type="text/javascript" src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js?lang=css&skin=sunburst"></script>
	<style>
	.block-of-text p {
	  position: relative;
	  top: 50%;
	    -webkit-transform: translateY(-50%);
	    -ms-transform: translateY(-50%);
	    transform: translateY(-50%);
	}
	section {
	  display: block;
	  max-width: 400px;
	  margin: 0 auto 1em;
	  border-radius: .5em;
	  border-style: none;
	  border-color: black;
	  overflow: auto;
	  img, p {
	    padding: 1em;
	  }
	}
	</style>	
</head>
<body>
	<br />
	<br />
	<br />
	<section class="block-of-text">
		<?php echo 'Last modified: ' . $post_last_modified . '<br /> by '. $post_username;?>
	</section>
	<br />
	<br />
	<br />
	<section class="block-of-text">
		<?php echo "<strong><u>{$post_title}</u></strong>"; ?>
	</section>
	<section class="block-of-text">
		<?php echo nl2br($post_body); ?>
	</section>	
	<section class="block-of-text">
		<?php echo "<a href='" . Config::$configuration['rootpath'] . "/index.php?controller=post&amp;action=edit'>Edit post</a>";?>
	</section>
	<section class="block-of-text">
		<?php 
		echo "<form action='" . Config::$configuration['rootpath'] . "/index.php?controller=post&amp;action=delete' method='post' name='delPost'>";
		echo "<input type='hidden' name='post_id' value={$post_id} />";
		?>
			<a href="#" onclick="document.delPost.submit();">Delete post</a>
	</section>
	<section class="block-of-text">
		<?php echo "<a href='" . Config::$configuration['rootpath'] . "/index.php?controller=nested_category&amp;action=show_posts&amp;nested_category_id=" . $nested_category_id . "'>Back</a>";?>
	</section>
	<section class="block-of-text">
		<a href=<?php echo Config::$configuration['rootpath'] . "/index.php?controller=user&amp;action=logout";?>>Logout</a>
	</section>
</body>
</html>