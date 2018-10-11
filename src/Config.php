<?php

namespace PackagistRequestor;

abstract class Config {

	const SUBDIVIDE_BY_ORG = 1;
	const SUBDIVIDE_BY_NAME = 2;

	protected static $_instance;

	/**
	 * @var string Time To Live
	 *
	 * @see http://php.net/manual/en/dateinterval.construct.php#refsect1-dateinterval.construct-parameters
	 *
	 * 'P1W': Period 1 Hour
	 */
	public $ttl = 'PT1H';

	/**
	 * @var string
	 */
	public $latest_group;

	/**
	 * @var string
	 */
	public $provider;

	/**
	 * This is used
	 * @var string
	 */
	protected $_data_dir = __DIR__ . '/../data';

	/**
	 * @var string
	 */
	protected $_base_url;

	/**
	 * @var string
	 */
	protected $_subdivide_by;

	/**
	 * @var string
	 */
	protected $_subdir;

	static function instance() {
		if ( ! isset( self::$_instance ) ) {
			static::$_instance = new static();
		}
		return static::$_instance;
	}

	protected function __construct() {
		$this->_data_dir = realpath( $this->_data_dir );
	}

	function packages_url() {
		return "{$this->_base_url}/packages.json";
	}

	function subdivide_by() {
		return $this->_subdivide_by;
	}

	function set_subdivide_by( $subdivide_by ) {
		$this->_subdivide_by = $subdivide_by;
	}

	function data_dir() {
		return $this->_data_dir;
	}

	function set_data_dir( $data_dir ) {
		$this->_data_dir = $data_dir;
	}

	function base_url() {
		return $this->_base_url;
	}

	function set_base_url( $base_url ) {
		$this->_base_url = $base_url;
	}

	function subdir() {
		return $this->_subdir;
	}

	function set_subdir( $subdir ) {
		$this->_subdir = $subdir;
	}

	function memory_used() {
		return Util::memory_used();
	}
}



