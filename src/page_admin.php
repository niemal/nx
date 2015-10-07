<?php
if (!defined('NX-ANALYTICS')) die('Go away.');

?><!DOCTYPE html><html>
<head>
	<meta charset="utf-8">
	<title>Admin panel | NX ANALYTICS</title>
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
	<link rel="stylesheet" href="assets/admin.css">
</head>
<body>

	<div id="layout">
		<a href="#menu" id="menuLink" class="menu-link"><span></span></a>

		<div id="menu" class="dark-glass">
			<div class="pure-menu">
				<a class="pure-menu-heading dark-glass" href="#">NX ANALYTICS</a>

				<ul class="pure-menu-list">
					<li class="pure-menu-item"><a href="#" class="pure-menu-link">Dashboard</a></li>
					<li class="pure-menu-item"><a href="#" class="pure-menu-link">Pretty graphs</a></li>
					<li class="pure-menu-item"><a href="#" class="pure-menu-link">Tables and stuff</a></li>
					<li class="pure-menu-item"><a href="#" class="pure-menu-link">Other</a></li>
				</ul>
			</div>
		</div>

		<div id="main">
			<div class="header">
				<h1>Title</h1>
				<h2>Whatever</h2>
			</div>

			<div class="content glass">
				<div class="article">
					<h2 class="article-h2">Title</h2>
					<p>Welcome and stuff</p>
				</div>

				<div class="pure-g">
					<div class="pure-u-1-4">
						<img class="pure-img-responsive" src="http://farm8.staticflickr.com/7357/9086701425_fda3024927.jpg">
					</div>
					<div class="pure-u-1-4">
						<img class="pure-img-responsive" src="http://farm3.staticflickr.com/2813/9069585985_80da8db54f.jpg">
					</div>
					<div class="pure-u-1-4">
						<img class="pure-img-responsive" src="http://farm6.staticflickr.com/5456/9121446012_c1640e42d0.jpg">
					</div>
					<div class="pure-u-1-4">
						<img class="pure-img-responsive" src="http://farm3.staticflickr.com/2875/9069037713_1752f5daeb.jpg">
					</div>
				</div>
			</div>
		</div>
	</div>


	<script>
		(function (window, document) {

			var layout   = document.getElementById('layout'),
				menu     = document.getElementById('menu'),
				menuLink = document.getElementById('menuLink');

			// this is here because of old browsers
			function toggleClass(element, className) {
				var classes = element.className.split(/\s+/),
					length = classes.length,
					i = 0;

				for(; i < length; i++) {
				  if (classes[i] === className) {
					classes.splice(i, 1);
					break;
				  }
				}
				// The className is not found
				if (length === classes.length) {
					classes.push(className);
				}

				element.className = classes.join(' ');
			}

			menuLink.onclick = function (e) {
				var active = 'active';

				e.preventDefault();
				toggleClass(layout, active);
				toggleClass(menu, active);
				toggleClass(menuLink, active);
			};

		}(this, this.document));
	</script>

</body>
</html>