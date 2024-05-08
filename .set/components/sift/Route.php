<?php

namespace set\components\sift;

class Route
{

	public $flag;

	public $pattern;

	public $callback;

	public $params = array();

    public $regex;

	public $splat = '';

    public $segs = array();

    public $regs = array();

	public $middleware = array();

	private $alias = array(
		':all' 			=> '.*'
		, ':ru' 		=> '[a-z\p{Cyrillic}]+'
		, ':seg' 		=> '[^/]+'
		, ':slug' 		=> '[a-z0-9-]+'
		, ':slug2' 		=> '[\w-]+'
		, ':num' 		=> '[0-9]+'
		, ':alpha' 		=> '[A-Za-z]+'
		, ':alnum' 		=> '[0-9A-Za-z]+'
		, ':any' 		=> '[0-9A-Za-z\.\-\_\%\=]+'
		, ':segment' 	=> '[a-z0-9\-\_]+'
		, ':segments' 	=> '[a-z0-9\-\_\/]+'
	);

    public function __construct($pattern, $callback, $flag = 3)
    {
        $this->pattern = $pattern;
		$this->callback = $callback;
		$this->flag = $flag;
		$this->_regex();
    }

    public function set($string)
    {
        $this->haystack = $string;
    }

    public function get()
    {
        return $this->haystack;
    }

    public function match($haystack = false)
    {
		$haystack = $haystack ? $haystack : $this->haystack;
		$haystack = urldecode(strval($haystack));
		if($this->regex === '*')
		{
			return true;
		}
		$case_insensitive = $this->flag === 1 ? true : false;
        $match = preg_match($this->regex, $haystack, $params) === 1;
		if($match)
		{
			if($params)
			{
				if(isset($params['0']))
				{
					$lastChar = substr(trim($this->pattern), -1);
					if($lastChar === '*')
					{
						$this->splat = $this->_substr($haystack, $params['0'], $case_insensitive);
					}
				}
				foreach($params as $key => $value)
				{
					if(is_numeric($key))
					{
						unset($params[$key]);
					}
				}
				foreach($this->segs as $key => $value)
				{
					$this->params[$key] = array_key_exists($key, $params) ? urldecode($params[$key]) : null;
				}
			}
			return true;
		}
		return false;
    }

    public function addMiddleware($middleware)
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

    public function _substr($haystack, $needle, $case_insensitive = false)
	{
		$strpos = ($case_insensitive) ? 'stripos' : 'strpos';
		return ($pos = $strpos($haystack, $needle)) !== false ? substr($haystack, $pos + strlen($needle)) : $pos;
	}

	public function _regex()
	{
		$pattern = $this->_escapeConsecutiveAtSymbols(trim($this->pattern));
		if(
			$pattern === '*'
			or $pattern === ''
			or $pattern === false
		)
		{
			$this->regex = '(.*)';
			return true;
		}
		elseif(isset($pattern[0]) && $pattern[0] === '$')
		{
			$this->regex = substr($pattern, 1);
			return true;
		}
		else
		{
			$pattern = preg_replace_callback(
				'#(\\p{L}+)#u'
				, array($this, '_urlencode')
				, $pattern
			);
			$pattern = preg_replace_callback(
				'#\*(?=.*?\*)#'
				, function($matches)
				{
					static $matchcount = 0;
					$matchcount++;
					return "@var{$matchcount}";
				}
				, $pattern
			);
			$lastChar = substr($pattern, -1);
			$array = str_replace(
				array(')', '/*' , ' *')
				, array(')?', '(/?|/.*?)', '(.*?)')
				, $pattern
			);
			$this->segs = array();
			$sweet = '#@([\w]+)(:([^/\.\(\)]*))?#';
			$sweet = '#(?<!\\\\)@([\w]+)(:([^/\.\(\)]*))?#';
			$regex = preg_replace_callback(
				$sweet
				, array($this, '_compile')
				, $array
			);
			$regex = str_replace('\@', '@', $regex);
			$regex .= $lastChar === '/' ? '?' : '/?';
			$caseSensitive = 'i';
			$endOfLine = '';
			switch($this->flag)
			{
				case 1:
					$caseSensitive = 'i';
					break;
				case 2:
					$endOfLine = '$';
					break;
				case 3:
					$caseSensitive = '';
					$endOfLine = '$';
					break;
			}
			$this->regex = '#' . $regex . '(?:\?.*)?' . $endOfLine . '#' . $caseSensitive;
			return true;
		}
		return false;
	}

    public function _urlencode($matches)
    {
        return urlencode($matches[0]);
    }

	public function _compile($matches)
	{
		$template = '.*';
		$template = '[^/\?]+';
		$this->segs[$matches[1]] = null;
		$var = $matches[1];
		$type = isset($matches[2]) && $matches[2] !== '' ? $matches[2] : (
			isset($matches[3])
			? $matches[3]
			: $template
		);
		$regex = null;
		if(isset($this->regs[$var]))
		{
			if($this->regs[$var][0] == ':')
			{
				$type = $this->regs[$var];
			}
		}
		if(isset($this->alias[$type]))
		{
			$regex = $this->alias[$type];
		}
		else
		{
			if(isset($matches[3]))
			{
				$regex = $matches[3];
			}
		}
		if($regex !== null)
		{
			return '(?P<'.$var.'>'.$regex.')';
		}
		return '(?P<'.$var.'>'.$template.')';
	}

	public function _escapeConsecutiveAtSymbols($input)
	{
		$pattern = '/(@{2,})/';
		$output = preg_replace_callback(
			$pattern
			, array($this, '_replaceConsecutiveAtSymbols')
			, $input
		);
		return $output;
	}

	public function _replaceConsecutiveAtSymbols($matches)
	{
		return '\\' . $matches[1];
	}

}

?>