<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use sgoendoer\json\JSONObject;
use sgoendoer\json\JSONArray;

class JSONObjectUnitTest extends PHPUnit_Framework_TestCase
{
	public function test()
	{
		$jsonString = '{}';
		$jsonStringString = '{"a": "a"}';
		$jsonStringInteger = '{"b": 1}';
		$jsonStringBool = '{"c": true}';
		$jsonStringNull = '{"d": null}';
		$jsonStringObject = '{"e": {}}';
		$jsonStringArray = '{"f": []}';
		
		// serialization // creation from string
		$this->assertEquals($jsonString, (new JSONObject())->__toString());
		$this->assertEquals(json_decode($jsonString), (new JSONObject())->toStdClass());
		$this->assertEquals(json_decode($jsonStringString), (new JSONObject($jsonStringString))->toStdClass());
		$this->assertEquals(json_decode($jsonStringInteger), (new JSONObject($jsonStringInteger))->toStdClass());
		$this->assertEquals(json_decode($jsonStringBool), (new JSONObject($jsonStringBool))->toStdClass());
		$this->assertEquals(json_decode($jsonStringNull), (new JSONObject($jsonStringNull))->toStdClass());
		$this->assertEquals(json_decode($jsonStringObject), (new JSONObject($jsonStringObject))->toStdClass());
		$this->assertEquals(json_decode($jsonStringArray), (new JSONObject($jsonStringArray))->toStdClass());
		
		// escaping / quoting
		$this->assertEquals('\\\n', JSONObject::quote('\n'));
		$this->assertEquals('\/', JSONObject::quote('/'));
		$this->assertEquals('\\\\', JSONObject::quote('\\'));
		
		// building objects
		$a = new JSONObject();
		$a->put('arr', new JSONArray());
		$a->get('arr')->put('value');
		
		$a1 = '{"arr":["value"]}';
		$this->assertEquals($a1, $a->write());
		
		$b = (new JSONObject())->put('obj', new JSONObject('{"a": "1"}'));
		$b->put("b", 2);
		
		$b1 = '{"obj":{"a":"1"},"b":2}';
		$this->assertEquals($b1, $b->write());
		
		// accessing parameters
		$this->assertEquals("1", $b->get('obj')->get("a"));
		$this->assertEquals(2, $b->get('b'));
	}
}

?>