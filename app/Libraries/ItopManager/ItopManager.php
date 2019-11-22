<?php

namespace App\Libraries\ItopManager;

use stdClass;

class ItopManager
{
    private $server;
	private $version;
	private $auth_user;
	private $auth_pwd;

	public function __construct() {
        $this->version = '1.1';

		$this->server = env("SERVER").'/webservices/rest.php?version=' . $this->version;
		$this->auth_user = env("AUTH_USER");
		$this->auth_pwd = env("AUTH_PWD");
	}

	private function parseHeaders($headers) {
		$head = array();
		foreach ($headers as $k => $v) {
			$t = explode(':', $v, 2);
			if(isset($t[1]))
				$head[trim($t[0])] = trim($t[1]);
			else {
				$head[] = $v;
				if(preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $v, $out)) {
					$head['reponse_code'] = intval($out[1]);
				}
			}
		}
		return ($head);
	}

	private function request($type, $url, $data = null, $contentType = "application/x-www-form-urlencoded") {
		$result = new stdClass();
		$http = array();
		$http['header'] = "";
		$http['header'] .= "Content-Type:$contentType\r\n";
		$http['method'] = $type;
		$http['timeout'] = 5;
		if ($data != null && !empty($data)) {
			if (is_string($data)) {
				$http['content'] = $data;
			}
			else {
				$http['content'] = http_build_query($data);
			}
		}
		else {
			$http['content'] = "";
		}
		$http['header'] .= "Content-Length:".strlen($http['content'])."\r\n";
		$http['Content-Length'] = strlen($http['content']);
		$options = array(
			'http' => $http,
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
        );
		$context = stream_context_create($options);

		try {
			$result->content = @file_get_contents($url, false, $context);

			if ($result->content === false) {
				throw new ItopManagerException('Erreur de communication 1');
			}
			$result->header = self::parseHeaders($http_response_header);
		}
		catch (Exception $e) {
			throw new ItopManagerException('Erreur de communication 2');
		}
		return ($result);
	}

	private function baseData($operation, $class, $parameters) {
		$data = array(
			'auth_user' => $this->auth_user,
			'auth_pwd' => $this->auth_pwd,
			'json_data' => ''
		);
		$json_data = array(
			"operation" => $operation,
			'comment' => '',
			'class' => $class,
		);
		foreach ($parameters as $key => $value) {
			$json_data[$key] = $value;
		}
		$data['json_data'] = json_encode($json_data);
		return ($data);
	}

	/**
	 * @param string $class classname
	 * @param string $fields array('key' => 'value')
	 * @return int id
	 * @throws on error
	 **/
	public function create($class, $fields) {
		$data = self::baseData('core/create', $class, array('fields' => $fields));
		$ret = self::request('POST', $this->server, $data);
		if (isset($ret) && isset($ret->header['reponse_code']) && $ret->header['reponse_code'] == 200) {
			$content = json_decode($ret->content);
			if (isset($content->code) && $content->code == 0) {
				return (current($content->objects)->key);
			}
			else {
				throw new ItopManagerException("Impossible de créer l'objet dans itop: ".$content->code.": ".$content->message);
			}
		}
		throw new ItopManagerException("Error communication with iTop");
	}

	/**
	 * @param string $class classname
	 * @param string $output_fields output fields separated by ',';
	 * @param string $key array('key' => 'value') or $key as id of object
	 * @return array of object with key and fields
	 * @throws on error
	 **/
	public function read($class, $output_fields, $key) {
		$data = self::baseData('core/get', $class, array('key' => $key, 'output_fields' => $output_fields));
		$ret = self::request('POST', $this->server, $data);

		if (isset($ret) && isset($ret->header['reponse_code']) && $ret->header['reponse_code'] == 200) {
			$content = json_decode($ret->content);
			if (!(isset($content->code) && $content->code == 0)) {
				throw new ItopManagerException("Impossible de récuperer l'objet dans itop: ");
			}
		}
		else {
			throw new ItopManagerException("Error communication with iTop");
		}
		$ret = $ret->content;
		$ret = json_decode($ret, true);
		if ($ret['objects'] != null) {
			return ($ret['objects']);
		}
		return (array());
	}

	/**
	 * @param string $class classname
	 * @param string $fields array('key' => 'value')
	 * @param string $key array('key' => 'value') or $key as id of object
	 * @return int id
	 * @throws on error
	 **/
	public function update($class, $key, $fields) {
		$data = self::baseData('core/update', $class, array(
			'fields' => $fields,
			'key' => $key));

		$ret = self::request('POST', $this->server, $data);
		if (isset($ret) && isset($ret->header['reponse_code']) && $ret->header['reponse_code'] == 200) {
			$content = json_decode($ret->content);
			if (isset($content->code) && $content->code == 0) {
				return (current($content->objects)->key);
			}
			else {
				throw new ItopManagerException("Impossible d'update l'objet dans itop: ");
			}
		}
		throw new ItopManagerException("Error communication with iTop");
	}

	/**
	 * @param string $class classname
	 * @param string $key array('key' => 'value') or $key as id of object
	 * @throws on error
	 * @return boolean true
	 **/
	public function delete($class, $key) {
		$data = self::baseData('core/delete', $class, array(
			'class' => $class,
			'key' => $key));
		$ret = self::request('POST', $this->server, $data);
		if (isset($ret) && isset($ret->header['reponse_code']) && $ret->header['reponse_code'] == 200) {
			$content = json_decode($ret->content);
			if (isset($content->code) && $content->code == 0) {
				return (true);
			}
			else {
				$str = 'Impossible de supprimer l\'objet dans itop';
				if (isset($content->code)) {
					$str .= ': '.$content->code;
				}
				if (isset($content->message)) {
					$str .= ': '.$content->message;
				}
				throw new ItopManagerException($str, -5);
			}
		}
		throw new ItopManagerException("Error communication with iTop");
    }

    public function login($user , $pwd){
        $json_data = ['operation' => 'core/check_credentials', 'user' => $user , 'password' => $pwd];
        $data = array(
			'auth_user' => $this->auth_user,
			'auth_pwd' => $this->auth_pwd,
			'json_data' => ''
        );
        $data['json_data'] = json_encode($json_data);
        $ret = self::request('POST', $this->server, $data);
        $content = json_decode($ret->content);
        if($ret->header['reponse_code'] == 200)
        {
            return $content->authorized;
        }else{
            return 'error';
        }
    }
}
