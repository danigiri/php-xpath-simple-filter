<?php

use com\calidos\dani\php\XPathSimpleFilter;

// Used only within Eclipse debug
/*
echo getcwd();
$codePath = '../../../../../../main/php';
$testPath = '../../../../../../../target/php-test-deps';
$includePath = get_include_path() . $codePath . PATH_SEPARATOR . $testPath;
set_include_path($includePath);
require_once 'PHPUnit/Autoload.php';
*/
require_once 'com/calidos/dani/php/XPathSimpleFilter.php';

class XPathSimpleFilterTest extends PHPUnit_Framework_TestCase {

	private $xml;
	
	protected function setUp() {
		parent::setUp ();
		$xmlFile_ = 'test-classes/simple.xml';
		if (!file_exists($xmlFile_)) {
			$xmlFile_ = 'src/test/resources/simple.xml';
		}
		if (!file_exists($xmlFile_)) {
			$xmlFile_ = '../../../../../resources/simple.xml';
		}
		
		$this->xml = simplexml_load_file($xmlFile_);
		
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
		//var_dump($out_);
		$this->assertTrue(isset($foods_));
	
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
		$this->assertEquals($prices_[0], '1.00EUR');
		
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
		'testArrayXPath',
		'testEmptyXPath',
		'testBasicNamedXPath',
		'testMultipleXPath',
		'testMultipleNamedXPath',
		'testMultiple2NamedXPath',
		'testRecursiveNamedXPath',
		'testRecursiveMultipleNamedXPath',
		);

// Used only within Eclipse debug
/*
foreach ($tests as $test) {
	$result = PHPUnit_TextUI_TestRunner::run(new XPathSimpleFilterTest($test));
}*/