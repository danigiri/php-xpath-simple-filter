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
//use XPathSimpleFilterTest;

// Used only within Eclipse debug
// echo getcwd()."\n";
$codePath = '../../../../../../main/php';
$testPath = '../../../../../../../target/php-test-deps';
$includePath = get_include_path() . PATH_SEPARATOR . $codePath . PATH_SEPARATOR . $testPath;
set_include_path($includePath);
require_once 'PHPUnit/Autoload.php';

require_once 'com/calidos/dani/php/XPathSimpleFilter.php';
require_once 'XPathSimpleFilterTestHelper.php';

class XPathSimpleFilterXMLTest extends PHPUnit_Framework_TestCase {

	private $xml;

	protected function setUp() {
		parent::setUp ();
	
		$this->xml = XPathSimpleFilterTestHelper::readSampleXML('simple.xml');
	
	}
	
	protected function tearDown() {
		parent::tearDown ();
	}
	
	
	public function testBasic() {
		
		$out_ = XPathSimpleFilter::filterToSimpleXML($this->xml, array());
		$this->assertTrue(isset($out_));
		$this->assertTrue(is_a($out_,'SimpleXMLElement'));
		
		$this->assertEquals($out_->food[0]->name, 'Pa amb tomata');
		$this->assertEquals(count($out_->children()), 5);
		
	}
	
	
	public function testBasicXMLNode() {
	
		$foods_ = XPathSimpleFilter::filterToSimpleXML($this->xml, array('/yummy/food'));
		$this->assertTrue(isset($foods_));
		$this->assertTrue(is_a($foods_,'SimpleXMLElement'));
	
		$this->assertEquals(count($foods_->children()), 5);
		$name_ = $foods_->food[0]->name;
		$this->assertEquals($name_, 'Pa amb tomata');
	
	}
	
	public function testBasicXPath() {
	
		$a_ = array('/yummy/food[position() = 1]/name');
		$name_ = XPathSimpleFilter::filterToSimpleXML($this->xml, $a_);
		$this->assertTrue(isset($name_));
		$this->assertTrue(is_a($name_,'SimpleXMLElement'));
		$this->assertEquals($name_->getName(),'data');
		$this->assertEquals($name_, 'Pa amb tomata');
	
		// doesn't affect flattened structures
		$a_ = array('/yummy/food[position() = 1]/name[position() = 1]');
		$name_ = XPathSimpleFilter::filterToSimpleXML($this->xml, $a_);
		$this->assertTrue(isset($name_));
		$this->assertTrue(is_a($name_,'SimpleXMLElement'));
		$this->assertEquals($name_->getName(),'data');
		$this->assertEquals($name_, 'Pa amb tomata');
	
		$a_ = array('/yummy/food[position() = 3]/price/@currency');
		$currency_ = XPathSimpleFilter::filterToSimpleXML($this->xml, $a_);
		$this->assertTrue(isset($currency_));
		$this->assertTrue(is_a($currency_,'SimpleXMLElement'));
		$this->assertEquals($name_->getName(),'data');
		$this->assertEquals($currency_, 'USD');
	
	}
	
	
	public function testImplicitXPath() {
	
		$a_ = array('/yummy/food[position() = 1]/name',
				'/yummy/food[position() = 1]/calories');
		$food_ = XPathSimpleFilter::filterToSimpleXML($this->xml, $a_);
		$this->assertTrue(isset($food_));
		$this->assertTrue(is_a($food_,'SimpleXMLElement'));
		$this->assertEquals($food_->name, 'Pa amb tomata');
		$this->assertEquals($food_->calories, '222');
	
	}
	
	
	public function testArrayXPath() {
	
		$a_ = array('/yummy/food/name');
		$names_ = XPathSimpleFilter::filterToSimpleXML($this->xml, $a_);
		$this->assertTrue(isset($names_));
		$this->assertTrue(is_a($names_,'SimpleXMLElement'));
		$this->assertEquals(count($names_->children()), 5);
		$this->assertEquals($names_->name[0], 'Pa amb tomata');
		
	}
	

