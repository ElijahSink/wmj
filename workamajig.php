<?php

/**
 * A Flexible Wrapper to Workamajig
 * 
 */
class WMJ {
	
	var $access_token;
	var $user_token;
	
	var $token_hash;
	
	var $api_base = 'https://app.workamajig.com/api/beta1/';
	
	var $endpoints = array();
	var $reports = array();
	
	
	
	/**
	 * Construct
	 * 
	 * @param str $access_token The company token provided by WMJ.
	 * @param str $user_token The user token provided by WMJ.
	 */
	function __construct($access_token = false, $user_token = false) {
		if(!$access_token) {
			if(defined('WMJ_ACCESS_TOKEN')) {
				$access_token = WMJ_ACCESS_TOKEN;
			} else {
				throw new Exception('No access token specified.');
			}
		}
		if(!$user_token) {
			if(defined('WMJ_USER_TOKEN')) {
				$user_token = WMJ_USER_TOKEN;
			} else {
				throw new Exception('No user token specified.');
			}
		}
		
		$this->access_token = $access_token;
		$this->user_token = $user_token;
		
		$this->token_hash = sha1($access_token . $user_token);
		
		if(substr($this->api_base, -1) !== '/') {
			$this->api_base = $this->api_base . '/';
		}
		
		$this->register_endpoint(
			'reports',
			'report',
			array(
				(object) array(
					'local' => 'key',
					'remote' => 'reportKey',
					'default' => false,
					'process' => function($v) {return trim($v);}
				)
			)
		);
		
		$this->register_endpoint(
			'timesheets',
			'timesheet',
			array(
				(object) array(
					'local' => 'key',
					'remote' => 'timesheetKey',
					'default' => false,
					'process' => function($v = "") {return trim($v);}
				),
				(object) array(
					'local' => 'status',
					'remote' => 'status',
					'default' => false,
					'process' => function($v = "") {return trim($v);}
				),
				(object) array(
					'local' => 'user',
					'remote' => 'userKey',
					'default' => false,
					'process' => function($v = "") {return trim($v);}
				),
				(object) array(
					'local' => 'start',
					'remote' => 'startdate',
					'default' => '4 Mondays ago',
					'process' => function($v = "") {
						return date(
							'mdY', 
							strtotime($v)
						);
					}
				),
				(object) array(
					'local' => 'times',
					'remote' => 'includeTime',
					'default' => 1,
					'process' => function($v = false) {return $v ? 1 : 0;}
				)
			)
		);
		
		$this->register_endpoint(
			'projects',
			'project',
			array(
				(object) array(
					'local' => 'field',
					'remote' => 'searchField',
					'default' => NULL,
					'process' => function($v) {
						$v = trim($v);
						switch($v) {
							case 'ProjectNumber':
							case 'ProjectName': 
							case 'ClientName':
							case 'CampaignName':
							case 'Description':
								return $v;
							break;
						}
				
						return NULL;
					}
				),
				(object) array(
					'local' => 'search',
					'remote' => 'searchFor',
					'default' => NULL,
					'process' => function($v) {
						if(trim($v)) {
							return trim($v);
						}
						return NULL;
					}
				),
				(object) array(
					'local' => 'id',
					'remote' => 'id',
					'default' => NULL,
					'process' => function($v) {
						$v = trim($v);
						if($v) {
							return $v;
						}
						return NULL;
					}
				),
				(object) array(
					'local' => 'key',
					'remote' => 'projectKey',
					'default' => NULL,
					'process' => function($v) {
						$v = trim($v);
						if($v) {
							return $v;
						}
						return NULL;
					}
				),
				(object) array(
					'local' => 'team',
					'remote' => 'includeTeam',
					'default' => NULL,
					'process' => function($v) {
						return (int) !!$v;
					}
				),
				(object) array(
					'local' => 'diaries',
					'remote' => 'includeDiary',
					'default' => NULL,
					'process' => function($v) {
						return (int) !!$v;
					}
				),
				(object) array(
					'local' => 'todos',
					'remote' => 'includeTodos',
					'default' => NULL,
					'process' => function($v) {
						return (int) !!$v;
					}
				),
				(object) array(
					'local' => 'tasks',
					'remote' => 'includeTasks',
					'default' => NULL,
					'process' => function($v) {
						return (int) !!$v;
					}
				),
				(object) array(
					'local' => 'costs',
					'remote' => 'includeMiscCosts',
					'default' => NULL,
					'process' => function($v) {
						return (int) !!$v;
					}
				),
				(object) array(
					'local' => 'estimates',
					'remote' => 'includeEstimates',
					'default' => NULL,
					'process' => function($v) {
						return (int) !!$v;
					}
				)
			)
		);
		
		$this->register_endpoint(
			'users',
			'user',
			array(
				(object) array(
					'local' => 'id',
					'remote' => 'id',
					'default' => false,
					'process' => function($v) {
						$v = trim($v);
						if($v) {
							return $v;
						}
						return NULL;
					}
				),
				(object) array(
					'local' => 'key',
					'remote' => 'key',
					'default' => false,
					'process' => function($v) {
						$v = trim($v);
						if($v) {
							return $v;
						}
						return NULL;
					}
				)
			)
		);
		
		$this->register_endpoint(
			'services',
			'service',
			array()
		);
		
		$this->register_endpoint(
			'tasks',
			'task',
			array(
				(object) array(
					'local' => 'project_key',
					'remote' => 'projectKey',
					'default' => false,
					'process' => function($v) {
						$v = trim($v);
						if($v) {
							return $v;
						}
						return NULL;
					}
				),
				(object) array(
					'local' => 'project_number',
					'remote' => 'projectNumber',
					'default' => false,
					'process' => function($v) {
						$v = trim($v);
						if($v) {
							return $v;
						}
						return NULL;
					}
				),
				(object) array(
					'local' => 'key',
					'remote' => 'taskKey',
					'default' => false,
					'process' => function($v) {
						$v = trim($v);
						if($v) {
							return $v;
						}
						return NULL;
					}
				)
			)
		);
		
		$this->register_endpoint(
			'activities',
			'activity',
			array(
				(object) array(
					'local' => 'key',
					'remote' => 'activityKey',
					'default' => false,
					'process' => function($v) {
						$v = trim($v);
						if($v) {
							return $v;
						}
						return NULL;
					}
				)
			)
		);
		
		$this->register_endpoint(
			'companies',
			'company',
			array(
				(object) array(
					'local' => 'key',
					'remote' => 'companyKey',
					'default' => false,
					'process' => function($v) {
						$v = trim($v);
						if($v) {
							return $v;
						}
						return NULL;
					}
				)
			)
		);
		
		$this->register_endpoint(
			'companies/search',
			'company',
			array(
				(object) array(
					'local' => 'search',
					'remote' => 'text',
					'default' => false,
					'process' => function($v) {
						$v = trim($v);
						if($v) {
							return $v;
						}
						return NULL;
					}
				)
			)
		);
		
		$this->register_endpoint(
			'contacts',
			'contact',
			array(
				(object) array(
					'local' => 'key',
					'remote' => 'contactKey',
					'default' => false,
					'process' => function($v) {
						$v = trim($v);
						if($v) {
							return $v;
						}
						return NULL;
					}
				)
			)
		);
		
		$this->register_endpoint(
			'contacts/search',
			'contact',
			array(
				(object) array(
					'local' => 'search',
					'remote' => 'name',
					'default' => false,
					'process' => function($v) {
						$v = trim($v);
						if($v) {
							return $v;
						}
						return NULL;
					}
				)
			)
		);
		
		$this->register_endpoint(
			'opportunities',
			'data',
			array(
				(object) array(
					'local' => 'key',
					'remote' => 'opportunityKey',
					'default' => false,
					'process' => function($v) {
						$v = trim($v);
						if($v) {
							return $v;
						}
						return NULL;
					}
				)
			)
		);
		
		
	}
	
	
	
