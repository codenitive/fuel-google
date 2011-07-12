<?php

/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2011 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * FuelPHP Google package implementation. This namespace controls all Google
 * package functionality, including multiple sub-namespaces for the various
 * tools.
 *
 * @author     Chase "Syntaqx" Hutchins
 * @version    1.0
 * @package    Fuel
 * @subpackage Google
 */
namespace Google;

/**
 * Wrapper for google's url shortening service, goo.gl.
 *
 * @author   Chase "Syntaqx" Hutchins
 * @version  1.0
 * @package  Fuel
 * @package  Google
 * @category classes
 */
class Googl {
	
	/**
	 * POSTS data to a URL in JSON format
	 *
	 * @param   string         The URL to POST to
	 * @param   string         The parameters to pass
	 * @param   string         Whether to return the result (true), or a boolean (false)
	 * @return  string         Whatever the server returned, or boolean if the above flag is set
	 */
	protected static function post($url, $parameters, $return = true)
	{
		$ch = curl_init();

		$curl_options = array(
			CURLOPT_HTTPHEADER     => array('Content-Type: application/json'),
			CURLOPT_URL            => $url,
			CURLOPT_POST           => count($parameters),
			CURLOPT_POSTFIELDS     => json_encode($parameters),
			CURLOPT_RETURNTRANSFER => ($return == true),
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false
		);

		curl_setopt_array($ch, $curl_options);

		$result = curl_exec($ch);

		curl_close($ch);

		if (!$return)
		{
			if (!curl_errno($ch))
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		return $result;
	}

	/**
	 * GETS the contents of a URL.
	 *
	 * @param   string         The URL to GET from.
	 * @return  string         Returns what the server outputted.
	 */
	protected static function get($url)
	{
		return file_get_contents($url);
	}

	/**
	 * Shortens the $url.
	 *
	 * @param   string         The URL to shorten.
	 * @param   string         Your google key, optional.
	 * @param   boolean        If TRUE, returns an array containing information, if FALSE, returns a boolean corresponding to the success of the operation.
	 * @return  array|boolean  Returns information about the operation.
	*/
	public static function shorten($url, $key = false, $return = true)
	{
		$post_url = 'https://www.googleapis.com/urlshortener/v1/url'.(($key !== false) ? '?key='.$key : '');

		$execute = static::post($post_url, array('longUrl' => $url), $return);
		$execute = json_decode($execute, true);

		if (isset($execute['error']))
		{
			if ($return)
			{
				return array(
					'error'    => true,
					'resource' => $execute['error']['location'],
					'reason'   => $execute['error']['reason']
				);
			}
			else
			{
				return false;
			}
		}
		else
		{
			if ($return)
			{
				return array(
					'error'    => false,
					'shortUrl' => $execute['id'],
					'longUrl'  => $execute['longUrl']
				);
			}
			else
			{
				return true;
			}
		}
	}

	/**
	 * Expand a short URL to its original state.
	 *
	 * @param   string         The shortened URL to expand.
	 * @param   string         Your google key, optional.
	 * @return  array          Returns information about the operation.
	 */
	public static function expand($url, $key = false)
	{
		$get_url = 'https://www.googleapis.com/urlshortener/v1/url'.(($key !== false) ? '?key='.$key.'&' : '?').'shortUrl='.urlencode($url);

		$execute = self::get($get_url);
		$execute = json_decode($execute, true);

		if (isset($execute['error']))
		{
			return array(
				'error'    => true,
				'resource' => $execute['error']['location'],
				'reason'   => $execute['error']['reason']
			);
		}
		else
		{
			$response = array(
				'error'    => false,
				'shortUrl' => $execute['id'],
				'status'   => $execute['status']
			);

			if ($response['status'] != 'REMOVED')
			{
				$response['longUrl'] = $execute['longUrl'];
			}

			return $response;
		}
	}
}

/* End of file googl.php */