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
 * Google Analytics API & Tracking class
 *
 * This class is meant to provide not only the ability to dynamically inject
 * analytic tracking codes, but retrieve data from your Google Analytics account
 * to be utilized within your application.
 *
 * Note: This class is a really rough first draft, and although functional,
 * may need some love and recommendations for all of you potential forkers out
 * there. If you find something that's helpful, don't forget to make a pull
 * request and help everyone out!
 * 
 * @author   Chase "Syntaqx" Hutchins
 * @version  1.0
 * @package  Fuel
 * @package  Google
 * @category classes
 */
class Analytics {

	/**
	 * Cached instance of the configuration file
	 *
	 * @var   array
	 */
	protected static $configuration = array();
	
	// Seperation of configuration values into variables
	protected $email;
	protected $password;
	protected $auth_code;
	protected $profile_id;

	// Date ranges to extract analytical data from
	protected $date_end;
	protected $date_start;

	/**
	 * Factory method
	 *
	 * This method allows for auto population of some of your analytic settings
	 * based on your current base_url. This also attempts to remove additional
	 * steps from your process by setting the various values for data extraction
	 *
	 * @return  self
	 */
	public static function factory($config = array())
	{
		\Config::load('analytics', true);

		static::$configuration = array_merge(\Config::get('analytics'), $config);

		$instance = new static(static::$configuration['email'], static::$configuration['password']);

		if(static::$configuration['web_profile_id'] === null)
		{
			$profiles = $instance->get_website_profiles();
			$base_url = str_replace(Array('http://', 'https://', 'www.'), '', \Config::get('base_url'));
			
			foreach($profiles as $profile)
			{
				$profile['title'] = str_replace(Array('http://', 'https://', 'www.'), '', $profile['title']);
				
				if($base_url == $profile['title'])
				{
					\Config::set('analytics.web_profile_id', $profile['webProfileId']);
					\Config::set('analytics.profile_id', $profile['profileId']);
					\Config::save('analytics', \Config::get('analytics'));

					static::$configuration = Config::get('analytics');
					break;
				}
			}

			if(!static::$configuration['web_profile_id'])
			{
				throw new \Exception('Unable to determine your Google Analytics web profile id (UA-XXXXX-X) based on your applications url, please define one in your configuration to continue.');
			}
		}

		if(static::$configuration['profile_id'] === null)
		{
			$profiles = (isset($profiles) ? $profiles : $instance->get_website_profiles());
			
			foreach($profiles as $profile)
			{
				if($profile['webProfileId'] == \Config::get('analytics.web_profile_id'))
				{
					\Config::set('analytics.profile_id', $profile['profileId']);
					\Config::save('analytics', \Config::get('analytics'));
					static::$configuration = Config::get('analytics');

					break;
				}
			}
		}

		if(!empty(static::$configuration['profile_id']))
		{
			$instance->set_profile('ga:'.static::$configuration['profile_id']);
		}

		return $instance;
	}

	/**
	 * Returns an HTML snippet to allow tracking of a page
	 *
	 * @return  string
	 */
	public function track($web_profile_id = '')
	{
		if(empty($web_profile_id))
		{
			$web_profile_id = \Config::get('analytics.web_profile_id');

			if(empty($web_profile_id))
			{
				throw new \Exception('Please define your Google Analytics web profile id (UA-XXXXX-X) inside of your analytics configuration file.');
			}
		}

		return(
			'<script>' . PHP_EOL .
			'var _gaq=[["_setAccount","' . $web_profile_id . '"],["_trackPageview"]];' . PHP_EOL .
			'(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;' . PHP_EOL .
			'g.src=("https:"==location.protocol?"//ssl":"//www")+".google-analytics.com/ga.js";' . PHP_EOL .
			's.parentNode.insertBefore(g,s)}(document,"script"));' . PHP_EOL .
			'</script>' . PHP_EOL
		);
	}

	/**
	 * Constructor, called via static::factory() if configuration values are meant to be used
	 *
	 * @param   string
	 * @param   string
	 * @return  void
	 */
	public function __construct($email, $password)
	{
		$this->email = $email;
		$this->password = $password;
		
		// Default start and end dates
		$this->date_end = date('Y-m-d', mktime(0, 0, 0, date('m') , date('d') - 1, date('Y')));
		$this->date_start = date('Y-m-d', mktime(0, 0, 0, date('m') , date('d') - 31, date('Y')));
		
		if(!empty($this->email) and !empty($this->password))
		{
			if(!$this->authenticate())
			{
				throw new \Exception('Analytics authentication failed, please check your email and password');
			}
		}
		else
		{
			throw new \Exception('You must provide both an email and password in your analytics configuration file');
		}
	}
	
	/**
	 * Sets a Profile ID
	 *
	 * @param   string     Profile ID string (format: 'ga:1234')
	 * @return  boolean
	 */
	public function set_profile($id)
	{
		if (!preg_match('/^ga:\d{1,10}/', $id))
		{
			throw new \Exception('Invalid Analytics Profile ID set. The format should ga:XXXXXX, where XXXXXX is your profile number');
		}
		
		$this->profile_id = $id;
		
		return true;
	}

	/**
	 * Set the date range
	 *
	 * @param   string     Starting date, in the format of YYYY-MM-DD
	 * @param   string     Ending date, in the format of YYYY-MM-DD
	 */
	public function set_date_range($date_start, $date_end)
	{
		//validate the dates
		if (!preg_match('/\d{4}-\d{2}-\d{2}/', $date_start))
		{
			throw new \Exception('Format for start date is wrong, expecting YYYY-MM-DD format');
		}
		
		if (!preg_match('/\d{4}-\d{2}-\d{2}/', $date_end))
		{
			throw new \Exception('Format for end date is wrong, expecting YYYY-MM-DD format');
		}
		
		if (strtotime($date_start)>strtotime($date_end))
		{
			throw new \Exception('Invalid Date Range. Start Date is greated than End Date');
		}
		
		$this->date_start = $date_start;
		$this->date_end = $date_end;

		return $this;
	}

