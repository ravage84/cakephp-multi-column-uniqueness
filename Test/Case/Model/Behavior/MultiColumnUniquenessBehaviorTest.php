<?php

App::uses('MultiColumnUniquenessBehavior', 'MultiColumnUniqueness.Model/Behavior');

/**
 * Class MultiColumnUniquenessBehaviorTest
 * @property AppModel $Model
 */
class MultiColumnUniquenessBehaviorTest extends CakeTestCase {

/**
 * Model for tests
 *
 * @var
 */
	public $Model;

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('plugin.multi_column_uniqueness.multi_column_unique_model');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Model = ClassRegistry::init('MultiColumnUniqueModel');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Model);

		parent::tearDown();
	}

/**
 * Tests the default config (no config given)
 */
	public function testDefaultConfig() {
		$this->Model->Behaviors->load('MultiColumnUniqueness.MultiColumnUniqueness');

		$settings = $this->Model->Behaviors->MultiColumnUniqueness->settings['MultiColumnUniqueModel'];
		$expected = array(
			'fields' => array(),
			'onlyOnce' => true,
			'errMsg' => array()
		);
		$this->assertSame($expected, $settings);
	}

/**
 * Tests the config "One field group set, without error message set (most simple and shortest way)"
 */
	public function testOneGroupSimplestNoErrMsg() {
		$this->Model->Behaviors->load('MultiColumnUniqueness.MultiColumnUniqueness',
			array('fields' => array('integer1', 'integer2'))
		);

		$settings = $this->Model->Behaviors->MultiColumnUniqueness->settings['MultiColumnUniqueModel'];
		$expected = array(
			'fields' => array(array('integer1', 'integer2')),
			'onlyOnce' => true,
			'errMsg' => array('The fields integer1 and integer2 must be unique.')
		);
		$this->assertSame($expected, $settings);
	}

/**
 * Tests the config "One field group set, without error message set (array in array)"
 */
	public function testOneGroupNoErrMsg() {
		$this->Model->Behaviors->load('MultiColumnUniqueness.MultiColumnUniqueness',
			array('fields' => array(array('integer1', 'integer2')))
		);

		$settings = $this->Model->Behaviors->MultiColumnUniqueness->settings['MultiColumnUniqueModel'];
		$expected = array(
			'fields' => array(array('integer1', 'integer2')),
			'onlyOnce' => true,
			'errMsg' => array('The fields integer1 and integer2 must be unique.')
		);
		$this->assertSame($expected, $settings);
	}

/**
 * Tests the config "One field group and its error message set"
 */
	public function testOneGroupOneErrMsg() {
		$this->Model->Behaviors->load('MultiColumnUniqueness.MultiColumnUniqueness',
			array(
				'fields' => array(array('integer1', 'integer2')),
				'errMsg' => array('Integer1 and Integer2 must be unique.')
			)
		);

		$settings = $this->Model->Behaviors->MultiColumnUniqueness->settings['MultiColumnUniqueModel'];
		$expected = array(
			'fields' => array(array('integer1', 'integer2')),
			'onlyOnce' => true,
			'errMsg' => array('Integer1 and Integer2 must be unique.')
		);
		$this->assertSame($expected, $settings);
	}

/**
 * Tests the config "Two field groups and their corresponding error message set"
 */
	public function testTwoGroupsAndErrMsgs() {
		$this->Model->Behaviors->load('MultiColumnUniqueness.MultiColumnUniqueness',
			array(
				'fields' => array(array('integer1', 'integer2'), array('string1', 'string2')),
				'errMsg' => array('Integer1 and Integer2 must be unique.', 'String1 and String2 must be unique.')
			)
		);

		$settings = $this->Model->Behaviors->MultiColumnUniqueness->settings['MultiColumnUniqueModel'];
		$expected = array(
			'fields' => array(array('integer1', 'integer2'), array('string1', 'string2')),
			'onlyOnce' => true,
			'errMsg' => array('Integer1 and Integer2 must be unique.', 'String1 and String2 must be unique.')
		);
		$this->assertSame($expected, $settings);
	}

/**
 * Tests the config "Two field groups set but only the error message of the first group set"
 */
	public function testTwoGroupsOneErrMsg() {
		$this->Model->Behaviors->load('MultiColumnUniqueness.MultiColumnUniqueness',
			array(
				'fields' => array(array('integer1', 'integer2'), array('string1', 'string2')),
				'errMsg' => array('Integer1 and Integer2 must be unique.')
			)
		);

		$settings = $this->Model->Behaviors->MultiColumnUniqueness->settings['MultiColumnUniqueModel'];
		$expected = array(
			'fields' => array(array('integer1', 'integer2'), array('string1', 'string2')),
			'onlyOnce' => true,
			'errMsg' => array('Integer1 and Integer2 must be unique.', 'The fields string1 and string2 must be unique.')
		);
		$this->assertSame($expected, $settings);
	}

