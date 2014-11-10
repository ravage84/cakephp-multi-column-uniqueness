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
 * All MultiColumnUniqueness tests
 *
 * This test suite will run all Controller tests.
 */
class AllTests extends PHPUnit_Framework_TestSuite {

/**
 * Defines tests for this suite
 *
 * @return PHPUnit_Framework_TestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('All MultiColumnUniqueness tests');

		$path = dirname(__FILE__);
		$suite->addTestDirectory($path . DS . 'Model' . DS . 'Behavior');

		return $suite;
	}
}
