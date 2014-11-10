<?php
/**
 * MultiColumnUniqueness Behavior
 *
 * Licensed under The MIT License.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Marc Würth
 * @author Marc Würth <ravage@bluewin.ch>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/ravage84/cakephp-multi-column-uniqueness
 */

/**
 * MultiColumnUniqueness fixture
 */
class MultiColumnUniqueModelFixture extends CakeTestFixture {

/**
 * Table schema
 *
 * 1. A pair of integer fields
 * 2. A pair of string fields
 * 3. A pair of boolean fields
 * 4. A pair of datetime fields
 * 5. A pair of float fields
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
 * Fixture records
 *
 * There is a system behind this data:
 * 1. Both fields are NULL
 * 2. First field filled, second NULL
 * 3. First field NULL, second filled
 * 4. Both fields "filled" with empty or zero values
 * 5. Both fields filled with non-empty or non-zero values
 * 6. First field "filled" with empty or zero value, second filled with non-empty or non-zero value
 * 7. First field filled with non-empty or non-zero value, second "filled" with empty or zero value
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
		array('id' => 2,
			'integer1' => 3, 'integer2' => null,
			'string1' => 'three', 'string2' => null,
			'boolean1' => true, 'boolean2' => null,
			'datetime1' => '2013-01-03 12:13:14', 'datetime2' => null,
			'float1' => 3.0, 'float2' => null),
		array('id' => 3,
			'integer1' => null, 'integer2' => 40,
			'string1' => null, 'string2' => 'forty',
			'boolean1' => null, 'boolean2' => false,
			'datetime1' => null, 'datetime2' => '2013-04-01 12:13:14',
			'float1' => null, 'float2' => 44.0),
		array('id' => 4,
			'integer1' => 0, 'integer2' => 0,
			'string1' => '', 'string2' => '',
			'boolean1' => false, 'boolean2' => false,
			'datetime1' => '', 'datetime2' => '',
			'float1' => 0, 'float2' => 0),
		array('id' => 5,
			'integer1' => 6, 'integer2' => 60,
			'string1' => 'six', 'string2' => 'sixty',
			'boolean1' => true, 'boolean2' => true,
			'datetime1' => '2013-01-06 12:13:14', 'datetime2' => '2013-06-01 12:13:14',
			'float1' => 6.0, 'float2' => 66.0),
		array('id' => 6,
			'integer1' => 0, 'integer2' => 70,
			'string1' => '', 'string2' => 'seventy',
			'boolean1' => false, 'boolean2' => true,
			'datetime1' => '', 'datetime2' => '2013-07-01 12:13:14',
			'float1' => 0, 'float2' => 77.0),
		array('id' => 7,
			'integer1' => 8, 'integer2' => 0,
			'string1' => 'eight', 'string2' => '',
			'boolean1' => true, 'boolean2' => false,
			'datetime1' => '2013-01-08 12:13:14', 'datetime2' => '',
			'float1' => 8.0, 'float2' => 0),
	);

}
