PHP-XPathSimpleFilter
=====================

This is a simple PHP class to help in filtering SimpleXML content, using convention and configuration to a large extent.

Sample code
-----------
Check out the tests in 'src/test/php' using the following XML


	<?xml version="1.0" encoding="UTF-8"?>
	<yummy>
		<food>
			<name>Pa amb tomata</name>
			<price>1.00EUR</price>
			<calories>222</calories>
		</food>
		<food>
			<name>Pa amb tomata torrat</name>
			<price>2.00EUR</price>
			<calories>200</calories>
		</food>
		<food>
			<name>Crema catalana</name>
			<price>3.00EUR</price>
			<calories>100</calories>
		</food>
		<food>
			<name>Samfaina</name>
			<price>4.00EUR</price>
			<calories>200</calories>
		</food>
		<food>
			<name>Fuet</name>
			<price>1.00EUR</price>
			<calories>350</calories>
		</food>
	</yummy>


The class has one static method: 
	XPathSimpleFilter::filter($simpleXml, $xpathArray);

Which will filter the xml using the array of xpaths, that can be nested and tagged.

Examples
--------

		XPathSimpleFilter::filter($xml, array());

Will return identity.

		XPathSimpleFilter::filter($xml, array('/yummy/food'));

Will return an array of food nodes.

		$a_ = array('/yummy/food[position() = 1]/name');
		$name_ = XPathSimpleFilter::filter($xml, $a_);

Will return directly the content of the node.

		$a_ = array('/foobar');
		$empty_ = XPathSimpleFilter::filter($xml, $a_);

Will return an empty array.

		$a_ = array('/yummy/food[position() = 1]/name',
					'/yummy/food[position() = 2]/name');
		$names_ = XPathSimpleFilter::filter($xml, $a_);

Will return two name nodes in an array, neat.

		$a_ = array('food0' => '/yummy/food[position() = 1]/name',
				 	'food1' => '/yummy/food[position() = 2]/name');
		$names_ = XPathSimpleFilter::filter($xml, $a_);

Will return an array with two keys ('food0' and 'food1') with one 'name' node each.

		$a_ = array('foodNames' => '/yummy/food/name',
				'foodCalories' => '/yummy/food/calories');
		$foods_ = XPathSimpleFilter::filter($xml, $a_);

Will return array with keys 'foodNames' and 'foodCalories' having specified arrays of content.

		$a_ = array('foo' => array('/yummy/food[position() = 1]/name',
									'/yummy/food[position() = 2]/name')
		);
		$names_ = XPathSimpleFilter::filter($xml, $a_);

Returns an array with key 'foo' containing a linear array of two name nodes.


		$a_ = array('foodInfo' => array('foodNames' => '/yummy/food/name',
										'foodCalories' => '/yummy/food/calories'),
					'prices' => '/yummy/food/price');
		$foodMultiple_ = XPathSimpleFilter::filter($xml, $a_);

Just a more complex structure.


See LICENSE for the license.
Copyright [2013] [Daniel Giribet]

