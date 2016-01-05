<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use sgoendoer\json\JSONObject;
use sgoendoer\json\JSONArray;

class JSONObjectUnitTest extends PHPUnit_Framework_TestCase
{
	public function test()
	{
		$jsonString = '{}';
		
		$this->assertEquals($jsonString, (new JSONObject())->__toString());
		$this->assertEquals(json_decode($jsonString), (new JSONObject())->toStdClass());
	}
}

?>