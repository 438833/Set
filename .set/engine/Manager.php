<?php

namespace set\engine;

use set\engine\ManagerHandler as Handler;

final class Manager extends Handler
{

	protected $timeStart;
	protected $memoryStart;
	protected $started = false;
	protected $classes = array();
	protected $events = array();
    protected $vars;
	protected $filters = array();

    public function __construct()
	{
		$this->timeStart = microtime(true);
		$this->memoryStart = memory_get_usage();
        $this->vars = array();
        $this->init();
    }
    public function __destruct()
	{
        $this->deinit();
        $this->vars = array();
    }
	public function __call($name, $params)
	{
		if(method_exists($this, $name))
		{
			$method = $name;
		}
		else
		{
			if(is_string($name) !== false)
			{
				$instance = $this->run($name, $params);
				return $instance;
			}
		}
		$function = array($this, $method);
		return \call_user_func_array($function, $params);
    }

	private function diff($startTime, $startMemory)
	{
		$memory = memory_get_usage() - $startMemory;
		$name = array('байт', 'кб', 'мб');
		$i = 0;
		while(floor($memory / 1024) > 0)
		{
			$i++;
			$memory /= 1024;
		}
		$object = (object)array(
			'time' => number_format(microtime(true) - $startTime, 3) . ' сек'
			, 'memory' =>  number_format($memory, 2) . ' ' . $name[$i]
		);
		return $object;
	}

	private function extractClassAndMethod($string)
	{
		if(preg_match('/^(.+)(?:->|::)(.+)$/', $string, $matches))
		{
			return array($matches[1], $matches[2]);
		}
		return array(null, null);
	}

	private function processMiddleware($route, $type = 'before')
    {
        $failed = false;
		if(count($route->middleware) > 0)
		{
			$middlewares = $type === 'before' ? $route->middleware : array_reverse($route->middleware);
			$params = $route->params;
			for($i = 0, $l = count($middlewares); $i < $l; $i++)
			{
				$middleware = $middlewares[$i];
				$object = false;
				if($type === 'before')
				{
					$object = is_callable($middleware) === true
						? $middleware
						: (
							method_exists($middleware, 'before')
							? array($middleware, 'before')
							: false
						);
				}
				elseif($type === 'after')
				{
					if(is_object($middleware) && !($middleware instanceof Closure) && method_exists($middleware, 'after'))
					{
						$object = array($middleware, 'after');
					}
				}
				if($object === false)
				{
					continue;
				}
				$result = call_user_func($object, $params);
				if($result === false)
				{
					$failed = true;
					break;
				}
			}
		}
        return $failed;
    }

    private function filter($filters, &$params, &$output)
    {
		$filtersKeys = array_keys($filters);
		for($i = 0, $l = count($filtersKeys); $i < $l; $i++)
		{
			$key = $filtersKeys[$i];
			$callback = $filters[$key];
            if(!is_callable($callback))
			{
                throw new InvalidArgumentException("Недопустимый вызов {$filters[$key]}");
            }
            $continue = $callback($params, $output);
            if($continue === false)
			{
                break;
            }
        }
    }

	private function run($name, $params = array())
	{
        $output = '';
        if(!empty($this->filters[$name]['before']))
		{
            $this->filter($this->filters[$name]['before'], $params, $output);
        }
        $output = $this->instance($name, $params);
        if(!empty($this->filters[$name]['after']))
		{
            $this->filter($this->filters[$name]['after'], $params, $output);
        }
        return $output;
    }

	private function invoke($function, $params = array())
	{
		if(is_array($function) === true || is_string($function) === true)
		{
			if(is_array($function) === true)
			{
				list($class, $method) = $function;
				if(is_object($class))
				{
					return \call_user_func_array($function, $params);
				}
				$function = $class.'::'.$method;
			}
			return $this->run($function, $params);
		}
		if(is_object($function))
		{
			$className = get_class($function);
			$methods = get_class_methods($className);
			if(count($methods) === 1 && $methods[0] === '__construct')
			{
				$this->setevent($className, $function);
				return $this->instance($className, $params);
			}
		}
		return \call_user_func_array($function, $params);
	}

    public function set($key, $value = null)
	{
        if(\is_array($key) || \is_object($key))
		{
			$keys = array_keys($key);
			for($i = 0, $l = count($keys); $i < $l; $i++)
			{
				$this->vars[$keys[$i]] = $key[$i];
            }
        }
        else
		{
            $this->vars[$key] = $value;
        }
		return $this;
    }

    public function get($key = null)
	{
        if($key === null)
		{
			return $this->vars;
		}
        return isset($this->vars[$key]) ? $this->vars[$key] : null;
    }

    public function has($key)
	{
        return isset($this->vars[$key]);
    }

    public function clear($key = null)
	{
        if(is_null($key))
		{
            $this->vars = array();
        }
        else
		{
            unset($this->vars[$key]);
        }
    }

	public function setclass($name, $class, $params = false, $callback = false, $instance = false)
	{
        $this->classes[$name] = array($class, $params, $callback, $instance);
    }

