<?php

namespace set\engine;

/**
 * Библиотека для обработки исключений и ошибок
 */
class ErrorHandler
{
	
    static public $vesrion = '<a href="https://setcms.org">Set 1.0</a>';
    /**
     * Активный стек
     *
     * @var array
     */
    static public $stack;

    /**
     * Стиль загрузки валидатора
     *
     * @var bool
     */
    static public $styles = false;

    /**
     * Пользовательские методы
     *
     * @since 1.1
     *
     * @var bool
     */
    static public $customMethods = false;

    /**
     * Перехват ошибки и исключения и выполнение метода
     */
    public function __construct()
    {
		set_exception_handler(array($this, 'exception'));
		set_error_handler(array($this, 'error'));
    }

    /**
     * Обрабатка исключения
     *
     * @param object $e
     *                  string $e->getMessage()       → сообщение об исключении
     *                  int    $e->getCode()          → код исключения
     *                  string $e->getFile()          → файл
     *                  int    $e->getLine()          → строка
     *                  string $e->getTraceAsString() → трассировка в виде строки
     *                  int    $e->statusCode         → код статуса ответа HTTP
     */
    public function exception($e)
    {
        $traceString = preg_split("/#[\d]/", $e->getTraceAsString());
        unset($traceString[0]);
        array_pop($traceString);
        $trace = "\r\n<hr><br /><span class=\"handler-head\">Backtrace:</span>\r\n";
        foreach($traceString as $key => $value)
		{
			$value = str_replace('\\\\', '\\', $value);
            $trace .= "\n" . $key . ' &mdash; ' . $value;
        }
        $this->setParams(
            'Exception'
			, $e->getCode()
			, $e->getMessage()
			, $e->getFile()
			, $e->getLine()
			, $trace
			, (isset($e->statusCode)) ? $e->statusCode : 0
        );
        return $this->render();
    }

    /**
     * Обработка ошибок
     *
     * @param int $code → код ошибки
     * @param int $msg  → сообщение об ошибке
     * @param int $file → файл ошибок
     * @param int $line → строка ошибки
     *
     * @return boolean
     */
    public function error($code, $msg, $file, $line)
    {
        $type = $this->getErrorType($code);
        $this->setParams($type, $code, $msg, $file, $line, '', 0);
        return $this->render();
    }

    /**
     * Преобразование кода ошибки в текст
     *
     * @param int $code → код ошибки
     *
     * @return string → тип ошибки
     */
    public function getErrorType($code)
    {
        switch($code)
		{
            case E_ERROR:
                return self::$stack['type'] = 'Error'; // 1
            case E_WARNING:
                return self::$stack['type'] = 'Warning'; // 2
            case E_PARSE:
                return self::$stack['type'] = 'Parse'; // 4
            case E_NOTICE:
                return self::$stack['type'] = 'Notice'; // 8
            case E_CORE_ERROR:
                return self::$stack['type'] = 'Core-Error'; // 16
            case E_CORE_WARNING:
                return self::$stack['type'] = 'Core Warning'; // 32
            case E_COMPILE_ERROR:
                return self::$stack['type'] = 'Compile Error'; // 64
            case E_COMPILE_WARNING:
                return self::$stack['type'] = 'Compile Warning'; // 128
            case E_USER_ERROR:
                return self::$stack['type'] = 'User Error'; // 256
            case E_USER_WARNING:
                return self::$stack['type'] = 'User Warning'; // 512
            case E_USER_NOTICE:
                return self::$stack['type'] = 'User Notice'; // 1024
            case E_STRICT:
                return self::$stack['type'] = 'Strict'; // 2048
            case E_RECOVERABLE_ERROR:
                return self::$stack['type'] = 'Recoverable Error'; // 4096
            case E_DEPRECATED:
                return self::$stack['type'] = 'Deprecated'; // 8192
            case E_USER_DEPRECATED:
                return self::$stack['type'] = 'User Deprecated'; // 16384
            default:
                return self::$stack['type'] = 'Error';
        }
    }

    /**
     * Установка пользовательского метода для рендеринига
     *
     * @since 1.1
     *
     * @param string|object $class   → имя класса или объект класса
     * @param string        $method  → имя метода
     * @param int           $repeat  → количество повторений метода
     * @param bool          $default → показать представление по умолчанию
     */
    static public function setCustomMethod($class, $method, $repeat = 0, $default = false)
    {
        self::$customMethods[] = array($class, $method, $repeat, $default);
    }

