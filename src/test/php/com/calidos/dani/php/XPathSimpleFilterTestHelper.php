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

class XPathSimpleFilterTestHelper {

	
	public static function readSampleXML($name) {
	
		$xmlFile_ = 'test-classes/'.$name;
		if (!file_exists($xmlFile_)) {
			$xmlFile_ = 'src/test/resources/'.$name;
		}
		if (!file_exists($xmlFile_)) {
			$xmlFile_ = '../../../../../resources/'.$name;
		}
	
		return simplexml_load_file($xmlFile_);
	
	}	// readSampleXML
	
	
	public static function setupDebugEnvironment() {
		
		// echo getcwd();
		$codePath = '../../../../../../main/php';
		$testPath = '../../../../../../../target/php-test-deps';
		$includePath = get_include_path() . PATH_SEPARATOR . $codePath . PATH_SEPARATOR . $testPath;
		set_include_path($includePath);
		
	}	// setupDebugEnvironment
	
	
	public static function runTestsInDebugEnvironment($classname, $tests) {

		$testClass_ = new ReflectionClass($classname);
		foreach ($tests as $testName) {
			$instance_ = $testClass_->newInstanceArgs(array($testName));
			PHPUnit_TextUI_TestRunner::run($instance_);
		}
	
	}	// runTestsInDebugEnvironment
	
}

