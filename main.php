<?php

namespace WPackagistRequestor;

use Exception;

require __DIR__ . '/vendor/autoload.php';

$requester = new Requestor();
do {
	try {
		$groups = $requester->request_groups();
		foreach ( $groups as $group ) {
			$packages = $requester->request_packages( $group );
			echo "\n\nProcessing {$group->label}\n";

			foreach ( $packages as $package ) {
				if ( ! $requester->request_package( $package ) ) {
					echo ".";
					continue;
				}
				echo "\nSaving {$package->slug}";
				$package->persist_info();

			}
		}
	} catch ( Exception $e ) {
		sleep( 60 );
		continue;
	}
	/**
	 * Wait an hour after finishing
	 */
	sleep( 60 * 60 );
} while ( true );

