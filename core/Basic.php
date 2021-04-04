<?php

/**
 * BasicPHP - A frameworkless library-based approach for building web applications
 *          - and application programming interfaces or API's.
 *          - The aim of the project is for developers to build applications that
 *          - are framework-independent using native PHP functions and API's.
 *          -
 *          - To embed the application to any framework, copy BasicPHP class library
 *          - (Basic.php), and the 'classes', 'models', 'views' and 'controllers'
 *          - folders one (1) folder above the front controller (index.php) of the
 *          - chosen framework. In the controller file, at the top of the script,
 *          - include/require Basic.php.
 *
 * @package  BasicPHP
 * @version  v0.9.6
 * @author   Raymund John Ang <raymund@open-nis.org>
 * @license  MIT License
 */

class Basic
{

	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get URI segment value
	 *
	 * @param int $order    - URI segment position from base URL
	 *                      - Basic::segment(1) as first URI segment
	 * @return string|false - URI segment string or error
	 */

	public static function segment($order)
	{
		if (isset($_SERVER['REQUEST_URI'])) {
			$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
			$uri = explode('/', $uri);
		} else {
			return FALSE;
		}

		// Number of subdirectories from hostname to index.php
		$sub_dir = substr_count($_SERVER['SCRIPT_NAME'], '/') - 1;

		if (! empty($uri[$order+$sub_dir])) {
			return $uri[$order+$sub_dir];
		} else {
			return FALSE;
		}
	}

	/**
	 * Controller or callable-based endpoint routing
	 *
	 * @param string $http_method           - HTTP method (e.g. 'ANY', 'GET', 'POST', 'PUT', 'DELETE')
	 * @param string $path                  - URL path in the format '/url/path'
	 *                                      - Wildcard convention from CodeIgniter
	 *                                      - (:num) for number and (:any) for string
	 * @param string|callable $class_method - 'ClassController@method' format or callable function
	 */

	public static function route($http_method, $path, $class_method)
	{
		if ($http_method === 'ANY') $http_method = $_SERVER['REQUEST_METHOD']; // Any HTTP Method

		if ($_SERVER['REQUEST_METHOD'] === $http_method) {

			// Convert '/' and wilcards (:num) and (:any) to RegEx
			$pattern = str_ireplace('/', '\/', $path);
			$pattern = str_ireplace('(:num)', '[0-9]+', $pattern);
			$pattern = str_ireplace('(:any)', '[^\/]+', $pattern);
					
			// Check for subfolders from DocumentRoot and include in endpoint
			$sub = explode('/', dirname($_SERVER['SCRIPT_NAME']));
			$subfolder = (! empty($sub[1])) ? implode('\/', $sub) : '';

			$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
			if ( preg_match('/^' . $subfolder . $pattern . '+$/i', $uri) )  {
				if (is_string($class_method)) {
					if (strstr($class_method, '@')) {
						list($class, $method) = explode('@', $class_method);

						$object = new $class();
						$object->$method();
						exit;
					}
				} elseif (is_callable($class_method)) {
					$class_method();
					exit;
				}

			}

		}
	}

	/**
	 * Render view with data
	 *
	 * @param string $view - View file inside 'views' folder (exclude .php extension)
	 * @param array $data  - Data in array format
	 */

	public static function view($view, $data=NULL)
	{
		$file = '../views/' . $view . '.php';
		if (! empty($data)) extract($data); // Convert array keys to variables
		if (file_exists($file) && is_readable($file) && pathinfo($file)['extension'] === 'php') require_once $file; // Render page view
	}

	/**
	 * Handle HTTP API request call
	 *
	 * @param string $http_method - HTTP request method (e.g. 'GET', 'POST')
	 * @param string $url         - URL of API endpoint
	 * @param array $data         - Request body in array format
	 * @param string $user_token  - Username or API token
	 * @param string $password    - Password (no password for API token)
	 * @return (int|string)[]     - HTTP response code and result of cURL execution
	 */

