<?php
/**
 * Every array returned is supposed to be sorted, high-to-low.
 **/

require_once('nx.php');

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
		return $this->db->query("SELECT ref, count(*) as n FROM simple
		                                               GROUP BY ref LIMIT 5;");
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
		return $this->db->query("SELECT ref, count(*) as n FROM simple
		                                               GROUP BY ref;");
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
		return $this->db->query("SELECT ua, count(*) as n FROM simple
		                                              GROUP BY ua LIMIT 5;");
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
		return $this->db->query("SELECT ua, count(*) as n FROM simple
		                                              GROUP BY ua;");
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

		$all = Array();
		$top_5 = Array();

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

		return Array('top_5' => $top_5, 'all' => $all);
	}
}