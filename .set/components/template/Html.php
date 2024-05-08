<?php

namespace set\components\template;

class Html
{

    public $path;

    public $extension = '.php';

	public $useInclude = false;

    protected $vars = array();

    private $template;

    public function __construct($path = '.')
    {
        $this->path = $path;
    }

    public function get($key)
    {
        return isset($this->vars[$key]) ? $this->vars[$key] : null;
    }

    public function set($key, $value = null)
    {
        if(is_array($key) || is_object($key))
        {
            foreach ($key as $k => $v)
            {
                $this->vars[$k] = $v;
            }
        }
        else
        {
            $this->vars[$key] = $value;
        }
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

    public function render($file, $data = null, $useInclude = false, $extension = false)
    {
        $this->template = $this->getTemplate($file, $extension);
        if(!file_exists($this->template))
        {
            throw new \Exception("Файл шаблона не найден: {$this->template}");
        }
        if(is_array($data))
        {
            $this->vars = array_merge($this->vars, $data);
        }
		if(count($this->vars) >= 1)
		{
			extract($this->vars);
		}
		$useInclude ? include($this->template) : eval('?>' . $this->compile(file_get_contents($this->template)) . '<?php ');
    }

    public function fetch($file, $data = null)
    {
        ob_start();
        $this->render($file, $data, $this->useInclude);
        $output = ob_get_clean();
        return $output;
    }

    public function exists($file)
    {
        return file_exists($this->getTemplate($file));
    }

    public function getTemplate($file, $extension = false)
    {
		$extensions = $extension ? $extension : $this->extension;
		$extensions = explode('|', $extensions);
		foreach($extensions as $ext)
		{
			if(!empty($ext) && (substr($file, -1 * strlen($ext)) != $ext))
			{
				$file .= $ext;
			}
			if((substr($file, 0, 1) == '/'))
			{
				return $file;
			}
			return $this->path . DIRECTORY_SEPARATOR . $file;
		}
    }

    public function e($str)
    {
        echo htmlentities($str);
    }

	private function compile($string)
	{
		foreach($this->vars as $key => $value)
		{
			$placeholder = "@$key@";
			$string = str_replace($placeholder, $value, $string);
		}
		if(isset($string))
		{
			$keys = array(
				'{if %%}' => '<?php if (\1): ?>',
				'{elseif %%}' => '<?php ; elseif (\1): ?>',
				'{for %%}' => '<?php for (\1): ?>',
				'{foreach %%}' => '<?php foreach (\1): ?>',
				'{while %%}' => '<?php while (\1): ?>',
				'{/if}' => '<?php endif; ?>',
				'{/for}' => '<?php endfor; ?>',
				'{/foreach}' => '<?php endforeach; ?>',
				'{/while}' => '<?php endwhile; ?>',
				'{else}' => '<?php ; else: ?>',
				'{continue}' => '<?php continue; ?>',
				'{break}' => '<?php break; ?>',
				'{$%% = %%}' => '<?php $\1 = \2; ?>',
				'{$%%++}' => '<?php $\1++; ?>',
				'{$%%--}' => '<?php $\1--; ?>',
				'{$%%}' => '<?php echo $\1; ?>',
				'{comment}' => '<?php /*',
				'{/comment}' => '*/ ?>',
				'{/*}' => '<?php /*',
				'{*/}' => '*/ ?>',
				);
			foreach($keys as $key => $val)
			{
				$patterns[] = '#' . str_replace(
					'%%'
					, '(.+)'
					, preg_quote($key, '#')
				) . '#U';
				$replace[] = $val;
			}
			return preg_replace($patterns, $replace, $string);
		}
	}

}

?>