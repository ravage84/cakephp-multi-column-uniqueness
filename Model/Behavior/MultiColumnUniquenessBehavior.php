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
 * @link https://github.com/
 */

App::uses('ModelBehavior', 'Model');

/**
 * MultiColumnUniqueness Behavior
 *
 * Uses the CakePHP built-in method Model::isUnique() to check multiple columns for their uniqueness.<br>
 * Can be configured in many ways.
 *
 * **One field group set, without error message set (most simple and shortest way):**
 *
 * The validation rule will be executed for field1 only.<br>
 * The missing error message will be generated automatically.
 *
 * <code>
 * public $actsAs = array('MultiColumnUniqueness.MultiColumnUniqueness' => array(
 * 	'fields' => array('field1', 'field2')
 * ));
 * </code>
 *
 * **One field group set, without error message set (array in array):**
 *
 * Same as above.
 *
 * <code>
 * public $actsAs = array('MultiColumnUniqueness.MultiColumnUniqueness' => array(
 * 	'fields' => array(array('field1', 'field2'))
 * ));
 * </code>
 *
 * **One field group and its error message set:**
 *
 * The validation rule will be executed for field1 only.
 *
 * <code>
 * public $actsAs = array('MultiColumnUniqueness.MultiColumnUniqueness' => array(
 * 	'fields' => array('field1', 'field2'),
 * 	'errMsg' => 'Field1 and field2 must be unique.',
 * ));
 * </code>
 *
 * **Two field groups and their corresponding error message set:**
 *
 * The validation rule for the first field group will be executed for field1 only.<br>
 * The validation rule for the second field group will be executed for field3 only.
 *
 * <code>
 * public $actsAs = array('MultiColumnUniqueness.MultiColumnUniqueness' => array(
 * 	'fields' => array(array('field1', 'field2'), array('field3', 'field4')),
 * 	'errMsg' => array('Field1 and field2 must be unique.', 'Field3 and field4 must be unique.'),
 * ));
 * </code>
 *
 * **Two field groups set but only the error message of the first group set:**
 *
 * The validation rule for the first field group will be executed for field1 only.<br>
 * The validation rule for the second field group will be executed for field3 only.<br>
 * The missing error message will be generated automatically.
 *
 * <code>
 * public $actsAs = array('MultiColumnUniqueness.MultiColumnUniqueness' => array(
 * 	'fields' => array(array('field1', 'field2'), array('field3', 'field4')),
 * 	'errMsg' => array('Field1 and field2 must be unique.', 'Field3 and field4 must be unique.'),
 * ));
 * </code>
 *
 * **Option onlyOnce disabled:**
 *
 * The validation rule will be executed for all fields of **each** group!
 *
 * <code>
 * public $actsAs = array('MultiColumnUniqueness.MultiColumnUniqueness' => array(
 * 	'fields' => array(array('field1', 'field2'), array('field3', 'field4')),
 * 	'errMsg' => array('Field1 and field2 must be unique.', 'Field3 and field4 must be unique.'),
 * 	'onlyOnce' => false,
 * ));
 * </code>
 *
 * @link http://book.cakephp.org/2.0/en/models/data-validation.html#Model::Validation::isUnique
 * @see Model::isUnique() Makes use of
 */
