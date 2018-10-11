<?php

namespace PackagistRequestor;

use Exception;

require __DIR__ . '/vendor/autoload.php';

$configs = array(
	new Config\WPackagistConfig(),
	new Config\PackagistConfig(),
);
/**
 * @var Config $config
 */
foreach( $configs as $config ) {

	do {

		try {
			$requester = new Requestor( $config );

			echo "\nProcessing [{$config->provider}] (Memory used: {$config->memory_used()})";
			foreach ( $requester->yield_requested_groups() as $group ) {
				echo "\n\nProcessing [{$group->label}] (Memory used: {$config->memory_used()})\n";

				foreach ( $requester->yield_requested_packages( $group ) as $package ) {
					if ( $package->exists() && ! $group->download_again() ) {
						continue;
					}
					if ( ! $requester->request_package( $package ) ) {
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

	} while ( false );

}
