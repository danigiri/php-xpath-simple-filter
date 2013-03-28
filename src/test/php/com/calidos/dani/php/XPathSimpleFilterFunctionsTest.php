<?php

/*
 *  Copyright 2013 Daniel Giribet <dani - calidos.cat>
*
*  Licensed under the Apache License, Version 2.0 (the "License");
*  you may not use this file except in compliance with the License.
*  You may obtain a copy of the License at
*
*      http://www.apache.org/licenses/LICENSE-2.0
*
*  Unless required by applicable law or agreed to in writing, software
*  distributed under the License is distributed on an "AS IS" BASIS,
*  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
*  See the License for the specific language governing permissions and
*  limitations under the License.
*/

use com\calidos\dani\php\XPathSimpleFilter;

// Used only within Eclipse debug
// echo getcwd();
// $codePath = '../../../../../../main/php';
// $testPath = '../../../../../../../target/php-test-deps';
// $includePath = get_include_path() . PATH_SEPARATOR . $codePath . PATH_SEPARATOR . $testPath;
// set_include_path($includePath);
// require_once 'PHPUnit/Autoload.php';

require_once 'com/calidos/dani/php/XPathSimpleFilter.php';
require_once 'XPathSimpleFilterTestHelper.php';

class XPathSimpleFilterFunctionsTest extends PHPUnit_Framework_TestCase {

	private $xml;
	
	protected function setUp() {
		parent::setUp ();
	
		$this->xml = XPathSimpleFilterTestHelper::readSampleXML('simple.xml');
	
	}
	
	
	protected function tearDown() {
		parent::tearDown ();
	}
	
	
	public function testBasic() {
	
		$out_ = XPathSimpleFilter::filterMaps($this->xml, array());
	
		$this->assertTrue(isset($out_));
	
		
	}
	
}//CLASS
	
$tests = array(
			''
);
	
// Used only within Eclipse debug
foreach ($tests as $test) {
 	$result = PHPUnit_TextUI_TestRunner::run(new XPathSimpleFilterFunctionsTest($test));
 }
