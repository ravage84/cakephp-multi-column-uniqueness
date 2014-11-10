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

App::uses('MultiColumnUniquenessBehavior', 'MultiColumnUniqueness.Model/Behavior');

/**
 * MultiColumnUniqueness test
 *
 * @property AppModel $_model
 * @coversDefaultClass MultiColumnUniquenessBehavior
 */
class MultiColumnUniquenessBehaviorTest extends CakeTestCase {

/**
 * Model for tests
 *
 * @var
 */
	protected $_model;

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('plugin.multi_column_uniqueness.multi_column_unique_model');

/**
 * An array of totally unique data
 *
 * @var array
 */
	protected $_uniqueData = array(
		'integer1' => 9, 'integer2' => 90,
		'string1' => 'nine', 'string2' => 'ninety',
		'boolean1' => null, 'boolean2' => true,
		'datetime1' => '2013-01-09 12:13:14', 'datetime2' => '2013-09-01 12:13:14',
		'float1' => 9.0, 'float2' => 99.0
	);

/**
 * An array of non-unique data
 *
 * @var array
 */
	protected $_nonUniqueData = array(
		'integer1' => 6, 'integer2' => 60,
		'string1' => 'six', 'string2' => 'sixty',
		'boolean1' => true, 'boolean2' => true,
		'datetime1' => '2013-01-06 12:13:14', 'datetime2' => '2013-06-01 12:13:14',
		'float1' => 6.0, 'float2' => 66.0
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_model = ClassRegistry::init('MultiColumnUniqueModel');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->_model);

		parent::tearDown();
	}

/**
 * Tests the default config (no config given)
 *
 * @return void
 * @coversNothing
 */
	public function testDefaultConfig() {
		$this->_loadBehavior();

		$settings = $this->_model->Behaviors->MultiColumnUniqueness->settings['MultiColumnUniqueModel'];
		$expected = array(
			'fields' => array(),
			'onlyOnce' => true,
			'errMsg' => array()
		);
		$this->assertSame($expected, $settings);
	}

/**
 * Tests the config "One field group set, without error message set (most simple and shortest way)"
 *
 * @return void
 * @covers ::setup
 */
	public function testOneGroupSimplestNoErrMsg() {
		$this->_loadBehavior(
			array('fields' => array('integer1', 'integer2'))
		);

		$settings = $this->_model->Behaviors->MultiColumnUniqueness->settings['MultiColumnUniqueModel'];
		$expected = array(
			'fields' => array(array('integer1', 'integer2')),
			'onlyOnce' => true,
			'errMsg' => array('The fields integer1 and integer2 must be unique.')
		);
		$this->assertSame($expected, $settings);
	}

/**
 * Tests the config "One field group set, without error message set (array in array)"
 *
 * @return void
 * @covers ::setup
 */
	public function testOneGroupNoErrMsg() {
		$this->_loadBehavior(
			array('fields' => array(array('integer1', 'integer2')))
		);

		$settings = $this->_model->Behaviors->MultiColumnUniqueness->settings['MultiColumnUniqueModel'];
		$expected = array(
			'fields' => array(array('integer1', 'integer2')),
			'onlyOnce' => true,
			'errMsg' => array('The fields integer1 and integer2 must be unique.')
		);
		$this->assertSame($expected, $settings);
	}

/**
 * Tests the config "One field group and its error message set"
 *
 * @return void
 * @covers ::setup
 */
	public function testOneGroupOneErrMsg() {
		$this->_loadBehavior(
			array(
				'fields' => array(array('integer1', 'integer2')),
				'errMsg' => array('Integer1 and Integer2 must be unique.')
			)
		);

		$settings = $this->_model->Behaviors->MultiColumnUniqueness->settings['MultiColumnUniqueModel'];
		$expected = array(
			'fields' => array(array('integer1', 'integer2')),
			'onlyOnce' => true,
			'errMsg' => array('Integer1 and Integer2 must be unique.')
		);
		$this->assertSame($expected, $settings);
	}

