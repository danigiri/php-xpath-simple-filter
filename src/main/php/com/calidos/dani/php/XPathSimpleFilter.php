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

namespace com\calidos\dani\php;
use Exception;
use \SimpleXMLElement;

/** XML XPath filter class
 *	@author daniel giribet
 */
class XPathSimpleFilter {

	const NODES = 'nodes_____';
	
	/** Filter SimpleXML using array of xpaths
   	 * @param SimpleXML $xml input
   	 * @param array $a xpath expressions array
     * @return filtered xmlElement nodes as flattened array structure
	 */
    static public function filter($xml, $a) {
    		    	
    	if (empty($a)) {
    		// trivial case return identity (XMLSimpleElement)
    		return $xml;
    	}
    		$provisionalKeys_ = array();
    	 
    		$out_ = XPathSimpleFilter::applyXPathToXml($xml, $a, $provisionalKeys_);
      		$out_ = XPathSimpleFilter::flattenGivenProvisionalKeys($out_,$provisionalKeys_);

    		return $out_;    		

    }	// filter
    
    
    /** Filter SimpleXML using array of xpaths
     * @param SimpleXML $xml input
     * @param array $a xpath expressions array
     * @return filtered xmlElement nodes as flattened SimpleXML
     */
    static public function filterToSimpleXML($xml, $a) {
    	
    	$out_ = XPathSimpleFilter::filter($xml, $a);
    	
    	if (is_array($out_)) {
    		if (is_numeric(key($out_))) {
    			// array of elements base case
    			 
    			simplexml_load_string("<data></data>");
    			
    		} else {
    		
	    	}
      	
    	} elseif (is_a($out,'XML'))
    	
    }	 

    
   	/** Apply filtering
   	 * @param SimpleXML $xml input
   	 * @param array $a xpath expressions array
   	 * @param array $provisionalKeys
     * @return filtered xmlElement nodes as a non-flattened array structure
   	 */
    static private function applyXPathToXml($xml, $a, &$provisionalKeys) {
    	
    	if (!is_array($a) or !isset($a)) {
    		throw new \Exception('Not passed an array as filter');
    	}
    	
    	$out_ = array();
    	foreach ($a as $key_ => $filter_) {
    	
    		if ($key_ === XPathSimpleFilter::NODES) {
    	
    			$node_ = $a[$key_];
    			if (!is_array($node_) || count($node_)!=2) {
   					throw new \Exception('Node value should have two elements');
    			}
   				$nodeXPath_ = $node_[0];
   				$xPathToApply_ = $node_[1];
    			$provisionalKey_ = XPathSimpleFilter::getProvisionalKey(0, $nodeXPath_, $provisionalKeys);
    			$nodes_ = $xml->xpath($nodeXPath_);
    	
    			$content_ = array();
    			foreach ($nodes_ as $n_) {
    				//recursive case (LOL!)
    				$nodeContent_ = XPathSimpleFilter::filter($n_, $xPathToApply_);
    				$content_ = XPathSimpleFilter::addContent($content_, $provisionalKey_, $nodeContent_);
    			}
    			$content_ = XPathSimpleFilter::flattenGivenProvisionalKeys($content_,$provisionalKeys);
    	
    		} else {
    	
    			$provisionalKey_ = XPathSimpleFilter::getProvisionalKey($key_, $filter_, $provisionalKeys);
    			$xpath_ = XPathSimpleFilter::getXPathFromFilter($filter_);
    			if (is_array($xpath_)) {
    				// recursive case (LOL!)
    				$content_ = XPathSimpleFilter::filter($xml, $xpath_);
    			} else {
    				// base case
    				$content_ = $xml->xpath($xpath_);
    			}
    	    	
    		}
    		
    		$out_ = XPathSimpleFilter::addContent($out_, $provisionalKey_, $content_);
    			 
    	}	// foreach
    	
    	return $out_;
    	
    }
    
    
    /** Flatten resulting structure given two criteria: 1) no 1-sized arrays 2) take out provisional keys
     * @param unknown $out
     * @param unknown $provisionalKeys
     * @return filtered xmlElement nodes as flattened array structure
     */
    static private function flattenGivenProvisionalKeys($structure, $provisionalKeys) {
    	
	    // if there is only one key and it was provisional, it means we should flatten
	    // otherwise, if there are more than one keys, we need to keep keys regardless of them
	    // being provisional or not, to disambiguate
	    if (count($structure)==1 &&
	    		XPathSimpleFilter::isRegisteredAsProvisionalKey(key($structure),$provisionalKeys)) {
	    
	    	$out_ = end($structure);
	    	$out_ = XPathSimpleFilter::flattenAsNeeded($out_);
	    
	    } else {
	    	// keys, but still flatten single strings if needed
	    	$outFlattened_ = array();
	    	foreach ($structure as $k_ => $structure) {
	    		$outFlattened_[$k_] = XPathSimpleFilter::flattenAsNeeded($structure);
	    	}
	    	$out_ = $outFlattened_;
	    }
	    
	    return $out_;
    
    }
    
    
    static private function getProvisionalKey($key, $filter, &$provisionalKeys) {

    	if (is_string($key)) {
    		return $key;
    	}
    	
		$keyArray_ = explode("/", $filter);
    	$provisionalKey_ = end($keyArray_);
    	// the provisional key might contain xpath filters '[xxx = yyy]'
    	$indexOfFilter_ = strpos($provisionalKey_, '[');
    	if ($indexOfFilter_ > 0) {
    		$provisionalKey_ = substr($provisionalKey_, 0,$indexOfFilter_);
    	}
    	$provisionalKeys[$provisionalKey_] = 1;

    	return $provisionalKey_;
    	
    }
    
    
    static private function isRegisteredAsProvisionalKey($key, $provisionalKeys) {
    	return key_exists($key, $provisionalKeys);  
    }
    
    
    static private function getXPathFromFilter($filter) {

    	if (is_string($filter)) {
    		return $filter;
    	}
    	if (!is_array($filter)) {
    		throw new \Exception('Filter is neither an array nor a string');
    	}
    	
    	return $filter;
    	
    }
    
    
    static private function addContent($current, $key, $content) {
    	
    		if (key_exists($key, $current)) {
    			$currentContentBucket_ = $current[$key];
    			$currentContentBucket_ = XPathSimpleFilter::prepareForMergeOntoArray($currentContentBucket_);
    			$contentToBeAdded_ = XPathSimpleFilter::prepareForMergeOntoArray($content);
	    		$current[$key] = array_merge($currentContentBucket_, $contentToBeAdded_);
    			
    		} else {
    			$current[$key] = $content;
    		}
    		
    	return $current;
    	
    }

    /**
     * 
     * @param unknown $content
     * @return numeric array
     */
    static private function prepareForMergeOntoArray($content) {

    	if (is_array($content) && is_numeric(key($content))) {
    		return $content; // already prepared
    	}
  
    	return array($content);
    	
    }
    
    
    static private function flattenAsNeeded($content) {
    	
    	if (is_array($content) && is_numeric(key($content))) {
    		if (count($content)==1) {
    			$flattened_ = end($content);
     			if (!is_array($flattened_))	{
    				return XPathSimpleFilter::flattenAsNeeded($flattened_);
     			}
    		}
    	} elseif (is_a($content, 'SimpleXMLElement')) {
    		if (key_exists('@attributes', $content)) {	// flatten attribute
    			//$content[@attributes] = ['name_of_the_attribute' => 'value' ] 
    			$flattened_ = end($content);
    			if (isset($flattened_)) {
    				if (is_string($flattened_)) {	// flattening a node with attributes
    					return $flattened_;
    				}
    				return end($flattened_);
    			}
    		} else {	// flatten xml node
	    		$content_ = end($content);
    		}
    		return XPathSimpleFilter::flattenAsNeeded($content_);
    	}
    	
    	return $content;
    	
    }
    
}//CLASS

?>