	/**
	 * Clean a Name
	 * 
	 * Sometimes, WMJ adds 2 spaces between names. Fix that.
	 * 
	 * @param str $name The name to clean up.
	 */
	function fname($name = '') {
		return trim(preg_replace('/\s+/', ' ', $name));
	}
	
	
	
	/**
	 * Normalize Results
	 * 
	 * @param str $type A resource type.
	 * @param object $data API call results data.
	 */
	function normalize($type = false, $data = array()) {
		if($data->errors) {
			return (object) array(
				'success' => false,
				'data' => $data->errors
			);
		}
		
		if(is_object($data)) {
			
			// Return the first item if not a valid type.
			if(isset($data->data) && (!$type || !isset($data->data->$type))) {
				foreach($data->data as $data_key => $data_value) {
					return (object) array(
						'success' => true,
						'data' => $data_value
					);
				}
			
			// Otherwise return the exact item.
			} else if(isset($data->data) && isset($data->data->$type)) {
				return (object) array(
					'success' => true,
					'data' => $data->data->$type
				);
			}
		}
		
		// Otherwise, whatever we got.
		return (object) array(
			'success' => true,
			'data' => $data
		);
	}
	
	
	
	/**
	 * Get Cache File Path
	 * 
	 * @param str $url A URL associated with the file.
	 */
	function cachefile($url = false) {
		if(!$url) {
			$url = uniqid();
		}
		
		$request_hash = sha1($url);
		
		$tmp_path = sys_get_temp_dir();
		
		$cache_name = 'wmjapi-' . $this->token_hash . '-' 
			. sha1($url) . '.json';
		
		if(substr($tmp_path, -1) !== '/') {
			$tmp_path = $tmp_path . '/';
		}
		
		$cache_file = $tmp_path . $cache_name;
		
		return $cache_file;
	}
	
	
	
