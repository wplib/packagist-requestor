<?php

namespace PackagistRequestor;

class Util {

	/**
	 * @param callable|mixed $arg
	 *
	 * @return mixed
	 */
	static function get_arg( $arg ) {
		return is_callable( $arg )
			? call_user_func( $arg )
			: $arg;
	}

	static function default_args( $args, $defaults ) {
		return array_merge( $defaults, $args );
	}

	static function memory_used() {
		$memory_used = memory_get_usage( true );
		if ($memory_used < 1024) {
			$memory_used .= ' bytes';
		} else  /* if ( $memory_used < 1048576 ) */ {
			$memory_used = number_format( round( $memory_used/1024, 2 ) ) . ' Kb';
		} /* else {
			$memory_used = round( $memory_used/1048576, 2 ) . ' Mb';
		} */
		return $memory_used;
	}

}



