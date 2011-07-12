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
 * Google API extension to the Number helpers class, allows for additional
 * formatting through Google's database that otherwise would be unmaintainable
 * for your average user.
 *
 * @author   Chase "Syntaqx" Hutchins
 * @version  1.0
 * @package  Fuel
 * @package  Google
 * @category classes
 */
class Num {

	/**
	 * Convert one currency value to another
	 *
	 * @param   integer    The amount of currency to convert, such as 5.25
	 * @param   string     The currencies current type, defaults to USD
	 * @param   string     The type of currency to convert to, defaults to EUR
	 * @return  string     The converted currency
	 */
	public static function convert_currency($amount = 1, $from = 'USD', $to = 'EUR')
	{
		$amount = urlencode($amount);
		$from = urlencode($from);
		$to = urlencode($to);

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, 'http://www.google.com/ig/calculator?hl=en&q='.$amount.$from.'=?'.$to);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)');
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);

		$raw = curl_exec($curl);
		curl_close($curl);

		$data = explode('"', $raw);
		$data = explode(' ', $data[3]);

		return($data[0]);
	}
}

/* End of file num.php */