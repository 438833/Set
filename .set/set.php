<?php
if(!function_exists('array_column'))
{
	function array_column($array, $column_name)
	{
		return array_map(
			function($element)
			use($column_name)
			{
				return $element[$column_name];
			}
			, $array
		);
	}
}

if(!defined('INIT_ERRORS'))
{
	define('SHOW_ERRORS', true);
	if(defined('SHOW_ERRORS'))
	{
		if(SHOW_ERRORS)
		{
			ini_set('display_errors', '1');
			error_reporting(E_ALL);
		}
		else
		{
			ini_set('display_errors', '0');
			error_reporting(0);
		}
		require_once 'engine/ErrorHandler/ErrorHandler.php';
		new \set\engine\ErrorHandler;
		define('INIT_ERRORS', true);
	}
}

if(!class_exists('Set'))
{
	$timeStart = microtime(true);
	$memoryStart = memory_get_usage();
	function diff($startTime, $startMemory)
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
	class Set
	{
		static private $extensions = array('php');
		static public $dirs = array();
		static private $app;
		public function __construct()
		{
			self::$app = Set::app();
		}
		public function __call($method, $params)
		{
			return self::__callStatic($method, $params);
		}
		static public function __callStatic($method, $params)
		{
			if(!is_object(self::$app))
			{
				self::$app = Set::app();
			}
			$function = array(self::$app, $method);
			if(is_callable($function))
			{
				return \call_user_func_array($function, $params);
			}
		}
		static public function app($dir = __DIR__)
		{
			static $initialized = false;
			if(!$initialized)
			{
				self::load($dir);
				self::$app = new \set\engine\Manager;
				$initialized = true;
			}
			return self::$app;
		}

		static public function path($dirs)
		{
			if(\is_array($dirs) || \is_object($dirs))
			{
				for($i = 0, $l = count($dirs); $i < $l; $i++)
				{
					self::path($dirs[$i]);
				}
			}
			elseif(\is_string($dirs) && !empty($dirs))
			{
				if(strpos($dirs, '|') !== false)
				{
					$values = array_diff(preg_split("/[\s,|;]/", $dirs), self::$dirs);
					array_map('self::path', $values);
				}
				elseif(!in_array($dirs, array_column(self::$dirs, 'dir'), true))
				{
					$newPath = realpath($dirs);
					$existingPath = '';
					$dirsKeys = array_keys(self::$dirs);
					for($i = 0, $l = count($dirsKeys); $i < $l; $i++)
					{
						$existingDir = self::$dirs[$dirsKeys[$i]];
						$existingPath = realpath($existingDir);
						if(!empty($existingPath) && strpos($newPath, $existingPath) === 0)
						{
							return;
						}
					}
					self::$dirs[] = $newPath;
				}
			}
			else
			{
				self::path(__DIR__);
			}
		}
		static public function load($dirs = false)
		{
			$base = realpath(__DIR__.DIRECTORY_SEPARATOR);	
			$dirs = !empty($dirs) ? (is_array($dirs) ? array_replace(array($base), $dirs) : array_replace(array($base), array($dirs))) : $base.DIRECTORY_SEPARATOR.'..';
			self::path($dirs);
			$iterators = array_map(
				function($dir)
				{
					if(is_dir($dir))
					{
						return new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
					}
					else
					{
						return new RecursiveIteratorIterator(new RecursiveArrayIterator(array()));
					}
				},
				self::$dirs
			);
			$extensions = self::$extensions;
			$files = call_user_func_array(
				'array_merge'
				, array_map(
					function($iterator)
					use($extensions)
					{
						return array_filter(
							iterator_to_array($iterator)
							, function($file)
							use($extensions)
							{
								return $file->isFile()
								&& in_array($file->getExtension(), $extensions);
							}
						);
					}
					, $iterators
				)
			);
			spl_autoload_register(
				function($className)
				use($files, $extensions)
				{
					$parts = explode('\\', $className);
					$className = end($parts);
					$filesKeys = array_keys($files);
					for($i = 0, $l = count($filesKeys); $i < $l; $i++)
					{
						$key = $filesKeys[$i];
						$file = $files[$key];
						//print_r(array($className, $file, strtolower($file->getBasename('.'.$file->getExtension()))));
						if(
							strtolower($className) === strtolower($file->getBasename('.'.$file->getExtension()))
							&& in_array($file->getExtension(), $extensions)
						)
						{
							require_once $file->getPathname();
						}
					}
				}
			);
		}
	}
}
else
{
	Set::on();
	/*$s = new Set;
	$s->on();*/
	/*$spent = diff($timeStart, $memoryStart);
	echo '<p class="spent"><font color="SteelBlue" size="1">Время генерации ' . $spent->time . '. . Потрачено памяти ' . $spent->memory . '. .</font></p>';*/
}

?>