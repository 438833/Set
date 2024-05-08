<?php

namespace set\engine;

class ManagerHandler
{

	public function _start()
	{
		$this->started = true;
		$dispatched = false;
		$middleware_check = false;
		$self = $this;
        $this->after(
			'start'
			, function($self)
			{
				return function($filters)
				use($self)
				{
					$self->filters = $filters;
				};
			}
		);
		$encoding = $this->encoding();
		$crypto = $this->crypto();
		$request = $this->request();
        $response = $this->response();
		$router = $this->router();
        if(ob_get_length() > 0)
		{
            $response->write(ob_get_clean());
        }
		if($request->getCli())
		{
			if($this->get('set.console.charset') === strtolower('Cp866'))
			{
				ob_start(array($encoding, 'strUtfToCp866'));
			}
		}
		else
		{
			ob_start('ob_iconv_handler');
		}
		if(!empty($router->routes))
		{
			$middleware_check = false;
			while($route = $router->routing($request))
			{
				$params = array_values($route->params);
				if($route->middleware && $this->processMiddleware($route, 'before'))
				{
					$middleware_check = true;
					break;
				}
				$continue = $this->invoke($route->callback, $params);
				if($route->middleware && $this->processMiddleware($route, 'after'))
				{
					$middleware_check = true;
					break;
				}
				$dispatched = true;
				if(!$continue)
				{
					break;
				}
				else
				{
					return $continue;
				}
				$router->next();
				$dispatched = false;
			}
			if($middleware_check === true)
			{
				$this->halt(403, 'Обнаружены нарушения безопасности в одном из middleware');
			}
			elseif($dispatched === false)
			{
				$this->notFound();
			}
		}
		$router->clear();
		$router->reset();
		return $this;
	}
    public function _error($e, $charset = false)
	{
		$type = $this->get('set.type');
        $msg = sprintf(
			'<h1>500 Внутренняя ошибка сервера</h1>'.
            '<h3>%s (%s)</h3>'.
            '<pre>%s</pre>'
			, $e->getMessage()
			, $e->getCode()
			, $e->getTraceAsString()
        );
        try
		{
            $this->response()
                ->clear()
                ->status(500)
				->type($type, $charset)
                ->write($msg)
                ->send();
        }
        catch(\Throwable $t)
		{
			// PHP 7.0+
            exit($msg);
        }
		catch(\Exception $e)
		{
			// PHP < 7
            exit($msg);
        }
    }
	public function _halt($code = 200, $message = '', $exit = true, $charset = false)
    {
		$type = $this->get('set.type');
        $this->response()
			->clear()
			->status($code)
			->type($type, $charset)
			->write($message)
			->send();
        if($exit === true)
		{
			exit();
		}
    }
    public function _group($string, $callback, $middlewares = false)
	{
		$this->router()->add($string, $callback, 'group', $middlewares);
        return $this;
    }
    public function _route($string, $callback, $middlewares = false)
	{
		$this->router()->add($string, $callback, 'route', $middlewares);
        return $this;
    }
    public function _fetch($string, $callback, $middlewares = false)
	{
		$this->router()->add('method:GET url:'.$string, $callback, 'route', $middlewares);
        return $this;
    }
    public function _post($string, $callback, $middlewares = false)
	{
		$this->router()->add('method:POST url:'.$string, $callback, 'route', $middlewares);
        return $this;
    }
    public function _any($string, $callback, $middlewares = false)
	{
		$this->router()->add($string, $callback, 'route', $middlewares);
        return $this;
    }
    public function _render($file, $data = null, $key = null)
	{
        if($key !== null)
		{
            $this->html()->set($key, $this->html()->fetch($file, $data));
        }
        else
		{
            $this->html()->render($file, $data);
        }
    }
    public function _redirect($url, $code = 303)
	{
        $base = $this->get('set.base_url');
        if($base === null)
		{
            $base = $this->request()->base;
        }
        if($base != '/' && strpos($url, '://') === false)
		{
            $url = $base . preg_replace('#/+#', '/', '/'.$url);
        }
        $this->response()
            ->clear()
            ->status($code)
            ->header('Location', $url)
            ->send();
    }
    public function _notFound($charset = false)
	{
		$type = $this->get('set.type');
        $this->response()
            ->clear()
            ->status(404)
			->type($type, $charset)
            ->write(
                '<h1>404 Не найдено</h1>'
                .'<h3>Запрошенная страница не может быть найдена</h3>'
                .str_repeat(' ', 512)
            )
            ->send();
    }
    public function _json(
        $data
        , $code = 200
        , $encode = true
        , $charset = false
        , $option = 0
    )
	{
        $json = $encode ? json_encode($data, $option) : $data;
        $this->response()
            ->status($code)
			->type('json', $charset)
            ->write($json)
            ->send();
    }
    public function _jsonp(
        $data
        , $param = 'jsonp'
        , $code = 200
        , $encode = true
        , $charset = false
        , $option = 0
    )
	{
        $json = ($encode) ? json_encode($data, $option) : $data;
        $callback = $this->request()->query[$param];
        $this->response()
            ->status($code)
            ->type('js', $charset)
            ->write($callback.'('.$json.');')
            ->send();
    }
	public function _etag($id, $type = 'strong')
	{
        $id = ($type === 'weak' ? 'W/' : '').$id;
        $this->response()->header('ETag', '"'.str_replace('"', '\"', $id).'"');
        if(
			isset($_SERVER['HTTP_IF_NONE_MATCH'])
			&& $_SERVER['HTTP_IF_NONE_MATCH'] === $id
		)
		{
            $this->halt(304);
        }
    }
	public function _lastModified($time)
    {
        $this->response()->header('Last-Modified', gmdate('D, d M Y H:i:s \G\M\T', $time));
        if(
            isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
			&& strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) === $time
		)
		{
            $this->halt(304);
        }
    }
}

?>