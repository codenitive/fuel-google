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
 * @author     Mior Muhammad Zaki <crynobone@gmail.com>
 * @version    1.0
 * @package    Fuel
 * @subpackage Google
 */

namespace Google;

/**
 * Google APIs Visualization Library Class.
 *
 * @author   Mior Muhammad Zaki <crynobone@gmail.com>
 * @version  1.0
 * @package  Fuel
 * @package  Google
 * @category classes
 */
class Chart {

    /**
     * Cache Chart instance so we can reuse it on multiple request.
     * 
     * @static
     * @access  protected
     * @var     array
     */
    protected static $instances = array();

    /**
     * Initiate a new Chart_Driver instance.
     * 
     * @static
     * @access  public
     * @return  static 
     */
    public static function forge($name = null) 
    {
        if (is_null($name))
        {
            $name = 'default';
        }

        $name   = \Str::lower($name);

        if (!isset(static::$instances[$name]))
        {
            $driver = '\\Google\\Chart_' . ucfirst($name);
            
            if (class_exists($driver))
            {
                static::$instances[$name] = new $driver();
            }
            else 
            {
                throw new \Fuel_Exception("Requested {$driver} does not exist.");
            }
        }

        return static::$instances[$name];
    }

    /**
     * Shortcode to self::forge().
     *
     * @deprecated  1.3.0
     * @static
     * @access  public
     * @param   string  $name
     * @return  self::forge()
     */
    public static function factory($name = null)
    {
        return static::forge($name);
    }

    /**
     * Get cached instance, or generate new if currently not available.
     *
     * @static
     * @access  public
     * @return  Chart_Driver
     * @see     self::forge()
     */
    public static function instance($name = null)
    {
        return static::forge($name);
    }
    
    public static function js() 
    {
        return '<script type="text/javascript" src="https://www.google.com/jsapi"></script>';
    }
}