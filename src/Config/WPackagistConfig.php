<?php

namespace PackagistRequestor\Config;

use PackagistRequestor\Config;

class WPackagistConfig extends Config {

	function __construct() {
		$this->provider = 'WPackagist.org';
		$this->_base_url = 'https://wpackagist.org';
		$this->_subdivide_by = self::SUBDIVIDE_BY_NAME;
		$this->_subdir = '';
		parent::__construct();
	}
}