	/**
	 * Retrieve the report according the the properties set. For detailed
	 * instructions regarding the Google API properties, as well as the
	 * meaning of the returned values, reference below:
	 *
	 * @link	http://code.google.com/apis/analytics/docs/gdata/gdataReferenceDataFeed.html
	 * @param   array
	 * @return  array
	 */
	public function get_report($properties = array())
	{
		if (!count($properties))
		{
			throw new \Exception('Analytics->get_report requires at least one parameter to be passed');
			return false;
		}
		
		$params = array();

		//arrange the properties in key-value pairing
		foreach($properties as $key => $value)
		{
			$params[] = $key.'='.$value;
		}
		
		//compose the apiURL string
		$apiUrl = 'https://www.google.com/analytics/feeds/data?ids='.$this->profile_id.'&start-date='.$this->date_start.'&end-date='.$this->date_end.'&'.implode('&', $params);

		//call the API
		$xml = $this->call_api($apiUrl);

		//get the results
		if ($xml)
		{
			$dom = new \DOMDocument();
			$dom->loadXML($xml);
			
			$entries = $dom->getElementsByTagName('entry');
			
			foreach ($entries as $entry)
			{
				$dims='';
				$mets='';
				
				$dimensions = $entry->getElementsByTagName('dimension');
				
				foreach ($dimensions as $dimension)
				{
					$dims .= $dimension->getAttribute('value').'~~';
				}

				$metrics = $entry->getElementsByTagName('metric');
				
				foreach ($metrics as $metric)
				{
					$name = str_replace('ga:', '', $metric->getAttribute('name'));
					$mets[$name] = $metric->getAttribute('value');
				}

				$dims = trim($dims,'~~');
				$results[$dims] = $mets;
			}
		}
		else
		{
			throw new \Exception('Analytics->get_report() failed to get a valid XML from Google Analytics API service');
		}
		
		return $results;
	}

	/**
	 * Retrieve the list of Website Profiles according to your Analytics account
	 *
	 * @return  array
	 */
	public function get_website_profiles()
	{
		// make the call to the API
		$response = $this->call_api('https://www.google.com/analytics/feeds/accounts/default');
		$profiles = array();

		//parse the response from the API using DOMDocument.
		if ($response)
		{
			$dom = new \DOMDocument();
			$dom->loadXML($response);

			$entries = $dom->getElementsByTagName('entry');

			foreach($entries as $entry)
			{
				$tmp['title'] = $entry->getElementsByTagName('title')->item(0)->nodeValue;
				$tmp['id'] = $entry->getElementsByTagName('id')->item(0)->nodeValue;

				foreach($entry->getElementsByTagName('property') as $property)
				{
					if (strcmp($property->getAttribute('name'), 'ga:accountId') == 0)
					{
						$tmp['accountId'] = $property->getAttribute('value');
					}

					if (strcmp($property->getAttribute('name'), 'ga:accountName') == 0)
					{
						$tmp['accountName'] = $property->getAttribute('value');
					}

					if (strcmp($property->getAttribute('name'), 'ga:profileId') == 0)
					{
						$tmp['profileId'] = $property->getAttribute('value');
					}

					if (strcmp($property->getAttribute('name'), 'ga:webPropertyId') == 0)
					{
						$tmp['webProfileId'] = $property->getAttribute('value');
					}
				}

				$profiles[] = $tmp;
			}
		}
		else
		{
			throw new \Exception('get_website_profiles() failed to get a valid XML from Google Analytics API service');
		}
		
		return $profiles;
	}
	
	/**
	 * Make an API call to whichever $url
	 *
	 * @param   string
	 * @see     post_to
	 */
	protected function call_api($url)
	{
		return $this->post_to($url,array(),array("Authorization: GoogleLogin auth=".$this->auth_code));
	}

	/**
	 * Authenticate an analytics email and password with Google, and set the
	 * $auth_code returned by Google for later use.
	 *
	 * @return  void
	 */
	protected function authenticate()
	{
		$postdata = array(
			'accountType' => 'GOOGLE',
			'Email'       => $this->email,
			'Passwd'      => $this->password,
			'service'     => 'analytics',
			'source'      => 'askaboutphp-v01',
		);

		$response = $this->post_to('https://www.google.com/accounts/ClientLogin', $postdata);
		
		//process the response;
		if ($response)
		{
			preg_match('/Auth=(.*)/', $response, $matches);

			if(isset($matches[1]))
			{
				$this->auth_code = $matches[1];
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Performs a curl call to the specified $url
	 *
	 * @param   string
	 * @param   array     Specify the data to be 'POST'ed
	 * @param   array     Specify any additional headers
	 * @return  string    Response from the curl
	 */
	protected function post_to($url, array $data = array(), array $header = array())
	{
		// check that the url is provided
		if (empty($url))
		{
			return false;
		}

		//send the data by curl
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		
		if (count($data) > 0)
		{
			//POST METHOD
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		else
		{
			$header[] = 'application/x-www-form-urlencoded';
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		}

		$response = curl_exec($curl);
		$info = curl_getinfo($curl);

		curl_close($curl);

		if($info['http_code'] == 200)
		{
			return $response;
		}
		elseif ($info['http_code'] == 400)
		{
			throw new \Exception('Bad request - '.$response);
		}
		elseif ($info['http_code'] == 401)
		{
			throw new \Exception('Permission Denied - '.$response);
		}
		
		return false;
	}
}

/* End of file analytics.php */