class MultiColumnUniquenessBehavior extends ModelBehavior {

/**
 * Default settings for a model that has this behavior attached
 *
 * No fields must be unique<br>
 * No field group, no message<br>
 * Add the validation rule to the first column found in $this->data only
 *
 * @var array
 */
	protected $_defaults = array(
		'fields' => array(),
		'onlyOnce' => true,
		'errMsg' => array()
	);

/**
 * Setup the behavior
 *
 * Checks if the configuration settings are set in the model,
 * merges them with the the defaults.
 * If the fields array is one dimensional,
 * it moves it to the second dimension.
 * If the errMsg array is one dimensional,
 * it moves it to the second dimension.
 * Adds missing error messages for each field group.
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		if (!isset($this->settings[$model->alias])) {
			$this->settings[$model->alias] = $this->_defaults;
		}
		$this->settings[$model->alias] = array_merge(
			$this->settings[$model->alias], (array)$config);

		if (!empty($this->settings[$model->alias]['fields'][0]) &&
			!is_array($this->settings[$model->alias]['fields'][0])) {
			$fieldGroup = $this->settings[$model->alias]['fields'];
			$this->settings[$model->alias]['fields'] = array($fieldGroup);
		}
		if (!empty($this->settings[$model->alias]['errMsg']) &&
			!is_array($this->settings[$model->alias]['errMsg'])) {
			$errMsg = $this->settings[$model->alias]['errMsg'];
			$this->settings[$model->alias]['errMsg'] = array($errMsg);
		}

		$fieldGroupCount = count($this->settings[$model->alias]['fields']);
		for ($groupNr = 0;$groupNr < $fieldGroupCount; $groupNr++) {
			if (!isset($this->settings[$model->alias]['errMsg'][$groupNr])) {
				$fieldNames = String::toList($this->settings[$model->alias]['fields'][$groupNr]);
				$this->settings[$model->alias]['errMsg'][$groupNr] = sprintf('The fields %s must be unique.', $fieldNames);
			}
		}
	}

/**
 * Adds the multiColumnUnique data validation rule dynamically
 *
 * Loops through the field groups and their fields which need to be unique.
 * First removes the multiColumnUniqueness rule from each unique field.
 * If the 'onlyOnce' option is set to false,
 * it adds the rule to each field of the field group.
 * Otherwise, by default, it adds the rule only to the first of the
 * relevant fields found in the data array.
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save() (unused).
 * @return bool True if validate operation should continue, false to abort
 */
	public function beforeValidate(Model $model, $options = array()) {
		$fieldGroupCount = count($this->settings[$model->alias]['fields']);
		for ($groupNr = 0; $groupNr < $fieldGroupCount; $groupNr++) {
			$uniqueFieldGrp = $this->settings[$model->alias]['fields'][$groupNr];
			$fieldGroupName = 'multiColumnUniqueness-group_' . ($groupNr + 1);
			foreach ($uniqueFieldGrp as $uniqueField) {
				if ($model->validator()->getField($uniqueField)) {
					$model->validator()->remove($uniqueField, $fieldGroupName);
				}
			}
			foreach ($uniqueFieldGrp as $uniqueField) {
				if (isset($model->data[$model->name]) && array_key_exists($uniqueField, $model->data[$model->name])) {
					$model->validator()->add($uniqueField, $fieldGroupName, array(
						'rule' => array('multiColumnUniqueness', $uniqueFieldGrp),
						'message' => $this->settings[$model->alias]['errMsg'][$groupNr],
					));
					if ($this->settings[$model->alias]['onlyOnce']) {
						break;
					}
				}
			}
		}

		return parent::beforeValidate($model, $options);
	}

/**
 * Checks the uniqueness of multiple fields
 *
 * Loops through the field that should be unique.
 * If a field is not present in the data array and
 * the validation is for an existing record,
 * (if an ID is present) it is tried to load the value of the field.
 * Otherwise it's considered to be empty (e.g. when adding).
 *
 * @param Model $model Model using this behavior
 * @param array $data Unused
 * @param array $fields The fields to be checked
 * @return bool True if valid, else false
 * @see Model::isUnique() Makes use of
 */
	public function multiColumnUniqueness(Model $model, $data, $fields) {
		if (!is_array($fields)) {
			$fields = array($fields);
		}

		$check = array();
		foreach ($fields as $key) {
			if (isset($model->data[$model->name][$key])) {
				$value = $model->data[$model->name][$key];
			} elseif (!empty($model->id)) {
				$value = $model->field($key, array($model->primaryKey => $model->id));
				if ($value === false) {
					$value = null;
				}
			} else {
				$value = null;
			}
			$check[$key] = $value;
		}

		return $model->isUnique($check, false);
	}

}
