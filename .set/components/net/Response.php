<?php

namespace set\components\net;

class Response
{

	private $debug = true;

    protected $status = 200;

    protected $headers = array();

    protected $body;

    protected $sent = false;

    public $content_length = true;

    public $codes = array(
		/* 1xx: Informational (информационные): */
		100 => array('Continue' , 'Продолжай')
		, 101 => array('Switching Protocols' , 'Переключение протоколов')
		, 102 => array('Processing' , 'Идёт обработка')
		, 103 => array('Early Hints' , 'Ранняя метаинформация')
		/** 2xx: Success (успешно): */
		, 200 => array('OK' , 'Хорошо')
		, 201 => array('Created' , 'Создано')
		, 202 => array('Accepted' , 'Принято')
		, 203 => array('Non-Authoritative Information' , 'Информация не авторитетна')
		, 204 => array('No Content' , 'Нет содержимого')
		, 205 => array('Reset Content' , 'Сбросить содержимое')
		, 206 => array('Partial Content' , 'Частичное содержимое')
		, 207 => array('Multi-Status' , 'Многостатусный')
		, 208 => array('Already Reported' , 'Уже сообщалось')
		, 226 => array('IM Used' , 'Использовано IM')
		/* 3xx: Redirection (перенаправление): */
		, 300 => array('Multiple Choices' , 'Множество выборов')
		, 301 => array('Moved Permanently' , 'Перемещено навсегда')
		, 302 => array('Moved Temporarily' , 'Перемещено временно')
		, 302 => array('Found' , 'Найдено')
		, 303 => array('See Other' , 'Смотреть другое')
		, 304 => array('Not Modified' , 'Не изменялось')
		, 305 => array('Use Proxy' , 'Использовать прокси')
		, 306 => array('', '')
		, 307 => array('Temporary Redirect' , 'Временное перенаправление')
		, 308 => array('Permanent Redirect' , 'Постоянное перенаправление')
		/* 4xx: Client Error (ошибка клиента): */
		, 400 => array('Bad Request' , 'Неправильный, некорректный запрос')
		, 401 => array('Unauthorized' , 'Не авторизован')
		, 402 => array('Payment Required' , 'Необходима оплата')
		, 403 => array('Forbidden' , 'Запрещено')
		, 404 => array('Not Found' , 'Не найдено')
		, 405 => array('Method Not Allowed' , 'Метод не поддерживается')
		, 406 => array('Not Acceptable' , 'Неприемлемо')
		, 407 => array('Proxy Authentication Required' , 'Необходима аутентификация прокси')
		, 408 => array('Request Timeout' , 'Истекло время ожидания')
		, 409 => array('Conflict' , 'Конфликт')
		, 410 => array('Gone' , 'Удалён')
		, 411 => array('Length Required' , 'Необходима длина')
		, 412 => array('Precondition Failed' , 'Условие ложно')
		, 413 => array('Payload Too Large' , 'Полезная нагрузка слишком велика')
		, 414 => array('URI Too Long' , 'URI слишком длинный')
		, 415 => array('Unsupported Media Type' , 'Неподдерживаемый тип данных')
		, 416 => array('Range Not Satisfiable' , 'Диапазон не достижим')
		, 417 => array('Expectation Failed' , 'Ожидание не удалось')
		, 418 => array('I’m a teapot' , 'Я — чайник')
		, 419 => array('Authentication Timeout (not in RFC 2616)' , 'Ошибка проверки CSRF')
		, 421 => array('Misdirected Request', '')
		, 422 => array('Unprocessable Entity' , 'Необрабатываемый экземпляр')
		, 423 => array('Locked' , 'Заблокировано')
		, 424 => array('Failed Dependency' , 'Невыполненная зависимость')
		, 425 => array('Too Early' , 'Слишком рано')
		, 426 => array('Upgrade Required' , 'Необходимо обновление')
		, 428 => array('Precondition Required' , 'Необходимо предусловие')
		, 429 => array('Too Many Requests' , 'Слишком много запросов')
		, 431 => array('Request Header Fields Too Large' , 'Поля заголовка запроса слишком большие')
		, 449 => array('Retry With' , 'Повторить с')
		, 451 => array('Unavailable For Legal Reasons' , 'Недоступно по юридическим причинам')
		, 499 => array('Client Closed Request', 'Клиент закрыл соединение')
		/* 5xx: Server Error (ошибка сервера): */
		, 500 => array('Internal Server Error' , 'Внутренняя ошибка сервера')
		, 501 => array('Not Implemented' , 'Не реализовано')
		, 502 => array('Bad Gateway' , 'Плохой, ошибочный шлюз')
		, 503 => array('Service Unavailable' , 'Сервис недоступен')
		, 504 => array('Gateway Timeout' , 'Шлюз не отвечает')
		, 505 => array('HTTP Version Not Supported' , 'Версия HTTP не поддерживается')
		, 506 => array('Variant Also Negotiates' , 'Вариант тоже проводит согласование')
		, 507 => array('Insufficient Storage' , 'Переполнение хранилища')
		, 508 => array('Loop Detected' , 'Обнаружено бесконечное перенаправление')
		, 509 => array('Bandwidth Limit Exceeded' , 'Исчерпана пропускная ширина канала')
		, 510 => array('Not Extended' , 'Не расширено')
		, 511 => array('Network Authentication Required' , 'Требуется сетевая аутентификация')
		, 520 => array('Unknown Error' , 'Неизвестная ошибка')
		, 521 => array('Web Server Is Down' , 'Веб-сервер не работает')
		, 522 => array('Connection Timed Out' , 'Соединение не отвечает')
		, 523 => array('Origin Is Unreachable' , 'Источник недоступен')
		, 524 => array('A Timeout Occurred' , 'Время ожидания истекло')
		, 525 => array('SSL Handshake Failed' , 'Квитирование SSL не удалось')
		, 526 => array('Invalid SSL Certificate' , 'Недействительный сертификат SSL')
    );