/**
 * Tests the config "Two field groups and their corresponding error message set"
 *
 * @return void
 * @covers ::setup
 */
	public function testTwoGroupsAndErrMsgs() {
		$this->_loadBehavior(
			array(
				'fields' => array(array('integer1', 'integer2'), array('string1', 'string2')),
				'errMsg' => array('Integer1 and Integer2 must be unique.', 'String1 and String2 must be unique.')
			)
		);

		$settings = $this->_model->Behaviors->MultiColumnUniqueness->settings['MultiColumnUniqueModel'];
		$expected = array(
			'fields' => array(array('integer1', 'integer2'), array('string1', 'string2')),
			'onlyOnce' => true,
			'errMsg' => array('Integer1 and Integer2 must be unique.', 'String1 and String2 must be unique.')
		);
		$this->assertSame($expected, $settings);
	}

/**
 * Tests the config "Two field groups set but only the error message of the first group set"
 *
 * @return void
 * @covers ::setup
 */
	public function testTwoGroupsOneErrMsg() {
		$this->_loadBehavior(
			array(
				'fields' => array(array('integer1', 'integer2'), array('string1', 'string2')),
				'errMsg' => array('Integer1 and Integer2 must be unique.')
			)
		);

		$settings = $this->_model->Behaviors->MultiColumnUniqueness->settings['MultiColumnUniqueModel'];
		$expected = array(
			'fields' => array(array('integer1', 'integer2'), array('string1', 'string2')),
			'onlyOnce' => true,
			'errMsg' => array('Integer1 and Integer2 must be unique.', 'The fields string1 and string2 must be unique.')
		);
		$this->assertSame($expected, $settings);
	}

