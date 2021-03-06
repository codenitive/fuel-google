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
abstract class Chart_Driver {

    /**
     * Load config file
     * 
     * @static
     * @access  public
     */
    public static function _init() 
    {
        \Config::load('chart', true);
    }

    /**
     * A shortcode to initiate this class as a new object
     * 
     * @static
     * @access  public
     * @return  static 
     */
    public static function forge() 
    {
        return new static();
    }

    public static function factory()
    {
        return static::forge();
    }

    protected $options  = array();
    protected $hAxis    = 'string';
    protected $columns  = '';
    protected $rows     = '';

    /**
     * Clean-up private property on new object
     * 
     * @access  public
     */
    public function __construct() 
    {
        $this->clear();
    }

    /**
     * Run the clean-up
     * 
     * @access  public
     * @return  bool
     */
    public function clear() 
    {
        $this->options  = array();
        $this->columns  = '';
        $this->rows     = '';

        return true;
    }

    /**
     * Set columns information
     * 
     * @access  public
     * @param   array   $data 
     */
    public function set_columns($data = array()) 
    {
        $this->columns  = '';

        $count          = 0;

        if (count($data) > 0) 
        {
            foreach ($data as $key => $value) 
            {
                if ($count === 0) 
                {
                    $this->hAxis = $value;
                }
                
                if (is_numeric($key))
                {
                    $key = 'string';
                }
                
                $this->columns .= "data.addColumn('{$value}', '{$key}');\r\n";
                $count++;
            }
        }
    }

    /**
     * Set chart options
     * 
     * @access  public
     * @param   mixed   $name
     * @param   mixed   $value
     * @return  bool
     */
    public function set_options($name, $value = '') 
    {
        if (is_null($name)) 
        {
            return false;
        }

        if (is_array($name)) 
        {
            foreach ($name as $key => $value) 
            {
                $this->options[$key] = $value;
            }
        }
        elseif (is_string($name)) 
        {
            $this->options[$name] = $value;
        }

        return true;
    }

    /**
     * Set rows information
     * 
     * @access  public
     * @param   array   $data 
     */
    public function set_rows($data = array()) 
    {
        $this->rows = "";
        $dataset = '';

        $x = 0;
        $y = 0;

        if (count($data) > 0) 
        {
            foreach ($data as $key => $value) 
            {
                if ($this->hAxis == 'date') 
                {
                    $key = $this->parse_date($key);
                } 
                else 
                {
                    $key = sprintf("'%s'", $key);
                }

                $dataset .= "data.setValue({$x}, {$y}, " . $key . ");\r\n";

                foreach ($value as $k => $v) 
                {
                    $y++;
                    $dataset .= "data.setValue({$x}, {$y}, {$v});\r\n";
                }
                $x++;
                $y = 0;
            }
        }
        
        $this->rows .= "data.addRows(" . $x . ");\r\n{$dataset}";
    }

    /**
     * Parse PHP Date Object into JavaScript new Date() format
     * 
     * @access  protected
     * @param   date    $date
     * @return  string 
     */
    protected function parse_date($date) 
    {
        $key = strtotime($date);
        return 'new Date(' . date('Y', $key) . ', ' . (date('m', $key) - 1) . ', ' . date('d', $key) . ')';
    }

    /**
     * Generate the chart
     * 
     * @abstract
     * @access  public
     * @param   int     $width
     * @param   int     $height
     */
    public abstract function generate($width, $height);
    
}