    /**
     * Обработка ошибок
     *
     * @since 1.1
     *
     * @param int    $code  → код исключения/ошибки
     * @param int    $msg   → сообщение исключения/ошибки
     * @param int    $file  → файл исключения/ошибки
     * @param int    $line  → строка исключения/ошибки
     * @param string $trace → трассировка исключения/ошибки
     * @param string $http  → HTTP код ответа
     *
     * @return array → стэк
     */
    protected function setParams($type, $code, $msg, $file, $line, $trace, $http)
    {
        return self::$stack = array(
            'type' => $type
			, 'message' => $msg
			, 'file' => $file
			, 'line' => $line
			, 'code' => $code
			, 'http-code' => ($http === 0) ? http_response_code() : $http
			, 'trace' => $trace
			, 'preview' => ''
        );
    }

    /**
     * Предварительный просмотр строки ошибки
     *
     * @since 1.1
     */
    protected function getPreviewCode()
    {
        $file = file(self::$stack['file']);
		$count = count($file);
        $line = self::$stack['line'];
        $start = ($line - 5 >= 0) ? $line - 5 : $line - 1;
        $end = ($line - 5 >= 0) ? $line + 4 : $line + 8;
		$end = $end >= $count ? $count : $end;
        for($i = $start; $i < $end; $i++)
		{
            if(!isset($file[$i]))
			{
                continue;
            }
			$class = $i == $start ? ' first' : ($i == $end - 1 ? ' last' : '');
            $text = trim($file[$i]);
            if($i == $line - 1)
			{
                self::$stack['preview'] .=
                    '<span class="handler-line">' . ($i + 1) . '</span>' .
                    '<span class="handler-mark text">' . $text . '</span><br />';
                continue;
            }
            self::$stack['preview'] .=
                '<span class="handler-line'.$class.'">' . ($i + 1) . '</span>' .
                '<span class="text">' . self::highlight($text) . '</span><br />';
        }
    }

	protected function highlight($text, $fileExt = 'php')
	{
		if($fileExt == 'php')
		{
			ini_set('highlight.comment', '#008000');
			ini_set('highlight.default', '#000000');
			ini_set('highlight.html', '#808080');
			ini_set('highlight.keyword', '#0000BB; font-weight: bold');
			ini_set('highlight.string', '#DD0000');
		}
		else if($fileExt == 'html')
		{
			ini_set('highlight.comment', 'green');
			ini_set('highlight.default', '#CC0000');
			ini_set('highlight.html', '#000000');
			ini_set('highlight.keyword', 'black; font-weight: bold');
			ini_set('highlight.string', '#0000FF');
		}
		$text = trim($text);
		$text = highlight_string('<?php ' . $text, true);
		$text = trim($text);
		$text = preg_replace("|^\\<code\\>\\<span style\\=\"color\\: #[a-fA-F0-9]{0,6}\"\\>|", "", $text, 1);
		$text = preg_replace("|\\</code\\>\$|", "", $text, 1);
		$text = trim($text);
		$text = preg_replace("|\\</span\\>\$|", "", $text, 1);
		$text = trim($text);
		$text = preg_replace("|^(\\<span style\\=\"color\\: #[a-fA-F0-9]{0,6}\"\\>)(&lt;\\?php&nbsp;)(.*?)(\\</span\\>)|", "\$1\$3\$4", $text);
		return $text;
	}

    /**
     * Пользовательский метод для рендеринга
     *
     * @since 1.1
     */
    protected function getCustomMethods()
    {
        $showDefaultView = true;
        $params = array(self::$stack);
        unset($params[0]['trace'], $params[0]['preview']);
        $count = count(self::$customMethods);
        $customMethods = self::$customMethods;
        for($i = 0; $i < $count; $i++)
		{
            $custom = $customMethods[$i];
            $class = isset($custom[0]) ? $custom[0] : false;
            $method = isset($custom[1]) ? $custom[1] : false;
            $repeat = $custom[2];
            $showDefault = $custom[3];
            if($showDefault === false)
			{
                $showDefaultView = false;
            }
            if($repeat === 0)
			{
                unset(self::$customMethods[$i]);
            }
			else
			{
                self::$customMethods[$i] = array($class, $method, $repeat--);
            }
            call_user_func_array(array($class, $method), $params);
        }
        self::$customMethods = false;
        return $showDefaultView;
    }

    /**
     * Рендеринг
     *
     * @return boolean
     */
    protected function render()
    {
		echo '<!DOCTYPE html><html><head><meta charset="utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />';
        self::$stack['mode'] = self::$vesrion;
        if(self::$customMethods && ! $this->getCustomMethods())
		{
            return false;
        }
        $this->getPreviewCode();
        if(!self::$styles)
		{
            self::$styles = true;
            self::$stack['css'] = require __DIR__ . '/src/css/styles.html';
        }
		echo '</head><body>';
        $stack = self::$stack;
        require __DIR__ . '/src/template/view.php';
		echo '</body>';
        return true;
    }
}

?>