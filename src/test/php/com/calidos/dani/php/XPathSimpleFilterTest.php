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


class XPathSimpleFilterTest extends PHPUnit_Framework_TestCase {

	private $xml;
	
	protected function setUp() {
		parent::setUp ();
	
		$this->xml = XPathSimpleFilterTestHelper::readSampleXML('simple.xml');
	
	}
	
	
	protected function tearDown() {
		parent::tearDown ();
	}
	
	
	public function testExceptionInput() {

		$failed = false;
		try {
			$out_ = XPathSimpleFilter::filter($this->xml, 'foo');
		} catch (\Exception $e) {
			$failed = true;
			$this->assertEquals($e->getMessage(),'Not passed an array as filter');
		}	
		$this->assertTrue($failed);
	}
	
	
	public function testBasic() {
		
		$out_ = XPathSimpleFilter::filter($this->xml, array());
		
		$this->assertTrue(isset($out_));		
		$menu_ = $out_->food;
		$this->assertTrue(isset($menu_));
		foreach ($out_->food as $food) {
			$name_ = $food->name;
			$this->assertTrue(isset($name_));	
		}
		$this->assertEquals($out_->food[0]->name, 'Pa amb tomata');
		
	}

	
	public function testBasicXMLNode() {
	
		$foods_ = XPathSimpleFilter::filter($this->xml, array('/yummy/food'));

		$this->assertTrue(isset($foods_));	
		$this->assertTrue(is_array($foods_));
		foreach ($foods_ as $food_) {
			$name_ = $food_->name;
			$this->assertTrue(isset($name_));
		}
		$this->assertEquals($foods_[0]->name, 'Pa amb tomata');
	
	}
	
	
	public function testBasicXPath() {

		$a_ = array('/yummy/food[position() = 1]/name');
		$name_ = XPathSimpleFilter::filter($this->xml, $a_);

		$this->assertTrue(isset($name_));
		$this->assertEquals($name_, 'Pa amb tomata');

		// doesn't affect flattened structures
		$a_ = array('/yummy/food[position() = 1]/name[position() = 1]');
		$name_ = XPathSimpleFilter::filter($this->xml, $a_);
		
		$this->assertTrue(isset($name_));
		$this->assertEquals($name_, 'Pa amb tomata');
		
		$a_ = array('/yummy/food[position() = 3]/price/@currency');
		$currency_ = XPathSimpleFilter::filter($this->xml, $a_);
		
		$this->assertTrue(isset($currency_));
		$this->assertEquals($currency_, 'USD');
				
	}
	
	
	public function testImplicitXPath() {
		
		$a_ = array('/yummy/food[position() = 1]/name',
					'/yummy/food[position() = 1]/calories');
		$food_ = XPathSimpleFilter::filter($this->xml, $a_);
		
		$this->assertTrue(isset($food_));
		$this->assertTrue(is_array($food_));
		$this->assertEquals($food_['name'], 'Pa amb tomata');
		$this->assertEquals($food_['calories'], '222');


		// let's test empty node values, xpath returns 'false' so we keep within that philosophy
        $a_ = array('/yummy/food[position() = 5]/name',
                    '/yummy/food[position() = 5]/emptynode',
					'/yummy/food[position() = 5]/emptynode2');
        $food_ = XPathSimpleFilter::filter($this->xml, $a_);

        $this->assertTrue(isset($food_));
        $this->assertTrue(is_array($food_));
        $this->assertEquals($food_['name'], 'Fuet');
        $this->assertEquals($food_['emptynode'], false);
        $this->assertEquals($food_['emptynode2'], false);
		
	}
	
	
	public function testImplicitXPathVariableNames() {
		
		$a_ = array('/yummy/food[position() = 1]/name[position() = 1]',
				'/yummy/food[position() = 1]/calories[position() = 1]');
		$food_ = XPathSimpleFilter::filter($this->xml, $a_);
		
		$this->assertTrue(isset($food_));
		$this->assertTrue(is_array($food_));
		$this->assertEquals($food_['name'], 'Pa amb tomata');
		$this->assertEquals($food_['calories'], '222');

	}
	
	
	public function testArrayXPath() {
	
		$a_ = array('/yummy/food/name');
		$names_ = XPathSimpleFilter::filter($this->xml, $a_);
		
		$this->assertTrue(isset($names_));
		$this->assertTrue(is_array($names_));
		$this->assertEquals(count($names_), 5);
	}
	
	
	public function testEmptyXPath() {
	
		$a_ = array('/foobar');
		$empty_ = XPathSimpleFilter::filter($this->xml, $a_);
		
		$this->assertTrue(isset($empty_));
		$this->assertTrue(is_array($empty_));
		$this->assertEquals(count($empty_), 0);
		
	}
	
