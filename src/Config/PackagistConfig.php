<?php

namespace PackagistRequestor\Config;

use PackagistRequestor\Config;

class PackagistConfig extends Config {

	function __construct() {
		$this->provider = 'Packagist.org';
		$this->latest_group = 'provider-latest';
		$this->_base_url = 'https://packagist.org';
		$this->_subdivide_by = self::SUBDIVIDE_BY_ORG;
		$this->_subdir = 'packagist';
		parent::__construct();
	}

}



