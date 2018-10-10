<?php

namespace WPackagistRequestor;

class Config {

	private static $_instance;

	/**
	 * @var string Time To Live
	 *
	 * @see http://php.net/manual/en/dateinterval.construct.php#refsect1-dateinterval.construct-parameters
	 *
	 * 'P1W': Period 1 Week
	 */
	public $ttl = 'P1H';

	/**
	 * @var string
	 */
	public $data_dir = __DIR__ . '/../data';

	/**
	 * @var string
	 */
	public $base_url = 'https://wpackagist.org';

	/**
	 * @var string
	 */
	public $packages_url = 'https://wpackagist.org/packages.json';

	private function __construct() {
		$this->data_dir = realpath( $this->data_dir );
	}

	static function instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

}



