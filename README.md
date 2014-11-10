MultiColumnUniqueness
=====================
[![Travis-CI Build Status](https://travis-ci.org/ravage84/cakephp-multi-column-uniqueness.png)](https://travis-ci.org/ravage84/cakephp-multi-column-uniqueness)
[![Coverage Status](https://img.shields.io/coveralls/ravage84/cakephp-multi-column-uniqueness.svg)](https://coveralls.io/r/ravage84/cakephp-multi-column-uniqueness?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ravage84/cakephp-multi-column-uniqueness/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ravage84/cakephp-multi-column-uniqueness/?branch=master)

A CakePHP 2.x behavior plugin to validate the uniqueness of multiple columns of a model.

## Requirements

The plugin has been tested with the following setup(s)

* CakePHP 2.4.1 - 2.5.4
* PHP 5.4.7
* MySQL

But it should work with

* CakePHP 2.4.1 or greater.
* PHP 5.2.8 or greater.
* Other data sources

## Installation

* Clone/Copy the files in this directory into `app/Plugin/MultiColumnUniqueness`
* Ensure the plugin is loaded in `app/Config/bootstrap.php` by calling `CakePlugin::load('MultiColumnUniqueness');` or `CakePlugin::loadAll();`

### Using Composer

See the [CakePHP plugin installation guide](http://book.cakephp.org/2.0/en/plugins/how-to-install-plugins.html#composer) in the CakePHP Cookbook.

## Reporting issues

If you have an problem with this plugin please [open an issue](https://github.com/ravage84/cakephp-multi-column-uniqueness/issues/new).
But check if [the problem has already been reported](https://github.com/ravage84/cakephp-multi-column-uniqueness/issues).

## Contributing

If you'd like to contribute to this project, check out the [open issues](https://github.com/ravage84/cakephp-multi-column-uniqueness/issues) for any planned features or open bugs.
You can fork the project, add features, documentation, and send pull requests; or [open issues](https://github.com/ravage84/cakephp-multi-column-uniqueness/issues/new).

# Versioning

This plugin adheres to [SemVer (Semantic Versioning)](http://semver.org/spec/v2.0.0.html).
Since the API is still in development, the plugin hasn't reached 1.0 yet.

# CakePHP Version Support

There are no plans to backport this to CakePHP 1.x.
The plugin should be updated to support future 2.x releases.
For CakePHP 3.0 it will need a total rewrite most probably...

## How To Use

Let's say you have a model ``Product`` which has many fields
but two of them need to be unique in conjunction with each other.
Those fields are ``name`` and ``manufacturer_id``.

As you may know you *could* use the CakePHP built-in data validation rule named
[isUnique](http://book.cakephp.org/2.0/en/models/data-validation.html#Model::Validation::isUnique).
This rule even takes multiple columns, which you could implement like this:

````php
public $validate = array(
	'first_name' => array(
		'unique_first_last' => array(
			'rule'    => array('checkMultiColumnUnique', array('first_name', 'last_name'), false)
			'message' => 'The first and last name must be unique.'
		)
	),
	// Additionally/optionally the same for the 'last_name' field
;)


public function checkMultiColumnUnique($ignoredData, $fields, $or = true) {
		return $this->isUnique($fields, $or);
}
````

So why bother using this plugin?
Because the solution above has a few downsides.
First either you setup the same rule for both/all fields that need to be unique
or you just set it for one of the fields.

If you set it for all fields the rule will be executed for each field
resulting in multiple (and redundant) SQL queries.
If you set it for only one field on the other hand, you have to include that field
in the data array every time you want to save one of the fields that need to be unique.
This plugins circumvents this by adding the rule dynamically.

To enable the MultiColumnUniquenessBehavior for these fields
you need to set the ``$actsAs`` property in the ``Product`` model.

````php
public $actsAs = array('MultiColumnUniqueness.MultiColumnUniqueness' => array(
	'fields' => array('name', 'manufacturer_id')
));
````

This is the simplest form of setup.
It would output a ``The fields mane and manufacturer_id must be unique.``
error message when the validation fails.

Additionally you could also choose a custom validation error message like this:

````php
public $actsAs = array('MultiColumnUniqueness.MultiColumnUniqueness' => array(
	'fields' => array('name', 'manufacturer_id'),
	'errMsg' => "This name and manufacturer ID can't be used twice."
));
````

Now let's say you also have three other fields named ``field1``, ``field2``, ``field13``
which need to be unique in conjunction with each other in the same model.
If you don't need custom error messages, you can set it up like this:

````php
public $actsAs = array('MultiColumnUniqueness.MultiColumnUniqueness' => array(
	'fields' => array(
		array('name', 'manufacturer_id'),
		array('field1', 'field2', 'field3'),
	)
));
````

For the same situation but with custom error messages, do it like this:

````php
public $actsAs = array('MultiColumnUniqueness.MultiColumnUniqueness' => array(
	'fields' => array(
		array('name', 'manufacturer_id'),
		array('field1', 'field2', 'field3'),
	),
	'errMsg' => array(
		"This name and manufacturer ID can't be used twice.",
		"This field1, field2, fiel3 can't be used twice"),
));
````

By default the data validation rule will be added only to the first field
which needs to be unique found in the data array.
If you want to enforce the data validation on each unique field,
set the ``onlyOnce`` option to false.
This way the validation error message will be shown on each unique field.

````php
public $actsAs = array('MultiColumnUniqueness.MultiColumnUniqueness' => array(
	'fields' => array(
		array('name', 'manufacturer_id'),
		array('field1', 'field2', 'field3'),
	),
	'onlyOnce' => false,
));
````
This option can only be set per model, not per unique field group.

## TODOs

* Running the tests with other databases in Travis, [as CakePHP does](https://github.com/cakephp/cakephp/blob/master/.travis.yml)
* See the [open issues](https://github.com/ravage84/cakephp-multi-column-uniqueness/issues)

## Background Story

I needed to validate the uniqueness of two fields of a model.
Not separately as CakePHP supports built-in by the [isUnique data validation rule](http://book.cakephp.org/2.0/en/models/data-validation.html#Model::Validation::isUnique)
but in conjunction with each other.
After I didn't found any built-in solution for my problem I turned to Google and [found some articles about this](http://stackoverflow.com/questions/2461267/cakephp-isunique-for-2-fields).
Also I asked [dereuromark](https://github.com/dereuromark) if he knew if there was an existing solution.
He pointed me to [his extended model called MyModel](https://github.com/dereuromark/tools/blob/master/Model/MyModel.php#L959) in his [Tools project](https://github.com/dereuromark/tools/).

Those solutions seemed to work but all of them either weren't up to date, weren't clean enough in their approach or forced me to add way too much code for my narrow needs.
So I decided to write a behavior for it (my first by the way).

After I got that to work I thought other people could benefit from it, so I created this project and implemented it as a plugin.