	public function testEmptyXPath() {
	
		$a_ = array('/foobar');
		$empty_ = XPathSimpleFilter::filterToSimpleXML($this->xml, $a_);
		$this->assertTrue(isset($empty_));
		$this->assertTrue(is_a($empty_,'SimpleXMLElement'));
		$this->assertEquals(count($empty_->children()), 0);
		$this->assertEquals($empty_->data, '');
		
	}
	
	
	public function testBasicNamedXPath() {
	
		$a_ = array('foo' => '/yummy/food[position() = 1]/name');
		$name_ = XPathSimpleFilter::filterToSimpleXML($this->xml, $a_);
		$this->assertTrue(isset($name_));
		$this->assertTrue(is_a($name_,'SimpleXMLElement'));
		$this->assertEquals(count($name_->children()), 0);
		$this->assertEquals($name_, 'Pa amb tomata');
	}
	
	
	public function testMultipleNamedXPath() {
	
		$a_ = array('food0' => '/yummy/food[position() = 1]/name',
					'food1' => '/yummy/food[position() = 2]/name');
		$names_ = XPathSimpleFilter::filterToSimpleXML($this->xml, $a_);
		$this->assertTrue(isset($names_));
		$this->assertTrue(is_a($names_,'SimpleXMLElement'));
		$this->assertEquals(count($names_->children()), 2);
		$this->assertEquals($names_->food0, 'Pa amb tomata');
		$this->assertEquals($names_->food1, 'Pa amb tomata torrat');
	
	}
	
	
	public function testMultiple2NamedXPath() {
	
		$a_ = array('foodNames' => '/yummy/food/name',
					'foodCalories' => '/yummy/food/calories');
		$foods_ = XPathSimpleFilter::filterToSimpleXML($this->xml, $a_);
		$this->checkFoodInfo($foods_);
		
	}
	
	
	public function testRecursiveNamedXPath() {
	
		$a_ = array('foo' => array('/yummy/food[position() = 1]/name',
								   '/yummy/food[position() = 2]/name')
		);
		$names_ = XPathSimpleFilter::filterToSimpleXML($this->xml, $a_);
		$this->assertTrue(isset($names_));
		$this->assertTrue(is_a($names_,'SimpleXMLElement'));
		$this->assertEquals(count($names_->children()), 2);
		$this->assertEquals($names_->name[0], 'Pa amb tomata');
		$this->assertEquals($names_->name[1], 'Pa amb tomata torrat');
	
	}
	
	
	public function testRecursiveMultipleNamedXPath() {
	
		$a_ = array('foodInfo' => array('foodNames' => '/yummy/food/name',
										'foodCalories' => '/yummy/food/calories'),
					'prices' => '/yummy/food/price');
		$foodMultiple_ = XPathSimpleFilter::filterToSimpleXML($this->xml, $a_);
		$this->assertTrue(isset($foodMultiple_));
		$this->assertTrue(is_a($foodMultiple_,'SimpleXMLElement'));
		$this->checkFoodInfo($foodMultiple_->foodInfo);
		$prices_ = $foodMultiple_->prices;
		$this->assertTrue(is_a($prices_,'SimpleXMLElement'));
		$this->assertEquals(count($prices_->children()), 5);
		$this->assertEquals($prices_->price[0], '1.00');
	
	}
	
	
	public function testCompositeBasic() {
	
		// note this returns a simple array, therefore the nodes are named as default
		$a_ = array(XPathSimpleFilter::NODES => array('/yummy/food',
														array('./name')
													  )
		);
		$foodNodes_ = XPathSimpleFilter::filterToSimpleXML($this->xml, $a_);
		$this->assertTrue(isset($foodNodes_));
		$this->assertTrue(is_a($foodNodes_,'SimpleXMLElement'));
		$this->assertEquals(count($foodNodes_->children()), 5);
		$this->assertEquals($foodNodes_->data[0], 'Pa amb tomata');
		$this->assertEquals($foodNodes_->data[1], 'Pa amb tomata torrat');
	
	}
	

