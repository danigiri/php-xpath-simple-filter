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
XPathSimpleFilterTestHelper::setupDebugEnvironment();
require_once 'PHPUnit/Autoload.php';

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
		
		$a_ = array('/yummy/food[position() = 1]/name');
		$name_ = XPathSimpleFilter::filter($this->xml, $a_);
		$nameXml_ = XPathSimpleFilter::asXML($name_);

		$this->assertTrue(isset($nameXml_));
		$this->assertTrue(is_string($nameXml_));
		$nameXml_ = XPathSimpleFilterAsXMLTest::strip($nameXml_);
		$this->assertEquals('<data>Pa amb tomata</data>', $nameXml_);
		
		$a_ = array('/yummy/food[position() = 1]/name',
					'/yummy/food[position() = 1]/calories');
		$food_ = XPathSimpleFilter::filter($this->xml, $a_);
		$foodXml_ = XPathSimpleFilter::asXML($food_);
		
		$this->assertTrue(isset($foodXml_));
		$this->assertTrue(is_string($foodXml_));
		$foodXml_ = XPathSimpleFilterAsXMLTest::strip($foodXml_);
		$this->assertEquals('<data><name>Pa amb tomata</name><calories>222</calories></data>', $foodXml_);
	}

	public function testAsXMLEmpty() {
	
		// let's test empty node values, which will have <emptynode/>  and <emptynode2>
		$a_ = array('/yummy/food[position() = 5]/name',
					'/yummy/food[position() = 5]/emptynode',
					'/yummy/food[position() = 5]/emptynode2');
		$food_ = XPathSimpleFilter::filter($this->xml, $a_);
		$foodXml_ = XPathSimpleFilter::asXML($food_);
		
		$this->assertTrue(isset($foodXml_));
		$this->assertTrue(is_string($foodXml_));
		$foodXml_ = XPathSimpleFilterAsXMLTest::strip($foodXml_);
		$this->assertEquals('<data><name>Fuet</name><emptynode/><emptynode2/></data>', $foodXml_);
		
	}
	
	
	public function testAsXMLMultiple() {
		$a_ = array('foodInfo' => array('foodNames' => '/yummy/food/name',
										'foodCalories' => '/yummy/food/calories'),
					'prices' => '/yummy/food/price');
		$foodMultiple_ = XPathSimpleFilter::filter($this->xml, $a_);
		$foodXml_ = XPathSimpleFilter::asXML($foodMultiple_);
		
		$this->assertTrue(isset($foodXml_), 'asXML should not return empty');
		$this->assertTrue(is_string($foodXml_), 'asXML should return a string');
		$foodXml_ = XPathSimpleFilterAsXMLTest::strip($foodXml_);
		$this->assertEquals('<data>'.
								'<foodInfo>'.
									'<foodNames>'.
										'<name>Pa amb tomata</name>'.
										'<name>Pa amb tomata torrat</name>'.
										'<name>Crema catalana</name>'.
										'<name>Samfaina</name>'.
										'<name>Fuet</name>'.
									'</foodNames>'.
									'<foodCalories>'.
										'<calories>222</calories>'.
										'<calories>200</calories>'.
										'<calories>100</calories>'.
										'<calories>200</calories>'.
										'<calories>350</calories>'.
									'</foodCalories>'.
							'</foodInfo>'.
								'<prices>'.
									'<price currency="EUR">1.00</price>'.
									'<price currency="EUR">2.00</price>'.
									'<price currency="USD">3.00</price>'.
									'<price currency="EUR">4.00</price>'.
									'<price currency="EUR">1.00</price>'.
								'</prices>'.
							'</data>', 
							$foodXml_);
		
	}

	private static function strip($str) {
		return str_replace( array("\r\n", "\r", "\n"), '', $str);  
	}
	
	
}//CLASS
	
$tests = array(
			'testAsXML',
			'testAsXMLEmpty',
			'testAsXMLMultiple'
);
	
// Used only within Eclipse debug
$className_ = 'XPathSimpleFilterAsXMLTest';
XPathSimpleFilterTestHelper::runTestsInDebugEnvironment($className_, $tests);
