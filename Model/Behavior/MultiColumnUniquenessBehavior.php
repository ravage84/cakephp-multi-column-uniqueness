<?php
/**
 * PHP 5
 *
 * @copyright Marc Würth @ ORCA Services AG
 * @author Marc Würth
 * @version 0.1
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/

App::uses('ModelBehavior', 'Model');

/**
 * MultiColumnUniqueness Behavior
 *
 * TODO Write long description
 *
 * @link http://book.cakephp.org/2.0/en/models/saving-your-data.html#using-created-and-modified
 */
class MultiColumnUniquenessBehavior extends ModelBehavior {

/**
 * Default settings for a model that has this behavior attached
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/models/saving-your-data.html#using-created-and-modified
 */
	protected $_defaults = array(
		'fields' => array(), // No fields must be unique
		'onlyOnce' => true, // Add the validation rule to the first column found in $this->data only
		'errMsg' => array() // No field group, no message
	);

/**
 * Setup the behavior
 *
 * Checks if the configuration settings are set in the model,
 * merges them with the the defaults.
 * If the fields array is one dimensional,
 * it moves it to the second dimension.
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
			unset($this->settings[$model->alias]['fields']);
			$this->settings[$model->alias]['fields'] = array($fieldGroup);
		}
		if (!empty($this->settings[$model->alias]['errMsg']) &&
			!is_array($this->settings[$model->alias]['errMsg'])) {
			$errMsg = $this->settings[$model->alias]['errMsg'];
			unset($this->settings[$model->alias]['errMsg']);
			$this->settings[$model->alias]['errMsg'] = array($errMsg);
		}

		for ($groupNr = 0;$groupNr < count($this->settings[$model->alias]['fields']); $groupNr++) {
			if (!isset($this->settings[$model->alias]['errMsg'][$groupNr])) {
				$fieldNames = String::toList($this->settings[$model->alias]['fields'][$groupNr]);
				$this->settings[$model->alias]['errMsg'][$groupNr] = sprintf('The fields %s must be unique.', $fieldNames);
			}
		}
		FireCake::log($this->settings);
	}

/**
 * Add the multiColumnUnique data validation rule dynamically
 *
 * Loops through the field groups and its fields which need to be unique.
 * First removes the 'multiColumnUniqueness' rule from all unique fields and then
 * adds the rule to the first of the related fields found in the data array only.
 *
 * @param Model $Model Model using this behavior
 * @param array $options Unused
 * @return bool
 * @todo Implement the 'onlyOnce' option
 */
	public function beforeValidate(Model $Model, $options = array()) {
		for ($groupNr = 0;$groupNr < count($this->settings[$Model->alias]['fields']); $groupNr++) {
			$uniqueFieldGrp = $this->settings[$Model->alias]['fields'][$groupNr];
			foreach ($uniqueFieldGrp as $uniqueField) {
				$Model->validator()->remove($uniqueField, 'multiColumnUniqueness');
			}
			foreach ($uniqueFieldGrp as $uniqueField) {
				if (isset($Model->data[$Model->name]) && array_key_exists($uniqueField, $Model->data[$Model->name])) {
					$Model->validator()->add($uniqueField, 'multiColumnUniqueness', array(
						'rule' => array('multiColumnUniqueness', $uniqueFieldGrp),
						'message' => $this->settings[$Model->alias]['errMsg'][$groupNr],
					));
					if ($this->settings[$Model->alias]['onlyOnce']) {
						break;
					}
				}
			}
		}

		return parent::beforeValidate($Model, $options);
	}

/**
 * Checks the uniqueness of multiple fields
 *
 * If a relevant field is not present in the data array
 * and the validation is for an existing record, the field will be loaded.
 * Otherwise it's considered to be empty (e.g. when adding).
 *
 * @param Model $Model Model using this behavior
 * @param $data array Unused
 * @param $fields array The fields to be checked
 * @return bool True if valid, else false
 */
	function multiColumnUniqueness(Model $Model, $data, $fields) {
		if (!is_array($fields)) {
			$fields = array($fields);
		}
		$check = array();
		foreach($fields as $key) {
			if (isset($Model->data[$Model->name][$key])) {
				$value = $Model->data[$Model->name][$key];
			} elseif (!empty($Model->id)) {
				$value = $Model->field($key, array($Model->primaryKey => $Model->data[$Model->name][$Model->primaryKey]));
			} else {
				$value = '';
			}
			$check[$key] = $value;
		}
		return $Model->isUnique($check, false);
	}

}
