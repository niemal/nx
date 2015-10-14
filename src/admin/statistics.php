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


			<form action="?admin/statistics" method="post" class="content glass">
				<div class="pure-form">
					<b>URIs</b>&nbsp;<input required name="uri" readonly="readonly">
					<select id="uri_opts">
						<option>this_domain</option>
						<option>that_domain</option>
					</select>
					<div id="add_uri_btn" class="pure-button">Add</div>
					<div id="del_uri_btn" class="pure-button">Delete</div>
					<div id="clear_uri_btn" class="pure-button">Clear</div>
				</div>


				<div style="margin-top: 30px" class="pure-form">
					<label for="urls">URLs</label>
					<img id="urls_tick" src="assets/tick_disabled.png">
					<img id="urls_x" src="assets/x_enabled.png">
					<input style="visibility: hidden;" name="urls">
				</div>


				<div class="pure-form">
					<label for="refs">Referers</label>
					<img id="refs_tick" src="assets/tick_disabled.png">
					<img id="refs_x" src="assets/x_enabled.png">
					<input style="visibility: hidden;" name="refs">

					<div id="ref_section" class="sections">
						<input required name="ref" readonly="readonly">
						<select id="ref_opts">
							<option>ALL</option>
							<option>this_ref</option>
							<option>that_ref</option>
						</select>
						<div id="add_ref_btn" class="pure-button">Add</div>
						<div id="del_ref_btn" class="pure-button">Delete</div>
						<div id="clear_ref_btn" class="pure-button">Clear</div>
					</div>
				</div>


				<div class="pure-form" style="margin-top: 15px">
					<label for="oss">Operating systems</label>
					<img id="oss_tick" src="assets/tick_disabled.png">
					<img id="oss_x" src="assets/x_enabled.png">
					<input style="visibility: hidden;" name="oss">

					<div id="os_section" class="sections">
						<input required name="os" readonly="readonly">
						<select id="os_opts">
							<option>ALL</option>
							<option>this_os</option>
							<option>that_os</option>
						</select>
						<div id="add_os_btn" class="pure-button">Add</div>
						<div id="del_os_btn" class="pure-button">Delete</div>
						<div id="clear_os_btn" class="pure-button">Clear</div>
					</div>
				</div>


				<div class="pure-form" style="margin-top: 15px">
					<label for="browsers">Browsers</label>
					<img id="browsers_tick" src="assets/tick_disabled.png">
					<img id="browsers_x" src="assets/x_enabled.png">
					<input style="visibility: hidden;" name="browsers">

					<div id="browser_section" class="sections">
						<input required name="browser" readonly="readonly">
						<select id="browser_opts">
							<option>ALL</option>
							<option>this_browser</option>
							<option>that_browser</option>
						</select>
						<div id="add_browser_btn" class="pure-button">Add</div>
						<div id="del_browser_btn" class="pure-button">Delete</div>
						<div id="clear_browser_btn" class="pure-button">Clear</div>
					</div>
				</div>


				<div class="pure-form" style="margin-top: 15px">
					<label for="uas">User agents</label>
					<img id="uas_tick" src="assets/tick_disabled.png">
					<img id="uas_x" src="assets/x_enabled.png">
					<input style="visibility: hidden;" name="uas">
				</div>


				<div style="margin-top: 30px" class="pure-form">
					<label for="from">From</label>
					<select required name="from">
						<option value="some_date">some_date</option>
					</select>

					<label for="to">To</label>
					<select required name="to">
						<option value="some_date">some_date</option>
					</select>

					<button class="pure-button" type="submit" name="submit">Submit</button>
				</div>
			</form>
		</div>
	</div>

	<script>
		var tokens = ['uri', 'ref', 'os', 'browser'];
		var img_tokens = ['urls', 'refs', 'oss', 'browsers', 'uas']

		tokens.forEach(function(token) { assign_buttons(token); });
		img_tokens.forEach(function(img_token) { assign_toggles_imgs(img_token); });

		function assign_toggles_imgs(img_token)
		{
			document.getElementById(img_token + '_tick').onclick = function(e) {
				this.src = 'assets/tick_enabled.png';
				document.getElementById(img_token + '_x').src = 'assets/x_disabled.png';

				document.getElementsByName(img_token)[0].value = 'true';

				var token = img_token.substring(0, img_token.length-1);
				if (token !== 'ua' || token !== 'url')
					document.getElementById(token + '_section').style.display = 'block';
			}

			document.getElementById(img_token + '_x').onclick = function(e) {
				this.src = 'assets/x_enabled.png';
				document.getElementById(img_token + '_tick').src = 'assets/tick_disabled.png';

				document.getElementsByName(img_token)[0].value = '';
				var token = img_token.substring(0, img_token.length-1);
				if (token !== 'ua' || token !== 'url')
					document.getElementById(token + '_section').style.display = 'none';
			}
		}

		function assign_buttons(token)
		{
			document.getElementById('add_' + token + '_btn').onclick = (function(token) {
				return function() {
					var opts = document.getElementById(token + '_opts'),
						elem = document.getElementsByName(token)[0];

					var selected = opts.options[opts.selectedIndex].text;

					if (selected === 'ALL') elem.value = selected;
					if (elem.value.indexOf('ALL') !== -1) elem.value = '';

					if (elem.value.indexOf(selected) === -1) {
						if (elem.value === '') elem.value = selected;
						else                   elem.value += ' ' + selected;
					}
				}
			})(token);

			document.getElementById('del_' + token + '_btn').onclick = (function(token) {
				return function() {
					var opts = document.getElementById(token + '_opts'),
						elem = document.getElementsByName(token)[0];
						
					elem.value = elem.value.replace(opts.options[opts.selectedIndex].text, '');
					var last_val_index = elem.value.length-1;
					if (elem.value[last_val_index] === ' ')
						elem.value = elem.value.substring(0, last_val_index);
				}
			})(token);

			document.getElementById('clear_' + token + '_btn').onclick = (function(token) {
				return function() { document.getElementsByName(token)[0].value = ''; }
			})(token);
		}
	</script>
</body>
</html>