/**
 * Tests when the onlyOnce option is active
 */
	public function testOnlyOnceTrue() {
		$this->Model->Behaviors->load('MultiColumnUniqueness.MultiColumnUniqueness',
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
		$this->Model->create($data);
		$this->Model->invalidFields();
		$this->assertSame(array(), $this->Model->validator()->getField());

		// All of the unique fields are set, the rule should be added only to the first field of each group
		$data = array('integer1' => 1, 'integer2' => 2, 'string1' => 'foo',
			'string2' => 'bar', 'boolean1' => true, 'boolean2' => false,
			'float1' => 123.456, 'float2' => 123.456);
		$this->Model->create($data);
		$this->Model->invalidFields();
		$this->assertInstanceOf('CakeValidationRule',
			$this->Model->validator()->getField('integer1')->getRule('multiColumnUniqueness'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->Model->validator()->getField('string2')->getRule('multiColumnUniqueness'));
		$this->assertNull($this->Model->validator()->getField('integer2'));
		$this->assertNull($this->Model->validator()->getField('string1'));
		$this->assertNull($this->Model->validator()->getField('boolean1'));
		$this->assertNull($this->Model->validator()->getField('boolean2'));
		$this->assertNull($this->Model->validator()->getField('datetime1'));
		$this->assertNull($this->Model->validator()->getField('datetime2'));
		$this->assertNull($this->Model->validator()->getField('float1'));
		$this->assertNull($this->Model->validator()->getField('float2'));

		// The second and third fields of each group are set,
		// the rule should be added only to the second field of each group
		// and should be removed from all other fields
		$data = array('integer2' => 2, 'string1' => 'foo',
			'boolean1' => true, 'boolean2' => false,
			'float1' => 123.456, 'float2' => 123.456);
		$this->Model->create($data);
		$this->Model->invalidFields();
		$this->assertInstanceOf('CakeValidationRule',
			$this->Model->validator()->getField('integer2')->getRule('multiColumnUniqueness'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->Model->validator()->getField('boolean1')->getRule('multiColumnUniqueness'));
		$this->assertNull($this->Model->validator()->getField('integer1')->getRule('multiColumnUniqueness'));
		$this->assertNull($this->Model->validator()->getField('string2')->getRule('multiColumnUniqueness'));
		$this->assertNull($this->Model->validator()->getField('string1'));
		$this->assertNull($this->Model->validator()->getField('boolean2'));
		$this->assertNull($this->Model->validator()->getField('datetime1'));
		$this->assertNull($this->Model->validator()->getField('datetime2'));
		$this->assertNull($this->Model->validator()->getField('float1'));
		$this->assertNull($this->Model->validator()->getField('float2'));

		// The third field of each group is set,
		// the rule should be added only to the third field of each group
		// and should be removed from all other fields
		$data = array('string1' => 'foo',
			'boolean2' => false,
			'float1' => 123.456, 'float2' => 123.456);
		$this->Model->create($data);
		$this->Model->invalidFields();
		$this->assertInstanceOf('CakeValidationRule',
			$this->Model->validator()->getField('string1')->getRule('multiColumnUniqueness'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->Model->validator()->getField('boolean2')->getRule('multiColumnUniqueness'));
		$this->assertNull($this->Model->validator()->getField('integer1')->getRule('multiColumnUniqueness'));
		$this->assertNull($this->Model->validator()->getField('integer2')->getRule('multiColumnUniqueness'));
		$this->assertNull($this->Model->validator()->getField('string2')->getRule('multiColumnUniqueness'));
		$this->assertNull($this->Model->validator()->getField('boolean1')->getRule('multiColumnUniqueness'));
		$this->assertNull($this->Model->validator()->getField('datetime1'));
		$this->assertNull($this->Model->validator()->getField('datetime2'));
		$this->assertNull($this->Model->validator()->getField('float1'));
		$this->assertNull($this->Model->validator()->getField('float2'));
	}

/**
 * Tests when the onlyOnce option is disabled
 */
	public function testOnlyOnceFalse() {
		$this->Model->Behaviors->load('MultiColumnUniqueness.MultiColumnUniqueness',
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
		$this->Model->create($data);
		$this->Model->invalidFields();
		$this->assertSame(array(), $this->Model->validator()->getField());

		// All of the unique fields are set, the rule should be added only to the first field of each group
		$data = array('integer1' => 1, 'integer2' => 2, 'string1' => 'foo',
			'string2' => 'bar', 'boolean1' => true, 'boolean2' => false,
			'float1' => 123.456, 'float2' => 123.456);
		$this->Model->create($data);
		$this->Model->invalidFields();
		$this->assertInstanceOf('CakeValidationRule',
			$this->Model->validator()->getField('integer1')->getRule('multiColumnUniqueness'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->Model->validator()->getField('integer2')->getRule('multiColumnUniqueness'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->Model->validator()->getField('string1')->getRule('multiColumnUniqueness'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->Model->validator()->getField('string2')->getRule('multiColumnUniqueness'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->Model->validator()->getField('boolean1')->getRule('multiColumnUniqueness'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->Model->validator()->getField('boolean2')->getRule('multiColumnUniqueness'));
		$this->assertNull($this->Model->validator()->getField('datetime1'));
		$this->assertNull($this->Model->validator()->getField('datetime2'));
		$this->assertNull($this->Model->validator()->getField('float1'));
		$this->assertNull($this->Model->validator()->getField('float2'));

		// The second and third fields of each group are set,
		// the rule should be added all fields of each group
		$data = array('integer2' => 2, 'string1' => 'foo',
			'boolean1' => true, 'boolean2' => false,
			'float1' => 123.456, 'float2' => 123.456);
		$this->Model->create($data);
		$this->Model->invalidFields();
		$this->assertNull($this->Model->validator()->getField('integer1')->getRule('multiColumnUniqueness'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->Model->validator()->getField('integer2')->getRule('multiColumnUniqueness'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->Model->validator()->getField('string1')->getRule('multiColumnUniqueness'));
		$this->assertNull($this->Model->validator()->getField('string2')->getRule('multiColumnUniqueness'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->Model->validator()->getField('boolean1')->getRule('multiColumnUniqueness'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->Model->validator()->getField('boolean2')->getRule('multiColumnUniqueness'));
		$this->assertNull($this->Model->validator()->getField('datetime1'));
		$this->assertNull($this->Model->validator()->getField('datetime2'));
		$this->assertNull($this->Model->validator()->getField('float1'));
		$this->assertNull($this->Model->validator()->getField('float2'));

		// The third field of each group is set,
		// the rule should be added all fields of each group
		$data = array('string1' => 'foo',
			'boolean2' => false,
			'float1' => 123.456, 'float2' => 123.456);
		$this->Model->create($data);
		$this->Model->invalidFields();
		$this->assertNull($this->Model->validator()->getField('integer1')->getRule('multiColumnUniqueness'));
		$this->assertNull($this->Model->validator()->getField('integer1')->getRule('multiColumnUniqueness'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->Model->validator()->getField('string1')->getRule('multiColumnUniqueness'));
		$this->assertNull($this->Model->validator()->getField('string2')->getRule('multiColumnUniqueness'));
		$this->assertNull($this->Model->validator()->getField('boolean1')->getRule('multiColumnUniqueness'));
		$this->assertInstanceOf('CakeValidationRule',
			$this->Model->validator()->getField('boolean2')->getRule('multiColumnUniqueness'));
		$this->assertNull($this->Model->validator()->getField('datetime1'));
		$this->assertNull($this->Model->validator()->getField('datetime2'));
		$this->assertNull($this->Model->validator()->getField('float1'));
		$this->assertNull($this->Model->validator()->getField('float2'));
	}

/**
 * Tests no fields
 *
 * @todo Not yet done.
 */
	public function testNoFields() {
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

/**
 * Tests a group of integer fields
 *
 * @todo Not yet done.
 */
	public function testIntegerFields() {
		// Test with unique data
		// Test with non-unique data & check error message(s)
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

/**
 * Tests a group of string fields
 *
 * @todo Not yet done.
 */
	public function testStringFields() {
		// Test with unique data
		// Test with non-unique data & check error message(s)
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

/**
 * Tests a group of boolean fields
 *
 * @todo Not yet done.
 */
	public function testBooleanFields() {
		// Test with unique data
		// Test with non-unique data & check error message(s)
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

/**
 * Tests a group of datetime fields
 *
 * @todo Not yet done.
 */
	public function testDatetimeFields() {
		// Test with unique data
		// Test with non-unique data & check error message(s)
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

/**
 * Tests a group of float fields
 *
 * @todo Not yet done.
 */
	public function testFloatFields() {
		// Test with unique data
		// Test with non-unique data & check error message(s)
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

/**
 * Tests a group of mixed fields
 *
 * @todo Not yet done.
 */
	public function testMixedFields() {
		// Test with unique data
		// Test with non-unique data & check error message(s)
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

/**
 * Tests when not all fields are set when adding a record
 *
 * Should treat the missing field(s) as null
 *
 * @todo Not yet done.
 */
	public function testNotAllFieldsSetOnAdd() {
		// Check against a record with no null values
		// Check against a record with null values
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

/**
 * Tests when not all fields are set when editing a record
 *
 * Should try tro fetch the value of the missing field(s)
 *
 * @todo Not yet done.
 */
	public function testNotAllFieldsSetOnEdit() {
		// Check against a record with no null values
		// Check against a record with null values
		// Check against a record that does not exist
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

}