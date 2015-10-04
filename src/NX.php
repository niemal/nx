<?php

class NX
{
	private $db;
	public $mode;
	private $config;
	public $error;

	function __construct() {
		define('NX-ANALYTICS', true);
		$this->config = $this->getConfig();
		$this->connectDb();
		$this->mode = $this->config['nx-mode'];
	}

	public function init() {
		if ($this->mode === 'simple') {
			require_once('NX_simple.php');
			$nx = new NX_simple($this->db, $this->config);
			$nx->log();
		} else {
			require_once('NX_advanced.php');
			$nx = new NX_advanced($this->db, $this->config);
			$nx->log();
		}
	}

	private function getConfig() {
		// install script makes sure everything is correctly set
		$filename = dirname(__FILE__) . '/config.php';
		if ( file_exists($filename) ) {
			require($filename);
			if( isset($nx_config) ){
				return json_decode($nx_config, true);
			} else {
				$this->error('critical - configuration damaged', true);
			}
		} else {
			$this->error('configuration file doesnt exist', true);
		}
	}

	private function connectDb() {
		$db = new mysqli(
			$this->config['db-host'],
			$this->config['db-user'],
			$this->config['db-pass'],
			$this->config['nx-db']
		);

		if ($db->connect_errno) {
			$this->error('Connection failed to establish: [' . $db->connect_errno . '] ' . $db->connect_error);
		}

		$this->db = $db;
	}

	public function error($err, $die=false) {
		$errs = $this->config['nx-errors'];
		if($die === true || $errs === 'show') {
			echo '[NX] Error: ' . $err . PHP_EOL;
			die();
		} else {
			error_log("[NX] $err");
		}
	}
}
