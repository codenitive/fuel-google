<?php

/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package		Fuel
 * @version		1.0
 * @author		Fuel Development Team
 * @license		MIT License
 * @copyright	2010 - 2011 Fuel Development Team
 * @link		http://fuelphp.com
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
Autoloader::add_core_namespace('Google');

// Define available classes into the Autoloader
Autoloader::add_classes(array(
	'Google\\Analytics'             => __DIR__.'/classes/analytics.php',
	'Google\\Googl'                 => __DIR__.'/classes/googl.php',
	'Google\\Num'                   => __DIR__.'/classes/num.php',
	'Google\\Pagerank'              => __DIR__.'/classes/pagerank.php',
	'Google\\Recaptcha'             => __DIR__.'/classes/recaptcha.php',
	'Google\\Recaptcha\\Response'   => __DIR__.'/classes/recaptcha/response.php',
	'Google\\Serp'                  => __DIR__.'/classes/serp.php',
));

/* End of file bootstrap.php */