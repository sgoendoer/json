# json

JSON for PHP

## Setup

    ./composer install sgoendoer/json

or configure your composer.json accordingly

    "require" : { "sgoendoer/json": "0.2.*" }

## Usage

    use sgoendoer\json\JSONObject;
    use sgoendoer\json\JSONArray;
    
    $jsonObject = new JSONObject();
    
    $jsonObject->put("key", "value");
    $jsonObject->put("array", new JSONArray());
    $jsonObject->put("object", new JSONObject('{"a", 1}'));
    
    echo $jsonObject->get("key");
    echo $jsonObject->write();
    
    $phpStyleJSON = $jsonObject->toStdClass();
    echo $phpStyleJSON->key;
    echo json_encode($phpStyleJSON);

## JSONObject

* public function __construct($param = NULL, $keys = NULL)
* public function get($key)
* public function put($key, $value)
* public function opt($key)
* public function has($key)
* public function remove($key)
* public function keys()
* public static function quote($string)
* public static function stringToValue($string)
* public static function valueToString($value)
* public static function numberToString($number = NULL)
* public static function testValidity($object)
* public function toJSONArray($names = NULL)
* public function toStdClass()
* public function write()
* public function __toString()

## JSONArray

* public function __construct($param = NULL)
* public function get($key)
* public function put($value, $key = NULL)
* public function opt($key)
* public function remove($index)
* public function keys()
* public function toJSONObject($names)
* public static function testValidity($object)
* public function toArray()
* public function write()
* public function __toString()
