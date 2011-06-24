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
	 * Public Key
	 * 
	 * This key is used within your JavaScript codes, and served to your users.
	 * 
	 * @var   string
	 */
	'public_key'	=> '',
	
	/**
	 * Private Key
	 * 
	 * This key is used when communicating between reCAPTCHA and your server. Be
	 * sure this is kept secret!
	 * 
	 * @var   string
	 */
	'private_key'	=> '',
	
	/**
	 * The reCAPTCHA server URL's
	 */
	'api_server'	      => 'http://www.google.com/recaptcha/api',
	'api_secure_server'   => 'https://www.google.com/recaptcha/api',
	'verify_server'       => 'www.google.com',

);

/* End of file recaptcha.php */