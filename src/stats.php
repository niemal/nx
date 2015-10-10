<?php
/**
 * Every array returned is supposed to be associative and sorted, high-to-low.
 **/

class SIMPLE
{
	public $db;

	public function SIMPLE($db)
	{
		$this->db =& $db;
	}


	/**
	 * @return
	 *	An array of arrays with indices 'uri' and 'url'.
	 *
	 * The 10 most recent visited URIs with their URLs.
	 **/
	public function most_recent_uris()
	{
		return $this->db
			->query("SELECT uri, url FROM simple ORDER BY ts DESC LIMIT 10;")
			->fetch_all(MYSQLI_ASSOC);
	}


	/**
	 * @return
	 *	An array of arrays with indices 'uri' and 'n'.
	 *
	 * All most visited URIs with their visits-number.
	 **/
	public function top_5_uris()
	{
		return $this->db
			->query("SELECT uri, sum(visits) AS n FROM simple GROUP BY uri ORDER BY n DESC;")
			->fetch_all(MYSQLI_ASSOC);
	}


	/**
	 * @return
	 *	An array of arrays with indices 'uri' and 'n'.
	 *
	 * All most visited URIs with their visits-number.
	 **/
	public function most_visited_uris()
	{
		return $this->db
			->query("SELECT uri, sum(visits) AS n FROM simple GROUP BY uri ORDER BY n DESC;")
			->fetch_all(MYSQLI_ASSOC);
	}


	/**
	 * @return
	 *	An array of indices 'series', 'labels' and 'total', first 2 intended for chartist JS.
	 *
	 * Last week's visits.
	 **/
	public function last_weeks_visits()
	{
		$out = [];
		$out['labels'] = '[';
		$out['series'] = '[';
		$out['total']  = 0;

		$dates = [];
		for ($i = 0; $i < 7; $i++)
			array_unshift( $dates, date('Ymd', strtotime('-'.$i.' days')) );

		foreach ($dates as $date) {
			$res = $this->db
				->query("SELECT sum(visits) AS visits FROM simple WHERE date='$date';")
				->fetch_assoc();
			
			$out_date = implode('/', str_split(substr($date, -4), 2));

			if (!$res['visits'])
				$out['series'] .= "'0', ";
			else {
				$out['series'] .= "'".$res['visits']."', ";
				$out['total'] += intval($res['visits']);
			}

			$out['labels'] .= "'".$out_date."', ";
		}

		$out['series'] = rtrim($out['series'], ', ') . ']';
		$out['labels'] = rtrim($out['labels'], ', ') . ']';

		return $out;
	}



