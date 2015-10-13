<!DOCTYPE html><html>
<head>
<meta charset="utf-8">
	<title>Statistics | NX</title>
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-min.css">
	<link rel="stylesheet" href="assets/chartist.min.css">
	<link rel="stylesheet" href="assets/admin.css">
</head>

<body>
	<div id="layout">
		<a href="#menu" id="menuLink" class="menu-link"><span></span></a>

		<div id="menu" class="dark-glass">
			<div class="pure-menu">
				<a class="pure-menu-heading dark-glass" href="#">nx analytics</a>

				<ul class="pure-menu-list">
					<li class="pure-menu-item"><a href="?admin/" class="pure-menu-link">Dashboard</a></li>
					<li class="pure-menu-item"><a href="?admin/statistics" class="pure-menu-link">Statistics</a></li>
					<li class="pure-menu-item"><a href="?admin/settings" class="pure-menu-link">Settings</a></li>
					<li class="pure-menu-item"><a href="?admin/logout" class="pure-menu-link">Logout</a></li>
				</ul>
			</div>
		</div>

		<div id="main">
			<div class="header">
				<h1>Statistics</h1>
			</div>

			<div class="content glass">
				<h2>Custom filters</h2>
				<p>Through this page you are able to produce statistics and graphs of your own preference. Furthermore, you are also able to export whatever you want in PDF/more formats.</p>
			</div>

			<form action="?admin/statistics" method="post" class="content glass pure-form">
				<fieldset>
					<label for="from">From</label>
					<select required name="from">
						<option value="some_date">some_date</option>
					</select>

					<label for="to">To</label>
					<select required name="to">
						<option value="some_date">some_date</option>
					</select>

					<button class="pure-button" type="submit" name="submit">Submit</button>
				</fieldset>
			</form>
		</div>
	</div>
</body>
</html>