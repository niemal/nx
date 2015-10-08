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
	 * browsers() and render_engines() were made according to:
	 *	https://developer.mozilla.org/en-US/docs/Browser_detection_using_the_user_agent
	 **/
	public function browsers()
	{
		$out = [];

		$res = $this->db->query("SELECT count(*) as n FROM simple
		WHERE ua NOT LIKE '%firefox/%' AND ua NOT LIKE '%seamonkey/%' AND ua NOT LIKE '%chrome/%'
		AND ua NOT LIKE '%chromium/%' AND ua NOT LIKE '%safari/%' AND ua NOT LIKE '%opr/%'
		AND ua NOT LIKE '%opera/%' AND ua NOT LIKE '%;msie %' AND ua NOT LIKE '%trident/%';");
		$res = $res->fetch_assoc();
		$out['Unknown'] = intval($res['n']);

		$res = $this->db->query("SELECT count(*) as n FROM simple
		WHERE ua LIKE '%firefox/%' AND ua NOT LIKE '%seamonkey/%';");
		$res = $res->fetch_assoc();
		$out['Firefox'] = intval($res['n']);

		$res = $this->db->query("SELECT count(*) as n FROM simple
		WHERE ua LIKE '%seamonkey/%';");
		$res = $res->fetch_assoc();
		$out['Seamonkey'] = intval($res['n']);

		$res = $this->db->query("SELECT count(*) as n FROM simple
		WHERE ua LIKE '%chrome/%' AND ua NOT LIKE '%chromium/%';");
		$res = $res->fetch_assoc();
		$out['Chrome'] = intval($res['n']);

		$res = $this->db->query("SELECT count(*) as n FROM simple
		WHERE ua LIKE '%chromium/%';");
		$res = $res->fetch_assoc();
		$out['Chromium'] = intval($res['n']);

		$res = $this->db->query("SELECT count(*) as n FROM simple
		WHERE ua LIKE '%safari/%' AND ua NOT LIKE '%chrome/%' AND ua NOT LIKE '%chromium/%';");
		$res = $res->fetch_assoc();
		$out['Safari'] = intval($res['n']);

		$res = $this->db->query("SELECT count(*) as n FROM simple
		WHERE ua LIKE '%opr/%' OR ua LIKE '%opera/%';");
		$res = $res->fetch_assoc();
		$out['Opera'] = intval($res['n']);

		$res = $this->db->query("SELECT count(*) as n FROM simple
		WHERE ua LIKE '%;msie %' OR ua LIKE '%trident/%';");
		$res = $res->fetch_assoc();
		$out['IE'] = intval($res['n']);

		arsort($out);
		return $out;
	}

	public function render_engines()
	{
		$out = [];

		$res = $this->db->query("SELECT count(*) as n FROM simple
		WHERE ua NOT LIKE '%gecko/%' AND ua NOT LIKE '%applewebkit/%'
		AND ua NOT LIKE '%opera/%' AND ua NOT LIKE '%trident/%'
		AND ua NOT LIKE '%chrome/%';");
		$res = $res->fetch_assoc();
		$out['Unknown'] = intval($res['n']);

		$res = $this->db->query("SELECT count(*) as n FROM simple
		WHERE ua LIKE '%gecko/%';");
		$res = $res->fetch_assoc();
		$out['Gecko'] = intval($res['n']);

		$res = $this->db->query("SELECT count(*) as n FROM simple
		WHERE ua LIKE '%applewebkit/%';");
		$res = $res->fetch_assoc();
		$out['Webkit'] = intval($res['n']);

		$res = $this->db->query("SELECT count(*) as n FROM simple
		WHERE ua LIKE '%opera/%';");
		$res = $res->fetch_assoc();
		$out['Presto'] = intval($res['n']);

		$res = $this->db->query("SELECT count(*) as n FROM simple
		WHERE ua LIKE '%trident/%';");
		$res = $res->fetch_assoc();
		$out['Trident'] = intval($res['n']);

		$res = $this->db->query("SELECT count(*) as n FROM simple
		WHERE ua LIKE '%chrome/%';");
		$res = $res->fetch_assoc();
		$out['Blink'] = intval($res['n']);

		arsort($out);
		return $out;
	}


	/**
	 * @return
	 *	type: Array(2, 5)
	 *		indices: 'ref', n'
	 *
	 *	context: Top 5 referers overall.
	 **/
	public function top_5_refs()
	{
		$res = $this->db->query("SELECT ref, count(*) as n FROM simple
		                                               GROUP BY ref LIMIT 5;");
		return $res->fetch_all(MYSQLI_ASSOC);
	}


	/**
	 * @return
	 *	type: Array(2, N)
	 *		indices: 'ref', 'n'
	 *
	 *	context: All referers.
	 **/
	public function all_refs()
	{
		$res = $this->db->query("SELECT ref, count(*) as n FROM simple
		                                               GROUP BY ref;");
		return $res->fetch_all(MYSQLI_ASSOC);
	}


	/**
	 * @return
	 *	type: Array(2, 5)
	 *		indices: 'ua', 'n'
	 *
	 *	context: All most used user-agents overall.
	 **/
	public function top_5_uas()
	{
		$res = $this->db->query("SELECT ua, count(*) as n FROM simple
		                                              GROUP BY ua LIMIT 5;");
		return $res->fetch_all(MYSQLI_ASSOC);
	}



	/**
	 * @return
	 *	type: Array(2, 5)
	 *		indices: 'ua', 'n'
	 *
	 *	context: Top 5 most used user-agents overall.
	 **/
	public function all_uas()
	{
		$res = $this->db->query("SELECT ua, count(*) as n FROM simple
		                                              GROUP BY ua;");
		return $res->fetch_all(MYSQLI_ASSOC);
	}


	/**
	 * @return
	 *	type: Array(2, Array(2, N))
	 *		indices: 'top_5', 'all'
	 *			-> $url => $visits
	 *
	 *	context: Most visited URLs.
	 *
	 * NOTE: Might need a little MySQL optimization over here.
	 **/
	public function mixed_urls()
	{
		$res = $this->db->query("SELECT url, visits FROM simple;");
		$res = $res->fetch_all(MYSQLI_ASSOC);

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