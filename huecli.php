#!/usr/bin/php
<?php

	$hue = new Libhue();
	$debug = false;

	for ($i = 1; $i < $argc; $i++)	{
		$p = $argv[$i];
		$v = @$argv[$i+1];

		if($p == "--all")	{
			$affectedLamps = $hue->getLightIdsList();
		}
		if($p == "--lamp")	{
			$affectedLamps[] = $v;
		}


		if($p == "--off")	{
			turnOff();
		}

		if($p == "--on")	{
			turnOn();
		}
		if($p == "--color")	{
			setColor($v);
		}
		if($p == "--brightness")	{
			setBrightness($v);
		}
		if($p == "--blink" or $p == "--pulse")	{
			setAlarm();
		}
		if($p == "--loop" or $p == "--freakout")	{
			setColorLoop();
		}
		if($p == "--stoploop" or $p == "--calmdown")	{
			stopColorLoop();
		}
		if($p == "--debug" or $p == "-d")	{
			$debug = true;
		}
		if($p == "--details")	{
			showDetails();
		}

		if($p == "--candle")	{
			script_CANDLE();
		}
		if($p == "--sunrise")	{
			script_SUNRISE();
		}

		if($p == "--list")	{
			showList();
		}
	}

		function turnoff()	{
			global $affectedLamps, $hue;
			$data = new DummyObject();
			foreach($affectedLamps as $lampid)	{
				dbg("TurnOff $lampid: ");
				$data->on = false;
				$res = json_decode($hue->setLight($lampid, $data));
				if(is_object($res[0]->success) )	{
					dbg(" OK\n");
				} else {
					print_r($res);
				}
			}
		}

		function turnon()	{
			global $affectedLamps, $hue;
			$data = new DummyObject();
			foreach($affectedLamps as $lampid)	{
				dbg("TurnOn $lampid: ");
				$data->on = true;
				$res = json_decode($hue->setLight($lampid, $data));
				if(is_object($res[0]->success) )	{
					dbg(" OK\n");
				} else {
					print_r($res);
				}
			}
		}	

		function stopColorLoop()	{
			global $affectedLamps, $hue;
			$data = new DummyObject();
			foreach($affectedLamps as $lampid)	{
				dbg("setColor $lampid: ");
				$fuu = array('effect'=>'none');
				foreach($fuu as $k=>$v)	{
					$data->$k = $v;
				}
				$res = json_decode($hue->setLight($lampid, $data));
				if(is_object($res[0]->success) )	{
					dbg(" OK\n");
				} else {
					print_r($res);
				}
			}

		}		

		function setColorLoop()	{
			global $affectedLamps, $hue;
			$data = new DummyObject();
			foreach($affectedLamps as $lampid)	{
				dbg("setColor $lampid: ");
				$fuu = array('effect'=>'colorloop');
				foreach($fuu as $k=>$v)	{
					$data->$k = $v;
				}
				$res = json_decode($hue->setLight($lampid, $data));
				if(is_object($res[0]->success) )	{
					dbg(" OK\n");
				} else {
					print_r($res);
				}
			}

		}

		function setAlarm()	{
			global $affectedLamps, $hue;
			$data = new DummyObject();
			foreach($affectedLamps as $lampid)	{
				dbg("setColor $lampid: ");
				$fuu = array('alert'=>'lselect');

				#print_r($fuu);
				#die();
				foreach($fuu as $k=>$v)	{
					$data->$k = $v;
				}
				$res = json_decode($hue->setLight($lampid, $data));
				if(is_object($res[0]->success) )	{
					dbg(" OK\n");
				} else {
					print_r($res);
				}
			}

		}

		function showDetails()	{
			global $affectedLamps, $hue;
			$data = new DummyObject();
			foreach($affectedLamps as $lampid)	{
				dbg("Details $lampid: ");
				$res = $hue->getLightState($lampid);
				print_r($res);
			}
		}			

		function setColor($color)	{
			global $affectedLamps, $hue;
			$data = new DummyObject();
			foreach($affectedLamps as $lampid)	{
				dbg("setColor $lampid: ");
				if(is_numeric($color))	{
					$fuu = array(
						'hue'	=> $color,
						'sat'	=> 254,
						'bri'	=> 254
					);
				} else {
					$fuu = $hue->predefinedColors($color);
				}

				#print_r($fuu);
				#die();
				foreach($fuu as $k=>$v)	{
					$data->$k = $v;
				}
				$res = json_decode($hue->setLight($lampid, $data));
				if(is_object($res[0]->success) )	{
					dbg(" OK\n");
				} else {
					print_r($res);
				}
			}
		}	

		function setBrightness($bri)	{
			global $affectedLamps, $hue;
			$data = new DummyObject();
			foreach($affectedLamps as $lampid)	{
				dbg("setBrightness $lampid: ");
				$data->bri = round($bri/100*255);
				$res = json_decode($hue->setLight($lampid, $data));
				if(is_object($res[0]->success) )	{
					dbg(" OK\n");
				} else {
					print_r($res);
				}
			}
		}

		function script_CANDLE()	{
			global $affectedLamps, $hue;
			$data = new DummyObject();
			while (true) {
				foreach($affectedLamps as $lampid)	{
					$data->ct = rand(350,500);
					$data->bri = rand(25,75);
					$hue->setLight(rand(1,3), $data);
					usleep(10000);
				}	
			}		
		}

		function showList()	{
			global $affectedLamps, $hue;
			$ret = $hue->getLightState();
			foreach($ret as $id=>$i)	{
				$st = $i['state'];

				echo(sprintf("%2s", $id)." ");
				echo(sprintf("%10s", $i['name'])." ");
				if($st['on'] == 1)	{ echo (" On  "); } else { echo(" Off "); }
				if($st['on'] > 0)	{
					echo("B ".sprintf("%3s", ceil($st['bri']/255*100))."% ");
					echo(sprintf("%-6s", $hue->getKnownColorName($st['hue'])));
				}

				echo("\n");
			}			
		}


		function dbg($fuu)	{
			global $debug;
			if($debug)	{
				echo($fuu);
			}
		}



		class libhue 	{

			var $bridge = 'BRIDGE-IP';
			var $key = 'YOUR-BRIDGE-USERNAME';

			// Registers your script with your Hue hub
			function register() {
				$pest = new Pest("http://{$this->bridge}/api");
				$data = json_encode(array('username' => 'abcd1234', 'devicetype' => 'Ray Solutions Scripts'));
				$result = $pest->post('', $data);
				return "$result\n";
			}
			// Returns a big array of the state of either a single light, or all your lights
			function getLightState($lightid = false) { 
				$targets = array();
				$result = array();

				if ($lightid === false) {	
					$targets = $this->getLightIdsList();
				} else {
					if (! is_array($lightid)) {
						$targets[] = $lightid;
					} else {
						$targets = $lightid;
					}
				}

				foreach ($targets as $id) {

					$pest = new PEST("http://{$this->bridge}/api/{$this->key}/");
					$deets = json_decode($pest->get("lights/$id"), true);
					$state = $deets['state'];
					
					$result[$id] = $deets;

				}
				return $result;
			}

			// Returns an array of the light numbers in the system
			function getLightIdsList() {
					$pest = new Pest("http://{$this->bridge}/api/{$this->key}/");

					$result = json_decode($pest->get('lights'), true);
					$targets = array_keys($result);
					return $targets;
			}

			// sets the alert state of a single light. 'select' blinks once, 'lselect' blinks repeatedly, 'none' turns off blinking
			function alertLight($target, $type = 'select') {
					$pest = new Pest("http://$bridge/api/{$this->key}/");
					$data = json_encode(array("alert" => $type));
					$result = $pest->put("lights/$target/state", $data);

					return $result;
			}

			// function for setting the state property of one or more lights
			function setLight($lightid, $input) {
				$pest = new Pest("http://{$this->bridge}/api/{$this->key}/");
				$data = json_encode($input);
				$result = '';

				if (is_array($lightid)) {
					foreach ($lightid as $id) {
						$pest = new Pest("http://{$this->bridge}/api/{$this->key}/");
						$result .= $pest->put("lights/$id/state", $data);
					}
				} else {
					$result = $pest->put("lights/$lightid/state", $data);
				}
				return $result;
			}

			// gin up a random color
			function getRandomColor() {
				$return = array();

				$return['hue'] = rand(0, 65535);
				$return['sat'] = rand(0,254);
				$return['bri'] = rand(0,254);

				return $return;
			}

			// gin up a random temp-based white setting
			function getRandomWhite() {
				$return = array();
				$return['ct'] = rand(150,500);
				$return['bri'] = rand(0,255);

				return $return;
			}

			function getKnownColorName($hue)	{
				switch ($hue)	{
					case 14922:
						return("white");
						break;
					case 0:
						return("red");
						break;
					case 25480:
						return("green");
						break;
					case 46956:
						return("blue");
						break;
					case 34534:
						return("coolwhite");
						break;
					case 12521:
						return("warmwhite");
						break;
					case 49140:
						return("purple");
						break;
					default: 
						return($hue);
						break;

				}
			}

			// build a few color commands based on color names.
			function predefinedColors($colorname) {
				$command = array();
				switch ($colorname) {
					case "green":
						$command['hue'] =  182 * 140;
						break;
					case "red":
						$command['hue'] =  0;
						break;
					case "blue":
						$command['hue'] =  182 * 258;
						break;
					case "coolwhite":
						$command['ct'] =  150;
						break;
					case "warmwhite":
						$command['ct'] =  500;
						break;
					case "purple":
						$command['hue'] =  182 * 270;
						break;

				}
				return $command;
			}
		}
		
		
	class PestJSON extends Pest	{
		public function post($url, $data, $headers=array()) {
			return parent::post($url, json_encode($data), $headers);
		}

		public function put($url, $data, $headers=array()) {
			return parent::put($url, json_encode($data), $headers);
		}

		protected function prepRequest($opts, $url) {
			$opts[CURLOPT_HTTPHEADER][] = 'Accept: application/json';
			$opts[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
			return parent::prepRequest($opts, $url);
		}

		public function processBody($body) {
			return json_decode($body, true);
		}
	}

	class Pest {
	  public $curl_opts = array(
	  	CURLOPT_RETURNTRANSFER => true,  // return result instead of echoing
	  	CURLOPT_SSL_VERIFYPEER => false, // stop cURL from verifying the peer's certificate
	  	CURLOPT_FOLLOWLOCATION => false,  // follow redirects, Location: headers
	  	CURLOPT_MAXREDIRS      => 10     // but dont redirect more than 10 times
	  );

	  public $base_url;
	  
	  public $last_response;
	  public $last_request;
	  public $last_headers;
	  
	  public $throw_exceptions = true;
	  
	  public function __construct($base_url) {
	    if (!function_exists('curl_init')) {
	  	    throw new Exception('CURL module not available! Pest requires CURL. See http://php.net/manual/en/book.curl.php');
	  	}
	  	
	  	// only enable CURLOPT_FOLLOWLOCATION if safe_mode and open_base_dir are not in use
	  	if(ini_get('open_basedir') == '' && strtolower(ini_get('safe_mode')) == 'off') {
	  	  $this->curl_opts['CURLOPT_FOLLOWLOCATION'] = true;
	  	}
	    
	    $this->base_url = $base_url;
	    
	    // The callback to handle return headers
	    // Using PHP 5.2, it cannot be initialised in the static context
	    $this->curl_opts[CURLOPT_HEADERFUNCTION] = array($this, 'handle_header');
	  }
	  
	  // $auth can be 'basic' or 'digest'
	  public function setupAuth($user, $pass, $auth = 'basic') {
	    $this->curl_opts[CURLOPT_HTTPAUTH] = constant('CURLAUTH_'.strtoupper($auth));
	    $this->curl_opts[CURLOPT_USERPWD] = $user . ":" . $pass;
	  }
	  
	  // Enable a proxy
	  public function setupProxy($host, $port, $user = NULL, $pass = NULL) {
	    $this->curl_opts[CURLOPT_PROXYTYPE] = 'HTTP';
	    $this->curl_opts[CURLOPT_PROXY] = $host;
	    $this->curl_opts[CURLOPT_PROXYPORT] = $port;
	    if ($user && $pass) {
	      $this->curl_opts[CURLOPT_PROXYUSERPWD] = $user . ":" . $pass;
	    }
	  }
	  
	  public function get($url) {
	    $curl = $this->prepRequest($this->curl_opts, $url);
	    $body = $this->doRequest($curl);
	    
	    $body = $this->processBody($body);
	    
	    return $body;
	  }
	  
	  public function prepData($data) {
	    if (is_array($data)) {
	        $multipart = false;
	        
	        foreach ($data as $item) {
	            if (strncmp($item, "@", 1) == 0 && is_file(substr($item, 1))) {
	                $multipart = true;
	                break;
	            }
	        }
	        
	        return ($multipart) ? $data : http_build_query($data);
	    } else {
	        return $data;
	    }
	  }
	  
	  public function post($url, $data, $headers=array()) {
	    $data = $this->prepData($data);
	        
	    $curl_opts = $this->curl_opts;
	    $curl_opts[CURLOPT_CUSTOMREQUEST] = 'POST';
	    if (!is_array($data)) $headers[] = 'Content-Length: '.strlen($data);
	    $curl_opts[CURLOPT_HTTPHEADER] = $headers;
	    $curl_opts[CURLOPT_POSTFIELDS] = $data;
	    
	    $curl = $this->prepRequest($curl_opts, $url);
	    $body = $this->doRequest($curl);
	    
	    $body = $this->processBody($body);
	    
	    return $body;
	  }
	  
	  public function put($url, $data, $headers=array()) {
	    $data = $this->prepData($data);
	    
	    $curl_opts = $this->curl_opts;
	    $curl_opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
	    if (!is_array($data)) $headers[] = 'Content-Length: '.strlen($data);
	    $curl_opts[CURLOPT_HTTPHEADER] = $headers;
	    $curl_opts[CURLOPT_POSTFIELDS] = $data;
	    
	    $curl = $this->prepRequest($curl_opts, $url);
	    $body = $this->doRequest($curl);
	    
	    $body = $this->processBody($body);
	    
	    return $body;
	  }
	  
	    public function patch($url, $data, $headers=array()) {
	    $data = (is_array($data)) ? http_build_query($data) : $data; 
	    
	    $curl_opts = $this->curl_opts;
	    $curl_opts[CURLOPT_CUSTOMREQUEST] = 'PATCH';
	    $headers[] = 'Content-Length: '.strlen($data);
	    $curl_opts[CURLOPT_HTTPHEADER] = $headers;
	    $curl_opts[CURLOPT_POSTFIELDS] = $data;
	    
	    $curl = $this->prepRequest($curl_opts, $url);
	    $body = $this->doRequest($curl);
	    
	    $body = $this->processBody($body);
	    
	    return $body;
	  }
	  
	  public function delete($url) {
	    $curl_opts = $this->curl_opts;
	    $curl_opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
	    
	    $curl = $this->prepRequest($curl_opts, $url);
	    $body = $this->doRequest($curl);
	    
	    $body = $this->processBody($body);
	    
	    return $body;
	  }
	  
	  public function lastBody() {
	    return $this->last_response['body'];
	  }
	  
	  public function lastStatus() {
	    return $this->last_response['meta']['http_code'];
	  }
	  
	  /**
	   * Return the last response header (case insensitive) or NULL if not present.
	   * HTTP allows empty headers (e.g. RFC 2616, Section 14.23), thus is_null()
	   * and not negation or empty() should be used.
	   */
	  public function lastHeader($header) {
	    if (empty($this->last_headers[strtolower($header)])) {
	      return NULL;
	    }
	    return $this->last_headers[strtolower($header)];
	  }
	  
	  protected function processBody($body) {
	    // Override this in classes that extend Pest.
	    // The body of every GET/POST/PUT/DELETE response goes through 
	    // here prior to being returned.
	    return $body;
	  }
	  
	  protected function processError($body) {
	    // Override this in classes that extend Pest.
	    // The body of every erroneous (non-2xx/3xx) GET/POST/PUT/DELETE  
	    // response goes through here prior to being used as the 'message'
	    // of the resulting Pest_Exception
	    return $body;
	  }

	  
	  protected function prepRequest($opts, $url) {
	    if (strncmp($url, $this->base_url, strlen($this->base_url)) != 0) {
	      $url = $this->base_url . $url;
	    }
	    $curl = curl_init($url);
	    
	    foreach ($opts as $opt => $val)
	      curl_setopt($curl, $opt, $val);
	      
	    $this->last_request = array(
	      'url' => $url
	    );
	    
	    if (isset($opts[CURLOPT_CUSTOMREQUEST]))
	      $this->last_request['method'] = $opts[CURLOPT_CUSTOMREQUEST];
	    else
	      $this->last_request['method'] = 'GET';
	    
	    if (isset($opts[CURLOPT_POSTFIELDS]))
	      $this->last_request['data'] = $opts[CURLOPT_POSTFIELDS];
	    
	    return $curl;
	  }
	  
	  private function handle_header($ch, $str) {
	    if (preg_match('/([^:]+):\s(.+)/m', $str, $match) ) {
	      $this->last_headers[strtolower($match[1])] = trim($match[2]);
	    }
	    return strlen($str);
	  }

	  private function doRequest($curl) {
	    $this->last_headers = array();
	    
	    $body = curl_exec($curl);
	    $meta = curl_getinfo($curl);
	    
	    $this->last_response = array(
	      'body' => $body,
	      'meta' => $meta
	    );
	    
	    curl_close($curl);
	    
	    $this->checkLastResponseForError();
	    
	    return $body;
	  }
	  
	  protected function checkLastResponseForError() {
	    if ( !$this->throw_exceptions)
	      return;
	      
	    $meta = $this->last_response['meta'];
	    $body = $this->last_response['body'];
	    
	    if (!$meta)
	      return;
	    
	    $err = null;
	    switch ($meta['http_code']) {
	      case 400:
	        throw new Pest_BadRequest($this->processError($body));
	        break;
	      case 401:
	        throw new Pest_Unauthorized($this->processError($body));
	        break;
	      case 403:
	        throw new Pest_Forbidden($this->processError($body));
	        break;
	      case 404:
	        throw new Pest_NotFound($this->processError($body));
	        break;
	      case 405:
	        throw new Pest_MethodNotAllowed($this->processError($body));
	        break;
	      case 409:
	        throw new Pest_Conflict($this->processError($body));
	        break;
	      case 410:
	        throw new Pest_Gone($this->processError($body));
	        break;
	      case 422:
	        // Unprocessable Entity -- see http://www.iana.org/assignments/http-status-codes
	        // This is now commonly used (in Rails, at least) to indicate
	        // a response to a request that is syntactically correct,
	        // but semantically invalid (for example, when trying to 
	        // create a resource with some required fields missing)
	        throw new Pest_InvalidRecord($this->processError($body));
	        break;
	      default:
	        if ($meta['http_code'] >= 400 && $meta['http_code'] <= 499)
	          throw new Pest_ClientError($this->processError($body));
	        elseif ($meta['http_code'] >= 500 && $meta['http_code'] <= 599)
	          throw new Pest_ServerError($this->processError($body));
	        elseif (!$meta['http_code'] || $meta['http_code'] >= 600) {
	        	print_r($meta);
	        	die($body);
	          throw new Pest_UnknownResponse($this->processError($body));
	        }
	    }
	  }
	}

	class dummyObject {}
	class Pest_Exception extends Exception { }
	class Pest_UnknownResponse extends Pest_Exception { }

	/* 401-499 */ class Pest_ClientError extends Pest_Exception {}
	/* 400 */ class Pest_BadRequest extends Pest_ClientError {}
	/* 401 */ class Pest_Unauthorized extends Pest_ClientError {}
	/* 403 */ class Pest_Forbidden extends Pest_ClientError {}
	/* 404 */ class Pest_NotFound extends Pest_ClientError {}
	/* 405 */ class Pest_MethodNotAllowed extends Pest_ClientError {}
	/* 409 */ class Pest_Conflict extends Pest_ClientError {}
	/* 410 */ class Pest_Gone extends Pest_ClientError {}
	/* 422 */ class Pest_InvalidRecord extends Pest_ClientError {}

	/* 500-599 */ class Pest_ServerError extends Pest_Exception {}
