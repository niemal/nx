<?php
/**
 * Every array returned is supposed to be associative and sorted, high-to-low.
 **/

require_once('NX.php');

class SIMPLE extends NX
{
	function __construct()
	{
		parent::__construct();
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