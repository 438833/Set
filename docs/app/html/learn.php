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
    $highlighted_code = str_replace('<code><span style="color: #000000">', '<pre class="highlight"><code>', $highlighted_code);
    $highlighted_code = str_replace('<span style="color: #0000BB">&lt;?php</span>', '<span class="nx">&lt;?php</span>', $highlighted_code);
    $highlighted_code = str_replace('<span style="color: #0000BB">', '<span class="k">', $highlighted_code);
    $highlighted_code = str_replace('<span style="color: #007700">', '<span class="s2">', $highlighted_code);
    $highlighted_code = str_replace('<span style="color: #FF8000">', '<span class="nx">', $highlighted_code);
    $highlighted_code = str_replace('<span style="color: #0000BB">', '<span class="o">', $highlighted_code);
    $highlighted_code = str_replace('<span style="color: #DD0000">', '<span class="n">', $highlighted_code);
    $highlighted_code = preg_replace('/^\h*\v+/m', '', $highlighted_code); // Remove empty lines
    $highlighted_code = str_replace('</span>&nbsp;', '&nbsp;</span>', $highlighted_code);
    $highlighted_code = str_replace('</span></code>', '</code></pre>', $highlighted_code);
    $highlighted_code = str_replace('&lt;?php', '', $highlighted_code);
    $highlighted_code = str_replace('<span class="k">?&gt;</span>', '', $highlighted_code);
    $highlighted_code = str_replace('<br />', '', $highlighted_code);
	$first_newline_pos = strpos($highlighted_code, "\n");
	if($first_newline_pos !== false)
	{
		$last_newline_pos = strrpos($highlighted_code, "\n");
		if($last_newline_pos !== false)
		{
			$highlighted_code = substr_replace($highlighted_code, '', $first_newline_pos, 1);
			$highlighted_code = substr_replace($highlighted_code, '', $last_newline_pos);
		}
	}
    return '<div class="language-php highlighter-rouge">'.trim($highlighted_code).'</div>';
}

echo $header_content;

?>			<div id="content">
				<div>
					<div id="menu">
						<ul>
							<li id="toneed"><a href="#top">Наверх</a></li>
							<li><a href="#routing">Маршрутизация</a></li>
							<li><a href="#extending">Расширение</a></li>
							<li><a href="#overriding">Переопределение</a></li>
							<li><a href="#filtering">Фильтрация</a></li>
							<li><a href="#variables">Переменные</a></li>
							<li><a href="#views">Шаблонизация</a></li>
							<li><a href="#errorhandling">Обработка ошибок</a></li>
							<li><a href="#redirects">Перенаправления</a></li>
							<li><a href="#requests">Запросы</a></li>
							<li><a href="#stopping">Остановка</a></li>
							<li><a href="#httpcaching">HTTP-кэширование</a></li>
							<li><a href="#json">JSON</a></li>
							<li><a href="#configuration">Конфигурация</a></li>
							<li><a href="#frameworkmethods">Методы платформы</a></li>
							<li><a href="#frameworkinstance">Экземпляр платформы</a></li>
						</ul>
					</div>
					<div id="docs">
						<a name="routing"></a>
						<h1 id="routing">Маршрутизация</h1>
						<p>Маршрутизация в Set выполняется путем сопоставления шаблона URL с функцией обратного вызова.</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
    <span class="k">echo</span> <span class="s1">'Привет, мир!'</span><span class="p">;</span>
<span class="p">});</span>
</code></pre>
						</div>
						<p>Функция обратного вызова может быть любым объектом, который можно вызвать. Так что вы можете использовать обычную функцию:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="k">function</span> <span class="nf">hello</span><span class="p">(){</span>
    <span class="k">echo</span> <span class="s1">'Привет, мир!'</span><span class="p">;</span>
<span class="p">}</span>
<span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/'</span><span class="p">,</span> <span class="s1">'hello'</span><span class="p">);</span>
</code></pre>
						</div>
						<p>Или метод класса:</p>
						
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="k">class</span> <span class="nc">Greeting</span> <span class="p">{</span>
    <span class="k">public</span> <span class="k">static</span> <span class="k">function</span> <span class="nf">hello</span><span class="p">()</span> <span class="p">{</span>
        <span class="k">echo</span> <span class="s1">'Привет, мир!'</span><span class="p">;</span>
    <span class="p">}</span>
<span class="p">}</span>
<span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/'</span><span class="p">,</span> <span class="k">array</span><span class="p">(</span><span class="s1">'Greeting'</span><span class="p">,</span><span class="s1">'hello'</span><span class="p">));</span>
</code></pre>
						</div>
						<p>Или метод объекта:</p>
						<div class="language-php highlighter-rouge"><pre class="highlight"><code><span class="k">class</span> <span class="nc">Greeting</span>
<span class="p">{</span>
    <span class="k">public</span> <span class="k">function</span> <span class="nf">__construct</span><span class="p">()</span> <span class="p">{</span>
        <span class="nv">$this</span><span class="o">-&gt;</span><span class="na">name</span> <span class="o">=</span> <span class="s1">'Джон'</span><span class="p">;</span>
    <span class="p">}</span>

    <span class="k">public</span> <span class="k">function</span> <span class="nf">hello</span><span class="p">()</span> <span class="p">{</span>
        <span class="k">echo</span> <span class="s2">"Здравствуй, </span><span class="si">{</span><span class="nv">$this</span><span class="o">-&gt;</span><span class="na">name</span><span class="si">}</span><span class="s2">!"</span><span class="p">;</span>
    <span class="p">}</span>
<span class="p">}</span>

<span class="nv">$greeting</span> <span class="o">=</span> <span class="k">new</span> <span class="nx">Greeting</span><span class="p">();</span>

<span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/'</span><span class="p">,</span> <span class="k">array</span><span class="p">(</span><span class="nv">$greeting</span><span class="p">,</span> <span class="s1">'hello'</span><span class="p">));</span>
</code></pre>
</div>
						<p>Маршруты сопоставляются в порядке их определения. Первый маршрут, который соответствует запросу, будет вызван.</p>
						<h2 id="method-routing">Маршрутизация по методам</h2>
						<p>По умолчанию шаблоны маршрутов сопоставляются со всеми методами запросов. Вы можете отвечать на конкретные методы, разместив идентификатор перед URL.</p>
						<div class="language-php highlighter-rouge"><pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'method:GET url:/'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
    <span class="k">echo</span> <span class="s1">'Выполнен запрос GET'</span><span class="p">;</span>
<span class="p">});</span>

<span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'method:POST url:/'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
    <span class="k">echo</span> <span class="s1">'Выполнен запрос POST'</span><span class="p">;</span>
<span class="p">});</span>
</code></pre>
</div>
						<p>Вы также можете сопоставить несколько методов с одним обратным вызовом, используя разделитель <code class="highlighter-rouge">|</code>:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'method:(GET|POST) url:/'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
    <span class="k">echo</span> <span class="s1">'Запрос GET или POST выполнен!'</span><span class="p">;</span>
<span class="p">});</span>
</code></pre>
						</div>
						<h2 id="regular-expressions">Регулярные выражения</h2>
						<p>Вы можете использовать регулярные выражения в ваших маршрутах:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/user/[0-9]+'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
    <span class="c1">// Это совпадет с /user/1234
</span><span class="p">});</span>
</code></pre>
						</div>
						<h2 id="named-parameters">Именованные параметры</h2>
						<p>Вы можете указывать именованные параметры в ваших маршрутах, которые будут переданы в вашу функцию обратного вызова.</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/@name/@id'</span><span class="p">,</span> <span class="k">function</span><span class="p">(</span><span class="nv">$name</span><span class="p">,</span> <span class="nv">$id</span><span class="p">){</span>
    <span class="k">echo</span> <span class="s2">"Здравствуй, </span><span class="nv">$name</span><span class="s2"> (</span><span class="nv">$id</span><span class="s2">)!"</span><span class="p">;</span>
<span class="p">});</span>
</code></pre>
						</div>
						<p>Вы также можете включать регулярные выражения в ваши именованные параметры, используя разделитель <code class="highlighter-rouge">:</code>:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/@name/@id:[0-9]{3}'</span><span class="p">,</span> <span class="k">function</span><span class="p">(</span><span class="nv">$name</span><span class="p">,</span> <span class="nv">$id</span><span class="p">){</span>
    <span class="c1">// Это совпадет с /bob/123
</span>    <span class="c1">// Но не совпадет с /bob/12345
</span><span class="p">});</span>
</code></pre>
						</div>
						<h2 id="optional-parameters">Необязательные параметры</h2>
						<p>Вы можете указывать именованные параметры, которые являются необязательными для сопоставления, обрамив сегменты в круглые скобки.</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/blog(/@year(/@month(/@day)))'</span><span class="p">,</span> <span class="k">function</span><span class="p">(</span><span class="nv">$year</span><span class="p">,</span> <span class="nv">$month</span><span class="p">,</span> <span class="nv">$day</span><span class="p">){</span>
    <span class="c1">// Это совпадет с следующими URL:
