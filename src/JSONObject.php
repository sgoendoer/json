<?php namespace sgoendoer\json;

use sgoendoer\json\JSONArray;
use sgoendoer\json\JSONException;

class JSONObject
{
	/**
	 * The array where the JSONObject's properties are kept.
	 */
	private $map;
	
	/**
	 * Construct a JSONObject from a parameter.
	 * 
	 * @param $param The parameter to create a JSONObject from.
	 * @param $keys An optional array of keys to use for JSONObject creation
	 * @throws JSONException if the provided parameter is not a valid value.
	 */
	public function __construct($param = NULL, $keys = NULL)
	{
		$this->map = array();
		
		// if param == NULL => new empty object
		if($param == NULL)
		{}
		// if param == JSON Object => create copy
		elseif($param instanceof JSONObject)
		{
			foreach($param->keys() as $key)
			{
				$this->put($param->get((string) $key), (string) $key);
			}
		}
		// if param == JSONArray => create JSONObject
		elseif(($param instanceof JSONArray))
		{
			if($keys == NULL)
				$keys = $param->keys();
			
			foreach($keys as $key)
			{
				$this->put($param->opt($key), (string) $key);
			}
		}
		// if param == assoc array => create JSONObject
		elseif(is_array($param))
		{
			foreach($param as $key => $value)
			{
				if(gettype($value) == 'string')
				{
					$this->put($value, (string) $key);
				}
				elseif(gettype($value) == 'object')
				{
					$this->put(new JSONObject($value), (string) $key);
				}
				elseif(gettype($value) == 'array')
				{
					$this->put(new JSONArray($value), (string) $key);
				}
				elseif(gettype($value) == 'bool')
				{
					$this->put($value, (string) $key);
				}
				elseif(gettype($value) == 'int' || gettype($value) == 'double')
				{
					$this->put($value, (string) $key);
				}
				else
				{
					throw new JSONException('Value [' . self::quote($value) . '] is not a valid value');
				}
			}
		}
		// if param == object => create object with keys
		elseif(gettype($param) == 'object')
		{
			if($keys == NULL || !is_array($keys))
				$keys = array_keys(get_object_vars($param));
			
			// case non-empty object
			if(count($keys) != 0)
			{
				foreach(get_object_vars($param) as $key => $value)
				{
					if(in_array((string) $key, $keys))
					{
						
						if(gettype($value) == 'object')
						{
							$this->put(new JSONObject($value), (string) $key);
						}
						elseif(gettype($value) == 'array')
						{
							$this->put(new JSONArray($value), (string) $key);
						}
						else
						{
							$this->put($value, (string) $key);
						}
					}
				}
			}
		}
		// if param == string => deserialize and work with result
		elseif(gettype($param) == 'string')
		{
			$tmp = json_decode($param);
			
			if(json_last_error() != JSON_ERROR_NONE)
				throw new JSONException(json_last_error_msg());
			
			if(gettype($tmp) != 'object')
				throw new JSONException('Provided string does not contain a valid JSON Object');
			
			foreach($tmp as $key => $value)
			{
				if(gettype($value) == 'string')
				{
					$this->put($value, (string) $key);
				}
				elseif(gettype($value) == 'object')
				{
					$this->put(new JSONObject($value), (string) $key);
				}
				elseif(gettype($value) == 'array')
				{
					$this->put(new JSONArray($value), (string) $key);
				}
				elseif(gettype($value) == 'bool')
				{
					$this->put($value, (string) $key);
				}
				elseif(gettype($value) == 'int' || gettype($value) == 'double')
				{
					$this->put($value, (string) $key);
				}
				else
				{
					throw new JSONException('Value [' . self::quote($value) . ']is not a valid value');
				}
			}
		}
	}
	
	/**
	 * Get the value associated with a key.
	 *
	 * @param key A key string.
	 * @return The object associated with the key.
	 * @throws JSONException if the key is not found.
	 */
	public function get($key)
	{
		if($key == NULL || $key == '')
		{
			throw new JSONException('Null key.');
		}
		
		$object = $this->opt($key);
		
		if($object == NULL)
		{
			throw new JSONException('JSONObject[' . self::quote($key) . '] not found.');
		}
		
		return $object;
	}
	
	// public function getInt(){}
	// public function getFloat(){}
	// public function getString(){}
	// public function getBoolean(){}
	// public function getJSONArray(){}
	// public function getJSONObject(){}
	
