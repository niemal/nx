<?php
	if (!defined('NX-ANALYTICS')) die('Go away.');

	require_once('src/stats.php');
	$stats = new SIMPLE($nx->db);

	$uris = $stats->get_uris();
	$refs = $stats->get_refs();
	$oss = $stats->operating_systems();
	$browsers = $stats->browsers();
	$dates = $nx->db->query('SELECT date FROM simple GROUP BY date ORDER BY date;')->fetch_all(MYSQLI_ASSOC);

?><!DOCTYPE html><html>
<head>
	<?php echo _headMeta('Statistics | NX'); ?>
	<link rel="stylesheet" href="assets/utils.css">
	<link rel="stylesheet" href="assets/admin.css">
	<style>
		.fancy-checkbox input {
			visibility: hidden;
		}

		.fancy-checkbox {
			display: inline-block;
			width: 40px;
			height: 10px;
			background: #333;
			margin: 1em; /* todo fix positioning */
			border-radius: 10px;
			position: relative;

			-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.5), 0 1px 0 rgba(255,255,255,0.2);
			-moz-box-shadow: inset 0 1px 1px rgba(0,0,0,0.5), 0 1px 0 rgba(255,255,255,0.2);
			box-shadow: inset 0 1px 1px rgba(0,0,0,0.5), 0 1px 0 rgba(255,255,255,0.2);
		}

		.fancy-checkbox label {
			display: block;
			width: 16px;
			height: 16px;
			border-radius: 10px;
			margin: 0;

			-webkit-transition: all .2s ease;
			-moz-transition: all .2s ease;
			-o-transition: all .2s ease;
			-ms-transition: all .2s ease;
			transition: all .2s ease;
			cursor: pointer;
			position: absolute;
			top: -3px;
			left: -3px;

			-webkit-box-shadow: 0 2px 5px 0 rgba(0,0,0,0.3);
			-moz-box-shadow: 0 2px 5px 0 rgba(0,0,0,0.3);
			box-shadow: 0 2px 5px 0 rgba(0,0,0,0.3);
			background: #FFF8F8;
		}

		.fancy-checkbox input:checked + label {
			left: 27px;
			background: #CDFFD5;
		}


		.ui--section {
			display: none;
		}
	</style>
</head>

