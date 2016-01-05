<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use sgoendoer\json\JSONObject;
use sgoendoer\json\JSONArray;

class JSONArrayUnitTest extends PHPUnit_Framework_TestCase
{
	public function test()
	{
		$jsonString = '[]';
		
		$this->assertEquals($jsonString, (new JSONArray())->__toString());
		$this->assertEquals(json_decode($jsonString), (new JSONArray())->toArray());
	}
}

?>