	/**
	 * Cache API Data
	 * 
	 * @param str $url The url of the reuqest.
	 * @param array $data Data to store.
	 */
	function cache($url = false, $data = false) {
		if(!$url) {
			return false;
		}
		
		$cache_file = $this->cachefile($url);
		
		if(is_null(json_decode($data))) {
			$data = json_encode($data); 
		}
		
		file_put_contents($cache_file, $data);
		
		return $cache_file;
	}
	
	
	
	/**
	 * Get Cached API Data
	 * 
	 * @param str $url The url to get data for.
	 * @param int $cache_timeout How old the data can be.
	 */
	function cached($url = false, $cache_timeout = 15) {
		if(!$url) {
			return false;
		}
		
		$cache_file = $this->cachefile($url);
		
		$cache_timeout = $cache_timeout * 60;
		
		$cache_exists = file_exists($cache_file);
		
		if($cache_exists) {
			$cache_valid = (filemtime($cache_file)+$cache_timeout > time());
		} else {
			$cache_valid = false;
		}
		
		if($cache_exists && $cache_valid) {
			$cache_data = trim(file_get_contents($cache_file));
			$cache_object = @json_decode($cache_data);
			if($cache_data && $cache_object) {
				return $cache_object;
			}
		} else if($cache_exists && !$cache_valid) {
			unlink($cache_file);
		}
		
		return false;
	}
	
	
	
