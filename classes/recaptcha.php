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
 * Google reCAPTCHA class
 * 
 * This class is primarily based from the reCAPTCHA class created by Mike
 * Crawford and Ben Maurer provided from the reCAPTCHA documentation. It has
 * been altered to seamlessly integrate into the Fuel framework.
 * 
 * - Documentation and latest version
 *		http://recaptcha.net/plugins/php/
 * 
 * - Get a reCAPTCHA key
 *		https://www.google.com/recaptcha/admin/create
 * 
 * - Discussion group
 *		http://groups.google.com/group/recaptcha
 * 
 * @author   Chase "Syntaqx" Hutchins
 * @author   Mike Crawford
 * @author   Ben Maurer
 * @version  1.0
 * @package  Fuel
 * @package  Google
 * @category classes
 */
class Recaptcha {

	/**
	 * Class initialization callback
	 *
	 * @return  void
	 */
	public static function _init()
	{
		\Config::load('recaptcha', true);
		\Config::load('mailhide', true);
	}

	/**
	 * Encodes the given data into a query string format
	 * 
	 * @param   array     Array of string elements to be encoded
	 * @return  string    Encoded request
	 */
	protected static function _qsencode($data)
	{
		$req = "";

		foreach ($data as $key => $value)
		{
			$req .= $key . '=' . urlencode(stripslashes($value)).'&';
		}

		// Cut the last '&'
		$req = substr($req, 0, strlen($req) - 1);

		return $req;
	}

	/**
	 * Submits an HTTP POST to a reCAPTCHA server
	 * 
	 * @param   string
	 * @param   string
	 * @param   array
	 * @param   integer
	 * @return  array
	 */
	protected static function _http_post($host, $path, $data, $port = 80)
	{
		$req = static::_qsencode($data);

		$http_request  = "POST $path HTTP/1.0\r\n";
		$http_request .= "Host: $host\r\n";
		$http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
		$http_request .= "Content-Length: " . strlen($req) . "\r\n";
		$http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
		$http_request .= "\r\n";
		$http_request .= $req;

		$response = '';

		if(false == ($fs = @fsockopen($host, $port, $errno, $errstr, 10)))
		{
			throw new \Exception('Could not open socket');
		}

		fwrite($fs, $http_request);

		while (!feof($fs))
		{
			$response .= fgets($fs, 1160); // One TCP-IP packet
		}

		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);

