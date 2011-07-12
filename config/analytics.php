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

return array(

	/**
	 * Google Analytics Login Email Address
	 * 
	 * @var   string
	 */
	'email'	           => '',

	/**
	 * Password for the above mentioned email address
	 * 
	 * @var   string
	 */
	'password'	       => '',

	/**
	 * Default Website Profile ID (Usually referred to as a UA code)
	 * 
	 * If your Google Analytics account domain is set up the same as your
	 * base url, this will automatically be determined, when left as null
	 * 
	 * @var   string
	 */
	'web_profile_id'    => '',

	/**
	 * Default Profile ID
	 * 
	 * If your Google Analytics account contains a valid entry for your
	 * web_profile_id, this value will automatically be determined, when left as
	 * null
	 * 
	 * @var   string
	 */
	'profile_id'        => '',
);

/* End of file analytics.php */