	public function type($type = 'html', $charset = 'utf-8')
	{
		$charset = $charset === false ? 'utf-8' : $charset;
		$mime_types = array(
			'txt' => "text/plain; charset={$charset}"
			, 'html' => "text/html; charset={$charset}"
			, 'json' => "application/json; charset={$charset}"
			, 'js' => "application/javascript; charset={$charset}"
			, 'php' => "text/plain; charset={$charset}"
		);
		if(isset($mime_types[$type]))
		{
			$this->header('Content-Type', $mime_types[$type]);
		}
		else
		{
			throw new \Exception('Недопустимый тип документа');
		}
		return $this;
	}

    public function status($code = null)
    {
        if($code === null)
        {
            return $this->status;
        }
        if(isset($this->codes[$code]))
        {
            $this->status = $code;
        }
        else
        {
            throw new Exception('Недействительный код статуса');
        }
        return $this;
    }

    public function header($name, $value = null)
    {
        if(is_array($name))
        {
            foreach($name as $k => $v)
            {
                $this->headers[$k] = $v;
            }
        }
        else
        {
            $this->headers[$name] = $value;
        }
        return $this;
    }

    public function headers()
    {
        return $this->headers;
    }

    public function write($str)
    {
        $this->body .= $str;
        return $this;
    }

    public function clear()
    {
        $this->status = 200;
        $this->headers = array();
        $this->body = '';
        return $this;
    }

    public function cache($expires)
    {
        if($expires === false)
        {
            $this->headers['Expires'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
            $this->headers['Cache-Control'] = array(
                'no-store, no-cache, must-revalidate'
                , 'post-check=0, pre-check=0'
                , 'max-age=0'
            );
            $this->headers['Pragma'] = 'no-cache';
        }
        else
        {
            $expires = is_int($expires) ? $expires : strtotime($expires);
            $this->headers['Expires'] = gmdate('D, d M Y H:i:s', $expires).' GMT';
            $this->headers['Cache-Control'] = 'max-age=' . ($expires - time());
            if(isset($this->headers['Pragma']) && $this->headers['Pragma'] == 'no-cache')
            {
                unset($this->headers['Pragma']);
            }
        }
        return $this;
    }

    public function sendHeaders()
    {
        // Отправка заголовка кода состояния
        if(strpos(php_sapi_name(), 'cgi') !== false) 
        {
            header(
                sprintf(
                    'Status: %d %s'
                    , $this->status
                    , $this->codes[$this->status][1]
                )
                , true
            );
        }
        else
        {
            header(
                sprintf(
                    '%s %d %s'
                    , (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1')
                    , $this->status
                    , $this->codes[$this->status][1]
				)
                , true
                , $this->status
            );
        }
        // Отправка других заголовков
        foreach($this->headers as $field => $value)
        {
            if(is_array($value))
            {
                foreach ($value as $v)
                {
                    header($field.': ' . $v, false);
                }
            }
            else
            {
                header($field.': '.$value);
            }
        }
        // Отправка размера содержимого
		if($this->content_length)
		{
			$length = $this->getContentLength();
			if($length > 0)
			{
				header('Content-Length: ' . $length);
			}
		}
        return $this;
    }

    public function getContentLength()
    {
        return extension_loaded('mbstring') ?
            mb_strlen($this->body, 'latin1') :
            strlen($this->body);
    }

    public function sent()
    {
        return $this->sent;
    }

    public function send()
    {
        if(ob_get_length() > 0)
        {
            ob_end_clean();
        }
        if(!headers_sent() || $this->debug)
        {
			$this->sendHeaders();
        }
        echo $this->body;
        $this->sent = true;
    }

}

?>