	/**
	 * Put a key/value pair in the JSONObject.
	 *
	 * @param value A boolean which is the value.
	 * @param key A key string.
	 * @return this.
	 * @throws JSONException If the key is null or the value is not a valid value
	 */
	public function put($value, $key)
	{
		if(gettype($value) == 'object' && !($value instanceof JSONObject) && !($value instanceof JSONArray))
		{
			$value = new JSONObject($value);
		}
		if($key == '' || $key == NULL)
			throw new JSONException("Null key.");
		
		if($value !== NULL) // "", 0, 0.0, false, etc evaluate to NULL
		{
			$this->testValidity($value);
			$this->map[$key] = $value;
		}
		else
		{
			$this->remove($key);
		}
		
		return $this;
	}
	
	// public function putInt(){}
	// public function putFloat(){}
	// public function putString(){}
	// public function putBoolean(){}
	// public function putJSONArray(){}
	// public function putJSONObject(){}
	
	/**
	 * Get an optional value associated with a key.
	 *
	 * @param key A key string.
	 * @return An object which is the value, or null if there is no value.
	 */
	public function opt($key)
	{
		if($key == NULL || $key == '' || !array_key_exists($key, $this->map))
		{
			return NULL;
		}
		else
		{
			return $this->map[$key];
		}
	}
	
	// public function optInt(){}
	// public function optFloat(){}
	// public function optString(){}
	// public function optBoolean(){}
	// public function optJSONArray(){}
	// public function optJSONObject(){}
	
	/**
	 * Determine if the JSONObject contains a specific key.
	 *
	 * @param key: A key string.
	 * @return true if the key exists in the JSONObject.
	 */
	public function has($key)
	{
		return array_key_exists($key, $this->map);
	}
	
	/**
	 * Remove a name and its value, if present.
	 *
	 * @param key The name to be removed.
	 * @return The value that was associated with the name, or null if there was no value.
	 */
	public function remove($key)
	{
		if(array_key_exists($key, $this->map))
		{
			$tmp = $this->map[$key]; 
			unset($this->map[$key]);
			return $tmp;
		}
		else
			return NULL;
	}
	
	/**
	 * Get an array of the keys of the JSONObject.
	 *
	 * @return An array with the keys
	 */
	public function keys()
	{
		return array_keys($this->map);
	}
	
	/**
	 * Produce a string in double quotes with backslash sequences in all the right places. A backslash will be inserted 
	 * within </, producing <\/, allowing JSON text to be delivered in HTML. In JSON text, a string cannot contain a 
	 * control character or an unescaped quote or backslash.
	 *
	 * @param string A string
	 * @return A string correctly formatted for insertion in a JSONObject or JSONArray.
	 */
	public static function quote($string)
	{
		// TODO escape all the chars
		return $string;
	}
	
	/**
	 * Try to convert a string into a number, boolean, or null. If the string can't be converted, return the string.
	 *
	 * @param string A String.
	 * @return A simple JSON value.
	 */
	public static function stringToValue($string)
	{
		if($string == '')
		{
			return $string;
		}
		if(is_float($string))
		{
			return (float) $string;
		}
		if(is_int($string))
		{
			return (int) $string;
		}
		if(strtolower($string) == 'true')
		{
			return true;
		}
		if(strtolower($string) == 'false')
		{
			return false;
		}
		if(strtolower($string) == 'null')
		{
			return NULL;
		}
		
		return $string;
	}
	
	/**
	 * Make a JSON text of an Object value. If the value is an array, then a JSONArray will be made from it and its
	 * write() method will be called. If the value is an associative array or an object, then a JSONObject will be made 
	 * from it and its write() method will be called. If the value is an object, the value's toString method will be 
	 * called, and the result will be quoted.
	 * <p>
	 * Warning: This method assumes that the data structure is acyclical.
	 *
	 * @param value The value to be serialized.
	 * @return a printable, displayable, transmittable representation of the object, beginning with <code>{</code>&nbsp;
	 * <small>(left brace)</small> and ending with <code>}</code>&nbsp;<small>(right brace)</small>.
	 * @throws JSONException If the value cannot be coerced to a string value.
	 */
	public static function valueToString($value)
	{
		if($value == NULL)
		{
			return 'null';
		}
		
		if(is_numeric($value))
		{
			return self::numberToString($value);
		}
		
		if(is_string($value))
		{
			return $value;
		}
		
		if($value instanceof JSONObject	|| $value instanceof JSONArray)
		{
			return $value->write();
		}
		
		if(is_array($value))
		{
			if(self::isAssoc($value))
				return (new JSONObject($value))->write();
			else
				return (new JSONArray($value))->write();
		}
		
		if(is_object($value))
			return (new JSONObject($value))->write();
		
		throw new JSONException('Provided value cannot be coerced to a string');
	}
	
