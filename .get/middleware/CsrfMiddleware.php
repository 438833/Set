<?php

class CsrfMiddleware
{
    public function before(params)
    {
        if(Set::request()->method == 'POST')
		{
            $token = Set::request()->data->csrf_token;
            if($token !== Set::session()->get('csrf_token'))
			{
                Set::halt(403, '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><pre style="background: #222; color: #98fb98; padding:20px; font-size: 16px;">Недопустимый токен CSRF</pre>');
            }
        }
    }
}

?>