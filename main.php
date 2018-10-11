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
	$yield = array( 'yield' => true );
	do {

		try {
			$requester = new Requestor( $config );

			echo "\nProcessing {$config->provider}";

			foreach ( $requester->request_groups( $yield ) as $group ) {
				echo "\n\nProcessing {$group->label}\n";

				foreach ( $requester->request_packages( $group, $yield ) as $package ) {
					if ( $package->exists() && ! $group->download_again() ) {
						continue;
					}
					if ( ! $requester->request_package( $package ) ) {
						echo ".";
						continue;
					}
					echo "\nSaving {$package->slug}";
					$package->persist_info();

				}
			}
		} catch ( Exception $e ) {
			/**
			 * Wait a minute after an error.
			 */
			echo "\n\nSleeping for a minute";
			sleep( 60 );
			continue;
		}

	} while ( false );

}