	/**
	 * Generic Fetch with Caching
	 * 
	 * @param str $endpoint The endpoint to query.
	 * @param array $parameters Associative array of querystring params.
	 */
	function fetch($endpoint = false, $parameters = array()) {
		if(!$endpoint) {
			return false;
		}
		
		$endpoint = trim($endpoint);
		
		// Generic cache timeout.
		$cache_timeout = 5;
		
		switch($endpoint) {
			case 'reports':
				// Reports can be requested 5 times every 15 minutes.
				// So, cache each report for 15 minutes?
				$cache_timeout = 15;
				if(!isset($parameters['reportKey'])) {
					throw new Exception('No report key specified.');
				}
			break;
		}
		
		$url = $this->api_base . $endpoint;
		if($parameters) {
			$query_string = http_build_query($parameters);
			if($query_string) {
				$url .= '?' . $query_string;
			}
		}
		
		$cached = $this->cached($url, $cache_timeout);
		if($cached) {
			return $cached;
		}
		
		$ch = curl_init();
		
		curl_setopt(
			$ch, 
			CURLOPT_HTTPHEADER, 
			array(
				'APIAccessToken: ' . $this->access_token,
				'UserToken: ' . $this->user_token
			)
		);
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		$results = curl_exec($ch);
		curl_close($ch);
		
		$results_parts = explode("\r\n\r\n", $results);
		$headers = $results_parts[0];
		$body = $results_parts[1];
		
		if(stristr($headers, 'HTTP 1.1 500')) {
			throw new Exception(
				'WMJ Internal Server Error Encountered: ' . $results
			);
		}
		
		$return = @json_decode($body);
		
		if(!$return) {
			return false;
		}
		
		preg_match(
			'/^HTTP.*?\s(\d+)\s(.*?)[\r\n]/', 
			$headers, 
			$http_code_matches
		);
		
		$http_status_code = (int) $http_code_matches[1];
		$http_status_msg = (int) $http_code_matches[2];
		
		switch($http_status_code) {
			case 200:
			case 201:
			case 207:
			case 400:
				$this->cache($url, $body);
			break;
			case 401:
				throw new Exception(
					'WMJ Tokens Invalid'
				);
			break;
			case 404:
			case 405:
				throw new Exception(
					'WMJ Endpoint Unknown: ' . $url
				);
			break;
			case 429:
				throw new Exception(
					'WMJ Rate Limit'
				);
			break;
			case 500:
				throw new Exception(
					'WMJ Internal Server Error Encountered: ' . $results
				);
			break;
		}
		
		return $return;
	}
	
	
	
	/**
	 * Register Endpoint
	 * 
	 * @param str $path The path of the endpoint.
	 * @param str $return_key The key for the endpoint data.
	 * @param array $parameters Available parameters to the endpoint as:
	 *    [
	 *       {
	 *          'local' => 'key', 
	 *          'remote' => 'wmj_key', 
	 *          'default' => false,
	 *          'process' => function($v){return $v;}
	 *       }
	 *    ]
	 * @param function $handler A hanlder if not the `get` endpoint handler.
	 */
	function register_endpoint(
		$path = '', 
		$return_key = '', 
		$parameters = array(),
		$handler = false
	) {
		
		$path = trim((string) $path);
		
		if(!$path) {
			throw new Exception(
				'Endpoint registration failed due to no path being specified.'
			);
			return false;
		}
		
		
		if(!$return_key) {
			throw new Exception(
				'Endpoint registration failed due to no return key being ' . 
					'specified.'
			);
			return false;
		}
		
		$use_params = array();
		
		foreach($parameters as $parameter) {
			
			$keep = true;
			
			if(!is_object($parameter)) {
				$parameter = (object) $parameter;
			}
			
			if(!isset($parameter->local) || !$parameter->local) {
				$keep = false;
			}
			
			if(!isset($parameter->remote) || !$parameter->remote) {
				$keep = false;
			}
			
			if(!isset($parameter->process) || !$parameter->process) {
				$keep = false;
			}
			
			if((!isset($parameter->default) || !$parameter->default) && !is_null($parameter->default)) {
				$parameter->default = false;
			}
			
			if($keep) {
				$use_params[] = $parameter;
			}
		}
		
		if(!$handler) {
			$handler = false;
		}
		
		$this->endpoints[$path] = (object) array(
			'key' => $return_key,
			'parameters' => $use_params,
			'handler' => $handler
		);
		
		return true;
	}
	
	
	