	/**
	 * @return
	 *	A value-sorted (high-to-low) array with browsers as indices.
	 *
	 *	Based on: https://developer.mozilla.org/en-US/docs/Browser_detection_using_the_user_agent
	 **/
	public function browsers()
	{
		$out = [];

		$res = $this->db
			->query("SELECT count(*) as n FROM simple
				WHERE ua NOT LIKE '%firefox/%' AND ua NOT LIKE '%seamonkey/%' AND ua NOT LIKE '%chrome/%'
				AND ua NOT LIKE '%chromium/%' AND ua NOT LIKE '%safari/%' AND ua NOT LIKE '%opr/%'
				AND ua NOT LIKE '%opera/%' AND ua NOT LIKE '%;msie %' AND ua NOT LIKE '%trident/%';")
			->fetch_assoc();
		$out['Unknown'] = intval($res['n']);

		$res = $this->db
			->query("SELECT count(*) as n FROM simple WHERE ua LIKE '%firefox/%' AND ua NOT LIKE '%seamonkey/%';")
			->fetch_assoc();
		$out['Firefox'] = intval($res['n']);

		$res = $this->db
			->query("SELECT count(*) as n FROM simple WHERE ua LIKE '%seamonkey/%';")
			->fetch_assoc();
		$out['Seamonkey'] = intval($res['n']);

		$res = $this->db
			->query("SELECT count(*) as n FROM simple WHERE ua LIKE '%chrome/%' AND ua NOT LIKE '%chromium/%';")
			->fetch_assoc();
		$out['Chrome'] = intval($res['n']);

		$res = $this->db
			->query("SELECT count(*) as n FROM simple WHERE ua LIKE '%chromium/%';")
			->fetch_assoc();
		$out['Chromium'] = intval($res['n']);

		$res = $this->db
			->query("SELECT count(*) as n FROM simple WHERE ua LIKE '%safari/%' AND ua NOT LIKE '%chrome/%' AND ua NOT LIKE '%chromium/%';")
			->fetch_assoc();
		$out['Safari'] = intval($res['n']);

		$res = $this->db
			->query("SELECT count(*) as n FROM simple WHERE ua LIKE '%opr/%' OR ua LIKE '%opera/%';")
			->fetch_assoc();
		$out['Opera'] = intval($res['n']);

		$res = $this->db
			->query("SELECT count(*) as n FROM simple WHERE ua LIKE '%;msie %' OR ua LIKE '%trident/%';")
			->fetch_assoc();
		$out['IE'] = intval($res['n']);

		arsort($out);
		return $out;
	}


	/**
	 * @return
	 *	A value-sorted (high-to-low) array with browsers as indices.
	 *
	 *	Based on: https://developer.mozilla.org/en-US/docs/Browser_detection_using_the_user_agent
	 **/
	public function render_engines()
	{
		$out = [];

		$res = $this->db
			->query("SELECT count(*) as n FROM simple
				WHERE ua NOT LIKE '%gecko/%' AND ua NOT LIKE '%applewebkit/%'
				AND ua NOT LIKE '%opera/%' AND ua NOT LIKE '%trident/%'
				AND ua NOT LIKE '%chrome/%';")
			->fetch_assoc();
		$out['Unknown'] = intval($res['n']);

		$res = $this->db
			->query("SELECT count(*) as n FROM simple WHERE ua LIKE '%gecko/%';")
			->fetch_assoc();
		$out['Gecko'] = intval($res['n']);

		$res = $this->db
			->query("SELECT count(*) as n FROM simple WHERE ua LIKE '%applewebkit/%';")
			->fetch_assoc();
		$out['Webkit'] = intval($res['n']);

		$res = $this->db
			->query("SELECT count(*) as n FROM simple WHERE ua LIKE '%opera/%';")
			->fetch_assoc();
		$out['Presto'] = intval($res['n']);

		$res = $this->db
			->query("SELECT count(*) as n FROM simple WHERE ua LIKE '%trident/%';")
			->fetch_assoc();
		$out['Trident'] = intval($res['n']);

		$res = $this->db
			->query("SELECT count(*) as n FROM simple WHERE ua LIKE '%chrome/%' AND ua NOT LIKE '%applewebkit/%';")
			->fetch_assoc();
		$out['Blink'] = intval($res['n']);

		arsort($out);
		return $out;
	}


	/**
	 * @return
	 *	An array of arrays with indices 'ref' and 'n'.
	 *
	 *	Top 5 referers (most frequent) overall.
	 **/
	public function top_5_refs()
	{
		return $this->db
			->query("SELECT ref, count(ref) AS n FROM simple GROUP BY ref ORDER BY n DESC LIMIT 5;")
			->fetch_all(MYSQLI_ASSOC);
	}


	/**
	 * @return
	 *	An array of arrays with indices 'ref' and 'n'.
	 *
	 *	All referers.
	 **/
	public function all_refs()
	{
		return $this->db
			->query("SELECT ref, count(ref) as n FROM simple GROUP BY ref ORDER BY n DESC;")
			->fetch_all(MYSQLI_ASSOC);
	}


	/**
	 * @return
	 *	An array of arrays with indices 'ua' and 'n'.
	 *
	 *	Top 5 most-used user agents.
	 **/
	public function top_5_uas()
	{
		return $this->db
			->query("SELECT ua, count(ua) as n FROM simple GROUP BY ua ORDER BY n DESC LIMIT 5;")
			->fetch_all(MYSQLI_ASSOC);
	}



	/**
	 * @return
	 *	An array of arrays with indices 'ua' and 'n'.
	 *
	 *	All user agents.
	 **/
	public function all_uas()
	{
		return $this->db
			->query("SELECT ua, count(ua) as n FROM simple GROUP BY ua ORDER BY n DESC;")
			->fetch_all(MYSQLI_ASSOC);
	}


	/**
	 * @return
	 *	type: Array(2, Array(2, N))
	 *		indices: 'top_5', 'all'
	 *			-> $url => $visits
	 *
	 *
	 * NOTE: Might need a little MySQL optimization over here.
	 **/
	public function mixed_urls()
	{
		$res = $this->db
			->query("SELECT url, visits FROM simple;")
			->fetch_all(MYSQLI_ASSOC);

		$all = [];
		$top_5 = [];

		foreach ($res as $item) {
			if (!array_key_exists($item['url'], $all))
				$all[$item['url']] = intval($item['visits']);
			else
				$all[$item['url']] += intval($item['visits']);
		}

		arsort($all);

		$counter = 0;
		foreach ($all as $url => $visits) {
			$top_5[$url] = $visits;
			$counter++;
			if ($counter === 5) break;
		}

		return ['top_5' => $top_5, 'all' => $all];
	}
}