/**
 * Tests when the onlyOnce option is set to true (default)
 *
 * @return void
 * @covers ::beforeValidate
 */
	public function testOnlyOnceTrue() {
		$this->_loadBehavior(
			array(
				'fields' => array(
					array('integer1', 'integer2', 'string1'),
					array('string2', 'boolean1', 'boolean2')
				),
				'onlyOnce' => true
			)
		);

		// None of the unique fields are set, no rule should be added
		$data = array('float1' => 123.456, 'float2' => 123.456);
		$this->_validateData($data);

		$this->assertSame(array(), $this->_model->validator()->getField());

		// All of the unique fields are set, the rule should be added only to the first field of each group
		$data = array('integer1' => 1, 'integer2' => 2, 'string1' => 'foo',
			'string2' => 'bar', 'boolean1' => true, 'boolean2' => false,
			'float1' => 123.456, 'float2' => 123.456);
		$this->_validateData($data);

		$this->assertInstanceOf('CakeValidationRule',
			$this->_model->validator()->getField('integer1')->getRule('multiColumnUniqueness-group_1'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->_model->validator()->getField('string2')->getRule('multiColumnUniqueness-group_2'));
		$this->assertNull($this->_model->validator()->getField('integer2'));
		$this->assertNull($this->_model->validator()->getField('string1'));
		$this->assertNull($this->_model->validator()->getField('boolean1'));
		$this->assertNull($this->_model->validator()->getField('boolean2'));
		$this->assertNull($this->_model->validator()->getField('datetime1'));
		$this->assertNull($this->_model->validator()->getField('datetime2'));
		$this->assertNull($this->_model->validator()->getField('float1'));
		$this->assertNull($this->_model->validator()->getField('float2'));

		// The second and third fields of each group are set,
		// the rule should be added only to the second field of each group
		// and should be removed from all other fields
		$data = array('integer2' => 2, 'string1' => 'foo',
			'boolean1' => true, 'boolean2' => false,
			'float1' => 123.456, 'float2' => 123.456);
		$this->_validateData($data);

		$this->assertInstanceOf('CakeValidationRule',
			$this->_model->validator()->getField('integer2')->getRule('multiColumnUniqueness-group_1'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->_model->validator()->getField('boolean1')->getRule('multiColumnUniqueness-group_2'));
		$this->assertNull($this->_model->validator()->getField('integer1')->getRule('multiColumnUniqueness-group_1'));
		$this->assertNull($this->_model->validator()->getField('string2')->getRule('multiColumnUniqueness-group_2'));
		$this->assertNull($this->_model->validator()->getField('string1'));
		$this->assertNull($this->_model->validator()->getField('boolean2'));
		$this->assertNull($this->_model->validator()->getField('datetime1'));
		$this->assertNull($this->_model->validator()->getField('datetime2'));
		$this->assertNull($this->_model->validator()->getField('float1'));
		$this->assertNull($this->_model->validator()->getField('float2'));

		// The third field of each group is set,
		// the rule should be added only to the third field of each group
		// and should be removed from all other fields
		$data = array('string1' => 'foo',
			'boolean2' => false,
			'float1' => 123.456, 'float2' => 123.456);
		$this->_validateData($data);

		$this->assertInstanceOf('CakeValidationRule',
			$this->_model->validator()->getField('string1')->getRule('multiColumnUniqueness-group_1'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->_model->validator()->getField('boolean2')->getRule('multiColumnUniqueness-group_2'));
		$this->assertNull($this->_model->validator()->getField('integer1')->getRule('multiColumnUniqueness-group_1'));
		$this->assertNull($this->_model->validator()->getField('integer2')->getRule('multiColumnUniqueness-group_1'));
		$this->assertNull($this->_model->validator()->getField('string2')->getRule('multiColumnUniqueness-group_2'));
		$this->assertNull($this->_model->validator()->getField('boolean1')->getRule('multiColumnUniqueness-group_2'));
		$this->assertNull($this->_model->validator()->getField('datetime1'));
		$this->assertNull($this->_model->validator()->getField('datetime2'));
		$this->assertNull($this->_model->validator()->getField('float1'));
		$this->assertNull($this->_model->validator()->getField('float2'));
	}

/**
 * Tests when the onlyOnce option is set to false
 *
 * @return void
 * @covers ::beforeValidate
 */
	public function testOnlyOnceFalse() {
		$this->_loadBehavior(
			array(
				'fields' => array(
					array('integer1', 'integer2', 'string1'),
					array('string2', 'boolean1', 'boolean2')
				),
				'onlyOnce' => false
			)
		);

		// None of the unique fields are set, no rule should be added
		$data = array('float1' => 123.456, 'float2' => 123.456);
		$this->_validateData($data);

		$this->assertSame(array(), $this->_model->validator()->getField());

		// All of the unique fields are set, the rule should be added only to the first field of each group
		$data = array('integer1' => 1, 'integer2' => 2, 'string1' => 'foo',
			'string2' => 'bar', 'boolean1' => true, 'boolean2' => false,
			'float1' => 123.456, 'float2' => 123.456);
		$this->_validateData($data);

		$this->assertInstanceOf('CakeValidationRule',
			$this->_model->validator()->getField('integer1')->getRule('multiColumnUniqueness-group_1'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->_model->validator()->getField('integer2')->getRule('multiColumnUniqueness-group_1'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->_model->validator()->getField('string1')->getRule('multiColumnUniqueness-group_1'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->_model->validator()->getField('string2')->getRule('multiColumnUniqueness-group_2'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->_model->validator()->getField('boolean1')->getRule('multiColumnUniqueness-group_2'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->_model->validator()->getField('boolean2')->getRule('multiColumnUniqueness-group_2'));
		$this->assertNull($this->_model->validator()->getField('datetime1'));
		$this->assertNull($this->_model->validator()->getField('datetime2'));
		$this->assertNull($this->_model->validator()->getField('float1'));
		$this->assertNull($this->_model->validator()->getField('float2'));

		// The second and third fields of each group are set,
		// the rule should be added all fields of each group
		$data = array('integer2' => 2, 'string1' => 'foo',
			'boolean1' => true, 'boolean2' => false,
			'float1' => 123.456, 'float2' => 123.456);
		$this->_validateData($data);

		$this->assertNull($this->_model->validator()->getField('integer1')->getRule('multiColumnUniqueness-group_1'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->_model->validator()->getField('integer2')->getRule('multiColumnUniqueness-group_1'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->_model->validator()->getField('string1')->getRule('multiColumnUniqueness-group_1'));
		$this->assertNull($this->_model->validator()->getField('string2')->getRule('multiColumnUniqueness-group_2'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->_model->validator()->getField('boolean1')->getRule('multiColumnUniqueness-group_2'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->_model->validator()->getField('boolean2')->getRule('multiColumnUniqueness-group_2'));
		$this->assertNull($this->_model->validator()->getField('datetime1'));
		$this->assertNull($this->_model->validator()->getField('datetime2'));
		$this->assertNull($this->_model->validator()->getField('float1'));
		$this->assertNull($this->_model->validator()->getField('float2'));

		// The third field of each group is set,
		// the rule should be added all fields of each group
		$data = array('string1' => 'foo',
			'boolean2' => false,
			'float1' => 123.456, 'float2' => 123.456);
		$this->_validateData($data);

		$this->assertNull($this->_model->validator()->getField('integer1')->getRule('multiColumnUniqueness-group_1'));
		$this->assertNull($this->_model->validator()->getField('integer1')->getRule('multiColumnUniqueness-group_1'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->_model->validator()->getField('string1')->getRule('multiColumnUniqueness-group_1'));
		$this->assertNull($this->_model->validator()->getField('string2')->getRule('multiColumnUniqueness-group_2'));
		$this->assertNull($this->_model->validator()->getField('boolean1')->getRule('multiColumnUniqueness-group_2'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->_model->validator()->getField('boolean2')->getRule('multiColumnUniqueness-group_2'));
		$this->assertNull($this->_model->validator()->getField('datetime1'));
		$this->assertNull($this->_model->validator()->getField('datetime2'));
		$this->assertNull($this->_model->validator()->getField('float1'));
		$this->assertNull($this->_model->validator()->getField('float2'));
	}

/**
 * Tests with no fields
 *
 * Should never generate an error.
 *
 * @return void
 * @covers ::multiColumnUniqueness
 */
	public function testNoFields() {
		$this->_loadBehavior();

		// Test with unique data
		$data = $this->_uniqueData;
		$expected = array();
		$this->_assertValidate($data, $expected);

		// Test with non-unique data
		$data = $this->_nonUniqueData;
		$expected = array();
		$this->_assertValidate($data, $expected);
	}

/**
 * Tests with a group containing only one field
 *
 * Should work just fine, even though for just one field.
 *
 * @return void
 * @covers ::multiColumnUniqueness
 */
	public function testOneField() {
		$this->_loadBehavior(
			array(
				'fields' => array(
					array('integer1')
				)
			)
		);

		// Test with unique data
		$data = $this->_uniqueData;
		$expected = array();
		$this->_assertValidate($data, $expected);

		// Test with non-unique data
		$data = $this->_nonUniqueData;
		$expected = array(
			'integer1' => array(
				0 => 'The fields integer1 must be unique.'
			)
		);
		$this->_assertValidate($data, $expected);
	}

/**
 * Tests with a group of integer fields
 *
 * @return void
 * @covers ::multiColumnUniqueness
 */
	public function testIntegerFields() {
		$this->_loadBehavior(
			array(
				'fields' => array(
					array('integer1', 'integer2')
				)
			)
		);

		// Test with unique data
		$data = $this->_uniqueData;
		$expected = array();
		$this->_assertValidate($data, $expected);

		// Test with non-unique data
		$data = $this->_nonUniqueData;
		$expected = array(
			'integer1' => array(
				0 => 'The fields integer1 and integer2 must be unique.'
			)
		);
		$this->_assertValidate($data, $expected);

		// Test with partially unique data containing empty or zero value
		$data = array('integer1' => 3, 'integer2' => 0);
		$expected = array();
		$this->_assertValidate($data, $expected);

		// Test with partially unique data containing NULL
		$data = array('integer1' => 8, 'integer2' => null);
		$expected = array();
		$this->_assertValidate($data, $expected);
	}

/**
 * Tests with a group of string fields
 *
 * @return void
 * @covers ::multiColumnUniqueness
 */
	public function testStringFields() {
		$this->_loadBehavior(
			array(
				'fields' => array(
					array('string1', 'string2')
				)
			)
		);

		// Test with unique data
		$data = $this->_uniqueData;
		$expected = array();
		$this->_assertValidate($data, $expected);

		// Test with non-unique data
		$data = $this->_nonUniqueData;
		$expected = array(
			'string1' => array(
				0 => 'The fields string1 and string2 must be unique.'
			)
		);
		$this->_assertValidate($data, $expected);

		// Test with partially unique data containing empty or zero value
		$data = array('string1' => 'three', 'string2' => 0);
		$expected = array();
		$this->_assertValidate($data, $expected);

		// Test with partially unique data containing NULL
		$data = array('string1' => 'eight', 'string2' => null);
		$expected = array();
		$this->_assertValidate($data, $expected);
	}

/**
 * Tests with a group of boolean fields
 *
 * @return void
 * @covers ::multiColumnUniqueness
 */
	public function testBooleanFields() {
		$this->_loadBehavior(
			array(
				'fields' => array(
					array('boolean1', 'boolean2')
				)
			)
		);

		// Test with unique data
		$data = $this->_uniqueData;
		$expected = array();
		$this->_assertValidate($data, $expected);

		// Test with non-unique data
		$data = $this->_nonUniqueData;
		$expected = array(
			'boolean1' => array(
				0 => 'The fields boolean1 and boolean2 must be unique.'
			)
		);
		$this->_assertValidate($data, $expected);

		// Test with partially unique data containing empty or zero value
		// We can't because 0 = false, we already covered all cases in the fixtures

		// Test with partially unique data containing NULL
		$data = array('boolean1' => false, 'boolean2' => null);
		$expected = array();
		$this->_assertValidate($data, $expected);
	}

/**
 * Tests with a group of datetime fields
 *
 * @return void
 * @covers ::multiColumnUniqueness
 */
	public function testDatetimeFields() {
		$this->_loadBehavior(
			array(
				'fields' => array(
					array('datetime1', 'datetime2')
				)
			)
		);

		// Test with unique data
		$data = $this->_uniqueData;
		$expected = array();
		$this->_assertValidate($data, $expected);

		// Test with non-unique data
		$data = $this->_nonUniqueData;
		$expected = array(
			'datetime1' => array(
				0 => 'The fields datetime1 and datetime2 must be unique.'
			)
		);
		$this->_assertValidate($data, $expected);

		// Test with partially unique data containing empty or zero value
		$data = array('datetime1' => '2013-01-03 12:13:14', 'datetime2' => 0);
		$expected = array();
		$this->_assertValidate($data, $expected);

		// Test with partially unique data containing NULL
		$data = array('datetime1' => '2013-01-08 12:13:14', 'datetime2' => null);
		$expected = array();
		$this->_assertValidate($data, $expected);
	}

/**
 * Tests with a group of float fields
 *
 * @return void
 * @covers ::multiColumnUniqueness
 */
	public function testFloatFields() {
		$this->_loadBehavior(
			array(
				'fields' => array(
					array('float1', 'float2')
				)
			)
		);

		// Test with unique data
		$data = $this->_uniqueData;
		$expected = array();
		$this->_assertValidate($data, $expected);

		// Test with non-unique data
		$data = $this->_nonUniqueData;
		$expected = array(
			'float1' => array(
				0 => 'The fields float1 and float2 must be unique.'
			)
		);
		$this->_assertValidate($data, $expected);

		// Test with partially unique data containing empty or zero value
		$data = array('float1' => 3.0, 'float2' => 0);
		$expected = array();
		$this->_assertValidate($data, $expected);

		// Test with partially unique data containing NULL
		$data = array('float1' => 8.0, 'float2' => null);
		$expected = array();
		$this->_assertValidate($data, $expected);
	}

/**
 * Tests with a group of mixed fields
 *
 * @return void
 * @covers ::multiColumnUniqueness
 */
	public function testMixedFields() {
		$this->_loadBehavior(
			array(
				'fields' => array(
					array('integer1', 'integer2', 'string1', 'string2', 'boolean1', 'boolean2', 'datetime1', 'datetime2', 'float1', 'float2')
				)
			)
		);

		// Test with unique data
		$data = $this->_uniqueData;
		$expected = array();
		$this->_assertValidate($data, $expected);

		// Test with non-unique data
		$data = $this->_nonUniqueData;
		$expected = array(
			'integer1' => array(
				0 => 'The fields integer1, integer2, string1, string2, boolean1, boolean2, datetime1, datetime2, float1 and float2 must be unique.'
			)
		);
		$this->_assertValidate($data, $expected);

		// Test with partially unique data containing empty or zero value
		$data = array_merge($this->_uniqueData, array('float2' => 0));
		$expected = array();
		$this->_assertValidate($data, $expected);

		// Test with partially unique data containing NULL
		$data = array_merge($this->_uniqueData, array('float2' => null));
		$expected = array();
		$this->_assertValidate($data, $expected);
	}

/**
 * Tests when not all fields are set when adding a record
 *
 * Should treat the missing field(s) as null
 *
 * @return void
 * @covers ::multiColumnUniqueness
 */
	public function testNotAllFieldsSetOnAdd() {
		$this->_loadBehavior(
			array(
				'fields' => array(
					array('integer1', 'string1', 'boolean1', 'datetime2', 'float1')
				)
			)
		);

		// Try to save a new record resembling record 5, except the field integer1 is missing
		// Since the missing field contains a non-null value, it validates
		$data = array('integer2' => 60,
			'string1' => 'six', 'string2' => 'sixty',
			'boolean1' => true, 'boolean2' => true,
			'datetime1' => '2013-01-06 12:13:14', 'datetime2' => '2013-06-01 12:13:14',
			'float1' => 6.0, 'float2' => 66.0);
		$expected = array();
		$this->_assertValidate($data, $expected);

		// Try to save a new record resembling record 3, except the field integer1 is missing
		// Since the missing field contains a null value, it doesn't validate
		$data = array('integer2' => 40,
			'string1' => null, 'string2' => 'forty',
			'boolean1' => null, 'boolean2' => false,
			'datetime1' => null, 'datetime2' => '2013-04-01 12:13:14',
			'float1' => null, 'float2' => 44.0);
		$expected = array(
			'string1' => array(
				0 => 'The fields integer1, string1, boolean1, datetime2 and float1 must be unique.',
			)
		);
		$this->_assertValidate($data, $expected);

		// Same as above but with the ID field set within the array
		// It should still fail to validate, because the ID property of the model isn't set
		$data = array('id' => 3, 'integer2' => 40,
			'string1' => null, 'string2' => 'forty',
			'boolean1' => null, 'boolean2' => false,
			'datetime1' => null, 'datetime2' => '2013-04-01 12:13:14',
			'float1' => null, 'float2' => 44.0);
		$expected = array(
			'string1' => array(
				0 => 'The fields integer1, string1, boolean1, datetime2 and float1 must be unique.',
			)
		);
		$this->_assertValidate($data, $expected);
	}

/**
 * Tests when not all fields are set when editing a record
 *
 * Should try tro fetch the value of the missing field(s)
 *
 * @return void
 * @covers ::multiColumnUniqueness
 */
	public function testNotAllFieldsSetOnEdit() {
		$this->_loadBehavior(
			array(
				'fields' => array(
					array('integer1', 'string1', 'boolean1', 'datetime2', 'float1')
				)
			)
		);

		// Try to save record 5 with changed data, except the field integer1 is missing
		// Since we just update the record with some new, unique data, it should validate
		$data = array('integer2' => 600,
			'string1' => 'sixty', 'string2' => 'six hundred',
			'boolean1' => true, 'boolean2' => true,
			'datetime1' => '2013-01-06 12:13:14', 'datetime2' => '2013-06-01 12:13:14',
			'float1' => 60.0, 'float2' => 660.0);
		$expected = array();
		$this->_assertValidate($data, $expected, 5);

		// Try to save record 5 but with the values of record 2, except the field integer1 is missing
		// Since we load the value of the integer1 field from record 5, it should validate
		$data = array('integer2' => null,
			'string1' => 'three', 'string2' => null,
			'boolean1' => true, 'boolean2' => null,
			'datetime1' => '2013-01-03 12:13:14', 'datetime2' => null,
			'float1' => 3.0, 'float2' => null);
		$expected = array();
		$this->_assertValidate($data, $expected, 5);

		// Try to save a non-existent record with the values of record 3,
		// except the field integer1 is missing and the ID is set wrong
		// Since the value of the missing field can't be load and
		// the value of the missing field in record 3 is a null it will fail to validate
		$data = array('integer2' => 40,
			'string1' => null, 'string2' => 'forty',
			'boolean1' => null, 'boolean2' => false,
			'datetime1' => null, 'datetime2' => '2013-04-01 12:13:14',
			'float1' => null, 'float2' => 44.0);
		$expected = array(
			'string1' => array(
				0 => 'The fields integer1, string1, boolean1, datetime2 and float1 must be unique.',
			)
		);
		$this->_assertValidate($data, $expected, 123456789);
	}

/**
 * Tests with two field groups.
 *
 * @return void
 * @covers ::multiColumnUniqueness
 */
	public function testTwoGroups() {
		$this->_loadBehavior(
			array(
				'fields' => array(
					array('integer1', 'string1', 'boolean1', 'datetime1', 'float1'),
					array('integer2', 'string2', 'boolean2', 'datetime2', 'float2')
				)
			)
		);

		// Test with unique data
		$data = $this->_uniqueData;
		$expected = array();
		$this->_assertValidate($data, $expected);

		// Test with non-unique data
		$data = $this->_nonUniqueData;
		$expected = array(
			'integer1' => array(
				0 => 'The fields integer1, string1, boolean1, datetime1 and float1 must be unique.',
			),
			'integer2' => array(
				0 => 'The fields integer2, string2, boolean2, datetime2 and float2 must be unique.',
			)
		);
		$this->_assertValidate($data, $expected);

		// Test with non-unique data in the first group
		$data = array_merge($this->_nonUniqueData, array(
			'integer2' => 90,
			'string2' => 'ninety',
			'boolean2' => true,
			'datetime2' => '2013-09-01 12:13:14',
			'float2' => 9.0
		));
		$expected = array(
			'integer1' => array(
				0 => 'The fields integer1, string1, boolean1, datetime1 and float1 must be unique.',
			)
		);
		$this->_assertValidate($data, $expected);

		// Test with non-unique data in the second group
		$data = array_merge($this->_nonUniqueData, array(
			'integer1' => 9,
			'string1' => 'nine',
			'boolean1' => null,
			'datetime1' => '2013-01-09 12:13:14',
			'float1' => 9.0
		));
		$expected = array(
			'integer2' => array(
				0 => 'The fields integer2, string2, boolean2, datetime2 and float2 must be unique.',
			)
		);
		$this->_assertValidate($data, $expected);
	}

/**
 * Tests with two field groups with overlapping fields.
 *
 * @return void
 * @covers ::multiColumnUniqueness
 */
	public function testTwoGroupsOverlappingFields() {
		$this->_loadBehavior(
			array(
				'fields' => array(
					array('string1', 'integer1'),
					array('string2', 'integer1')
				)
			)
		);

		// Test with unique data
		$data = $this->_uniqueData;
		$expected = array();
		$this->_assertValidate($data, $expected);

		// Test with non-unique data
		$data = $this->_nonUniqueData;
		$expected = array(
			'string1' => array(
				0 => 'The fields string1 and integer1 must be unique.',
			),
			'string2' => array(
				0 => 'The fields string2 and integer1 must be unique.',
			)
		);
		$this->_assertValidate($data, $expected);

		// Test with non-unique data in the first group
		$data = array_merge($this->_uniqueData, array(
			'integer1' => 6,
			'string1' => 'six',
		));
		$expected = array(
			'string1' => array(
				0 => 'The fields string1 and integer1 must be unique.',
			)
		);
		$this->_assertValidate($data, $expected);

		// Test with non-unique data in the second group
		$data = array_merge($this->_uniqueData, array(
			'integer1' => 6,
			'string2' => 'sixty',
		));
		$expected = array(
			'string2' => array(
				0 => 'The fields string2 and integer1 must be unique.',
			)
		);
		$this->_assertValidate($data, $expected);
	}

/**
 * Tests with two field groups with overlapping fields and same first field
 *
 * @return void
 * @covers ::multiColumnUniqueness
 */
	public function testTwoGroupsOverlappingFieldsSameFirstField() {
		$this->_loadBehavior(
			array(
				'fields' => array(
					array('integer1', 'string1'),
					array('integer1', 'string2')
				)
			)
		);

		// Test with unique data
		$data = $this->_uniqueData;
		$expected = array();
		$this->_assertValidate($data, $expected);

		// Test with non-unique data
		$data = $this->_nonUniqueData;
		$expected = array(
			'integer1' => array(
				0 => 'The fields integer1 and string1 must be unique.',
				// Only the first, as it stops because of 'last' = true
			),
		);
		$this->_assertValidate($data, $expected);

		// Test with non-unique data in the first group
		$data = array_merge($this->_uniqueData, array(
			'integer1' => 6,
			'string1' => 'six',
		));
		$expected = array(
			'integer1' => array(
				0 => 'The fields integer1 and string1 must be unique.',
			)
		);
		$this->_assertValidate($data, $expected);

		// Test with non-unique data in the second group
		$data = array_merge($this->_uniqueData, array(
			'integer1' => 6,
			'string2' => 'sixty',
		));
		$expected = array(
			'integer1' => array(
				0 => 'The fields integer1 and string2 must be unique.',
			)
		);
		$this->_assertValidate($data, $expected);
	}

/**
 * Loads the behavior with the given config
 *
 * Specifically handles the case of setting no config at all.
 *
 * @param null|array $config Optional Behavior config to set.
 * @return void
 */
	protected function _loadBehavior($config = null) {
		if ($config === null) {
			$this->_model->Behaviors->load('MultiColumnUniqueness.MultiColumnUniqueness');
		} else {
			$this->_model->Behaviors->load('MultiColumnUniqueness.MultiColumnUniqueness', $config);
		}
	}

/**
 * Creates a new record from given data and validates it.
 *
 * Does the repetitive work for the tests above.
 *
 * @param array $data The data array to validate.
 * @return array The invalid fields.
 */
	protected function _validateData($data) {
		$this->_model->create($data);
		return $this->_model->invalidFields();
	}

/**
 * Assert the validated data.
 *
 * Does the repetitive work for the tests above.
 *
 * @param array $data The data array to validate.
 * @param array $expected The expected validation error array.
 * @param int|bool $id The ID of the record (optional).
 * @return void
 */
	protected function _assertValidate($data, $expected, $id = false) {
		$this->_model->create($data);
		$this->_model->id = $id;
		$this->assertSame($expected, $this->_model->invalidFields());
	}

}
