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
		$jsonStringInteger = '{"1": 1}';
		
		$this->assertEquals($jsonString, (new JSONObject())->__toString());
		$this->assertEquals(json_decode($jsonString), (new JSONObject())->toStdClass());
		
		$this->assertEquals(json_decode($jsonStringString), (new JSONObject($jsonStringString))->toStdClass());
		$this->assertEquals(json_decode($jsonStringInteger), (new JSONObject($jsonStringInteger))->toStdClass());
		
		$this->assertEquals('\\\n', JSONObject::quote('\n'));
		$this->assertEquals('\/', JSONObject::quote('/'));
		$this->assertEquals('\\\\', JSONObject::quote('\\'));
	}
}

?>