</span>    <span class="c1">// /blog/2012/12/10
</span>    <span class="c1">// /blog/2012/12
</span>    <span class="c1">// /blog/2012
</span>    <span class="c1">// /blog
</span><span class="p">});</span>
</code></pre>
						</div>
						<p>Любые несопоставленные необязательные параметры будут переданы как NULL.</p>
						<h2 id="wildcards">Маски</h2>
						<p>Сопоставление происходит только на отдельных сегментах URL. Если вы хотите сопоставить несколько сегментов, вы можете использовать маску <code class="highlighter-rouge">*</code>.</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/blog/*'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
    <span class="c1">// Это совпадет с /blog/2000/02/01
</span><span class="p">});</span>
</code></pre>
						</div>
						<p>Чтобы направить все запросы на один обратный вызов, вы можете сделать так:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'*'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
    <span class="c1">// Сделать что-то
</span><span class="p">});</span>
</code></pre>
						</div>
						<h2 id="passing">Передача</h2>
						<p>Вы можете передать выполнение следующему совпадающему маршруту, вернув <code class="highlighter-rouge">true</code> из вашей функции обратного вызова.</p>
						<div class="language-php highlighter-rouge"><pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/user/@name'</span><span class="p">,</span> <span class="k">function</span><span class="p">(</span><span class="nv">$name</span><span class="p">){</span>
    <span class="c1">// Проверить некоторое условие
</span>    <span class="k">if</span> <span class="p">(</span><span class="nv">$name</span> <span class="o">!=</span> <span class="s2">"Bob"</span><span class="p">)</span> <span class="p">{</span>
        <span class="c1">// Продолжить к следующему маршруту
</span>        <span class="k">return</span> <span class="kc">true</span><span class="p">;</span>
    <span class="p">}</span>
<span class="p">});</span>

<span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/user/*'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
    <span class="c1">// Это будет вызвано
</span><span class="p">});</span>
</code></pre>
</div>
						<h2 id="route-info">Информация о маршруте</h2>
						<p>Если вы хотите изучить информацию о сопоставленном маршруте, вы можете запросить передачу объекта маршрута в ваш обратный вызов, передав <code class="highlighter-rouge">true</code> в качестве третьего параметра в методе маршрута. Объект маршрута всегда будет последним параметром, переданным в вашу функцию обратного вызова.</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/'</span><span class="p">,</span> <span class="k">function</span><span class="p">(</span><span class="nv">$route</span><span class="p">){</span>
    <span class="c1">// Массив именованных параметров
</span>    <span class="nv">$route</span><span class="o">-&gt;</span><span class="na">params</span><span class="p">;</span>

    <span class="c1">// Сопоставленное регулярное выражение
</span>    <span class="nv">$route</span><span class="o">-&gt;</span><span class="na">regex</span><span class="p">;</span>

    <span class="c1">// Содержит содержимое любого '*' в шаблоне URL
</span>    <span class="nv">$route</span><span class="o">-&gt;</span><span class="na">splat</span><span class="p">;</span>
<span class="p">},</span> <span class="kc">true</span><span class="p">);</span>
</code></pre>
						</div>
						
						<a name="extending"></a>
						<h1 id="extending">Расширение</h1>
						<p>Set разработан для расширения. Фреймворк поставляется с набором
							стандартных методов и компонентов, но позволяет вам отображать свои собственные
							методы, регистрировать свои собственные классы или даже переопределять существующие классы и методы.
						</p>
						<h2 id="mapping-methods">Отображение методов</h2>
						<p>Для отображения вашего собственного метода используйте функцию <code class="highlighter-rouge">map</code>:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="c1">// Отображение вашего метода
</span><span class="nx">Set</span><span class="o">::</span><span class="na">map</span><span class="p">(</span><span class="s1">'hello'</span><span class="p">,</span> <span class="k">function</span><span class="p">(</span><span class="nv">$name</span><span class="p">){</span>
    <span class="k">echo</span> <span class="s2">"Здравствуй, </span><span class="nv">$name</span><span class="s2">!"</span><span class="p">;</span>
<span class="p">});</span>

<span class="c1">// Вызов вашего пользовательского метода
</span><span class="nx">Set</span><span class="o">::</span><span class="na">hello</span><span class="p">(</span><span class="s1">'Боб'</span><span class="p">);</span>
</code></pre>
						</div>
						<h2 id="registering-classes">Регистрация классов</h2>
						<p>Чтобы зарегистрировать свой собственный класс, используйте функцию <code class="highlighter-rouge">register</code>:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="c1">// Регистрация вашего класса
</span><span class="nx">Set</span><span class="o">::</span><span class="na">register</span><span class="p">(</span><span class="s1">'user'</span><span class="p">,</span> <span class="s1">'User'</span><span class="p">);</span>

<span class="c1">// Получение экземпляра вашего класса
</span><span class="nv">$user</span> <span class="o">=</span> <span class="nx">Set</span><span class="o">::</span><span class="na">user</span><span class="p">();</span>
</code></pre>
						</div>
						<p>Метод регистрации также позволяет передавать параметры в конструктор вашего класса.
							Поэтому при загрузке вашего пользовательского класса он будет предварительно инициализирован.
							Вы можете определить параметры конструктора, передавая дополнительный массив.
							Вот пример загрузки подключения к базе данных:
						</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="c1">// Регистрация класса с параметрами конструктора
</span><span class="nx">Set</span><span class="o">::</span><span class="na">register</span><span class="p">(</span><span class="s1">'db'</span><span class="p">,</span> <span class="s1">'PDO'</span><span class="p">,</span> <span class="k">array</span><span class="p">(</span><span class="s1">'mysql:host=localhost;dbname=test;charset=utf8'</span><span class="p">,</span><span class="s1">'user'</span><span class="p">,</span><span class="s1">'pass'</span><span class="p">));</span>

<span class="c1">// Получение экземпляра вашего класса
// Это создаст объект с определенными параметрами
//
//     new PDO('mysql:host=localhost;dbname=test;charset=utf8','user','pass');
//
</span><span class="nv">$db</span> <span class="o">=</span> <span class="nx">Set</span><span class="o">::</span><span class="na">db</span><span class="p">();</span>
</code></pre>
						</div>
						<p>Если вы передадите дополнительный параметр обратного вызова, он будет выполнен немедленно
							после создания объекта класса. Это позволяет выполнить любые процедуры настройки для вашего
							нового объекта. Функция обратного вызова принимает один параметр - экземпляр нового объекта.
						</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="c1">// Обратный вызов будет получать объект, который был создан (сконструирован)
</span><span class="nx">Set</span><span class="o">::</span><span class="na">register</span><span class="p">(</span><span class="s1">'db'</span><span class="p">,</span> <span class="s1">'PDO'</span><span class="p">,</span> <span class="k">array</span><span class="p">(</span><span class="s1">'mysql:host=localhost;dbname=test;charset=utf8'</span><span class="p">,</span><span class="s1">'user'</span><span class="p">,</span><span class="s1">'pass'</span><span class="p">),</span>
  <span class="k">function</span><span class="p">(</span><span class="nv">$db</span><span class="p">){</span>
    <span class="nv">$db</span><span class="o">-&gt;</span><span class="na">setAttribute</span><span class="p">(</span><span class="nx">PDO</span><span class="o">::</span><span class="na">ATTR_ERRMODE</span><span class="p">,</span> <span class="nx">PDO</span><span class="o">::</span><span class="na">ERRMODE_EXCEPTION</span><span class="p">);</span>
  <span class="p">}</span>
<span class="p">);</span>
</code></pre>
						</div>
						<p>По умолчанию каждый раз, когда вы загружаете свой класс, вы получаете общий экземпляр.
							Чтобы получить новый экземпляр класса, просто передайте <code class="highlighter-rouge">false</code> в качестве параметра:
						</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="c1">// Общий экземпляр класса
</span><span class="nv">$shared</span> <span class="o">=</span> <span class="nx">Set</span><span class="o">::</span><span class="na">db</span><span class="p">();</span>

<span class="c1">// Новый экземпляр класса
</span><span class="nv">$new</span> <span class="o">=</span> <span class="nx">Set</span><span class="o">::</span><span class="na">db</span><span class="p">(</span><span class="kc">false</span><span class="p">);</span>
</code></pre>
						</div>
						<p>Имейте в виду, что отображенные методы имеют приоритет над зарегистрированными классами. Если вы
							определите оба с одним и тем же именем, будет вызван только отображенный метод.
						</p>
						<p>Вы также можете использовать встроенный экземпляр класса `PdoWrapper`, который является наследником класса PDO. При этом конструктор `PdoWrapper` принимает параметры для подключения к базе данных.</p>

<div class="language-php highlighter-rouge">
    <pre class="highlight"><code><span class="k">use</span> <span class="nx">set\components\data\PdoWrapper</span><span class="p">;</span>

<span class="c1">// Регистрация класса PdoWrapper с параметрами конструктора
</span><span class="nx">Set</span><span class="o">::</span><span class="na">register</span><span class="p">(</span><span class="s1">'db'</span><span class="p">,</span> <span class="nx">PdoWrapper</span><span class="o">::</span><span class="na">class</span><span class="p">,</span> <span class="k">array</span><span class="p">(</span>
        <span class="s1">'mysql:host=localhost;dbname=test;charset=utf8'</span><span class="p">,</span>
        <span class="s1">'user'</span><span class="p">,</span>
        <span class="s1">'pass'</span>
    <span class="p">)</span>
<span class="p">);</span>

<span class="c1">// Получение экземпляра класса PdoWrapper
// Это создаст объект PdoWrapper с определенными параметрами подключения
//
//     new PdoWrapper('mysql:host=localhost;dbname=test;charset=utf8','user','pass');
//
</span><span class="nv">$db</span> <span class="o">=</span> <span class="nx">Set</span><span class="o">::</span><span class="na">db</span><span class="p">();</span>
</code></pre>
</div>


<p>Методы `runQuery`, `fetchField`, `fetchRow`, `fetchAll` предоставляют удобный способ выполнения запросов к базе данных и получения результата. Ниже приведены примеры использования каждого метода с пояснениями:</p>

<p>Метод <code>runQuery</code> выполняет SQL-запрос и возвращает объект <code>PDOStatement</code>. Этот метод удобно использовать для запросов, которые не возвращают результат, либо когда результат не требуется:</p>
<div class="language-php highlighter-rouge">
    <pre class="highlight"><code><span class="c1">// Выполнение запроса без параметров
</span><span class="nv">$queryResult</span> <span class="o">=</span> <span class="nv">$db</span><span class="o">-></span><span class="na">runQuery</span><span class="p">(</span><span class="s1">'SELECT * FROM users'</span><span class="p">);</span>

<span class="c1">// Выполнение запроса с параметрами
</span><span class="nv">$params</span> <span class="o">=</span> <span class="p">array(</span><span class="s1">'id'</span> <span class="o">=&gt;</span> <span class="mi">1</span><span class="p">);</span>
<span class="nv">$queryResult</span> <span class="o">=</span> <span class="nv">$db</span><span class="o">-></span><span class="na">runQuery</span><span class="p">(</span><span class="s1">'SELECT * FROM users WHERE id = :id'</span><span class="p">,</span> <span class="nv">$params</span><span class="p">);</span>
</code></pre>
</div>

<p>Метод <code>fetchField</code> возвращает значение одного поля из результата запроса. Это удобно, когда вам нужно получить только одно значение из запроса:</p>
<div class="language-php highlighter-rouge">
    <pre class="highlight"><code><span class="c1">// Получение значения одного поля
</span><span class="nv">$userId</span> <span class="o">=</span> <span class="nv">$db</span><span class="o">-></span><span class="na">fetchField</span><span class="p">(</span><span class="s1">'SELECT id FROM users WHERE username = :username'</span><span class="p">,</span> <span class="nv">$params</span><span class="p">);</span>
</code></pre>
</div>

<p>Метод <code>fetchRow</code> возвращает одну строку из результата запроса в виде объекта <code>Collection</code>. Этот метод удобно использовать, когда вы ожидаете получить только одну строку из запроса:</p>
<div class="language-php highlighter-rouge">
    <pre class="highlight"><code><span class="c1">// Получение одной строки
</span><span class="nv">$userRow</span> <span class="o">=</span> <span class="nv">$db</span><span class="o">-></span><span class="na">fetchRow</span><span class="p">(</span><span class="s1">'SELECT * FROM users WHERE id = :id'</span><span class="p">,</span> <span class="nv">$params</span><span class="p">);</span>
</code></pre>
</div>

<p>Метод <code>fetchAll</code> возвращает все строки из результата запроса в виде массива объектов <code>Collection</code>. Это удобно использовать, когда вы ожидаете получить все строки из запроса:</p>
<div class="language-php highlighter-rouge">
    <pre class="highlight"><code><span class="c1">// Получение всех строк
</span><span class="nv">$allUsers</span> <span class="o">=</span> <span class="nv">$db</span><span class="o">-></span><span class="na">fetchAll</span><span class="p">(</span><span class="s1">'SELECT * FROM users'</span><span class="p">);</span>
</code></pre>
</div>

<p>Помните, что перед использованием этих примеров вы должны иметь экземпляр класса `PdoWrapper`, который вы получили через метод `Set::db()` после его регистрации, как показано в предыдущем блоке кода.</p>

<h2>Контейнер внедрения зависимостей</h2>
<p>Контейнер внедрения зависимостей (DI контейнер) - это мощный инструмент, который позволяет управлять зависимостями вашего приложения. Это ключевое понятие в современных фреймворках PHP и используется для управления созданием и настройкой объектов. Некоторые примеры библиотек DI включают: Dice, Pimple, PHP-DI и league/container.</p>

<p><b><em>Внедрение зависимостей (или Dependency Injection, сокращенно "DI")</em></b> - явная передача зависимости в объект, который в ней нуждается извне, вместо создания зависимого объекта в коде нуждающегося. Это полезно, когда вам нужно передать один и тот же объект нескольким классам (например, вашим контроллерам). Простой пример может помочь лучше понять это.</p>

<h3>Базовый пример:</h3>
<p>Мы создаем новый объект PDO и передаем его в наш класс UserController. Это подходит для небольшого приложения, но по мере роста приложения вы обнаружите, что создаете один и тот же объект PDO в нескольких местах. Именно здесь пригодится DI контейнер.</p>
<div class="language-php highlighter-rouge">
    <pre class="highlight"><code><span class="c1">// класс для управления пользователями из базы данных</span>
<span class="k">class</span> <span class="nc">UserController</span>
<span class="p">{</span>
    <span class="k">protected</span> <span class="nv">$pdo</span><span class="p">;</span>

    <span class="k">public</span> <span class="k">function</span> <span class="nf">__construct</span><span class="p">(</span><span class="nv">$pdo</span><span class="p">)</span>
    <span class="p">{</span>
        <span class="nv">$this</span><span class="o">-&gt;</span><span class="na">pdo</span> <span class="o">=</span> <span class="nv">$pdo</span><span class="p">;</span>
    <span class="p">}</span>

    <span class="k">public</span> <span class="k">function</span> <span class="nf">view</span><span class="p">(</span><span class="nv">$id</span><span class="p">)</span>
    <span class="p">{</span>
        <span class="nv">$stmt</span> <span class="o">=</span> <span class="nv">$this</span><span class="o">-&gt;</span><span class="na">pdo</span><span class="o">-&gt;</span><span class="na">prepare</span><span class="p">(</span><span class="s1">'SELECT * FROM users WHERE id = :id'</span><span class="p">);</span>
        <span class="nv">$stmt</span><span class="o">-&gt;</span><span class="na">execute</span><span class="p">(</span><span class="k">array</span><span class="p">(</span><span class="s1">'id'</span> <span class="o">=&gt;</span> <span class="nv">$id</span><span class="p">));</span>
        <span class="k">print_r</span><span class="p">(</span><span class="nv">$stmt</span><span class="o">-&gt;</span><span class="na">fetch</span><span class="p">());</span>
    <span class="p">}</span>
<span class="p">}</span>

<span class="nv">$User</span> <span class="o">=</span> <span class="k">new</span> <span class="nx">UserController</span><span class="p">(</span><span class="k">new</span> <span class="nx">PDO</span><span class="p">(</span><span class="s1">'mysql:host=localhost;dbname=test;charset=utf8'</span><span class="p">,</span> <span class="s1">'user'</span><span class="p">,</span> <span class="s1">'pass'</span><span class="p">));</span>
<span class="c1">// теперь мы можем использовать контейнер для создания нашего UserController</span>
<span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/user/@id'</span><span class="p">,</span> <span class="k">array</span><span class="p">(</span><span class="nv">$UserController</span><span class="p">,</span> <span class="s1">'view'</span><span class="p">));</span>
<span class="c1">// или альтернативно можно определить маршрут так</span>
<span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/user/@id'</span><span class="p">,</span> <span class="s1">'UserController-&gt;view'</span><span class="p">);</span>
<span class="c1">// или</span>
<span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/user/@id'</span><span class="p">,</span> <span class="s1">'UserController::view'</span><span class="p">);</span></code></pre>
</div>


<h2>Промежуточное программное обеспечение для маршрутов (Middleware)</h2>
<p>Set поддерживает маршруты и промежуточное программное обеспечение для групп маршрутов. Промежуточное программное обеспечение - это функция, которая выполняется перед (или после) обратного вызова маршрута. Это отличный способ добавить проверку аутентификации API в ваш код или проверить, есть ли у пользователя разрешение на доступ к маршруту.</p>

<h3>Основное промежуточное программное обеспечение</h3>
<p>Пример:</p>
<div class="language-php highlighter-rouge">
    <pre class="highlight"><code><span class="c1">// Если вы предоставляете только анонимную функцию, она будет выполнена перед обратным вызовом маршрута. 
// кроме классов нет функций "после" промежуточного программного обеспечения (см. ниже)</span>
<span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/путь'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
	<span class="k">echo</span> <span class="s1">' Здравствуй, мир!'</span><span class="p">;</span>
<span class="p">})</span><span class="o">-&gt;</span><span class="na">addMiddleware</span><span class="p">(</span><span class="k">function</span><span class="p">(){</span>
    <span class="k">echo</span> <span class="s1">'Промежуточное программное обеспечение выполненное в первую очередь!'</span><span class="p">;</span>
<span class="p">});</span>

<span class="c1">// Это выведет "Промежуточное программное обеспечение выполненное в первую очередь! Здравствуй, мир!"</span>
</code></pre>
</div>


<h4>Есть несколько очень важных замечаний о промежуточном программном обеспечении, о которых вам следует знать перед использованием:</h4>
<ul>
    <li>Функции промежуточного программного обеспечения выполняются в порядке добавления к маршруту. Выполнение подобно тому, как это делает <a href="https://www.slimframework.com/docs/v4/concepts/middleware.html#how-does-middleware-work">Slim Framework</a>.
        <ul>
            <li>Предварительные функции выполняются в порядке их добавления, а постфункции выполняются в обратном порядке.</li>
        </ul>
    </li>
    <li>Если ваша функция промежуточного программного обеспечения возвращает false, все выполнение останавливается, и генерируется ошибка 403 Forbidden. Вероятно, вам захочется более грациозно обработать это с помощью <code>Flight::redirect()</code> или чего-то подобного.</li>
    <li>Если вам нужны параметры из вашего маршрута, они будут передаваться в виде одного массива в вашу функцию промежуточного программного обеспечения. (<code>function($params) { ... }</code> или <code>public function before($params) {}</code>). Причина в том, что вы можете структурировать свои параметры в группы, и в некоторых из этих групп ваши параметры могут фактически появиться в другом порядке, что приведет к нарушению функции промежуточного программного обеспечения путем ссылки на неправильный параметр. Таким образом, вы можете получить к ним доступ по имени вместо позиции.</li>
</ul>

<h3>Классы промежуточного программного обеспечения</h3>
<p>Промежуточное программное обеспечение также может быть зарегистрировано как класс. Если вам нужна функциональность "после", вы должны использовать класс.</p>
<div class="language-php highlighter-rouge">
    <pre class="highlight"><code><span class="c1">// Класс для промежуточного программного обеспечения</span>
<span class="k">class</span> <span class="nx">MiddlewareApi</span><span class="p">{</span>
    <span class="c1">// Метод выполняемый перед маршрутом</span>
    <span class="k">public</span> <span class="k">function</span> <span class="nf">before</span><span class="p">(</span><span class="nv">$params</span><span class="p">){</span>
        <span class="k">echo</span> <span class="s1">'Промежуточное программное обеспечение выполненное в первую очередь!'</span><span class="p">;</span>
    <span class="p">}</span>

    <span class="c1">// Метод выполняемый после маршрута</span>
    <span class="k">public</span> <span class="k">function</span> <span class="nf">after</span><span class="p">(</span><span class="nv">$params</span><span class="p">){</span>
        <span class="k">echo</span> <span class="s1">'Выполнение последнего промежуточного программного обеспечения!'</span><span class="p">;</span>
    <span class="p">}</span>
<span class="p">}</span>

<span class="c1">// Создание экземпляра класса промежуточного программного обеспечения</span>
<span class="nv">$middleware</span> <span class="o">=</span> <span class="k">new</span> <span class="nx">MiddlewareApi</span><span class="p">();</span>

<span class="c1">// Добавление маршрута с промежуточным программным обеспечением</span>
<span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/путь'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
	<span class="k">echo</span> <span class="s1">' Здравствуй, мир!'</span><span class="p">;</span>
<span class="p">})</span><span class="o">-&gt;</span><span class="na">addMiddleware</span><span class="p">(</span><span class="nv">$middleware</span><span class="p">);</span> <span class="c1">// также -&gt;addMiddleware(aray($middleware, $middleware2));</span>

<span class="c1">// Это выведет "Промежуточное программное обеспечение выполненное в первую очередь! Здравствуй, мир! Выполнение последнего промежуточного программного обеспечения!"</span>
</code></pre>
</div>


<h3>Группировка промежуточного программного обеспечения</h3>
<p>Вы можете добавить группу маршрутов, и тогда каждый маршрут в этой группе также будет иметь то же промежуточное программное обеспечение. Это полезно, если вам нужно сгруппировать несколько маршрутов, скажем, с помощью промежуточного программного обеспечения для проверки ключа API в заголовке.</p>
<div class="language-php highlighter-rouge">
    <pre class="highlight"><code><span class="c1">// добавлено в конце метода group</span>
<span class="nx">Set</span><span class="o">::</span><span class="na">group</span><span class="p">(</span><span class="s1">'/api'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
    <span class="c1">// Этот "пустой" маршрут на самом деле совпадет с /api</span>
    <span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">''</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
		<span class="k">echo</span> <span class="s1">'Выполнение API'</span><span class="p">;</span>
	<span class="p">},</span> <span class="k">false</span><span class="p">,</span> <span class="s1">'api'</span><span class="p">);</span>
    <span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/users'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
		<span class="k">echo</span> <span class="s1">'Все пользователи'</span><span class="p">;</span>
	<span class="p">},</span> <span class="k">false</span><span class="p">,</span> <span class="s1">'users'</span><span class="p">);</span>
    <span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/users/@id'</span><span class="p">,</span> <span class="k">function</span><span class="p">(</span><span class="nv">$id</span><span class="p">){</span>
		<span class="k">echo</span> <span class="s1">"Найден пользователь: &#123;$id&#125;"</span><span class="p">;</span>
	<span class="p">},</span> <span class="k">false</span><span class="p">,</span> <span class="s1">'user_view'</span><span class="p">);</span>
<span class="p">},</span> <span class="k">array</span><span class="p">(</span><span class="k">new</span> <span class="nx">MiddlewareApi</span><span class="p">()));</span>
</code></pre>
</div>


<p>Если вы хотите применить общее промежуточное программное обеспечение ко всем своим маршрутам, вы можете добавить "пустую" группу:</p>
<div class="language-php highlighter-rouge">
    <pre class="highlight"><code><span class="c1">// добавлено в конце метода group</span>
<span class="nx">Set</span><span class="o">::</span><span class="na">group</span><span class="p">(</span><span class="s1">''</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
    <span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/users'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
		<span class="k">echo</span> <span class="s1">'Все пользователи'</span><span class="p">;</span>
	<span class="p">},</span> <span class="k">false</span><span class="p">,</span> <span class="s1">'users'</span><span class="p">);</span>
    <span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/users/@id'</span><span class="p">,</span> <span class="k">function</span><span class="p">(</span><span class="nv">$id</span><span class="p">){</span>
		<span class="k">echo</span> <span class="s1">"Найден пользователь: &#123;$id&#125;"</span><span class="p">;</span>
	<span class="p">},</span> <span class="k">false</span><span class="p">,</span> <span class="s1">'user_view'</span><span class="p">);</span>
<span class="p">},</span> <span class="k">array</span><span class="p">(</span><span class="k">new</span> <span class="nx">MiddlewareApi</span><span class="p">()));</span>
</code></pre>
</div>






						<a name="overriding"></a>
						<h1 id="overriding">Переопределение</h1>
						<p>Set позволяет вам переопределять его стандартное поведение под ваши собственные нужды,
							не изменяя при этом какой-либо код.
						</p>
						<p>Например, когда Set не может сопоставить URL с маршрутом, он вызывает метод <code class="highlighter-rouge">notFound</code>,
							который отправляет общий ответ <code class="highlighter-rouge">HTTP 404</code>. Вы можете изменить это поведение,
							используя метод <code class="highlighter-rouge">map</code>:
						</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">map</span><span class="p">(</span><span class="s1">'notFound'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
    <span class="c1">// Показать пользовательскую страницу 404
</span>    <span class="k">include</span> <span class="s1">'errors/404.html'</span><span class="p">;</span>
<span class="p">});</span>
</code></pre>
						</div>
						<p>Set также позволяет вам заменять основные компоненты платформы.
							Например, вы можете заменить стандартный класс Router на свой собственный класс:
						</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="c1">// Зарегистрируйте ваш собственный класс
</span><span class="nx">Set</span><span class="o">::</span><span class="na">register</span><span class="p">(</span><span class="s1">'router'</span><span class="p">,</span> <span class="s1">'MyRouter'</span><span class="p">);</span>
<span class="c1">// Когда Set загружает экземпляр Router, он загрузит ваш класс
</span><span class="nv">$myrouter</span> <span class="o">=</span> <span class="nx">Set</span><span class="o">::</span><span class="na">router</span><span class="p">();</span>
</code></pre>
						</div>
						<p>Однако методы платформы, такие как <code class="highlighter-rouge">map</code> и <code class="highlighter-rouge">register</code>, не могут быть переопределены. Вы получите ошибку, если попытаетесь это сделать.</p>
						<a name="filtering"></a>
						<h1 id="filtering">Фильтрация</h1>
						<p>Set позволяет вам фильтровать методы до и после их вызова. Нет
							предопределенных хуков, которые вам нужно запоминать. Вы можете фильтровать любые из стандартных методов платформы
							а также любые пользовательские методы, которые вы отображали.
						</p>
						<p>Функция фильтра выглядит так:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="k">function</span><span class="p">(</span><span class="o">&amp;</span><span class="nv">$params</span><span class="p">,</span> <span class="o">&amp;</span><span class="nv">$output</span><span class="p">)</span> <span class="p">{</span>
    <span class="c1">// Код фильтрации
</span><span class="p">}</span>
</code></pre>
						</div>
						<p>Используя переданные переменные, вы можете изменять входные параметры и/или выходные данные.</p>
						<p>Вы можете запустить фильтр перед методом, сделав следующее:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">before</span><span class="p">(</span><span class="s1">'start'</span><span class="p">,</span> <span class="k">function</span><span class="p">(</span><span class="o">&amp;</span><span class="nv">$params</span><span class="p">,</span> <span class="o">&amp;</span><span class="nv">$output</span><span class="p">){</span>
    <span class="c1">// Сделайте что-то
</span><span class="p">});</span>
</code></pre>
						</div>
						<p>Вы можете запустить фильтр после метода, сделав следующее:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">after</span><span class="p">(</span><span class="s1">'start'</span><span class="p">,</span> <span class="k">function</span><span class="p">(</span><span class="o">&amp;</span><span class="nv">$params</span><span class="p">,</span> <span class="o">&amp;</span><span class="nv">$output</span><span class="p">){</span>
    <span class="c1">// Сделайте что-то
</span><span class="p">});</span>
</code></pre>
						</div>
						<p>Вы можете добавить столько фильтров, сколько хотите к любому методу. Они будут вызваны в
							порядке их объявления.
						</p>
						<p>Вот пример процесса фильтрации:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="c1">// Отобразить пользовательский метод
</span><span class="nx">Set</span><span class="o">::</span><span class="na">map</span><span class="p">(</span><span class="s1">'hello'</span><span class="p">,</span> <span class="k">function</span><span class="p">(</span><span class="nv">$name</span><span class="p">){</span>
    <span class="k">return</span> <span class="s2">"Здравствуй, </span><span class="nv">$name</span><span class="s2">!"</span><span class="p">;</span>
<span class="p">});</span>
<span class="c1">// Добавить фильтр перед
</span><span class="nx">Set</span><span class="o">::</span><span class="na">before</span><span class="p">(</span><span class="s1">'hello'</span><span class="p">,</span> <span class="k">function</span><span class="p">(</span><span class="o">&</span><span class="nv">$params</span><span class="p">,</span> <span class="o">&</span><span class="nv">$output</span><span class="p">){</span>
<span class="c1">// Изменить параметр
</span> <span class="nv">$params</span><span class="p">[</span><span class="mi">0</span><span class="p">]</span> <span class="o">=</span> <span class="s1">'Фред'</span><span class="p">;</span>
<span class="p">});</span>

<span class="c1">// Добавить фильтр после
</span><span class="nx">Set</span><span class="o">::</span><span class="na">after</span><span class="p">(</span><span class="s1">'hello'</span><span class="p">,</span> <span class="k">function</span><span class="p">(</span><span class="o">&</span><span class="nv">$params</span><span class="p">,</span> <span class="o">&</span><span class="nv">$output</span><span class="p">){</span>
<span class="c1">// Изменить вывод
</span> <span class="nv">$output</span> <span class="o">.=</span> <span class="s2">" Прекрасный день!"</span><span class="p">;</span>
<span class="p">});</span>

<span class="c1">// Вызов пользовательского метода
</span><span class="k">echo</span> <span class="nx">Set</span><span class="o">::</span><span class="na">hello</span><span class="p">(</span><span class="s1">'Боб'</span><span class="p">);</span>
</code></pre>
						</div>
						<p>Это должно отобразить:</p>
						<div class="highlighter-rouge">
							<pre class="highlight"><code>Здравствуй, Фред! Прекрасный день!
</code></pre>
						</div>
						<p>Если вы определили несколько фильтров, вы можете прервать цепочку, вернув <code class="highlighter-rouge">false</code>
							в любой из ваших функций фильтра:
						</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">before</span><span class="p">(</span><span class="s1">'start'</span><span class="p">,</span> <span class="k">function</span><span class="p">(</span><span class="o">&amp;</span><span class="nv">$params</span><span class="p">,</span> <span class="o">&amp;</span><span class="nv">$output</span><span class="p">){</span>
    <span class="k">echo</span> <span class="s1">'Раз'</span><span class="p">;</span>
<span class="p">});</span>

<span class="nx">Set</span><span class="o">::</span><span class="na">before</span><span class="p">(</span><span class="s1">'start'</span><span class="p">,</span> <span class="k">function</span><span class="p">(</span><span class="o">&amp;</span><span class="nv">$params</span><span class="p">,</span> <span class="o">&amp;</span><span class="nv">$output</span><span class="p">){</span>
    <span class="k">echo</span> <span class="s1">'Два'</span><span class="p">;</span>
    <span class="c1">// Это прервет цепочку
</span>    <span class="k">return</span> <span class="kc">false</span><span class="p">;</span>
<span class="p">});</span>

<span class="c1">// Это не будет вызвано
</span><span class="nx">Set</span><span class="o">::</span><span class="na">before</span><span class="p">(</span><span class="s1">'start'</span><span class="p">,</span> <span class="k">function</span><span class="p">(</span><span class="o">&amp;</span><span class="nv">$params</span><span class="p">,</span> <span class="o">&amp;</span><span class="nv">$output</span><span class="p">){</span>
    <span class="k">echo</span> <span class="s1">'Три'</span><span class="p">;</span>
<span class="p">});</span>
</code></pre>
						</div>
						<p>Обратите внимание, что основные методы, такие как <code class="highlighter-rouge">map</code> и <code class="highlighter-rouge">register</code>, не могут быть отфильтрованы, потому что они
							вызываются непосредственно, а не динамически.
						</p>
						<a name="variables"></a>
						<h1 id="variables">Переменные</h1>
						<p>Set позволяет вам сохранять переменные, чтобы их можно было использовать где угодно в вашем приложении.</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="c1">// Сохраните вашу переменную
</span><span class="nx">Set</span><span class="o">::</span><span class="na">set</span><span class="p">(</span><span class="s1">'id'</span><span class="p">,</span> <span class="mi">123</span><span class="p">);</span>
<span class="c1">// В другом месте вашего приложения
</span><span class="nv">$id</span> <span class="o">=</span> <span class="nx">Set</span><span class="o">::</span><span class="na">get</span><span class="p">(</span><span class="s1">'id'</span><span class="p">);</span>
</code></pre>
						</div>
						<p>Чтобы узнать, установлена ли переменная, вы можете сделать следующее:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="k">if</span> <span class="p">(</span><span class="nx">Set</span><span class="o">::</span><span class="na">has</span><span class="p">(</span><span class="s1">'id'</span><span class="p">))</span> <span class="p">{</span>
     <span class="c1">// Сделайте что-то
</span><span class="p">}</span>
</code></pre>
						</div>
						<p>Вы можете очистить переменную, сделав следующее:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="c1">// Очищает переменную id
</span><span class="nx">Set</span><span class="o">::</span><span class="na">clear</span><span class="p">(</span><span class="s1">'id'</span><span class="p">);</span>
<span class="c1">// Очищает все переменные
</span><span class="nx">Set</span><span class="o">::</span><span class="na">clear</span><span class="p">();</span>
</code></pre>
						</div>
						<p>Set также использует переменные для целей конфигурации.</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">set</span><span class="p">(</span><span class="s1">'set.log_errors'</span><span class="p">,</span> <span class="kc">true</span><span class="p">);</span>
</code></pre>
						</div>
						<a name="views"></a>
						<h1 id="views">Шаблонизация</h1>
						<p>Set предоставляет некоторую базовую функциональность представлений по умолчанию. Чтобы отобразить представление,
							вызовите метод <code class="highlighter-rouge">render</code> с именем файла шаблона и опциональными данными шаблона:
						</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">render</span><span class="p">(</span><span class="s1">'hello.php'</span><span class="p">,</span> <span class="k">array</span><span class="p">(</span><span class="s1">'name'</span> <span class="o">=&gt;</span> <span class="s1">'Bob'</span><span class="p">));</span>
</code></pre>
						</div>
						<p>Данные шаблона, которые вы передаете, автоматически встраиваются в шаблон и могут
							быть обращены как локальная переменная. Файлы шаблонов просто PHP-файлы. Если
							содержимое файла шаблона <code class="highlighter-rouge">hello.php</code> такое:
						</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Здравствуй</span><span class="p">,</span> <span class="s1">'&lt;?php echo $name; ?&gt;'</span><span class="o">!</span>
</code></pre>
						</div>
						<p>Вывод будет:</p>
						<div class="highlighter-rouge">
							<pre class="highlight"><code>Здравствуй, Боб!
</code></pre>
						</div>
						<p>Вы также можете вручную устанавливать переменные шаблона с помощью метода set:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">view</span><span class="p">()</span><span class="o">-&gt;</span><span class="na">set</span><span class="p">(</span><span class="s1">'name'</span><span class="p">,</span> <span class="s1">'Боб'</span><span class="p">);</span>
</code></pre>
						</div>
						<p>Теперь переменная <code class="highlighter-rouge">name</code> доступна во всех ваших представлениях. Поэтому вы можете просто сделать:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">render</span><span class="p">(</span><span class="s1">'hello'</span><span class="p">);</span>
</code></pre>
						</div>
						<p>Обратите внимание, что при указании имени шаблона в методе render
							вы можете опустить расширение <code class="highlighter-rouge">.php</code>.
						</p>
						<p>По умолчанию Set будет искать каталог <code class="highlighter-rouge">views</code> для файлов шаблонов. Вы можете
							указать альтернативный путь для ваших шаблонов, установив следующую конфигурацию:
						</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">set</span><span class="p">(</span><span class="s1">'set.template.path'</span><span class="p">,</span> <span class="s1">'/путь/к/шаблонам'</span><span class="p">);</span>
</code></pre>
						</div>
						<h2 id="layouts">Макеты</h2>
						<p>Часто веб-сайты имеют один шаблон макета с меняющимся
							содержанием. Чтобы отобразить содержимое для использования в макете, вы можете передать
							дополнительный параметр в метод <code class="highlighter-rouge">render</code>.
						</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">render</span><span class="p">(</span><span class="s1">'header'</span><span class="p">,</span> <span class="k">array</span><span class="p">(</span><span class="s1">'heading'</span> <span class="o">=&gt;</span> <span class="s1">'Hello'</span><span class="p">),</span> <span class="s1">'header_content'</span><span class="p">);</span>
<span class="nx">Set</span><span class="o">::</span><span class="na">render</span><span class="p">(</span><span class="s1">'body'</span><span class="p">,</span> <span class="k">array</span><span class="p">(</span><span class="s1">'body'</span> <span class="o">=&gt;</span> <span class="s1">'World'</span><span class="p">),</span> <span class="s1">'body_content'</span><span class="p">);</span>
</code></pre>
						</div>
						<p>Затем ваше представление будет иметь сохраненные переменные с именами <code class="highlighter-rouge">header_content</code> и <code class="highlighter-rouge">body_content</code>.
							Затем вы можете отобразить ваш макет, сделав:
						</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">render</span><span class="p">(</span><span class="s1">'layout'</span><span class="p">,</span> <span class="k">array</span><span class="p">(</span><span class="s1">'title'</span> <span class="o">=&gt;</span> <span class="s1">'Домашняя страница'</span><span class="p">));</span>
</code></pre>
						</div>
						<p>Если файлы шаблонов выглядят так:</p>
						<p><code class="highlighter-rouge">header.php</code>:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="o">&lt;</span><span class="nx">h1</span><span class="o">&gt;&lt;?</span><span class="nx">php</span> <span class="k">echo</span> <span class="nv">$heading</span><span class="p">;</span> <span class="cp">?&gt;</span><span class="nt">&lt;/h1&gt;</span>
</code></pre>
						</div>
						<p><code class="highlighter-rouge">body.php</code>:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="o">&lt;</span><span class="nx">div</span><span class="o">&gt;&lt;?</span><span class="nx">php</span> <span class="k">echo</span> <span class="nv">$body</span><span class="p">;</span> <span class="cp">?&gt;</span><span class="nt">&lt;/div&gt;</span>
</code></pre>
						</div>
						<p><code class="highlighter-rouge">layout.php</code>:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="o">&lt;</span><span class="nx">html</span><span class="o">&gt;</span>
<span class="o">&lt;</span><span class="nx">head</span><span class="o">&gt;</span>
<span class="o">&lt;</span><span class="nx">title</span><span class="o">&gt;&lt;?</span><span class="nx">php</span> <span class="k">echo</span> <span class="nv">$title</span><span class="p">;</span> <span class="cp">?&gt;</span><span class="nt">&lt;/title&gt;</span>
<span class="nt">&lt;/head&gt;</span>
<span class="nt">&lt;body&gt;</span>
<span class="cp">&lt;?php</span> <span class="k">echo</span> <span class="nv">$header_content</span><span class="p">;</span> <span class="cp">?&gt;</span>
<span class="cp">&lt;?php</span> <span class="k">echo</span> <span class="nv">$body_content</span><span class="p">;</span> <span class="cp">?&gt;</span>
<span class="nt">&lt;/body&gt;</span>
<span class="nt">&lt;/html&gt;</span>
</code></pre>
						</div>
						<p>Вывод будет:</p>
						<div class="language-html highlighter-rouge">
							<pre class="highlight"><code><span class="nt">&lt;html&gt;</span>
<span class="nt">&lt;head&gt;</span>
<span class="nt">&lt;title&gt;</span>Домашняя страница<span class="nt">&lt;/title&gt;</span>
<span class="nt">&lt;/head&gt;</span>
<span class="nt">&lt;body&gt;</span>
<span class="nt">&lt;h1&gt;</span>Привет<span class="nt">&lt;/h1&gt;</span>
<span class="nt">&lt;div&gt;</span>Мир<span class="nt">&lt;/div&gt;</span>
<span class="nt">&lt;/body&gt;</span>
<span class="nt">&lt;/html&gt;</span>
</code></pre>
						</div>

<p>Пример шаблона:</p>
<div class="language-smarty highlighter-rouge">
    <pre class="highlight"><code><span class="cp">&#123;if</span> <span class="nv">$isLoggedIn</span><span class="cp">&#125</span>
    Привет, <span class="cp">&#123;$username&#125;</span>!

    <span class="cp">&#123;if</span> <span class="nv">$isAdmin</span><span class="cp">&#125</span>
        У вас есть права администратора
    <span class="cp">&#123;elseif</span> <span class="nv">$isModerator</span><span class="cp">&#125</span>
        У вас есть права модератора
    <span class="cp">&#123;else&#125;</span>
        У вас есть права обычного пользователя
    <span class="cp">&#123;&#47;if&#125;</span>

    <span class="cp">&#123;for</span> <span class="nv">$i</span> = 0; <span class="nv">$i</span> &lt; 5; <span class="nv">$i</span>++<span class="cp">&#125</span>
        Итерация <span class="cp">&#123;$i&#125;</span>&lt;br&gt;
    <span class="cp">&#123;&#47;for&#125;</span>

    <span class="cp">&#123;foreach</span> <span class="nv">$items</span> as <span class="nv">$item</span><span class="cp">&#125</span>
        <span class="cp">&#123;$item&#125;</span>&lt;br&gt;
    <span class="cp">&#123;&#47;foreach&#125;</span>

    <span class="cp">&#123;while</span> <span class="nv">$condition</span><span class="cp">&#125</span>
        Это цикл while&lt;br&gt;
        <span class="cp">&#123;break&#125; &#47;&#47;Выход из цикла</span>
    <span class="cp">&#123;&#47;while&#125;</span>

<span class="cp">&#123;else&#125;</span>
    Пожалуйста, войдите, чтобы получить доступ к этому контенту
<span class="cp">&#123;&#47;if&#125;</span>

<span class="cp">&#123;comment&#125;</span>
    Это комментарий, и он не будет обработан
<span class="cp">&#123;&#47;comment&#125;</span>
</code></pre>
</div>



						<a name="errorhandling"></a>
						<h1 id="error-handling">Обработка ошибок</h1>
						<h2 id="errors-and-exceptions">Ошибки и исключения</h2>
						<p>Все ошибки и исключения перехватываются Set и передаются методу <code class="highlighter-rouge">error</code>.
							По умолчанию отправляется общий ответ <code class="highlighter-rouge">HTTP 500 Internal Server Error</code>
							с некоторой информацией об ошибке.
						</p>
						<p>Вы можете переопределить это поведение для ваших собственных нужд:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">map</span><span class="p">(</span><span class="s1">'error'</span><span class="p">,</span> <span class="k">function</span><span class="p">(</span><span class="nx">Exception</span> <span class="nv">$ex</span><span class="p">){</span>
    <span class="c1">// Обработка ошибки
</span>    <span class="k">echo</span> <span class="nv">$ex</span><span class="o">-&gt;</span><span class="na">getTraceAsString</span><span class="p">();</span>
<span class="p">});</span>
</code></pre>
						</div>
						<p>По умолчанию ошибки не записываются в журнал веб-сервера. Вы можете включить это, изменив конфигурацию:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">set</span><span class="p">(</span><span class="s1">'set.log_errors'</span><span class="p">,</span> <span class="kc">true</span><span class="p">);</span>
</code></pre>
						</div>
						<h2 id="not-found">Не найдено</h2>
						<p>Когда URL не может быть найден, Set вызывает метод <code class="highlighter-rouge">notFound</code>. По умолчанию
							отправляется ответ <code class="highlighter-rouge">HTTP 404 Not Found</code> с простым сообщением.
						</p>
						<p>Вы можете переопределить это поведение для ваших собственных нужд:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">map</span><span class="p">(</span><span class="s1">'notFound'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
    <span class="c1">// Обработка не найденного
</span><span class="p">});</span>
</code></pre>
						</div>
						<a name="redirects"></a>
						<h1 id="redirects">Перенаправления</h1>
						<p>Вы можете перенаправить текущий запрос, используя метод <code class="highlighter-rouge">redirect</code> и передавая
							новый URL:
						</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">redirect</span><span class="p">(</span><span class="s1">'/новое/местоположение'</span><span class="p">);</span>
</code></pre>
						</div>
						<p>По умолчанию Set отправляет HTTP-статус 303. Вы также можете установить настраиваемый код:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">redirect</span><span class="p">(</span><span class="s1">'/новое/местоположение'</span><span class="p">,</span> <span class="mi">401</span><span class="p">);</span>
</code></pre>
						</div>
						<a name="requests"></a>
						<h1 id="requests">Запросы</h1>
						<p>Set инкапсулирует HTTP-запрос в единственный объект, к которому можно получить доступ, выполнив:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nv">$request</span> <span class="o">=</span> <span class="nx">Set</span><span class="o">::</span><span class="na">request</span><span class="p">();</span>
</code></pre>
						</div>
						<p>Объект запроса предоставляет следующие свойства:</p>
						<div class="highlighter-rouge">
							<pre class="highlight"><code>url - Запрашиваемый URL
base - Родительский подкаталог URL
method - Метод запроса (GET, POST, PUT, DELETE)
referrer - URL реферера
ip - IP-адрес клиента
ajax - Является ли запрос AJAX-запросом
scheme - Протокол сервера (http, https)
user_agent - Информация о браузере
type - Тип контента
length - Длина контента
query - Параметры строки запроса
data - Данные POST или JSON
cookies - Данные cookie
files - Загруженные файлы
secure - Является ли соединение защищенным
accept - Параметры accept HTTP
proxy_ip - IP-адрес прокси-сервера клиента
</code></pre>
						</div>
						<p>Вы можете получить доступ к свойствам <code class="highlighter-rouge">query</code>, <code class="highlighter-rouge">data</code>, <code class="highlighter-rouge">cookies</code> и <code class="highlighter-rouge">files</code> как к массивам или объектам.</p>
						<p>Таким образом, чтобы получить параметр строки запроса, вы можете выполнить:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nv">$id</span> <span class="o">=</span> <span class="nx">Set</span><span class="o">::</span><span class="na">request</span><span class="p">()</span><span class="o">-&gt;</span><span class="na">query</span><span class="p">[</span><span class="s1">'id'</span><span class="p">];</span>
</code></pre>
						</div>
						<p>Или вы можете сделать так:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nv">$id</span> <span class="o">=</span> <span class="nx">Set</span><span class="o">::</span><span class="na">request</span><span class="p">()</span><span class="o">-&gt;</span><span class="na">query</span><span class="o">-&gt;</span><span class="na">id</span><span class="p">;</span>
</code></pre>
						</div>
						<h2 id="raw-request-body">RAW Request Body</h2>
						<p>Чтобы получить необработанное тело HTTP-запроса, например, при работе с запросами PUT, вы можете выполнить:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nv">$body</span> <span class="o">=</span> <span class="nx">Set</span><span class="o">::</span><span class="na">request</span><span class="p">()</span><span class="o">-&gt;</span><span class="na">getBody</span><span class="p">();</span>
</code></pre>
						</div>
						<h2 id="json-input">JSON Input</h2>
						<p>Если вы отправляете запрос с типом <code class="highlighter-rouge">application/json</code> и данными <code class="highlighter-rouge"><span class="p">{</span><span class="nt">"id"</span><span class="p">:</span><span class="w"> </span><span class="mi">123</span><span class="p">}</span></code>, они будут доступны из свойства <code class="highlighter-rouge">data</code>:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nv">$id</span> <span class="o">=</span> <span class="nx">Set</span><span class="o">::</span><span class="na">request</span><span class="p">()</span><span class="o">-&gt;</span><span class="na">data</span><span class="o">-&gt;</span><span class="na">id</span><span class="p">;</span>
</code></pre>
						</div>
						<a name="stopping"></a>
						<h1 id="stopping">Остановка</h1>
						<p>Вы можете остановить работу платформы в любой момент, вызвав метод <code class="highlighter-rouge">halt</code>:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">halt</span><span class="p">();</span>
</code></pre>
						</div>
						<p>Вы также можете указать необязательный код состояния <code class="highlighter-rouge">HTTP</code> и сообщение:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">halt</span><span class="p">(</span><span class="mi">200</span><span class="p">,</span> <span class="s1">'Ошибка'</span><span class="p">);</span>
</code></pre>
						</div>
						<p>Вызов метода <code class="highlighter-rouge">halt</code> приведет к отбрасыванию любого содержимого ответа до этого момента. Если вы хотите остановить платформа и вывести текущий ответ, используйте метод <code class="highlighter-rouge">stop</code>:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">stop</span><span class="p">();</span>
</code></pre>
						</div>
						<a name="httpcaching"></a>
						<h1 id="http-caching">HTTP-кэширование</h1>
						<p>Set предоставляет встроенную поддержку кэширования на уровне HTTP. Если условие кэширования
							выполняется, Set вернет HTTP-ответ <code class="highlighter-rouge">304 Not Modified</code>. В следующий раз, когда клиент запросит
							тот же ресурс, ему будет предложено использовать закэшированную локально версию.
						</p>
						<h2 id="last-modified">Последнее изменение</h2>
						<p>Вы можете использовать метод <code class="highlighter-rouge">lastModified</code> и передать в него временную метку UNIX для установки даты
							и времени последнего изменения страницы. Клиент будет продолжать использовать свой кэш до тех пор,
							пока значение последнего изменения не изменится.
						</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/news'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
    <span class="nx">Set</span><span class="o">::</span><span class="na">lastModified</span><span class="p">(</span><span class="mi">1234567890</span><span class="p">);</span>
    <span class="k">echo</span> <span class="s1">'Этот материал будет закэширован'</span><span class="p">;</span>
<span class="p">});</span>
</code></pre>
						</div>
						<h2 id="etag">ETag</h2>
						<p>Кэширование по <code class="highlighter-rouge">ETag</code> аналогично кэшированию по <code class="highlighter-rouge">Last-Modified</code>, за исключением того, что вы можете указать любой идентификатор для ресурса:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/news'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
    <span class="nx">Set</span><span class="o">::</span><span class="na">etag</span><span class="p">(</span><span class="s1">'my-unique-id'</span><span class="p">);</span>
    <span class="k">echo</span> <span class="s1">'Этот материал будет закэширован'</span><span class="p">;</span>
<span class="p">});</span>
</code></pre>
						</div>
						<p>Имейте в виду, что вызовы <code class="highlighter-rouge">lastModified</code> или <code class="highlighter-rouge">etag</code> как устанавливают, так и проверяют значение кэша. Если значение кэша такое же между запросами, Set немедленно отправит ответ <code class="highlighter-rouge">HTTP 304</code> и прекратит обработку.</p>
						<a name="json"></a>
						<h1 id="json">JSON</h1>
						<p>Set предоставляет поддержку для отправки ответов в формате JSON и JSONP. Чтобы отправить ответ в формате JSON, передайте данные, которые нужно закодировать в JSON:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">json</span><span class="p">(</span><span class="k">array</span><span class="p">(</span><span class="s1">'id'</span> <span class="o">=&gt;</span> <span class="mi">123</span><span class="p">));</span>
</code></pre>
						</div>
						<p>Для запросов в формате JSONP вы можете необязательно указать имя параметра запроса, который используется для определения имени вашей функции обратного вызова:</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">jsonp</span><span class="p">(</span><span class="k">array</span><span class="p">(</span><span class="s1">'id'</span> <span class="o">=&gt;</span> <span class="mi">123</span><span class="p">),</span> <span class="s1">'q'</span><span class="p">);</span>
</code></pre>
						</div>
						<p>Таким образом, при выполнении GET-запроса с использованием <code class="highlighter-rouge">?q=my_func</code>, вы должны получить вывод:</p>
						<div class="highlighter-rouge">
							<pre class="highlight"><code>my_func({"id":123});
</code></pre>
						</div>
						<p>Если вы не передадите имя параметра запроса, оно по умолчанию будет <code class="highlighter-rouge">jsonp</code>.</p>
						<a name="configuration"></a>
						<h1 id="configuration">Конфигурация</h1>
						<p>Вы можете настроить определенные поведения Set, установив значения конфигурации с помощью метода <code class="highlighter-rouge">set</code>.</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">set</span><span class="p">(</span><span class="s1">'set.log_errors'</span><span class="p">,</span> <span class="kc">true</span><span class="p">);</span>
</code></pre>
						</div>
						<p>Ниже приведен список всех доступных параметров конфигурации:</p>
						<div class="highlighter-rouge">
							<pre class="highlight"><code>set.version - Предопределенная переменная, содержащая номер версии. (по умолчанию: <span class="s1">1.2.1</span>)
set.start - Автоматическое использование метода <span class="na">start</span>. (по умолчанию: <span class="mi">true</span>)
set.console.charset - Указание используемой кодировки для отображения для работы с выбранным терминалом. (по умолчанию: <span class="mi">utf8</span>)
set.type - Предопределенный тип данных для отправки ответов. (по умолчанию: <span class="mi">html</span>)
set.base_url - Переопределить базовый URL запроса. (по умолчанию: <span class="mi">null</span>)
set.case_sensitive - Сопоставление URL чувствительное к регистру. (по умолчанию: <span class="mi">false</span>)
set.handle_errors - Позволяет Set обрабатывать все ошибки внутренне. (по умолчанию: <span class="mi">true</span>)
set.log_errors - Регистрация ошибок в файле журнала ошибок веб-сервера. (по умолчанию: <span class="mi">false</span>)
set.template.path - Каталог, содержащий файлы шаблонов представления. (по умолчанию содержит путь до папки <span class="mi">template</span> в <span class="mi">Компонентах</span>)
set.template.name - Имя шаблона. (по умолчанию: <span class="mi">manlix</span>)
set.template.extension - Расширение файлов шаблонов представления. (по умолчанию: <span class="mi">.php</span>)
</code></pre>
						</div>
						<a name="frameworkmethods"></a>
						<h1 id="framework-methods">Методы платформы</h1>
						<p>Set разработан таким образом, чтобы быть простым в использовании и понимании. Ниже приведен полный набор методов для платформы. Он состоит из основных методов, которые являются обычными статическими методами, и расширяемых методов, которые являются отображенными методами, которые можно фильтровать или переопределить.</p>
						<h2 id="core-methods">Основные методы</h2>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">map</span><span class="p">(</span><span class="nv">$name</span><span class="p">,</span> <span class="nv">$callback</span><span class="p">)</span> <span class="c1">// Создает пользовательский метод платформы.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">register</span><span class="p">(</span><span class="nv">$name</span><span class="p">,</span> <span class="nv">$class</span><span class="p">,</span> <span class="p">[</span><span class="nv">$params</span><span class="p">],</span> <span class="p">[</span><span class="nv">$callback</span><span class="p">])</span> <span class="c1">// Регистрирует класс для метода платформы.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">before</span><span class="p">(</span><span class="nv">$name</span><span class="p">,</span> <span class="nv">$callback</span><span class="p">)</span> <span class="c1">// Добавляет фильтр перед методом платформы.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">after</span><span class="p">(</span><span class="nv">$name</span><span class="p">,</span> <span class="nv">$callback</span><span class="p">)</span> <span class="c1">// Добавляет фильтр после метода платформы.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">path</span><span class="p">(</span><span class="nv">$path</span><span class="p">)</span> <span class="c1">// Добавляет путь для автозагрузки классов.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">get</span><span class="p">(</span><span class="nv">$key</span><span class="p">)</span> <span class="c1">// Получает переменную.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">set</span><span class="p">(</span><span class="nv">$key</span><span class="p">,</span> <span class="nv">$value</span><span class="p">)</span> <span class="c1">// Устанавливает переменную.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">has</span><span class="p">(</span><span class="nv">$key</span><span class="p">)</span> <span class="c1">// Проверяет, установлена ли переменная.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">clear</span><span class="p">([</span><span class="nv">$key</span><span class="p">])</span> <span class="c1">// Очищает переменную.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">init</span><span class="p">()</span> <span class="c1">// Инициализирует платформа до его настроек по умолчанию.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">app</span><span class="p">([</span><span class="nv">$path</span><span class="p">])</span> <span class="c1">// Получает экземпляр объекта приложения.
</span></code></pre>
						</div>
						<h2 id="extensible-methods">Расширяемые методы</h2>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">start</span><span class="p">()</span> <span class="c1">// Запускает платформа.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">stop</span><span class="p">()</span> <span class="c1">// Останавливает платформа и отправляет ответ.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">halt</span><span class="p">([</span><span class="nv">$code</span><span class="p">],</span> <span class="p">[</span><span class="nv">$message</span><span class="p">])</span> <span class="c1">// Останавливает платформа с необязательным статусным кодом и сообщением.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="nv">$pattern</span><span class="p">,</span> <span class="nv">$callback</span><span class="p">)</span> <span class="c1">// Отображает шаблон URL на обратный вызов.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">redirect</span><span class="p">(</span><span class="nv">$url</span><span class="p">,</span> <span class="p">[</span><span class="nv">$code</span><span class="p">])</span> <span class="c1">// Перенаправляет на другой URL.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">render</span><span class="p">(</span><span class="nv">$file</span><span class="p">,</span> <span class="p">[</span><span class="nv">$data</span><span class="p">],</span> <span class="p">[</span><span class="nv">$key</span><span class="p">])</span> <span class="c1">// Рендерит файл шаблона.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">error</span><span class="p">(</span><span class="nv">$exception</span><span class="p">)</span> <span class="c1">// Отправляет ответ HTTP 500.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">notFound</span><span class="p">()</span> <span class="c1">// Отправляет ответ HTTP 404.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">etag</span><span class="p">(</span><span class="nv">$id</span><span class="p">,</span> <span class="p">[</span><span class="nv">$type</span><span class="p">])</span> <span class="c1">// Выполняет кэширование HTTP с использованием ETag.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">lastModified</span><span class="p">(</span><span class="nv">$time</span><span class="p">)</span> <span class="c1">// Выполняет кэширование HTTP по последнему изменению.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">json</span><span class="p">(</span><span class="nv">$data</span><span class="p">,</span> <span class="p">[</span><span class="nv">$code</span><span class="p">],</span> <span class="p">[</span><span class="nv">$encode</span><span class="p">],</span> <span class="p">[</span><span class="nv">$charset</span><span class="p">],</span> <span class="p">[</span><span class="nv">$option</span><span class="p">])</span> <span class="c1">// Отправляет ответ в формате JSON.
</span><span class="nx">Set</span><span class="o">::</span><span class="na">jsonp</span><span class="p">(</span><span class="nv">$data</span><span class="p">,</span> <span class="p">[</span><span class="nv">$param</span><span class="p">],</span> <span class="p">[</span><span class="nv">$code</span><span class="p">],</span> <span class="p">[</span><span class="nv">$encode</span><span class="p">],</span> <span class="p">[</span><span class="nv">$charset</span><span class="p">],</span> <span class="p">[</span><span class="nv">$option</span><span class="p">])</span> <span class="c1">// Отправляет ответ в формате JSONP.
</span></code></pre>
						</div>
						<p>Любые пользовательские методы, добавленные с помощью <code class="highlighter-rouge">map</code> и <code class="highlighter-rouge">register</code>, также могут быть отфильтрованы.</p>
						<a name="frameworkinstance"></a>
						<h1 id="framework-instance">Экземпляр платформы</h1>
						<p>Вместо того чтобы запускать Set как глобальный статический класс, вы можете
							необязательно запустить его как объектный экземпляр.
						</p>
						<div class="language-php highlighter-rouge">
							<pre class="highlight"><code><span class="k">require</span> <span class="s1">'.set/set.php'</span><span class="p">;</span>

<span class="k">use</span> <span class="nx">set</span><span class="p">;</span>

<span class="nv">$app</span> <span class="o">=</span> <span class="k">new</span> <span class="nx">Set</span><span class="p">();</span>

<span class="nv">$app</span><span class="o">-&gt;</span><span class="na">route</span><span class="p">(</span><span class="s1">'/'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
    <span class="k">echo</span> <span class="s1">'Привет, мир!'</span><span class="p">;</span>
<span class="p">});</span>

<span class="nv">$app</span><span class="o">-&gt;</span><span class="na">start</span><span class="p">();</span>
</code></pre>
</div>
						<p>Таким образом, вместо вызова статического метода вы будете вызывать метод экземпляра с
							тем же именем на объекте Set.
						</p>
					</div>
				</div>
			</div>
<?php echo $footer_content; ?>