	/**
	 * Register Reports
	 * 
	 * @param str $name The short name of the report.
	 * @param str $key The key of the report.
	 * @param array $parameters Available filters to the report as:
	 *    [
	 *       {
	 *          'local' => 'key', 
	 *          'remote' => 'wmj_key', 
	 *          'process' => function($v){return $v;}
	 *       }
	 *    ]
	 */
	function register_report(
		$name = '', 
		$key = '', 
		$parameters = array()
	) {
		
		$name = trim((string) $name);
		$key = trim((string) $key);
		
		if(!$name) {
			throw new Exception(
				'Report registration failed due to no name being specified.'
			);
			return false;
		}
		
		if(!$key) {
			throw new Exception(
				'Report registration failed due to no key being specified.'
			);
			return false;
		}
		
		if(!$parameters || !is_array($parameters)) {
			$parameters = array();
		}
		
		$use_params = array();
		
		foreach($parameters as $parameter) {
			
			$keep = true;
			
			if(!is_object($parameter)) {
				$parameter = (object) $parameter;
			}
			
			if(!isset($parameter->local) || !$parameter->local) {
				$keep = false;
			}
			
			if(!isset($parameter->remote) || !$parameter->remote) {
				$keep = false;
			}
			
			if(!isset($parameter->process) || !$parameter->process) {
				$keep = false;
			}
			
			if(!isset($parameter->default) || !$parameter->default) {
				$parameter->default = false;
			}
			
			if($keep) {
				$use_params[] = $parameter;
			}
		}
		
		$this->reports[$name] = (object) array(
			'key' => $key,
			'parameters' => $use_params
		);
		
		return true;
	}
	
	
	
	/**
	 * Get Endpoint
	 * 
	 * @param str $endpoint The endpoint to get.
	 * @param array $parameters Parameters to send.
	 */
	function get($endpoint = false, $parameters = array()) {
		
		if(isset($this->endpoints[$endpoint])) {
			
			$endpoint_details = $this->endpoints[$endpoint];
			
			$params = array();
			
			foreach($endpoint_details->parameters as $param) {
				$param_local = $param->local;
				$param_remote = $param->remote;
				
				$update_value = NULL;
				
				
				if(isset($param->default) && !is_null($param->default)) {
					$update_value = $param->default;
				}
				
				if(isset($parameters[$param_local])) {
					$update_value = $parameters[$param_local];
				}
				
				if(!isset($parameters[$param_remote])) {
					$parameters[$param_remote] = $update_value;
				}
				
				if(is_null($parameters[$param_remote])) {
					unset($parameters[$param_remote]);
				}
				
				if(isset($parameters[$param_remote]) && $param->process) {
					$parameters[$param_remote] = call_user_func_array(
						$param->process, 
						array($parameters[$param_remote])
					);
				}
			}
			
			$return_key = $endpoint_details->key;
			
			if($endpoint_details->handler) {
				return call_user_func_array(
					$endpoint_details->handler, 
					$parameters
				);
			} else {
				$results = $this->fetch($endpoint, $parameters);
				return $this->normalize($return_key, $results);
				return $results;
			}
			
			
		} else {
			$return_key = preg_replace('/(es|s)$/', '', $endpoint);
			$results = $this->fetch($endpoint, $parameters);
			return $this->normalize($return_key, $results);
		}
	}
	
	
	
	/**
	 * Get A Report
	 * 
	 * @param str $key The report key or keyword to get.
	 * @param array $parameters Associative array of querystring params.
	 */
	function report($key = false, $parameters = array()) {
		
		if(!$key) {
			return false;
		}
		
		
		if(!$parameters) {
			$parameters = array();
		}
		
		
		if($this->reports[$key]) {
			$report = $this->reports[$key];
			$key = $report->key;
			$validation = $report->validation;
			$report_parameters = $report->parameters;
			
			foreach($report_parameters as $param) {
				
				if($parameters[$param->local]) {
					
					$param_local = $param->local;
					$param_remote = $param->remote;
					
					$update_value = NULL;
					
					
					if(isset($param->default) && !is_null($param->default)) {
						$update_value = $param->default;
					}
					
					if(isset($parameters[$param_local])) {
						$update_value = $parameters[$param_local];
					}
					
					if(!isset($parameters[$param_remote])) {
						$parameters[$param_remote] = $update_value;
					}
					
					if(is_null($parameters[$param_remote])) {
						unset($parameters[$param_remote]);
					}
					
					if(isset($parameters[$param_remote]) && $param->process) {
						$parameters[$param_remote] = call_user_func_array(
							$param->process, 
							array($parameters[$param_remote])
						);
					}
					
					if($param_local !== $param_remote) {
						unset($parameters[$param_local]);
					}
					
				}
			}
		}
		
		$parameters = array_merge(
			array('reportKey' => $key),
			$parameters
		);
		
		$results = $this->fetch('reports', $parameters);
		
		return $this->normalize('reports', $results);
	}
}

























