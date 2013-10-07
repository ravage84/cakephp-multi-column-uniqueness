<?php
/**
 * MultiColumnUniquenessFixture
 *
 * @todo Define $records
 */
class MultiColumnUniqueModelFixture extends CakeTestFixture {

/**
 * Table schema
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'length' => 11, 'key' => 'primary'),
		'integer1' => array('type' => 'integer', 'null' => true),
		'integer2' => array('type' => 'integer', 'null' => true),
		'string1' => array('type' => 'string', 'length' => 10, 'null' => true),
		'string2' => array('type' => 'string', 'length' => 10, 'null' => true),
		'boolean1' => array('type' => 'boolean', 'null' => true, 'length' => '1'),
		'boolean2' => array('type' => 'boolean', 'null' => true, 'length' => '1'),
		'datetime1' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'datetime2' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'float1' => array('type' => 'float', 'null' => true, 'length' => '9'),
		'float2' => array('type' => 'float', 'null' => true, 'length' => '9'),
	);

/**
 * records property
 *
 * @var array
 */
	public $records = array(
		array('id' => 1,
			'integer1' => null, 'integer2' => null,
			'string1' => null, 'string2' => null,
			'boolean1' => null, 'boolean2' => null,
			'datetime1' => null, 'datetime2' => null,
			'float1' => null, 'float2' => null),
	);

}
