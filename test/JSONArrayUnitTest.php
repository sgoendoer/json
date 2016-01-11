<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use sgoendoer\json\JSONObject;
use sgoendoer\json\JSONArray;

class JSONArrayUnitTest extends PHPUnit_Framework_TestCase
{
	public function test()
	{
		$jsonString = '[]';
		$jsonStringString = '["a"]';
		$jsonStringInteger = '[2]';
		$jsonStringBool = '[true]';
		$jsonStringNull = '[null]';
		$jsonStringObject = '[{}]';
		$jsonStringArray = '[[]]';
		
		// serialization // creation from string
		$this->assertEquals($jsonString, (new JSONArray())->__toString());
		
		$this->assertEquals(json_decode($jsonString), (new JSONArray())->toArray());
		$this->assertEquals(json_decode($jsonStringString), (new JSONArray($jsonStringString))->toArray());
		$this->assertEquals(json_decode($jsonStringInteger), (new JSONArray($jsonStringInteger))->toArray());
		$this->assertEquals(json_decode($jsonStringBool), (new JSONArray($jsonStringBool))->toArray());
		$this->assertEquals(json_decode($jsonStringNull), (new JSONArray($jsonStringNull))->toArray());
		$this->assertEquals(json_decode($jsonStringObject), (new JSONArray($jsonStringObject))->toArray());
		$this->assertEquals(json_decode($jsonStringArray), (new JSONArray($jsonStringArray))->toArray());
		
		// building arrays
		$a = new JSONArray();
		$a->put('x');
		$a->put(new JSONArray());
		
		$a1 = '["x",[]]';
		$this->assertEquals($a1, $a->write());
		
		// accessing parameters
		$this->assertEquals("x", $a->get(0));
		$this->assertEquals("[]", $a->get(1));
	}
}

?>