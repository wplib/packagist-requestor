<?php

namespace WPackagistRequestor;

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

}