	public function testBasicNamedXPath() {
	
		$a_ = array('foo' => '/yummy/food[position() = 1]/name');
		$name_ = XPathSimpleFilter::filter($this->xml, $a_);
		
		$this->assertTrue(isset($name_));
		$this->assertTrue(is_array($name_));
		$this->assertEquals(count($name_), 1);		
		$this->assertEquals($name_['foo'], 'Pa amb tomata');
	}

	
	public function testMultipleXPath() {
	
		$a_ = array('/yummy/food[position() = 1]/name',
					'/yummy/food[position() = 2]/name');
		$names_ = XPathSimpleFilter::filter($this->xml, $a_);
		
		$this->assertTrue(isset($names_));
		$this->assertTrue(is_array($names_));
		$this->assertEquals(count($names_), 2);
		$this->assertEquals($names_[0], 'Pa amb tomata');
		$this->assertEquals($names_[1], 'Pa amb tomata torrat');
		
	}
	
	
	public function testMultipleNamedXPath() {
	
		$a_ = array('food0' => '/yummy/food[position() = 1]/name',
				 	'food1' => '/yummy/food[position() = 2]/name');
		$names_ = XPathSimpleFilter::filter($this->xml, $a_);
		$this->assertTrue(isset($names_));
		$this->assertTrue(is_array($names_));
		$this->assertEquals(count($names_), 2);
		$this->assertEquals($names_['food0'], 'Pa amb tomata');
		$this->assertEquals($names_['food1'], 'Pa amb tomata torrat');
	
	}


