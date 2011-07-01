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
 * Google Dym (Did You Mean)
 * 
 * This class provides spell-checking via Google's "Did You Mean?" API service.
 * 
 * @author   Chase "Syntaqx" Hutchins
 * @version  1.0
 * @package  Fuel
 * @package  Google
 * @category classes
 */
class Dym {

	/**
	 * To change the language, switch the tld.
	 * 
	 * Example: "es" would return spanish translations.
	 * 
	 * @var   string
	 */
	public static $tld = "com"; // Default is COM, if you want to look for example Spanish suggestion, use "es"  

	/**
	 * Request a translation
	 * 
	 * @param   string     The string to translate
	 * @return  string     The potential translation of the string
	 */
	public static function search($query)
	{
		$url = 'http://www.google.'.static::$tld.'/m?q='.str_replace(' ', '+', $query);  // URI of Google Did You Mean
		$html = static::curl($url); // Fetch HTML data

		// Extract suggestion and return it if any
		preg_match('#spell=1(.*?)>(.*?)</a>#is', $html, $matches);

		if($matches)
		{
			$spell = strip_tags($matches[2]); 
			return $spell;
		}
	}

	/**
	 * Curl fetching from the Google server
	 * 
	 * @param   string     The URL to request
	 * @param   array      Additional headers, if desired
	 * @return  string     The returned data
	 */
	private static function curl($url, $headers = array())
	{
		$headers[]  = "User-Agent:Mozilla/5.0 (Linux; U; Android 2.2.1; en-us; device Build/FRG83) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Safari/533.1";
		$headers[]  = "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
		$headers[]  = "Accept-Language:en-us,en;q=0.5";
		$headers[]  = "Accept-Encoding:gzip,deflate";
		$headers[]  = "Accept-Charset:ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$headers[]  = "Keep-Alive:115";
		$headers[]  = "Connection:keep-alive";
		$headers[]  = "Cache-Control:max-age=0";

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_ENCODING, "gzip");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$data = curl_exec($curl);

		curl_close($curl);

		return $data;
	}
}

/* End of file dym.php */