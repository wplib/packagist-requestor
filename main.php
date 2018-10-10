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
		/**
		 * Wait a minute after an error.
		 */
		echo "\n\nSleeping for a minute.\n\n";
		sleep( 60 );
		continue;
	}
	/**
	 * Wait an hour after processing the full list.
	 */
	echo "\n\nSleeping for an hour.\n\n";
	sleep( 60 * 60 );
} while ( true );

