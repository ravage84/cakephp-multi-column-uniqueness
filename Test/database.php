<?php

use AD7six\Dsn\Wrapper\CakePHP\V2\DbDsn;

class DATABASE_CONFIG {

/**
 * Define connections using environment variables
 *
 * @return void
 */
	public function __construct() {
		$this->default = DbDsn::parse("sqlserver://sa:Password12!@.\\SQL2012SP1/cakephp?MultipleActiveResultSets=false");
		$this->test = DbDsn::parse("sqlserver://sa:Password12!@.\\SQL2012SP1/cakephp?MultipleActiveResultSets=false");
	}

}
