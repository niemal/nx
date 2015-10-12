<?php
/**
 * Every array returned is supposed to be ASsociative and sorted, high-to-low.
 **/

class SIMPLE
{
	public $db;

	public function SIMPLE($db)
	{
		$this->db =& $db;
	}


	/**
	 * @param
	 *	UNIX epoch timestamp.
	 *
	 * @return
	 *	A string represeting how long ago was the timestamp.
	 **/
	private function how_long_ago($ts)
	{
		$diff = time() - $ts;
		if ($diff > 5183999)
			return intval($diff/2592000) . ' months ago';
		else if ($diff > 2591999)
			return '1 month ago';
		else if ($diff > 172799)
			return intval($diff/86400) . ' days ago';
		else if ($diff > 86399)
			return '1 day ago';
		else if ($diff > 7199)
			return intval($diff/3600) . ' hours ago';
		else if ($diff > 3599)
			return '1 hour ago';
		else if ($diff > 119)
			return intval($diff/60) . ' minutes ago';
		else if ($diff > 59)
			return '1 minute ago';
		else if ($diff > 1)
			return $diff . ' seconds ago';
		else
			return 'Just now';
	}


	/**
	 * @return
	 *	An array of arrays with indices 'uri' and 'url'.
	 *
	 * The 10 most recent visited URIs with their URLs.
	 **/
	public function most_recent_uris()
	{
		$rows = $this->db
			->query("SELECT simple.uri, urls.url, urls.ts FROM simple INNER JOIN urls ON simple.id = urls.id ORDER BY urls.ts DESC LIMIT 10;")
			->fetch_all(MYSQLI_ASSOC);

		foreach ($rows as &$row) {
			$row['time'] = $this->how_long_ago($row['ts']);
			unset($row['ts']);
		}

		return $rows;
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
	 * LASt week's visits.
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
	 * An array of arrays with indices 'os' and 'n'.
	 *
	 **/
	public function operating_systems()
	{
		return $this->db
			->query("SELECT 'Unknown' AS 'os', count(*) AS n FROM simple WHERE ua NOT LIKE '%windows%' AND ua NOT LIKE '%winnt%'
				AND ua NOT LIKE '%linux%' AND ua NOT LIKE '%open bsd%' AND ua NOT LIKE '%mac os%' AND ua NOT LIKE '%search bot%'
				AND ua NOT LIKE '%iphone%' AND ua NOT LIKE '%ipod%' AND ua NOT LIKE '%ipad%' AND ua NOT LIKE '%android%' AND ua NOT LIKE '%bot%'
				AND ua NOT LIKE '%crawler%' AND ua NOT LIKE '%spider%'
				AND ua NOT LIKE '%http%' HAVING n <> 0
				UNION SELECT 'Windows' AS 'os', count(*) AS n FROM simple WHERE ua LIKE '%windows%' OR ua LIKE '%winnt%'
				AND ua NOT LIKE '%bot%' AND ua NOT LIKE '%spider%' AND ua NOT LIKE '%crawler%' AND ua NOT LIKE '%http%' HAVING n <> 0
				UNION SELECT 'Linux' AS 'os', count(*) AS n FROM simple WHERE ua LIKE '%linux%' OR ua LIKE '%open bsd%'
				AND ua NOT LIKE '%android%' HAVING n <> 0
				UNION SELECT 'Macintosh' AS 'os', count(*) AS n FROM simple WHERE ua LIKE '%mac os%' AND ua NOT LIKE '%iphone%'
				AND ua NOT LIKE '%ipod%' AND ua NOT LIKE '%ipad%' HAVING n <> 0
				UNION SELECT 'Bots' AS 'os', count(*) AS n FROM simple WHERE ua LIKE '%slurp%' OR ua LIKE '%bot%' OR ua LIKE '%spider%'
				OR ua LIKE '%crawler%' OR ua LIKE '%http%' HAVING n <> 0
				UNION SELECT 'iOS' AS 'os', count(*) AS n FROM simple WHERE ua LIKE '%iphone%' OR ua LIKE '%ipod%' OR ua LIKE '%ipad%' HAVING n <> 0
				UNION SELECT 'Android' AS 'os', count(*) AS n FROM simple WHERE ua LIKE '%android%' HAVING n <> 0
				ORDER BY n DESC;")
			->fetch_all(MYSQLI_ASSOC);
	}


	/**
	 * @return
	 *	An array of arrays with 'ua' and 'n' as indices.
	 *
	 *	Based on: https://developer.mozilla.org/en-US/docs/Browser_detection_using_the_user_agent
	 **/
	public function browsers()
	{
		return $this->db
			->query("SELECT 'Unknown' AS 'ua', count(*) AS n FROM simple WHERE ua NOT LIKE '%firefox/%' AND ua NOT LIKE '%seamonkey/%'
				AND ua NOT LIKE '%chrome/%' AND ua NOT LIKE '%chromium/%' AND ua NOT LIKE '%safari/%' AND ua NOT LIKE '%opr/%'
				AND ua NOT LIKE '%opera/%' AND ua NOT LIKE '%msie%' AND ua NOT LIKE '%trident/%' AND ua NOT LIKE '%slurp%'
				AND ua NOT LIKE '%bot%' AND ua NOT LIKE '%crawler%' AND ua NOT LIKE '%spider%' AND ua NOT LIKE '%http%' HAVING n <> 0
				UNION SELECT 'Firefox' AS 'ua', count(*) AS n FROM simple WHERE ua LIKE '%firefox/%' AND ua NOT LIKE '%seamonkey/%' HAVING n <> 0
				UNION SELECT 'Seamonkey' AS 'ua', count(*) AS n FROM simple WHERE ua LIKE '%seamonkey/%' HAVING n <> 0
				UNION SELECT 'Chrome' AS 'ua', count(*) AS n FROM simple WHERE ua LIKE '%chrome/%' AND ua NOT LIKE '%chromium/%' HAVING n <> 0
				UNION SELECT 'Chromium' AS 'ua', count(*) AS n FROM simple WHERE ua LIKE '%chromium/%' HAVING n <> 0
				UNION SELECT 'Safari' AS 'ua', count(*) AS n FROM simple WHERE ua LIKE '%safari/%' AND ua NOT LIKE '%chrome/%'
				AND ua NOT LIKE '%chromium/%' HAVING n <> 0
				UNION SELECT 'Opera' AS 'ua', count(*) AS n FROM simple WHERE ua LIKE '%opr/%' OR ua LIKE '%opera/%' HAVING n <> 0
				UNION SELECT 'Bots' AS 'os', count(*) AS n FROM simple WHERE ua LIKE '%bot%' OR ua LIKE '%spider%' OR ua LIKE '%crawler%'
				OR ua LIKE '%http%' HAVING n <> 0
				UNION SELECT 'IE' AS 'ua', count(*) AS n FROM simple WHERE ua LIKE '%msie%' OR ua LIKE '%trident/%'
				AND ua NOT LIKE '%spider%' AND ua NOT LIKE '%bot%' AND ua NOT LIKE '%crawler%' AND ua NOT LIKE '%http%' HAVING n <> 0
				ORDER BY n DESC;")
			->fetch_all(MYSQLI_ASSOC);
	}


	/**
	 * @return
	 *	An array of arrays with 'eng' and 'n' as indices.
	 *
	 *	BASed on: https://developer.mozilla.org/en-US/docs/Browser_detection_using_the_user_agent
	 **/
	public function render_engines()
	{
		return $this->db
			->query("SELECT 'Unknown' AS 'eng', count(*) AS n FROM simple WHERE ua NOT LIKE '%gecko/%' AND ua NOT LIKE '%applewebkit/%'
				AND ua NOT LIKE '%opera/%' AND ua NOT LIKE '%trident/%' AND ua NOT LIKE '%chrome/%' AND ua NOT LIKE '%bot%'
				AND ua NOT LIKE '%crawler%' AND ua NOT LIKE '%spider%' AND ua NOT LIKE '%http%' HAVING n <> 0
				UNION SELECT 'Gecko' AS 'eng', count(*) AS n FROM simple WHERE ua LIKE '%gecko/%' HAVING n <> 0
				UNION SELECT 'Webkit' AS 'eng', count(*) AS n FROM simple WHERE ua LIKE '%applewebkit/%' HAVING n <> 0
				UNION SELECT 'Presto' AS 'eng', count(*) AS n FROM simple WHERE ua LIKE '%opera/%' HAVING n <> 0
				UNION SELECT 'Trident' AS 'eng', count(*) AS n FROM simple WHERE ua LIKE '%trident/%' HAVING n <> 0
				UNION SELECT 'Blink' AS 'eng', count(*) AS n FROM simple WHERE ua LIKE '%chrome/%' AND ua NOT LIKE '%applewebkit/%' HAVING n <> 0
				ORDER BY n DESC;")
			->fetch_all(MYSQLI_ASSOC);
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
			->query("SELECT ref, count(ref) AS n FROM simple GROUP BY ref ORDER BY n DESC;")
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
			->query("SELECT ua, count(ua) AS n FROM simple GROUP BY ua ORDER BY n DESC LIMIT 5;")
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
			->query("SELECT ua, count(ua) AS n FROM simple GROUP BY ua ORDER BY n DESC;")
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