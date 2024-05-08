<?php

function highlight_code($code)
{
    $code = str_replace("\t", str_repeat(' ', 4), $code);
    $code = stripslashes($code); 
    if(!strpos($code, "<?") && substr($code, 0, 2) !== "<?")
	{
        $code = "<?php\n" . trim($code) . "\n?>"; 
    }  
    $code = trim($code); 
    $highlighted_code = highlight_string($code, true);
    $highlighted_code = str_replace('<code>', '<pre class="highlight"><code>', $highlighted_code);
    $highlighted_code = str_replace('<span style="color: #0000BB">&lt;?php</span>', '<span class="c1">&lt;?php</span>', $highlighted_code);
    $highlighted_code = str_replace('<span style="color: #0000BB">', '<span class="k">', $highlighted_code);
    $highlighted_code = str_replace('<span style="color: #007700">', '<span class="s2">', $highlighted_code);
    $highlighted_code = str_replace('<span style="color: #FF8000">', '<span class="nx">', $highlighted_code);
    $highlighted_code = str_replace('<span style="color: #0000BB">', '<span class="o">', $highlighted_code);
    $highlighted_code = str_replace('<span style="color: #DD0000">', '<span class="n">', $highlighted_code);
    $highlighted_code = str_replace('</code>', '</code></pre>', $highlighted_code);
    return $highlighted_code;
}

?>