	public function testCompositeNamed() {
	
		$a_ = array(XPathSimpleFilter::NODES => array('/yummy/food',
														array('foodName' => './name')
													 )
		);
		$foodNodes_ = XPathSimpleFilter::filterToSimpleXML($this->xml, $a_);
		$this->assertTrue(isset($foodNodes_));
		$this->assertTrue(is_a($foodNodes_,'SimpleXMLElement'));
		$this->assertEquals(count($foodNodes_->children()), 5);
		$this->assertEquals($foodNodes_->foodName[0], 'Pa amb tomata');
		$this->assertEquals($foodNodes_->foodName[1], 'Pa amb tomata torrat');
	
	}
	
	
	public function testCompositeMultiple() {
	
		$a_ = array(
				XPathSimpleFilter::NODES => array('/yummy/food',
													array('./name',
														  './calories',
														  'empty' => './nonexistant')
				)
		);
		$foodNodes_ = XPathSimpleFilter::filterToSimpleXML($this->xml, $a_);
		$this->assertTrue(isset($foodNodes_));
		$this->assertTrue(is_a($foodNodes_,'SimpleXMLElement'));
		$this->assertEquals(count($foodNodes_->children()), 5);
		$food_ = $foodNodes_->data[0];
		$this->assertTrue(is_a($food_,'SimpleXMLElement'));
		$this->assertEquals(count($food_->children()), 3);
		$this->assertEquals($food_->name, 'Pa amb tomata');
		$this->assertEquals($food_->calories, '222');
		$this->assertEquals(count($food_->empty->children()), 0);
		$this->assertEquals($food_->empty->data, '');
		
	}
	
	public function testCompositeRecursive() {
	
		$ingredients_ = array('./ingredients',array('./ingredient'));	//ingredients is an array
		$a_ = array(
				XPathSimpleFilter::NODES => array('/yummy/food',
												array('./name',
												XPathSimpleFilter::NODES => $ingredients_)
				)
		);
	
		$foodNodes_ = XPathSimpleFilter::filterToSimpleXML($this->xml, $a_);
		$this->assertTrue(isset($foodNodes_));
		$this->assertTrue(is_a($foodNodes_,'SimpleXMLElement'));
		$this->assertEquals(count($foodNodes_->children()), 5);
		$food_ = $foodNodes_->data[0];
		$this->assertTrue(is_a($food_,'SimpleXMLElement'));
		$this->assertEquals(count($food_->children()), 2);
		$this->assertEquals($food_->name, 'Pa amb tomata');
		$ingredients_ = $food_->ingredients;
		$this->assertTrue(is_a($ingredients_,'SimpleXMLElement'));
		$this->assertEquals(count($ingredients_->children()), 4);
		$this->assertEquals($ingredients_->ingredient[0], 'Pa');
	
	}
	
	
	private function checkFoodInfo($foods) {
	
		$this->assertTrue(isset($foods));
		$this->assertTrue(is_a($foods,'SimpleXMLElement'));
		$this->assertEquals(count($foods->children()), 2);
		$foodNames = $foods->foodNames;
		$this->assertTrue(is_a($foodNames,'SimpleXMLElement'));
		$this->assertEquals(count($foodNames->children()),5);
		$this->assertEquals($foodNames->name[0], 'Pa amb tomata');
		$foodCalories_ = $foods->foodCalories;
		$this->assertEquals($foodCalories_->calories[0], '222');
	
	}
	
	
}

$tests = array(
		'testBasic',
		'testBasicXMLNode',
		'testBasicXPath',
		'testImplicitXPath',
		'testArrayXPath',
		'testEmptyXPath',
		'testBasicNamedXPath',
		'testMultipleNamedXPath',
		'testMultiple2NamedXPath',
		'testRecursiveNamedXPath',
		'testRecursiveMultipleNamedXPath',
		'testCompositeBasic',
		'testCompositeNamed',
		'testCompositeMultiple',
		'testCompositeRecursive'
);

// Used only within Eclipse debug
foreach ($tests as $test) {
	$result = PHPUnit_TextUI_TestRunner::run(new XPathSimpleFilterXMLTest($test));
}
