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

    'Google\\Chart'                 => __DIR__.'/classes/chart.php',
    'Google\\Chart_Driver'          => __DIR__.'/classes/chart/driver.php',
    'Google\\Chart_Area'            => __DIR__.'/classes/chart/area.php',
    'Google\\Chart_Bar'             => __DIR__.'/classes/chart/bar.php',
    'Google\\Chart_GeoMap'          => __DIR__.'/classes/chart/geomap.php',
    'Google\\Chart_Line'            => __DIR__.'/classes/chart/line.php',
    'Google\\Chart_Pie'             => __DIR__.'/classes/chart/pie.php',
    'Google\\Chart_Scatter'         => __DIR__.'/classes/chart/scatter.php',
    'Google\\Chart_Table'           => __DIR__.'/classes/chart/table.php',
    'Google\\Chart_Timeline'        => __DIR__.'/classes/chart/timeline.php',
));

/* End of file bootstrap.php */