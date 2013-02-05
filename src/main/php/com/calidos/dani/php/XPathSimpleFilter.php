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

/**
 *
 * @author daniel giribet
 */
class XPathSimpleFilter {

    /**

     *
     * @return filtered xml
     */
    static public function filter($xml, $a) {

    	if (!is_array($a) or !isset($a)) {
    		throw new \Exception('Not passed an array as filter');
    	}
    	
    	if (empty($a)) {
    		// trivial case return identity (XMLSimpleElement)
    		return $xml;	
    	} else {
    		$out_ = array();
    		$provisionalKeys_ = array();
    		foreach ($a as $key_ => $filter_) {
  
    			$provisionalKey_ = XPathSimpleFilter::getProvisionalKey($key_, $filter_);
    			if (XPathSimpleFilter::isProvisionalKey($key_)) {
					$provisionalKeys_[$provisionalKey_] = 1;   				
    			}
    			$xpath_ = XPathSimpleFilter::getXPathFromFilter($filter_);
    			if (is_array($xpath_)) {
    				// recursive case (LOL!)
    				$outRecurse_ = XPathSimpleFilter::filter($xml, $xpath_); 
    				$out_ = XPathSimpleFilter::addContent($out_, $provisionalKey_, $outRecurse_);
    			} else {
					// base case
    				$content_ = $xml->xpath($xpath_);
    				$out_ = XPathSimpleFilter::addContent($out_, $provisionalKey_, $content_);
    			}
    		
    		}	// foreach
    		
    		// if there is only one key and it was provisional, it means we should flatten
    		// otherwise, if there are more than one keys, we need to keep keys regardless of them
    		// being provisional or not, to disambiguate
    		if (count($out_)==1 && key_exists(key($out_), $provisionalKeys_)) {	
    			$out_ = end($out_);
		 		$out_ = XPathSimpleFilter::flattenAsNeeded($out_);
    		} else {
    			// keys, but still flatten single strings if needed
    			$outFlattened_ = array();
    			foreach ($out_ as $k => $content_) {
    				$outFlattened_[$k] = XPathSimpleFilter::flattenAsNeeded($content_);
    			}
    			$out_ = $outFlattened_;
    		}
    		
    		return $out_;    		
    	}
    	
    	

    }

    
    static private function getProvisionalKey($key, $filter) {
    	if (is_string($key)) {
    		return $key;
    	}
    	return end(explode("/", $filter));
    }
    
    
    static private function isProvisionalKey($key) {
    	return !is_string($key);
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
    			if (!is_array($content)) {
    				$current[$key] = array_merge($current[$key], array($content));
    			} else {			
	    			$current[$key] = array_merge($current[$key], $content);
    			}
    		} else {
    			$current[$key] = $content;
    		}
    	return $current;
    }

    static private function concatenate($content, $newContentArray) {
    	$content = array($content);
   		return array_merge(array($content),$newContentArray);
    }
    
    static private function flattenAsNeeded($content) {
    	if (is_array($content)) {
    		if (count($content)==1) {
    			$flattened_ = end($content);
    			if (!is_array($flattened_))	{
    				return $flattened_;
    			}
    		}
    	}
    	return $content;
    }
    
}//CLASS

?>
