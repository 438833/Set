<?php

namespace set\components\net;

class Curl
{

    static private $instance = false;
    private $handle = null;

    private $config = array(
        'url' => ''
	);

    public function __construct($config = array())
    {
        if(!empty($config))
		{
            $this->config = $config;
		}
        $this->handle = curl_init();
        curl_setopt_array(
            $this->handle
			, array(
                CURLOPT_RETURNTRANSFER 		=> true
                , CURLOPT_FOLLOWLOCATION	=> true
				, CURLOPT_SSL_VERIFYPEER 	=> 0
				, CURLOPT_USERAGENT 		=> 'App 1.0'
                , CURLOPT_URL 				=> $this->config['url']
            )
        );
    }

    public function __destruct()
    {
        curl_close($this->handle);
        self::$instance = false;
    }

    static public function getInstance($config = array())
    {
        if(self::$instance === false)
		{
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function get()
    {
        $result = curl_exec($this->handle);
        return $result;
    }

	public function clean($data)
	{
		return trim(htmlspecialchars($data, ENT_COMPAT, 'UTF-8'));
	}

	public function cleanUrl($url)
	{
		return str_replace(array('%20', ' '), '-', $url);
	}

}

?>