		return $response;
	}
	
	/**
	 * Gets the challenge HTML (javascript and non-javascript version). This is
	 * claled from the browser, and the resulting reCAPTCHA HTML widget is
	 * embedded within the HTML form it was called from.
	 * 
	 * @param   string     The error given by reCAPTCHA (optional, default is null)
	 * @param   boolean    Should the request be made over ssl? (optional, default is false)
	 * @return  string     The HTML to be embedded in the user's form
	 */
	public static function get_html($error = null, $use_ssl = false)
	{
		$pubkey = \Config::get('recaptcha.public_key');

		if (empty($pubkey))
		{
			throw new \Exception('To use reCAPTCHA you must get an API key from <a href="https://www.google.com/recaptcha/admin/create">https://www.google.com/recaptcha/admin/create</a>');
		}

		if ($use_ssl)
		{
			$server = \Config::get('recaptcha.api_secure_server');
		}
		else
		{
			$server = \Config::get('recaptcha.api_server');
		}

		$errorpart = "";

		if ($error)
		{
			$errorpart = "&amp;error=" . $error;
		}

		return '<script type="text/javascript" src="'. $server . '/challenge?k=' . $pubkey . $errorpart . '"></script>

		<noscript>
		<iframe src="'. $server . '/noscript?k=' . $pubkey . $errorpart . '" height="300" width="500" frameborder="0"></iframe><br/>
		<textarea name="challenge_field" rows="3" cols="40"></textarea>
		<input type="hidden" name="response_field" value="manual_challenge"/>
		</noscript>';
	}

	/**
	 * Calls an HTTP POST function to verify if the user's guess was correct.
	 * 
	 * @param   string
	 * @param   string
	 * @param   string
	 * @param   array
	 */
	public static function check_answer($remoteip, $challenge, $response, $extra_params = array())
	{
		$privkey = \Config::get('recaptcha.private_key');

		if (empty($privkey))
		{
			throw new \Exception('To use reCAPTCHA you must get an API key from <a href="https://www.google.com/recaptcha/admin/create">https://www.google.com/recaptcha/admin/create</a>');
		}

		if ($remoteip == null or $remoteip == '')
		{
			throw new \Exception("For security reasons, you must pass the remote ip to reCAPTCHA");
		}

		// Discard spam submissions
        if ($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0)
		{
			$response = new Recaptch_Response();
			$response->is_valid = false;
			$response->error = 'incorrect-captcha-sol';
			
			return $response;
        }

		$response = static::_http_post(\Config::get('recaptcha.verify_server'), "/recaptcha/api/verify", array(
			'privatekey'  => $privkey,
			'remoteip'    => $remoteip,
			'challenge'   => $challenge,
			'response'    => $response
		) + $extra_params);

		$answers = explode ("\n", $response [1]);
		$response = new Recaptcha\Response();

		if (trim ($answers [0]) == 'true')
		{
			$response->is_valid = true;
		}
		else
		{
			$response->is_valid = false;
			$response->error = $answers [1];
		}

		return $response;
	}

	/**
	 * Gets a URL where the user can sign up for reCAPTCHA. If your application
	 * has a configuration page where you enter a key, you should provide a link
	 * using this function.
	 *
	 * @param   string     The domain where the page is hosted
	 * @param   string     The name of your application
	 * @return  string
	 */
	public static function get_signup_url($appname = null)
	{
		return 'https://www.google.com/recaptcha/admin/create?' .  static::_qsencode(array('domains' => \Config::get('base_url'), 'app' => $appname));
	}

	// -------------------------------------------------------------------------
	// Mailhide related code
	// -------------------------------------------------------------------------

	/**
	 * Pad a value to 16 bytes, as required by AES.
	 * 
	 * @param   string
	 * @return  string
	 */
	protected static function _aes_pad($val)
	{
		$block_size = 16;
		$numpad = $block_size - (strlen ($val) % $block_size);
		return str_pad($val, strlen ($val) + $numpad, chr($numpad));
	}

	/**
	 * AES encrypt a string. The private key is our AES encryption key. AES CBC
	 * mode is used with an initialization vector of 16 null bytes (in theory,
	 * using a common IVC would allow an attacker to know if emails encrypted 
	 * with the same key have a common 16 byte prefix. However, in order to
	 * decode both emails, the attacker still must solve a CAPTCHA. On the other
	 * hand, an IV would make URLs significantly larger)
	 * 
	 * @param   string
	 * @param   string
	 * @return  string
	 */
	protected static function _aes_encrypt($val,$ky)
	{
		if (!function_exists ("mcrypt_encrypt"))
		{
			throw new \Exception('to use reCAPTCHA Mailhide, you must have the mcrypt php module installed.');
		}

		$mode = MCRYPT_MODE_CBC;   
		$enc  = MCRYPT_RIJNDAEL_128;
		$val  = static::_aes_pad($val);

		return mcrypt_encrypt($enc, $ky, $val, $mode, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
	}

	/**
	 * Base64 encoding for a mailhide url
	 * 
	 * @param   string
	 * @return  string
	 */
	protected static function _mailhide_urlbase64($x)
	{
		return strtr(base64_encode ($x), '+/', '-_');
	}

	/**
	 * Gets the parts of an email address to expose to the user.
	 * 
	 * Usage:
	 * <code>
	 * Recaptcha::_mailhide_email_parts('johndoe@example.com') // ["john", "example.com"]
	 * </code>
	 * 
	 * The email would then be displayed as john...@example.com
	 * 
	 * @param  string    The email address to part
	 * @return array     The email parts
	 */
	protected static function _mailhide_email_parts($email)
	{
		$arr = preg_split("/@/", $email );

		if (strlen ($arr[0]) <= 4)
		{
			$arr[0] = substr ($arr[0], 0, 1);
		}
		elseif (strlen ($arr[0]) <= 6)
		{
			$arr[0] = substr ($arr[0], 0, 3);
		}
		else
		{
			$arr[0] = substr ($arr[0], 0, 4);
		}
		
		return $arr;
	}

	/**
	 * Gets html to display an email address
	 * 
	 * To get a key, go to: http://www.google.com/recaptcha/mailhide/apikey
	 * 
	 * @param   string     The email address to hide
	 * @return  string     The html
	 */
	public static function mailhide_html($email)
	{
		$emailparts = static::_mailhide_email_parts($email);
		$url        = static::mailhide_url($email);

		return(
			htmlentities($emailparts[0]) .
			\Html::anchor(htmlentities($url), '...', array(
				'onclick' => 'window.open(\'' . htmlentities($url) . '\', \'\', \'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300\'); return false',
				'title' => 'Reveal this e-mail address',
			)) .
			'@' . htmlentities ($emailparts [1])
		);
	}

	/**
	 * Gets a reCAPTCHA Mailhide url for a given email address
	 * 
	 * @param   string     The email address to hide
	 * @return  string     The url for the reCAPTCHA Mailhide
	 */
	public static function mailhide_url($email)
	{
		$pubkey = \Config::get('mailhide.public_key');
		$privkey = \Config::get('mailhide.private_key');

		if (empty($pubkey) or empty($privkey))
		{
			throw new \Exception('To use reCAPTCHA Mailhide, you have to sign up for a public and private key, you can do so at a href="http://www.google.com/recaptcha/mailhide/apikey">http://www.google.com/recaptcha/mailhide/apikey</a>');
		}

		$ky = pack('H*', $privkey);
		$cryptmail = static::_aes_encrypt ($email, $ky);

		return 'http://www.google.com/recaptcha/mailhide/d?k=' . $pubkey . '&c=' . static::_mailhide_urlbase64($cryptmail);
	}
}

/* End of file recaptcha.php */