	public function getclass($name)
	{
        return isset($this->classes[$name]) ? $this->classes[$name] : false;
    }

	public function setevent($name, $callback)
    {
        $this->events[$name] = $callback;
        return $this;
    }

    public function getevent($name)
    {
        return isset($this->events[$name]) ? $this->events[$name] : false;
    }

	public function instance($name, $params = array())
	{
		$method = false;
        $array = $this->getclass($name);
		$instance = $this->getevent($name);
		if($array && \is_array($array))
		{
			list($class, $parameters, $callback) = $array;
			$params = $params === false || empty($params) ? $parameters : $params;
			if(is_string($class) === true && (strpos($class, '->') !== false || strpos($class, '::') !== false))
			{
				list($class, $method) = $this->extractClassAndMethod($class);
			}
			if($params && !is_array($params))
			{
				$params = array($params);
			}
			if(!$instance)
			{
				if(!class_exists($class))
				{
					throw new \Exception("Метод или класс {$class} не определён");
				}
				if($params && $method === false)
				{
					$refClass = new \ReflectionClass($class);
					if($refClass->hasMethod('__construct'))
					{
						$instance = $refClass->newInstanceArgs($params);
					}
					else
					{
						$instance = $refClass->newInstanceWithoutConstructor();
					}
				}
				else
				{
					$instance = new $class();
				}
				$this->setclass($class, false, $callback);
				$this->setevent($name, $instance);
			}
			if($method)
			{
				if(method_exists($instance, $method))
				{
					return \call_user_func_array(array($instance, $method), $params);
				}
			}
			if($callback)
			{
				\call_user_func($callback, $instance);
			}
			return $instance;
		}
		else
		{
			if(is_callable($instance))
			{
				return \call_user_func_array($instance, $params);
			}
			else
			{
				if(is_string($name) === true)
				{
					$this->register($name, $name, $params);
					return $this->run($name, $params);
				}
				throw new \Exception('Невозможно переопределить существующий метод платформы');
			}
		}
		return false;
	}

    public function register($name, $class, $params = array(), $callback = false)
	{
        $this->setclass($name, $class, $params, $callback);
		return $this;
    }

    public function map($name, $callback)
    {
        if(method_exists($this, $name))
		{
			throw new Exception('Невозможно переопределить существующий метод платформы');
        }
		return $this->setevent($name, $callback);
    }

    public function middle($middleware)
    {
		if(is_array($middleware) === true)
		{
            $this->middleware = array_merge($this->middleware, $middleware);
        }
		else
		{
            $this->middleware[] = $middleware;
        }
        return $this;
    }

    public function before($name, $callback)
	{
		$this->filters[$name]['before'][] = $callback;
		return $this;
    }

    public function after($name, $callback)
	{
		$this->filters[$name]['after'][] = $callback;
		return $this;
    }

    public function init()
	{
		$self = $this;
		$this->vars = array();
        $customArray = array(
			'configuration' => array(
                array('set.version', '1.2.1')
				, array('set.base_url', null)
                , array('set.start', true)
				, array('set.console.charset', 'utf8')
                , array('set.type', 'html')
                , array('set.template.path', realpath(__DIR__.DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR.'manlix')
                , array('set.template.name', 'manlix')
                , array('set.global.module', 'news')
            )
			, 'register' => array(
				array('encoding', '\set\components\util\Encoding')
				, array('crypto', '\set\components\util\CryptoAes')
				, array('request', '\set\components\net\Request')
				, array('response', '\set\components\net\Response')
				, array('router', '\set\components\sift\Router')
				, array(
					'html'
					, '\set\components\template\Html'
					, array()
					, function($html)
					use($self)
					{
						$html->path = $self->get('set.template.path');
					}
                )
            )
			, 'methods' => array(
                'start', 'stop', 'error', 'halt'
				, 'render', 'group', 'route', 'redirect'
				, 'fetch', 'post', 'any'
				, 'notFound'
				, 'etag', 'lastModified', 'json', 'jsonp'
            )
        );
		$customArrayKeys = array_keys($customArray);
		for($i = 0, $l = count($customArray); $i < $l; $i++)
        {
			$row = $customArrayKeys[$i];
			$innerArray = $customArray[$row];
			for($y = 0, $j = count($innerArray); $y < $j; $y++)
            {
				$value = $innerArray[$y];
                switch($row)
                {
                    case 'configuration':
                        call_user_func_array(array($this, 'set'),  $value);
                        break;
                    case 'register':
                        call_user_func_array(array($this, 'setclass'),  $value);
                        break;
                    case 'methods':
						call_user_func_array(array($this, 'setevent'),  array($value, array($this, '_' . $value)));
                        break;
                }
            }
        }
	}

    public function on()
	{
		static $initialized = false;
		if($initialized)
		{
			return;
		}
		$initialized = true;
		if($this->get('set.start'))
		{
			return $this->start();
		}
		return $this;
	}

    public function off()
	{
		exit();
	}

    public function deinit()
	{
		$this->off();
	}

}

?>