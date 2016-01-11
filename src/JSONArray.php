<?php namespace sgoendoer\json;

use sgoendoer\json\JSONObject;
use sgoendoer\json\JSONException;

/**
 * PHP JSONArray
 * version 20160111
 *
 * author: Sebastian Goendoer
 * copyright: Sebastian Goendoer <sebastian.goendoer@rwth-aachen.de>
 */
class JSONArray
{
	/**
	 * The array where the JSONArray's properties are kept.
	 */
	private $map;

	/**
	 * Construct a JSONArray.
	 * 
	 * @param $param An optional parameter to create the JSONArray from
	 */
	public function __construct($param = NULL)
	{
		$this->map = array();
		
		if($param === NULL)
		{}
		// if param == JSON Object => create JSONArray from JSONObjects parameters
		elseif($param instanceof JSONObject)
		{
			foreach($param->keys() as $key)
			{
				$this->put($param->get($key));
			}
		}
		// if param == JSON Object => create copy
		elseif($param instanceof JSONArray)
		{
			foreach($param->keys() as $key)
			{
				$this->put($param->get($key));
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
		elseif(is_array($param))
		{
			foreach($param as $value)
			{
				if(gettype($value) == 'object')
				{
					$this->put(new JSONObject($value));
				}
				elseif(gettype($value) == 'array')
				{
					$this->put(new JSONArray($value));
				}
				else
				{
					$this->put($value);
				}
			}
		}
		elseif(gettype($param) == 'string')
		{
			$tmp = json_decode($param);
			
			if(json_last_error() != JSON_ERROR_NONE)
				throw new JSONException(json_last_error_msg());
			
			if(!is_array($tmp))
				throw new JSONException('A JSONArray text must start with "["');
			
			foreach($tmp as $value)
			{
				$this->put($value);
			}
		}
		else
		{
			throw new JSONException("JSONArray initial value should be a string or array.");
		}
	}
	
	/**
	 * Get the value associated with a key.
	 *
	 * @param key A key string.
	 * @return The object associated with the key.
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
			throw new JSONException('JSONArray[' . self::quote($key) . '] not found.');
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
	 * Put a key/value pair in the JSONArray.
	 *
	 * @param value The value to put
	 * @param key An optional key
	 * @return this
	 */
	public function put($value, $key = NULL)
	{
		// disallow non-integer keys
		if($key != NULL && gettype($key) != 'integer')
		{
			throw new JSONException('Key value must be an integer value');
		}
		
		if(gettype($value) == 'object' && !($value instanceof JSONObject) && !($value instanceof JSONArray))
		{
			$value = new JSONObject($value);
		}
		elseif(gettype($value) == 'array')
		{
			$value = new JSONArray($value);
		}
		
		if($value !== NULL) // "", 0, 0.0, false, etc evaluate to NULL
		{
			$this->testValidity($value);
			
			if($key != NULL && in_array($key, $this->map))
				$this->map[$key] = $value;
			else
				$this->map[] = $value;
		}
		else
		{
			throw new JSONException('Null value');
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
	 * @return An object which is the value, or NULL if there is no value.
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
	 * Removes an index
	 *
	 * @param index The index of the element to be removed.
	 * @return The value that was associated with the index, or null if there was no value.
	 */
	public function remove($index)
	{
		$value = $this->opt($index);
		unset($this->map[$index]);
		
		return $value;
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
	 * Produces a JSONObject from the JSONArray
	 *
	 * @param names An array containing a list of key strings. These will be paired with the values. If $names contains
	 * less values than the source JSONArray, remaining values will be ommitted.
	 * @return A JSONObject, or null if there are no names or if this JSONArray has no values.
	 */
	public function toJSONObject($names)
	{
		if(!is_array($names))
			throw new JSONException('Provided value must be an array');
		
		if($names == NULL || count($names) == 0 || $this->length() == 0)
		{
			return NULL;
		}
		
		$jsonObject = new JSONObject();
		
		foreach($this->map as $index => $value)
		{
			$jsonObject->put($value, $names[$index]);
		}
		
		return $jsonObject;
	}
	
	/**
	 * Throw an exception if the object is not a valid JSON value
	 *
	 * @param object The object to test.
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
	 * returns the JSONArray as a PHP-style array.
	 * 
	 * @return The JSONArray as a regular array
	 */
	public function toArray()
	{
		return json_decode($this->write());
	}
	
	/**
	 * Write the contents of the JSONArray to a string
	 *
	 * @return string
	 */
	public function write()
	{
		$returnstring = '[';
		
		foreach($this->map as $value)
		{
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
		
		$returnstring .= ']';
		
		return $returnstring;
	}
	
	public function __toString()
	{
		try
		{
			return $this->write();
		}
		catch (\Exception $e)
		{
			// __toString must not throw any exceptions!
			//die($e->getMessage());
		}
	}
}

?>