	/**
	 * Determines if an value is an associative array
	 * 
	 * @param $value The value to check
	 * @return true, if the value is an associative array, else false
	 */
	private static function isAssoc($value)
	{
		if(!is_array($value)) return false;
		return array_keys($value) !== range(0, count($value) - 1);
	}
	
	/**
	 * Produce a string from a number.
	 *
	 * @param number A number
	 * @return A string.
	 * @throws JSONException If $number is a non-finite number.
	 */
	public static function numberToString($number = NULL)
	{
		if($number == NULL)
		{
			throw new JSONException('Null pointer');
		}
		
		self::testValidity($number);
		
		// Shave off trailing zeros and decimal point, if possible.
		$number = (string) $number;
		if(strpos($number, '.') > 0 && strpos($number, 'e') < 0 && strpos($number, 'E') < 0)
		{
			while(substr($number, -1) == '0')
			{
				$number = substr(0, strlen($number) - 1);
			}
			
			if(substr($number, -1) == '.')
			{
				$number = substr(0, strlen($number) - 1);
			}
		}
		
		return $number;
	}
	
	/**
	 * Throw an exception if the object is not a valid JSON value
	 *
	 * @param object The object to test.
	 * @throws JSONException If the object is not a valid JSON value
	 */
	public static function testValidity($object)
	{
		switch(gettype($object))
		{
			case 'integer':
			case 'double': // is returned when $object is a float
				if(is_nan($object) || is_infinite($object))
					throw new JSONException('JSON does not allow non-finite numbers.');
			break;
			
			case 'boolean':
			break;
			
			case 'string':
				// TODO check for unescaped chars
			break;
			
			case 'NULL':
			break;
			
			case 'array':
				throw new JSONException('JSON does not allow values of type ' . gettype($object));
			break;
			
			case 'object':
				if(!($object instanceof JSONObject) && !($object instanceof JSONArray))
					throw new JSONException('JSON does not allow values of type ' . gettype($object));
			break;
			
			case 'resource':
			case 'unknown type':
			default:
				throw new JSONException('JSON does not allow values of type ' . gettype($object));
			break;
		}
	}
	
	/**
	 * Produce a JSONArray containing the values of the members of this JSONObject.
	 *
	 * @param names A JSONArray containing a list of key strings. This determines the sequence of the values in the result.
	 * @return A JSONArray of values.
	 * @throws JSONException If any of the values are non-finite numbers.
	 */
	public function toJSONArray($names = NULL)
	{
		if($names == NULL || count($names) == 0)
		{
			return NULL;
		}
		
		$ja = new JSONArray();
		
		foreach($this->map as $key => $value)
		{
			$ja->put($this->opt($key));
		}
		
		return $ja;
	}
	
	/**
	 * returns the JSONObject as a PHP-style StdClass object.
	 * 
	 * @return The JSONObject as a StdClass object
	 */
	public function toStdClass()
	{
		return json_decode($this->write());
	}
	
	/**
	 * Returns the contents of the JSONObject as a JSONString. For compactness, no whitespace is added.
	 * <p>
	 * Warning: This method assumes that the data structure is acyclical.
	 *
	 * @return string
	 */
	public function write()
	{
		$returnstring = '{';
		
		foreach($this->map as $key => $value)
		{
			$returnstring .= '"' . $key . '":';
			
			if($value instanceof JSONObject || $value instanceof JSONArray)
				$returnstring .= $value->write();
			elseif(gettype($value) == 'integer' || gettype($value) == 'double')
				$returnstring .= $value;
			elseif(gettype($value) == 'boolean')
				$returnstring .= (($value) ? 'true' : 'false');
			elseif(gettype($value) == 'NULL')
				$returnstring .= 'null';
			elseif(gettype($value) == 'string')
				$returnstring .= '"' . $value . '"';
			else
				throw new JSONException('Invalid type ' . gettype($value));
			
			if($value !== end($this->map)) $returnstring .= ',';
		}
		
		$returnstring .= '}';
		
		return $returnstring;
	}
	
	/**
	 * Returns the contents of the JSONObject as a JSONString. For compactness, no whitespace is added.
	 * In case an Exception is thrown, the execution is halted.
	 * <p>
	 * Warning: This method assumes that the data structure is acyclical.
	 *
	 * @return string
	 */
	public function __toString()
	{
		try
		{
			return $this->write();
		}
		catch (\Exception $e)
		{
			//die($e->getMessage());
		}
	}
}

?>