	public static function apiCall($http_method, $url, $data=NULL, $user_token=NULL, $password=NULL)
	{
		$ch = curl_init(); // Initialize cURL
		$data_json = json_encode($data); // Convert data to JSON

		// Set cURL options
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $http_method);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_USERPWD, "$user_token:$password");
		// curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		// 	'Content-Type: application/json',                                                                                
		// 	'Content-Length: ' . strlen($data_json))                                                                       
		// );

		$result = curl_exec($ch); // Execute cURL
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // HTTP response code
		curl_close ($ch); // Close cURL connection

		return ['code' => $http_code, 'data' => $result];
	}

	/**
	 * Handle HTTP API response
	 *
	 * @param integer $code        - HTTP response code
	 * @param string $data         - Data to transmit
	 * @param string $content_type - Header: Content-Type
	 * @param string $message      - HTTP response message
	 */

	public static function apiResponse($code, $data=NULL, $content_type='text/plain', $message=NULL)
	{
		// OK response
		if ($code > 199 && $code < 300) {
			$message = 'OK';
			header($_SERVER['SERVER_PROTOCOL'] . ' ' . $code . ' ' . $message); // Set HTTP response code and message
		}

		// If no data, $data = $message
		if (($code < 200 || $code > 299) && $message === NULL) {
			$message = $data;
			header($_SERVER['SERVER_PROTOCOL'] . ' ' . $code . ' ' . $message); // Set HTTP response code and message
		}

		header('Content-Type: ' . $content_type);
		exit($data); // Data in string format
	}

	/**
	 * Base URL - Templating
	 *
	 * @return string - Base URL
	 */

	public static function baseUrl()
	{
		$http_protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
		$subfolder = (! empty(dirname($_SERVER['SCRIPT_NAME']))) ? dirname($_SERVER['SCRIPT_NAME']) : '';

		return $http_protocol . $_SERVER['SERVER_NAME'] . $subfolder . '/';
	}

	/**
	 * Prevent Cross-Site Request Forgery (CSRF)
	 * Create a per request token to handle CSRF using sessions
	 * Basic::setFirewall() should be executed. $verify_csrf_token = TRUE (default)
	 */

	public static function csrfToken()
	{
		if (defined('VERIFY_CSRF_TOKEN') && VERIFY_CSRF_TOKEN === TRUE) {
			$_SESSION['csrf-token'] = bin2hex(random_bytes(32));
			return $_SESSION['csrf-token'];
		}
	}

	/**
	 * Encrypt data using AES GCM, CTR-HMAC or CBC-HMAC
	 *
	 * @param string $plaintext - Plaintext to be encrypted
	 * @return string           - contains based64-encoded ciphertext
	 */

	public static function encrypt($plaintext)
	{
		// Require encryption middleware
		if (! defined('PASS_PHRASE') || ! defined('CIPHER_METHOD')) {
			self::apiResponse(501, 'Please activate Basic::setEncryption() middleware and set the pass phrase.');
		}

		// Encryption - Version 1
		if (! function_exists('encrypt_v1')) {

			function encrypt_v1($plaintext) {

				$version = 'enc-v1'; // Version
				$cipher = CIPHER_METHOD; // Cipher method - GCM, CTR or CBC
				$salt = random_bytes(16); // Salt
				$iv = $salt; // Initialization Vector

				// Derive keys
				$masterKey = hash_pbkdf2('sha256', PASS_PHRASE, $salt, 10000); // Master key
				$encKey = hash_hkdf('sha256', $masterKey, 32, 'aes-256-encryption', $salt); // Encryption key
				$hmacKey = hash_hkdf('sha256', $masterKey, 32, 'sha-256-authentication', $salt); // HMAC key

				if ($cipher === 'aes-256-gcm') {

					$ciphertext = openssl_encrypt($plaintext, $cipher, $encKey, $options=0, $iv, $tag);
					return $version . '::' . base64_encode($ciphertext) . '::' . base64_encode($tag) . '::' . base64_encode($salt);

				} else {

					$ciphertext = openssl_encrypt($plaintext, $cipher, $encKey, $options=0, $iv);
					$hash = hash_hmac('sha256', $ciphertext, $hmacKey);
					return $version . '::' . base64_encode($ciphertext) . '::' . base64_encode($hash) . '::' . base64_encode($salt);

				}

			}

		}

		/** Version-based encryption */
		return encrypt_v1($plaintext); // Default encryption function
	}

	/**
	 * Decrypt data using AES GCM, CTR-HMAC or CBC-HMAC
	 *
	 * @param string $encrypted - contains base64-encoded ciphertext
	 * @return string           - decrypted data
	 */

	public static function decrypt($encrypted)
	{
		// Require encryption middleware
		if (! defined('PASS_PHRASE') || ! defined('CIPHER_METHOD')) {
			self::apiResponse(501, 'Please activate Basic::setEncryption() middleware and set the pass phrase.');
		}

		// Decryption - Version 1
		if (! function_exists('decrypt_v1')) {

			function decrypt_v1($encrypted) {

				// Return empty if $encrypted is not set or empty.
				if (! isset($encrypted) || empty($encrypted)) { return ''; }

				$cipher = CIPHER_METHOD; // Cipher method - GCM, CTR or CBC

				if ($cipher === 'aes-256-gcm') {

					list($version, $ciphertext, $tag, $salt) = explode('::', $encrypted);
					$ciphertext = base64_decode($ciphertext);
					$tag = base64_decode($tag);
					$salt = base64_decode($salt);

					$iv = $salt; // Initialization Vector

					// Derive keys
					$masterKey = hash_pbkdf2('sha256', PASS_PHRASE, $salt, 10000); // Master key
					$encKey = hash_hkdf('sha256', $masterKey, 32, 'aes-256-encryption', $salt); // Encryption key
					$hmacKey = hash_hkdf('sha256', $masterKey, 32, 'sha-256-authentication', $salt); // HMAC key

					$plaintext = openssl_decrypt($ciphertext, $cipher, $encKey, $options=0, $iv, $tag);

					// GCM authentication
					if ($plaintext !== FALSE) {
						return $plaintext;
					} else {
						exit ('Please verify authenticity of ciphertext.');
					}

				} else {

					list($version, $ciphertext, $hash, $salt) = explode('::', $encrypted);
					$ciphertext = base64_decode($ciphertext);
					$hash = base64_decode($hash);
					$salt = base64_decode($salt);

					$iv = $salt; // Initialization Vector

					// Derive keys
					$masterKey = hash_pbkdf2('sha256', PASS_PHRASE, $salt, 10000); // Master key
					$encKey = hash_hkdf('sha256', $masterKey, 32, 'aes-256-encryption', $salt); // Encryption key
					$hmacKey = hash_hkdf('sha256', $masterKey, 32, 'sha-256-authentication', $salt); // HMAC key

					$digest = hash_hmac('sha256', $ciphertext, $hmacKey);

					// HMAC authentication
					if  ( hash_equals($hash, $digest) ) {
						return openssl_decrypt($ciphertext, $cipher, $encKey, $options=0, $iv);
						}
					else {
						exit ('Please verify authenticity of ciphertext.');
					}

				}

			}

		}

		$version = explode('::', $encrypted)[0]; // Retrieve encryption version

		/** Version-based decryption */
		switch ($version) {
			case 'enc-v1':
				return decrypt_v1($encrypted);
				break;
			default:
				return $encrypted; // Return $encrypted if no encryption detected.
		}
	}

	/*
	|--------------------------------------------------------------------------
	| MIDDLEWARE
	|--------------------------------------------------------------------------
	*/ 

	/**
	 * Error Reporting
	 * 
	 * @param boolean $boolean - TRUE or FALSE
	 */

	public static function setErrorReporting($boolean=TRUE)
	{
		if ($boolean === TRUE) {
			error_reporting(E_ALL);
		} elseif ($boolean === FALSE) {
			error_reporting(0);
		} else {
			exit('Boolean parameter for Basic::setErrorReporting() can only be TRUE or FALSE.');
		}
	}

	/**
	 * JSON Request Body as $_POST - API Access
	 */

	public static function setJsonBodyAsPOST() {
		$body = file_get_contents('php://input');
		if ( ! empty($body) && is_array(json_decode($body, TRUE)) ) $_POST = json_decode($body, TRUE);
	}

	/**
	 * Web Application Firewall
	 * 
	 * @param array $ip_blacklist          - Blacklisted IP addresses
	 * @param boolean $verify_csrf_token   - Verify CSRF token
	 * @param boolean $post_auto_escape    - Automatically escape $_POST
	 * @param string $uri_whitelist        - Whitelisted URI RegEx characters
	 */

	public static function setFirewall($ip_blacklist=[], $verify_csrf_token=TRUE, $post_auto_escape=TRUE, $uri_whitelist='\w\/\.\-\_\?\=\&\:')
	{
		// Deny access from blacklisted IP addresses
		if (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], $ip_blacklist)) {
			self::apiResponse(403, 'You are not allowed to access the application using your IP address.');
		}

		// Verify CSRF token
		if ($verify_csrf_token === TRUE) {
			define('VERIFY_CSRF_TOKEN', TRUE); // Used for Basic::csrfToken()
			session_set_cookie_params(NULL, NULL, NULL, NULL, TRUE); // Httponly session cookie
			session_start(); // Require sessions

			if (isset($_POST['csrf-token']) && isset($_SESSION['csrf-token']) && ! hash_equals($_POST['csrf-token'], $_SESSION['csrf-token'])) {
				self::apiResponse(400, 'Please check authenticity of CSRF token.');
			}
		}

		// Automatically escape $_POST values using htmlspecialchars()
		if ($post_auto_escape === TRUE && isset($_POST)) {
			foreach ($_POST as $key => $value) {
				$_POST[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
			}
		}

		// Allow only whitelisted URI characters
		if (! empty($uri_whitelist)) {

			$regex_array = str_replace('w', 'alphanumeric', $uri_whitelist);
			$regex_array = explode('\\', $regex_array);

			if (isset($_SERVER['REQUEST_URI']) && preg_match('/[^' . $uri_whitelist . ']/i', $_SERVER['REQUEST_URI'])) {
				header($_SERVER["SERVER_PROTOCOL"]." 400 Bad Request");
				exit('<p>The URI should only contain alphanumeric and GET request characters:</p><p><ul>' . implode('<li>', $regex_array) . '</ul></p>');
			}

		}

		// // Deny blacklisted $_POST characters. '\' is blacklisted by default.
		// if (! empty($post_blacklist)) {
		// 	$regex_array = explode('\\', $post_blacklist);

		// 	if (isset($_POST) && preg_match('/[' . $post_blacklist . '\\\]/i', implode('/', $_POST)) ) {
		// 		header($_SERVER["SERVER_PROTOCOL"] . ' 400 Bad Request');
		// 		exit('<p>Submitted data should NOT contain the following characters:</p><p><ul>' . implode('<li>', $regex_array) . '<li>\</ul></p>');
		// 	}
		// }
	}

	/**
	 * Force application to use TLS/HTTPS
	 */

	public static function setHttps()
	{
		if (! isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
			header('Location: https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
			exit;
		}
	}

	/**
	 * Enable encryption
	 * 
	 * @param string $pass_phrase   - Pass phrase used for encryption
	 * @param string $cipher_method - Only AES-256 GCM, CTR or CBC
	 */

	public static function setEncryption($pass_phrase, $cipher_method='aes-256-gcm')
	{
		if (! defined('PASS_PHRASE')) {
			define('PASS_PHRASE', $pass_phrase);
		}

		if (! defined('CIPHER_METHOD')) {
			define('CIPHER_METHOD', $cipher_method);
		}

		switch ($cipher_method) {
			case 'aes-256-gcm':
				return;
			case 'aes-256-ctr':
				return;
			case 'aes-256-cbc':
				return;
			default:
				self::apiResponse(501, "Encryption cipher method should either be 'aes-256-gcm', 'aes-256-ctr' or 'aes-256-cbc'.");
		}
	}

	/**
	 * Autoload Classes
	 * 
	 * @param array $classes - Array of folders to autoload classes
	 */

	public static function setAutoloadClass($classes)
	{
		define('AUTOLOADED_FOLDERS', $classes);
		spl_autoload_register(function ($class_name) {
			foreach (AUTOLOADED_FOLDERS as $folder) {
				if (file_exists('../' . $folder . '/' . $class_name . '.php') && is_readable('../' . $folder . '/' . $class_name . '.php')) {
					require_once '../' . $folder . '/' . $class_name . '.php';
				}
			}
		});
	}

	/**
	 * Render Homepage
	 * 
	 * @param string $controller - 'HomeController@index' format
	 */

	public static function setHomePage($controller)
	{
		if ( empty(self::segment(1)) ) {
			if (is_string($controller)) {
				if (strstr($controller, '@')) {
					list($class, $method) = explode('@', $controller);

					$object = new $class();
					$object->$method();
					exit;
				}
			} elseif (is_callable($controller)) {
				$controller();
				exit;
			}
		}
	}

	/**
	 * Automatic routing of Basic::segment(1) and (2) as class and method
	 * 'Controller' as default controller suffix
	 * 'index' as default method name
	 */

	public static function setAutoRoute()
	{
		if (self::segment(1) !== FALSE) { $class = ucfirst(strtolower(self::segment(1))) . 'Controller'; }
		if (self::segment(2) !== FALSE) { $method = strtolower(self::segment(2)); } else { $method = 'index'; }

		if (class_exists($class)) {
			$object = new $class();
			if (method_exists($object, $method)) {
				$object->$method();
				exit;
			} else {
				self::apiResponse(404, 'The page you requested could not be found.');
				exit;
			}
		}
	}

	/**
	 * JSON-RPC v2.0 middleware with 'method' member as 'class.method'
	 * 'Controller' as default controller suffix
	 */

	public static function setJsonRpc()
	{
		// Check if there is request body
		if (file_get_contents('php://input') !== FALSE) {

			// If data in request body is in JSON format
			if (json_decode(file_get_contents('php://input'), TRUE) !== NULL) {

				$json_rpc = json_decode(file_get_contents('php://input'), TRUE);

				// Send error message if server request method is not 'POST'.
				if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] !== 'POST') { exit(json_encode(['jsonrpc' => '2.0', 'error' => ['code' => -32600, 'message' => "Server request method should be 'POST'."]])); }
				// Send error message if 'jsonrpc' and 'method' members are not set.
				if (! isset($json_rpc['jsonrpc']) || ! isset($json_rpc['method']) ) { exit(json_encode(['jsonrpc' => '2.0', 'error' => ['code' => -32600, 'message' => "JSON-RPC 'version' and 'method' members should be set."]])); }
				// Send error message if JSON-RPC version is not '2.0'.
				if (isset($json_rpc['jsonrpc']) && $json_rpc['jsonrpc'] !== '2.0') { exit(json_encode(['jsonrpc' => '2.0', 'error' => ['code' => -32600, 'message' => "JSON-RPC version should be a string set to '2.0'."]])); }
				// Send error message if 'method' member is not in the format 'class.method'.
				if (isset($json_rpc['method']) && substr_count($json_rpc['method'], '.') !== 1) { exit(json_encode(['jsonrpc' => '2.0', 'error' => ['code' => -32602, 'message' => "The JSON-RPC 'method' member should have the format 'class.method'."]])); }

				// Require 'jsonrpc' and 'method' members as minimum for the request object.
				if (isset($json_rpc['jsonrpc']) && isset($json_rpc['method'])) {

					list($class, $method) = explode('.', $json_rpc['method']);
					$class = $class . 'Controller';

					// Respond if class exists and 'id' member is set.
					if (class_exists($class) && isset($json_rpc['id'])) {
						$object = new $class();
						if (method_exists($object, $method)) {
							$object->$method();
							exit;
						} else { exit(json_encode(['jsonrpc' => '2.0', 'error' => ['code' => -32601, 'message' => "Method not found."], 'id' => $json_rpc['id']])); }
					} else { exit(json_encode(['jsonrpc' => '2.0', 'error' => ['code' => -32601, 'message' => "Class not found."], 'id' => $json_rpc['id']])); }
				}

			} else {
				
				// If data in request body is not in JSON format
				exit(json_encode(['jsonrpc' => '2.0', 'error' => ['code' => -32700, 'message' => "Please provide data in valid JSON format."]]));
			
			}
		
		}
	}

}