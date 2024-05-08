<?php

class CorsMiddleware
{
    public function before($params)
    {
        $response = Set::response();
        if(isset($_SERVER['HTTP_ORIGIN']))
		{
            $this->allowOrigins();
            $response->header('Access-Control-Allow-Credentials: true');
            $response->header('Access-Control-Max-Age: 86400');
        }
        if($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
		{
            if(isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
			{
                $response->header(
                    'Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS'
                );
            }
            if(isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
			{
                $response->header(
                    "Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"
                );
            }
            $response->send();
            exit(0);
        }
    }

    private function allowOrigins()
    {
        $allowed = array(
            'capacitor://localhost',
            'ionic://localhost',
            'http://localhost',
            'http://localhost:4200',
            'http://localhost:8080',
            'http://localhost:8100',
        );
        if(in_array($_SERVER['HTTP_ORIGIN'], $allowed))
		{
            $response = Set::response();
            $response->header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        }
    }
}

?>