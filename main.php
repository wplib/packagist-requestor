<?php

namespace PackagistRequestor;

use Exception;

require __DIR__ . '/vendor/autoload.php';

$configs = array(
	new Config\WPackagistConfig(),
	//new Config\PackagistConfig(),
);
$index = 0;
do {
	/**
	 * @var Config $config
	 */
	$config = $configs[ $index ];

	try {

		$requestor = new Requestor( $config );

		echo "\nProcessing [{$config->provider}] (Memory used: {$config->memory_used()})";
		foreach ( $requestor->yield_requested_groups() as $group ) {
			echo "\n\nProcessing [{$group->label}] (Memory used: {$config->memory_used()})\n";

			foreach ( $requestor->yield_requested_packages( $group ) as $package ) {
				if ( $package->exists() && ! $group->download_again() ) {
					continue;
				}
				if ( ! $requestor->request_package( $package ) ) {
					echo ".";
					continue;
				}

				$package->persist_info();
				echo "\nPackage [{$package->slug}] saved (Memory used: {$config->memory_used()})";

			}
		}

	} catch ( Exception $e ) {
		/**
		 * Wait a minute after an error.
		 */
		echo "\n\nSleeping for a minute (Memory used: {$config->memory_used()})";
		sleep( 60 );
		continue;
	}
	unset( $requestor );

	echo "\n\nSleeping 10 minutes (Memory used: {$config->memory_used()})";
	sleep( 60*10 );

	$index = ++$index % count( $configs );

} while ( true );
