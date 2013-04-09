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

require_once 'XPathSimpleFilterTestHelper.php';

// Used only within Eclipse debug
// XPathSimpleFilterTestHelper::setupDebugEnvironment();
//require_once 'PHPUnit/Autoload.php';

require_once 'com/calidos/dani/php/XPathSimpleFilter.php';


class XPathSimpleFilterAsXMLTest extends PHPUnit_Framework_TestCase {

	private $xml;
	
	protected function setUp() {
		parent::setUp ();
	
		$this->xml = XPathSimpleFilterTestHelper::readSampleXML('simple.xml');
	
	}
	
	
	protected function tearDown() {
		parent::tearDown ();
	}
	
	public function testAsXML() {
		
		$foods_    = XPathSimpleFilter::filter($this->xml, array('/yummy/food'));
		$foodsXml_ = XPathSimpleFilter::asXML($this->xml, array($foods_));
		$this->assertTrue(isset($foodsXml_));
// 		$this->assertTrue(is_a($out_,'SimpleXMLElement'));
		
// 		$this->assertEquals($out_->food[0]->name, 'Pa amb tomata');
// 		$this->assertEquals(count($out_->children()), 5);
		
	}

	
}//CLASS
	
$tests = array(
			
);
	
// Used only within Eclipse debug
// $className_ = 'XPathSimpleFilterTest';
// XPathSimpleFilterTestHelper::runTestsInDebugEnvironment($className_, $tests);
	
	