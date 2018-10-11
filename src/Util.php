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

}



