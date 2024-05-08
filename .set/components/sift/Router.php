<?php

namespace set\components\sift;

use set\components\net;

class Router
{

	public $routes = array();
    public $index = 0;
	public $group_prefix = '';
	protected $group_middlewares = array();
    public $keys = array(
		'ip' => '[\d\.:]+'
		, 'host' =>  '[\w\.-]+'
		, 'port' => '\d+'
		, 'url' => '[\w\/\(\)\@]+'
		, 'method' => '[A-Z\(\)\|\*]+'
		, 'referrer' => '[^\s]+'
		, 'ajax' => '[^\s]+'
		, 'scheme' => '[^\s]+'
		, 'user_agent' => '[^\s]+'
		, 'type' => '[^\s]+'
		, 'proxy_ip' => '[\d\.:]+'
		, 'pattern' => '\(.*?\)'
	);

    public function current()
    {
        return isset($this->routes[$this->index]) ? $this->routes[$this->index] : false;
    }
    public function next()
    {
        $this->index++;
    }
    public function reset()
    {
        $this->index = 0;
    }
    public function clear()
    {
		$this->routes = array();
    }
    public function getRoutes()
    {
        return $this->routes;
    }
	public function parseString($input)
	{
		$data = array();
		$pattern = '/';
		foreach($this->keys as $key => $value)
		{
			$pattern .= '(?:' . $key . '\s*[:\s]*(' . $value . ').*?)?';
		}
		$pattern .= '/i';
		preg_match_all($pattern, $input, $matches, PREG_SET_ORDER);
		foreach($matches as $match)
		{
			for($i = 1, $l = count($match); $i < $l; $i++)
			{
				if(!empty($match[$i]))
				{
					$keys = array_keys($this->keys);
					$data[$keys[$i - 1]] = trim($match[$i]);
				}
			}
		}
		return $data;
	}
	public function transformString($input)
	{
		$output = preg_replace('/^(GET|POST) \/(\S+)$/', 'method:$1 url:/$2', $input);
		$output = preg_replace('/^\* \/(\S+)$/', 'method:(.*) url:/$1', $output);
		$output = preg_replace('/^($1) \/(\S+)$/', 'method:($1) url:/$2', $output);
		$output = preg_replace('/^ALL \*$/', '*', $output);
		return $output;
	}
    public function add($string, $callback = false, $type = 'route', $middlewares = array(), $flag = 3)
	{
		$type = $type ? $type : 'route';
		$string = preg_replace('/\s+/', ' ', $string);
		if($middlewares === false || empty($middlewares))
		{
			$middlewares = array();
		}
		elseif(is_array($middlewares) === true)
		{
			$middlewares = $middlewares;
		}
		elseif(is_string($middlewares) === true || is_object($middlewares) === true)
		{
			$middlewares = array($middlewares);
		}
		else
		{
			$middlewares = array();
		}
		if($type == 'route')
		{
			$string = $this->group_prefix !== '' ? trim($this->group_prefix.$string) : trim($string);
			$string = $this->transformString($string);
			$route = new Route($string, $callback, $flag);
			if(is_array($middlewares) === true && count($middlewares) > 0)
			{
				$route->addMiddleware($middlewares[0]);
			}
			else
			{
				for($i = 0, $l = count($this->group_middlewares); $i < $l; $i++)
				{
					$route->addMiddleware($this->group_middlewares[$i]);
				}
			}
			$this->routes[] = $route;
			return $route;
		}
		elseif($type == 'group')
		{
			$group_prefix = $this->group_prefix;
			$group_middlewares = $this->group_middlewares;
			$this->group_prefix .= $string;
			$this->group_middlewares = array_merge($this->group_middlewares, $middlewares);
			$callback && $callback($this);
			$this->group_prefix = $group_prefix;
			$this->group_middlewares = $group_middlewares;
		}
	}
    public function routing($request)
    {
		$requestProperties = get_object_vars($request);
		while($route = $this->current())
        {
			$string = $route->pattern;
			$callback =  $route->callback;
			$array = $this->parseString($string);
			$haystack = '';
			if(!isset($array) || !is_array($array) || empty($array))
			{
				if(isset($requestProperties['url']))
				{
					$haystack = $requestProperties['url'];
				}
			}
			else
			{
				foreach($array as $key => $value)
				{
					if($key !== 'url')
					{
						$string .= "{$key}:{$value} ";
						if(isset($requestProperties[$key]))
						{
							$haystack .= "{$key}:{$requestProperties[$key]} ";
						}
					}
				}
				if(isset($requestProperties['url']))
				{
					$haystack .= "url:{$requestProperties['url']}";
				}
			}
			$check = $route->match($haystack);
			if($check)
			{
				return $route;
			}
            $this->next();
        }
        return false;
    }

}

?>