<body>
	<div id="layout">
		<?php echo _navbar(); ?>

		<div id="main">
			<div class="header">
				<h1>Statistics</h1>
			</div>

			<div class="content">
				<div class="content-inner glass">
					<div class="article">
						<h2>Customize!</h2>
						<p>Through this page you are able to produce statistics and graphs of your own preference. Furthermore, you are able to export whatever preferences you selected in PDF/more formats.</p>
					</div>
				</div>
			</div>


			<form action="?admin/statistics" method="post" class="content">
				<div class="content-inner glass pure-form">
					<div id="uris">
						<label for="uris">URIs</label>
						<input class="ui--enabled" type="hidden" name="uris" value="false">
						<div class="fancy-checkbox">
							<input type="checkbox" class="ui--checkbox" id="uris_checkbox">
							<label for="uris_checkbox"></label>
						</div>

						<div class="ui--section">
							<input class="ui--filter-input" required name="uri" value="ALL" readonly="readonly">
							<select class="ui--options">
								<option>ALL</option>
								<?php foreach ($uris as $row) { ?>
								<option><?php echo $row['uri']; ?></option>
								<?php } ?>
							</select>
							<button type="button" class="pure-button ui--filter-add">Add</button>
							<button type="button" class="pure-button ui--filter-delete">Delete</button>
							<button type="button" class="pure-button ui--filter-clear">Clear</button>
							<br/>
							<label for="uris_regex">Regex filter</label>
							<input type="text" name="uris_regex">
						</div>
					</div>

					<br/>

					<div id="urls">
						<label for="urls">URLs</label>
						<input type="hidden" name="urls">
						<div class="fancy-checkbox">
							<input type="checkbox" class="ui--checkbox" id="urls_checkbox">
							<label for="urls_checkbox"></label>
						</div>

						<div class="ui--section">
							<label for="urls_regex">Regex filter</label>
							<input type="text" name="urls_regex">
						</div>
					</div>

					<br/>

					<div id="refs">
						<label for="refs">Referers</label>
						<input type="hidden" name="refs">
						<div class="fancy-checkbox">
							<input type="checkbox" class="ui--checkbox" id="refs_checkbox">
							<label for="refs_checkbox"></label>
						</div>

						<div class="ui--section">
							<input class="ui--filter-input" required name="ref" value="ALL" readonly="readonly">
							<select class="ui--options">
								<option>ALL</option>
								<?php foreach ($refs as $row) { ?>
								<option><?php echo $row['ref']; ?></option>
								<?php } ?>
							</select>
							<button type="button" class="pure-button ui--filter-add">Add</button>
							<button type="button" class="pure-button ui--filter-delete">Delete</button>
							<button type="button" class="pure-button ui--filter-clear">Clear</button>
							<br/>
							<label for="refs_regex">Regex filter</label>
							<input type="text" name="refs_regex">
						</div>
					</div>

					<br/>

					<div id="oss">
						<label for="oss">Operating systems</label>
						<input type="hidden" name="oss">
						<div class="fancy-checkbox">
							<input type="checkbox" class="ui--checkbox" id="oss_checkbox">
							<label for="oss_checkbox"></label>
						</div>

						<div class="ui--section">
							<input class="ui--filter-input" required name="os" value="ALL" readonly="readonly">
							<select class="ui--options">
								<option>ALL</option>
								<?php foreach ($oss as $row) { ?>
								<option><?php echo $row['os']; ?></option>
								<?php } ?>
							</select>
							<button type="button" class="pure-button ui--filter-add">Add</button>
							<button type="button" class="pure-button ui--filter-delete">Delete</button>
							<button type="button" class="pure-button ui--filter-clear">Clear</button>
							<br/>
							<label for="oss_regex">Regex filter</label>
							<input type="text" name="oss_regex">
						</div>
					</div>

					<br/>

					<div id="browsers">
						<label for="browsers">Browsers</label>
						<input type="hidden" name="browsers">
						<div class="fancy-checkbox">
							<input type="checkbox" class="ui--checkbox" id="browsers_checkbox">
							<label for="browsers_checkbox"></label>
						</div>

						<div class="ui--section">
							<input class="ui--filter-input" required name="browser" value="ALL" readonly="readonly">
							<select class="ui--options">
								<option>ALL</option>
								<?php foreach ($browsers as $row) { ?>
								<option><?php echo $row['ua']; ?></option>
								<?php } ?>
							</select>
							<button type="button" class="pure-button ui--filter-add">Add</button>
							<button type="button" class="pure-button ui--filter-delete">Delete</button>
							<button type="button" class="pure-button ui--filter-clear">Clear</button>
							<br/>
							<label for="browsers_regex">Regex filter</label>
							<input type="text" name="browsers_regex">
						</div>
					</div>

					<br/>

					<div id="uas">
						<span>User agents</span>
						<input type="hidden" name="uas">
						<div class="fancy-checkbox">
							<input type="checkbox" class="ui--checkbox" id="uas_checkbox">
							<label for="uas_checkbox"></label>
						</div>

						<div class="ui--section">
							<label for="uas_regex">Regex filter</label>
							<input type="text" name="uas_regex">
						</div>
					</div>

					<br/>

					<div>
						<label for="from">From</label>
						<select required name="from">
							<?php foreach ($dates as $row) { ?>
							<option value="<?php echo $row['date'] ?>"><?php echo $row['date'] ?></option>
							<?php } ?>
						</select>

						<?php $dates = array_reverse($dates); ?>
						<label for="to">to</label>
						<select required name="to">
							<?php foreach ($dates as $row) { ?>
							<option value="<?php echo $row['date'] ?>"><?php echo $row['date'] ?></option>
							<?php } ?>
						</select>

						<button class="pure-button" type="submit" name="submit">Submit</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<script src="http://inexist3nce.github.io/static/ayylmao-2.0.0.min.js"></script>

	<script>
		var tokens = ['uri', 'ref', 'os', 'browser'];
		var img_tokens = ['uris', 'urls', 'refs', 'oss', 'browsers', 'uas'];


		function toggleOption(el, option) {
			var options = el.value.split(/\s+/),
				length = options.length,
				i = 0;

			for(; i < length; i++) {
			  if (options[i] === option) {
				options.splice(i, 1);
				break;
			  }
			}
			if (length === options.length) {
				options.push(option);
			}

			el.value = options.join(' ');
		}

		var sections = ['uris', 'urls', 'refs', 'oss', 'browsers', 'uas'];
		for(var i=0; i<sections.length; i++){
			(function(i, section){
				var parentId = '#' + section,
					parent = Ayy('#' + section),
					checkbox = Ayy(parentId + ' .ui--checkbox'),
					filter = Ayy(parentId + ' .ui--filter-input');

				checkbox.onchange = function(){
					if(this.checked){
						Ayy(parentId + ' .ui--section').style.display = 'block';
						Ayy(parentId + ' .ui--enabled').value = 'true';
					} else {
						Ayy(parentId + ' .ui--section').style.display = 'none';
						Ayy(parentId + ' .ui--enabled').value = 'false';
					}
				}

				if(filter == null) return;

				var filterInput = Ayy(parentId + ' .ui--filter-input'),
					filterOptions = Ayy(parentId + ' .ui--options'),
					filterAdd = Ayy(parentId + ' .ui--filter-add'),
					filterDelete = Ayy(parentId + ' .ui--filter-delete'),
					filterClear = Ayy(parentId + ' .ui--filter-clear');

				filterAdd.onclick = function(){
					var selected = filterOptions.options[filterOptions.selectedIndex].text;
					toggleOption(filterInput, selected);
				}

				filterDelete.onclick = function(){
					var selected = filterOptions.options[filterOptions.selectedIndex].text;
					toggleOption(filterInput, selected);
				}

				filterClear.onclick = function(){
					filterInput.value = '';
				}

			})(i, sections[i]);
		}
	</script>
</body>
</html>