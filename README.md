MultiColumnUniqueness
=====================
![Traiv-CI Build Status](https://travis-ci.org/ravage84/MultiColumnUniqueness.png)

A CakePHP 2.x behavior plugin to validate the uniqueness of multiple columns of a model.

## Requirements

The plugin has been tested with the following setup(s)

* CakePHP 2.4.1
* PHP 5.4.7
* MySQL

But it should work with

* CakePHP 2.4.1 or greater.
* PHP 5.2.8 or greater.
* Ohter data sources

## Installation

* Clone/Copy the files in this directory into `app/Plugin/MultiColumnUniqueness`
* Ensure the plugin is loaded in `app/Config/bootstrap.php` by calling `CakePlugin::load('MultiColumnUniqueness');` or `CakePlugin::loadAll();`
 
### Using Composer

Not supported yet.

## Reporting issues

If you have an problem with this plugin please [open an issue](https://github.com/ravage84/MultiColumnUniqueness/issues/new).
But check if [the problem has already been reported](https://github.com/ravage84/MultiColumnUniqueness/issues).

## Contributing

If you'd like to contribute to this project, check out the [open issues](https://github.com/ravage84/MultiColumnUniqueness/issues) for any planned features or open bugs.
You can fork the project, add features, documentation, and send pull requests; or [open issues](https://github.com/ravage84/MultiColumnUniqueness/issues/new).

# Versioning

This plugin adheres to [SemVer (Semantic Versioning)](http://semver.org/spec/v2.0.0.html).
Since the API is still in development, the plugin hasn't reached 1.0 yet.

# CakePHP Version Support

There are no plans to backport this to CakePHP 1.x.
The plugin should be updated to support future 2.x releases.
For CakePHP 3.0 it will need a total rewrite most proably...

## Documentation

TODO

## TODOs

* A composer.json
* Documentation on how to use the plugin in this README (and not only within the behavior class)
* Checking the Code Style with phpcs in Travis, [as CakePHP dose](https://github.com/cakephp/cakephp/blob/master/.travis.yml)
* Running the tests with other databases in Travis, [as CakePHP dose](https://github.com/cakephp/cakephp/blob/master/.travis.yml)
* See the [open issues](https://github.com/ravage84/MultiColumnUniqueness/issues)
 
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
