<?php namespace sgoendoer\json;

/**
 * JSON Tools for PHP
 * version 20160124
 *
 * author: Sebastian Goendoer
 * copyright: Sebastian Goendoer <sebastian.goendoer@rwth-aachen.de>
 */

class JSONTools
{
	/**
	 * returns a string representation of the last JSON error
	 * 
	 * @return string The error string. NULL, if no error was found
	 */
	public static function getJSONErrorAsString()
	{
		switch(json_last_error())
		{
			case JSON_ERROR_NONE:
				$error =  NULL;
			break;
			
			case JSON_ERROR_DEPTH:
				$error = 'The maximum stack depth has been exceeded.';
			break;
				
			case JSON_ERROR_STATE_MISMATCH:
				$error = 'Invalid or malformed JSON.';
			break;

			case JSON_ERROR_CTRL_CHAR:
				$error = 'Control character error, possibly incorrectly encoded.';
			break;

			case JSON_ERROR_SYNTAX:
				$error = 'Syntax error, malformed JSON.';
			break;

			// PHP >= 5.3.3
			case JSON_ERROR_UTF8:
				$error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
			break;

			// PHP >= 5.5.0
			case JSON_ERROR_RECURSION:
				$error = 'One or more recursive references in the value to be encoded.';
			break;

			// PHP >= 5.5.0
			case JSON_ERROR_INF_OR_NAN:
				$error = 'One or more NAN or INF values in the value to be encoded.';
			break;

			case JSON_ERROR_UNSUPPORTED_TYPE:
				$error = 'A value of a type that cannot be encoded was given.';
			break;

			default:
				$error = NULL;
			break;
		}
		
		return $error;
	}
	
	/**
	 * Determines if a string contains a valid, decodable JSON object.
	 * 
	 * @param $string string to be tested
	 * 
	 * @return true, if given string contains valid JSON, else false
	 */
	public static function containsValidJSON($string)
	{
		try
		{
			$result = json_decode($string);
		}
		catch(\Exception $e)
		{}
		
		if(self::getJSONError() === NULL)
			return true;
		else
			return false;
	}
}

?>