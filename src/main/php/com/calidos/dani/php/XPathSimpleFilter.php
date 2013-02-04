<?php

namespace com\calidos\dani\php;
use Exception;

/**
 *
 * @author daniel giribet
 */
class XPathSimpleFilter {

    /**

     *
     * @return xxxx
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
    		foreach ($a as $key_ => $filter_) {
    			$name_  = XPathSimpleFilter::getKey($key_, $filter_);
    			$xpath_ = XPathSimpleFilter::getXPathFromFilter($filter_);
    			if (is_array($xpath_)) {
    				// recursive case (LOL!)
    				$outRecurse_ = XPathSimpleFilter::filter($xml, $xpath_); 
    				$out_ = XPathSimpleFilter::addContentToOutput($out_, $name_, $outRecurse_);
    			} else {
					// base case
    				$content_ = $xml->xpath($xpath_);
    				$out_ = XPathSimpleFilter::addContentToOutput($out_, $name_, $content_);
    			}
    		}
    	}
    	
    	if (count($out_)==1) {
    		$key_ = key($out_);
    		if (is_string($key_)) {
    			return $out_;
    		} else {
    			return end($out_);
    		}
    	}
    	
    	return $out_;
    }

    
    static private function getKey($key_, $filter) {
    	if (!is_string($key_)) {
			return 0;
    		//return end(explode("/", $filter));
    	}
    	return $key_;
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
    
    static private function addContentToOutput($existingContent, $name, $content) {

    	// adding an array
    	if (is_array($content)) {
    		if (count($content)==1) {
    			$contentToBeAdded_ = $content[0]; // flatten to string
    		} else {
    			$contentToBeAdded_ = $content;
    		}
    	} elseif (is_string($content)) {	// adding an string
    		$contentToBeAdded_ = $content;
    	}
    	 
    	if (is_string($existingContent)) {
    		if (is_string($contentToBeAdded_)) {
    			$output_ = array($existingContent, $contentToBeAdded_);
    		} else { // append to existing array
    			$output_ = $existingContent;
    			$output_[] = $contentToBeAdded_;
    		}
    		
    	} elseif (is_array($existingContent)) {
    		if (is_string($name)) {
    			if (key_exists($name, $existingContent)) {
    				$output_ = $existingContent;
    			} else {
    				
    			}
    		} else {	// adding without a name
    			
    		}
    		
    	} 	
    	return $output_;
    }
    
    static private function addContentWithName($existingContent, $name, $contentToBeAdded) {
    	
    }

}//CLASS

?>
