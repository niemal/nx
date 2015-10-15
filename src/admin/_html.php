<?php

function _navbar(){
	return <<<HTML
		<a href="#menu" id="menuLink" class="menu-link"><span></span></a>

		<div id="menu" class="dark-glass">
			<div class="pure-menu">
				<span class="pure-menu-heading dark-glass">NX Analytics</span>

				<ul class="pure-menu-list">
					<li class="pure-menu-item"><a href="?admin/dashboard" class="pure-menu-link">Dashboard</a></li>
					<li class="pure-menu-item"><a href="?admin/statistics" class="pure-menu-link">Statistics</a></li>
					<li class="pure-menu-item"><a href="?admin/settings" class="pure-menu-link">Settings</a></li>
					<li class="pure-menu-item"><a href="?admin/logout" class="pure-menu-link">Logout</a></li>
				</ul>
			</div>
		</div>
HTML;
}

function _headMeta($title){
	return <<<HTML
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>$title</title>
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
	<!--[if lte IE 8]>
		<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-old-ie-min.css">
	<![endif]-->
	<!--[if gt IE 8]><!-->
		<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-min.css">
	<!--<![endif]-->
HTML;
}