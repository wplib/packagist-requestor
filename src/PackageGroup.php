<?php

namespace PackagistRequestor;

class PackageGroup {

	/**
	 * @var string
	 */
	public $label;

	/**
	 * @var string
	 */
	public $url_template;

	/**
	 * @var string
	 */
	public $hash;

	/**
	 * @var string
	 */
	public $json;

	/**
	 * @var Config
	 */
	public $config;

	/**
	 * @var string[]
	 */
	public $plugins;

	function __construct( $url_template, $hash, $args = array() ) {
		$this->label = preg_replace(
			'#^p/(.+)\$\%hash\%\.json$#',
			'$1',
			$url_template
		);

		$args = array_merge( array(
			'config' => function() { return Config::instance(); },
		), $args );
		$this->config = Util::get_arg( $args[ 'config' ] );
		$this->url_template = $url_template;
		$this->hash = $hash;
	}

	function download_again() {
		return $this->label === $this->config->recent_updates;
	}

	/**
	 * @return string
	 */
	function group_url() {
		$path = str_replace( '%hash%', $this->hash, $this->url_template );
		return "{$this->config->base_url()}/{$path}";
	}

}