	public function testMultiple2NamedXPath() {
	
		$a_ = array('foodNames' => '/yummy/food/name',
					'foodCalories' => '/yummy/food/calories');
		$foods_ = XPathSimpleFilter::filter($this->xml, $a_);
		
		$this->checkFoodInfo($foods_);
	
	}
	
	
	public function testRecursiveNamedXPath() {
	
		$a_ = array('foo' => array('/yummy/food[position() = 1]/name',
									'/yummy/food[position() = 2]/name')
		);
		$names_ = XPathSimpleFilter::filter($this->xml, $a_);
		
		$this->assertTrue(isset($names_));
		$this->assertTrue(is_array($names_));
		$this->assertEquals(count($names_), 1);
		$this->assertEquals($names_['foo'][0], 'Pa amb tomata');
		$this->assertEquals($names_['foo'][1], 'Pa amb tomata torrat');
	
	}
	
	
	public function testRecursiveMultipleNamedXPath() {
	
		$a_ = array('foodInfo' => array('foodNames' => '/yummy/food/name',
										'foodCalories' => '/yummy/food/calories'),
					'prices' => '/yummy/food/price');
		$foodMultiple_ = XPathSimpleFilter::filter($this->xml, $a_);
		
		$this->assertTrue(isset($foodMultiple_));
		$this->assertTrue(is_array($foodMultiple_));
		$this->checkFoodInfo($foodMultiple_['foodInfo']);
		$prices_ = $foodMultiple_['prices'];
		$this->assertTrue(is_array($prices_));
		$this->assertEquals(count($prices_), 5);
		$this->assertEquals($prices_[0], '1.00');
		
	}
	
	
	public function testCompositeBasic() {
		
		$a_ = array(XPathSimpleFilter::NODES => array('/yummy/food', 
														array('./name')
													 )
				);
		$foodNodes_ = XPathSimpleFilter::filter($this->xml, $a_);
		
		$this->assertTrue(isset($foodNodes_));
		$this->assertTrue(is_array($foodNodes_));
		$this->assertEquals(count($foodNodes_), 5);
		$this->assertEquals($foodNodes_[0], 'Pa amb tomata');
		$this->assertEquals($foodNodes_[1], 'Pa amb tomata torrat');
		
	}
	
	
	public function testCompositeNamed() {
	
		$a_ = array(XPathSimpleFilter::NODES => array('/yummy/food',
														array('foodName' => './name')
													 )
		);
		$foodNodes_ = XPathSimpleFilter::filter($this->xml, $a_);
		
		$this->assertTrue(isset($foodNodes_));
		$this->assertTrue(is_array($foodNodes_));
		$this->assertEquals(count($foodNodes_), 5);
		$this->assertEquals($foodNodes_[0]['foodName'], 'Pa amb tomata');
		$this->assertEquals($foodNodes_[1]['foodName'], 'Pa amb tomata torrat');
		
		
		$a_ = array('foo' => array(XPathSimpleFilter::NODES => array('/yummy/food',
													array('foodName' => './name')
								   )
					)
		);
		$foodNodes_ = XPathSimpleFilter::filter($this->xml, $a_);
		
		$this->assertTrue(isset($foodNodes_));
		$this->assertTrue(is_array($foodNodes_));
		$this->assertEquals(count($foodNodes_['foo']), 5);
		
	}
	
	
	public function testCompositeMultiple() {
	
		$a_ = array(
				XPathSimpleFilter::NODES => array('/yummy/food', 
													array('./name',
														  './calories')
												)
						);
		$foodNodes_ = XPathSimpleFilter::filter($this->xml, $a_);
		
		$this->assertTrue(isset($foodNodes_));
		$this->assertTrue(is_array($foodNodes_));
		$this->assertEquals(count($foodNodes_), 5);
		$food_ = $foodNodes_[0];
		$this->assertTrue(is_array($food_));
		$this->assertEquals(count($food_), 2);
		$this->assertEquals($food_['name'], 'Pa amb tomata');
		$this->assertEquals($food_['calories'], '222');
		
	}
	
	
	public function testCompositeRecursive() {

		$ingredients_ = array('./ingredients',array('./ingredient'));	//ingredients is an array
		$a_ = array(
				XPathSimpleFilter::NODES => array('/yummy/food',
													array('./name',
													XPathSimpleFilter::NODES => $ingredients_)
				)
		);
		$foodNodes_ = XPathSimpleFilter::filter($this->xml, $a_);

		$this->assertTrue(isset($foodNodes_));
		$this->assertTrue(is_array($foodNodes_));
		$this->assertEquals(count($foodNodes_), 5);
		$food_ = $foodNodes_[0];
		$this->assertTrue(is_array($food_));
		$this->assertEquals(count($food_), 2);
		$this->assertEquals($food_['name'], 'Pa amb tomata');
		$ingredients_ = $food_['ingredients'];
		$this->assertTrue(is_array($ingredients_));
		$this->assertEquals(count($ingredients_), 4);
		$this->assertEquals($ingredients_[0], 'Pa');
				
	}
	
	
	private function checkFoodInfo($foods) {
		
		$this->assertTrue(isset($foods));
		$this->assertTrue(is_array($foods));
		$this->assertEquals(count($foods), 2);
		$foodNames = $foods['foodNames'];
		$this->assertEquals(count($foodNames),5);
		$this->assertEquals($foodNames[0], 'Pa amb tomata');
		$foodCalories_ = $foods['foodCalories'];
		$this->assertEquals($foodCalories_[0], '222');
		
	}

}//CLASS

$tests = array(
		'testExceptionInput',
		'testBasic',
		'testBasicXMLNode',
		'testBasicXPath',
		'testImplicitXPath',
		'testImplicitXPathVariableNames',
		'testArrayXPath',
		'testEmptyXPath',
		'testBasicNamedXPath',
		'testMultipleXPath',
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
// $className_ = 'XPathSimpleFilterTest';
// XPathSimpleFilterTestHelper::runTestsInDebugEnvironment($className_, $tests);
