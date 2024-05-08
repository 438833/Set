<?php

namespace set\components\net;

use set\components\util\Collection;

class Request
{

    public $url;

    public $base;

    public $method;

    public $referrer;

    public $ajax;

    public $scheme;

    public $user_agent;

    public $type;

    public $length;

    public $host;

    public $port;

    public $ip;

    public $proxy_ip;

    public $query;

    public $data;

    public $cookies;

    public $files;

    public $args;

    public function __construct($config = array())
    {
        if(empty($config))
        {
            $config = array(
                'url' => str_replace('@', '%40', self::getVar('REQUEST_URI', '/'))
                , 'base' => str_replace(array('\\',' '), array('/','%20'), dirname(self::getVar('SCRIPT_NAME')))
                , 'method' => self::getMethod()
                , 'referrer' => self::getVar('HTTP_REFERER')
                , 'ajax' => self::getVar('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest'
                , 'scheme' => self::getScheme()
                , 'user_agent' => self::getVar('HTTP_USER_AGENT')
                , 'type' => self::getVar('CONTENT_TYPE')
                , 'length' => self::getVar('CONTENT_LENGTH', 0)
				, 'host' => strtolower(self::getVar('HTTP_HOST', 'localhost'))
				, 'port' => strtolower(self::getVar('REMOTE_PORT', 80))
                , 'ip' => self::getIpAddress()
                , 'proxy_ip' => self::getProxyIpAddress()
                , 'query' => new Collection($_GET)
                , 'data' => new Collection($_POST)
                , 'cookies' => new Collection($_COOKIE)
                , 'files' => new Collection($_FILES)
                , 'args' => isset($_SERVER['argv']) ? new Collection($_SERVER['argv']) : new Collection()
            );
        }
        $this->init($config);
    }

    public function init($properties = array())
    {
        foreach($properties as $name => $value)
        {
            $this->{$name} = $value;
        }
        $this->url = ($this->base != '/' && strlen($this->base) > 0 && strpos($this->url, $this->base) === 0) 
			? substr($this->url, strlen($this->base)) 
			: $this->url;
        $this->url = empty($this->url) ? '/' : $this->url;
		if(!empty($this->url))
		{
			$this->query->setData(array_merge($_GET, self::parseQuery($this->url)));
		}
		if(strpos($this->type, 'application/json') === 0)
		{
			$body = $this->getBody();
			if($body && ($data = json_decode($body, true)) !== null)
			{
				$this->data->setData($data);
			}
		}
		return $this;
	}

	static public function parseQuery($url)
    {
        $params = array();
        $args = parse_url($url);
        if(isset($args['query']))
        {
            parse_str($args['query'], $params);
        }
        return $params;
    }

    static public function getVar($var, $default = '')
    {
        return isset($_SERVER[$var]) ? $_SERVER[$var] : $default;
    }

    static public function getScheme()
    {
        return (
            (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) === 'on' || $_SERVER['HTTPS'] == 1))
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
            || (isset($_SERVER['HTTP_FRONT_END_HTTPS']) && $_SERVER['HTTP_FRONT_END_HTTPS'] === 'on')
            || (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https')
        ) ? 'https' : 'http';
    }

    static public function getMethod()
    {
        $method = isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) 
			? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] 
			: (
				isset($_REQUEST['_method']) 
				? $_REQUEST['_method'] 
				: self::getVar('REQUEST_METHOD', 'GET')
			);
		return strtoupper($method);
    }

    static public function getBody()
    {
        static $body;
        if(!is_null($body))
        {
            return $body;
        }
        $method = self::getMethod();
        if($method == 'POST' || $method == 'PUT' || $method == 'DELETE' || $method == 'PATCH')
        {
            $body = file_get_contents('php://input');
        }
        return $body;
    }

    static public function getIpAddress()
	{
		$ip = isset($_SERVER['HTTP_CLIENT_IP'])
			? $_SERVER['HTTP_CLIENT_IP'] 
			: (
				isset($_SERVER['HTTP_X_FORWARDED_FOR'])
				? $_SERVER['HTTP_X_FORWARDED_FOR']
				: (
					isset($_SERVER['REMOTE_ADDR'])
					? $_SERVER['REMOTE_ADDR']
					: '127.0.0.1'
				)
			);
		return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '127.0.0.1';
	}

    static public function getProxyIpAddress()
    {
        static $forwarded = array(
            'HTTP_CLIENT_IP'
            , 'HTTP_X_FORWARDED_FOR'
            , 'HTTP_X_FORWARDED'
            , 'HTTP_X_CLUSTER_CLIENT_IP'
            , 'HTTP_FORWARDED_FOR'
            , 'HTTP_FORWARDED'
        );
        $flags = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
        foreach($forwarded as $key)
        {
            if(isset($_SERVER[$key]))
			{
                sscanf($_SERVER[$key], '%[^,]', $ip);
                if(filter_var($ip, FILTER_VALIDATE_IP, $flags) !== false)
                {
                    return $ip;
                }
            }
        }
        return '';
    }

    static public function getCli()
    {
        return (
            defined('STDIN')
            || php_sapi_name() === 'cli'
            || isset($_ENV['SHELL'])
            || (empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0)
            || !isset($_SERVER['REQUEST_METHOD'])
        ) ? true : false;
	}

    static public function getCurl()
    {
        return (
            isset($_SERVER['HTTP_USER_AGENT'])
        ) ? (
            (stristr($_SERVER['HTTP_USER_AGENT'], 'curl')) ? true : false
        ) : false;
	}

}

?>