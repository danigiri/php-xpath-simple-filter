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
// require_once 'PHPUnit/Autoload.php';

require_once 'com/calidos/dani/php/XPathSimpleFilter.php';


class XPathSimpleFilterOptionsTest extends PHPUnit_Framework_TestCase {

	private $xml;
	
	protected function setUp() {
		parent::setUp ();
	
		$this->xml = XPathSimpleFilterTestHelper::readSampleXML('entities.xml', LIBXML_NOCDATA);
	
	}
	
	
	protected function tearDown() {
		parent::tearDown ();
	}
	
	public function testAsXMLCDATA() {
		
			$a_ = array('/yummy/food/name');
			$names_ = XPathSimpleFilter::filter($this->xml, $a_);
			$namesXmlStr_ = XPathSimpleFilter::asXML($names_);
			$namesXml_ = simplexml_load_string($namesXmlStr_, "SimpleXMLElement", LIBXML_NOCDATA);
			
			$this->assertTrue(isset($namesXml_), 'asXML should return something');
			$this->assertTrue(is_a($namesXml_, 'SimpleXMLElement'), 'asXML should be a correct xml');
			$this->assertEquals(4, $namesXml_->name->count(), 'Should have all four elements');	
			$this->assertEquals('<Pa amb tomata>', $namesXml_->name[0]);
			$this->assertEquals('Pa amb oli & sal', $namesXml_->name[1]);
			$this->assertEquals('Pa amb oli & sal', $namesXml_->name[2]);
			$this->assertEquals('Ous i bacall&agrave;', $namesXml_->name[3]);
			
			$namesXml2_ = XPathSimpleFilter::filterToSimpleXML($this->xml, $a_, XPathSimpleFilter::CDATAWRAP);
				
			$this->assertEquals($namesXml_,$namesXml2_, 'filter+asXML and filterToSimpleXML should have same result');
				
	}
	
}	//CLASS

$tests = array(
		'testAsXMLCDATA'
);

// Used only within Eclipse debug
// $className_ = 'XPathSimpleFilterOptionsTest';
// XPathSimpleFilterTestHelper::runTestsInDebugEnvironment($className_, $tests);
