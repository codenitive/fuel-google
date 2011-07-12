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
namespace Google\Recaptcha;

/**
 * A RecaptchaResponse is returned from recaptcha_check_answer()
 *
 * @author   Chase "Syntaqx" Hutchins
 * @author   Mike Crawford
 * @author   Ben Maurer
 * @version  1.0
 * @package  Fuel
 * @package  Google
 * @category classes
 */
class Response {

	/**
	 * Whether the user-provided string matched the reCAPTCHA
	 *
	 * @var   boolean
	 */
	public $is_valid			= false;

	/**
	 * The response error message from the reCAPTCHA API
	 *
	 * @var   string
	 */
	public $error				= '';
}

/* End of file response.php */