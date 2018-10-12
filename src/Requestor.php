<?php

namespace PackagistRequestor;

use Exception;

class Requestor implements Logger {

	/**
	 * @var Config
	 */
	public $config;

	/**
	 * @var string
	 */
	public $last_error;

	function __construct( $config ) {
		$this->config = $config;
	}

	function log( $message ) {
		if ( 1 !== func_num_args() ) {
			$message = call_user_func_array( 'sprintf', func_get_args() );
		}
		echo $this->last_error = $message;
	}

	/**
`	 * @return PackageGroup[]
	 * @throws Exception
	 */
	function request_groups() {
		$groups = array();
		foreach ( $this->yield_requested_groups() as $group ) {
			/**
			 * @var PackageGroup $group
			 */
			$groups[ $group->url_template ] = $group;
		}
		return $groups;

	}

	/**
	 * @param array $args
	 * @return PackageGroup[]
	 * @throws Exception
	 */
	function yield_requested_groups() {

		do {

			$package_group = null;

			$success = false;
			$response = HttpRequest::get( $this->config->packages_url() );
			if ( 200 !== $response->status_code ) {
				$this->log( sprintf(
					'FAILED: Status code #%d: %s',
					$response->status_code,
					$this->config->packages_url()
				));
				break;
			}
			$packages_arr = (array)\json_decode( $response->body );
			if ( empty( $packages_arr ) ) {
				$this->log( "\nFAILED: List of packages was empty." );
				break;
			}
			if ( empty( $packages_arr[ 'provider-includes' ] ) ) {
				$this->log( "\nFAILED: List of packages was empty." );
				break;
			}
			if ( ! is_object( $packages_arr[ 'provider-includes' ] ) ) {
				$this->log( "\nFAILED: Package-Includes not an array." );
				break;
			}
			$providers_arr = get_object_vars( $packages_arr[ 'provider-includes' ] );
			krsort( $providers_arr );
			foreach( $providers_arr as $provider_url => $provider_obj ) {

				if ( ! isset( $provider_obj->sha256 ) ) {
					$this->log( "\nWARNING: {$provider_url} object has no sha256 property.\n" );
					continue;
				}

				yield new PackageGroup(
					$provider_url,
					$provider_obj->sha256,
					[ 'config' => $this->config, ]
				);

			}
			$success = true;

		} while ( false );

		if ( ! $success ) {
			throw new Exception( $this->last_error );
		}
		return array();
	}

	/**
	 * @param PackageGroup $package_group
	 * @return Package[]
	 * @throws Exception
	 */
	function request_packages( $package_group ) {
		$packages = array();
		foreach ( $this->yield_requested_packages( $package_group ) as $package ) {
			/**
			 * @var Package $package
			 */
			$packages[ $package->slug ] = $package;
		}
		return $packages;

	}

	/**
	 * @param PackageGroup $package_group
	 *
	 * @return Package[]
	 * @throws Exception
	 */
	function yield_requested_packages( $package_group ) {

		do {
			$success = false;
			$response = HttpRequest::get( $url = $package_group->group_url() );
			if ( 200 !== $response->status_code ) {
				$this->log( sprintf( "\nFAILED: Status code #%d: %s", $response->status_code, $url ) );
				break;
			}
			$providers_obj = \json_decode( $response->body );
			if ( empty( $providers_obj ) ) {
				$this->log( "\nFAILED: List of providers was empty from {$url}." );
				break;
			}
			if ( empty( $providers_obj->providers ) ) {
				$this->log( "\nFAILED: List of providers was empty from {$url}." );
				break;
			}
			if ( ! is_object( $providers_obj->providers ) ) {
				$this->log( "\nFAILED: Providers not an object from {$url}." );
				break;
			}
			foreach( get_object_vars( $providers_obj->providers ) as $package_slug => $package_obj ) {

				if ( ! isset( $package_obj->sha256 ) ) {
					$this->log( "\nWARNING: {$package_slug} object has no sha256 property from {$url}.\n" );
					continue;
				}

				yield new Package(
					$package_slug,
					$package_obj->sha256,
					[ 'config' => $this->config ]
				);

			}
			$success = true;

		} while ( false );
		if ( ! $success ) {
			throw new Exception( $this->last_error );
		}
		return array();
	}

	/**
	 * @param Package $package
	 * @return bool
	 * @throws Exception
	 */
	function request_package( $package ) {

		do {

			$success = true;

			$downloaded = false;

			if ( is_dir( $package->info_dir() ) ) {
				break;
			}

			if ( ! $package->needs_refresh( $this->config->ttl ) ) {
				continue;
			}

			$success = false;

			$response = HttpRequest::get( $url = $package->info_url() );
			if ( 200 !== $response->status_code ) {
				$this->log( sprintf(
					"\nFAILED: Status code #%d: %s",
					$response->status_code,
					$url
				));
				break;
			}
			$package->json = $response->body;
			$downloaded  = $success = true;

		} while ( false );
		if ( ! $success ) {
			throw new Exception( $this->last_error );
		}
		return $downloaded;
	}

}



