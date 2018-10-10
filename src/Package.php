<?php

namespace WPackagistRequestor;

use DateTime;
use DateInterval;
use DateTimeZone;
use Exception;

class Package {

	public $slug;

	public $json;

	public $hash;

	/**
	 * @var string Time To Live
	 */
	public $ttl = '+1 week';

	/**
	 * @var Config
	 */
	public $config;

	/**
	 * Package constructor.
	 *
	 * @param string $slug
	 * @param string $hash
	 * @param array $args
	 */
	function __construct( $slug, $hash, $args = array() ) {
		$args = array_merge( array(
			'config' => function() { return Config::instance(); },
		), $args );
		$this->config = Util::get_arg( $args[ 'config' ] );
		$this->slug = $slug;
		$this->hash = $hash;
	}

	/**
	 * @return string
	 */
	function info_url() {
		return "{$this->config->base_url}/p/{$this->slug}\${$this->hash}.json";
	}

	/**
	 * @return string
	 */
	function filepath() {
		return $this->info_dir() . '/package.json';
	}

	/**
	 * @return DateTime|null
	 */
	function file_datetime() {
		do {
			$datetime = null;
			if ( ! is_file( $filepath = $this->filepath() ) ) {
				break;
			}
			$datetime = new DateTime();
			$datetime->setTimezone( new DateTimeZone( DateTimeZone::UTC ) );
			$datetime->setTimestamp( filemtime( $filepath ) );
		} while ( false );
		return $datetime;
	}

	/**
	 * @return string
	 */
	function info_dir() {
		$two_letter = substr( basename( $this->slug ), 0, 2 );
		list( $type, $name ) = explode( '/', $this->slug );
		return "{$this->config->data_dir}/{$type}/{$two_letter}/{$name}";
	}

	/**
	 * @param string|null $json
	 */
	function persist_info( $json = null ) {
		if ( ! is_null( $json ) ) {
			$this->json = $json;
		}
		if ( ! is_dir( $dir = $this->info_dir() ) ) {
			mkdir( $dir, 0777, true );
		}
		file_put_contents( $this->filepath(), $this->json );
	}

	/**
	 * @param string $ttl Time To Live
	 * @return bool
	 */
	function needs_refresh( $ttl ) {

		do {

			$needs_refresh = false;
			try {
				$one_week = new DateInterval( $ttl );
			} catch ( Exception $exception ) {
				break;
			}

			if ( is_null( $expired = $this->file_datetime() ) ) {
				$needs_refresh = true;
				break;
			}

			$expired = $expired->add( $one_week );
			$expired->setTimezone( new DateTimeZone( DateTimeZone::UTC ) );

			$now = new DateTime( 'now' );
			$now->setTimezone( new DateTimeZone( DateTimeZone::UTC ) );

			$needs_refresh = $now > $expired;

		} while ( false );

		return $needs_refresh;

	}


}



