<?php echo $header_content; ?>			<div id="content">
    <h1 id="installation">Установка</h1>
    <h3 id="download-the-files">1. Скачайте файлы.</h3>
    <p>Если вы используете <a href="https://getcomposer.org/">Composer</a>, выполните следующую команду:</p>
    <div class="highlighter-rouge">
        <pre class="highlight"><code>composer require 438833/set
</code></pre>
    </div>
    <p><b><em>ИЛИ</em></b> вы можете <a href="https://github.com/438833/set/archive/master.zip">скачать</a> их напрямую и извлечь в свой веб-каталог.</p>
    <h3 id="configure-your-webserver">2. Настройте ваш веб-сервер.</h3>
    <p>Для <em>Apache</em> отредактируйте ваш файл <code class="highlighter-rouge">.htaccess</code> следующим образом:</p>
    <div class="language-apache highlighter-rouge">
        <pre class="highlight"><code><span class="nc">RewriteEngine</span> On
<span class="nc">RewriteCond</span> %{REQUEST_FILENAME} !-f
<span class="nc">RewriteCond</span> %{REQUEST_FILENAME} !-d
<span class="nc">RewriteRule</span> ^(.*)$ index.php [QSA,L]
</code></pre>
    </div>
	<p><b><em>ИЛИ</em></b> оставьте все без изменений, <font color="#d14"><b><em>указав в .htaccess абсолютные php_value auto_prepend_file и php_value auto_append_file</em></b></font>.</p>
    <p>Для <em>Nginx</em> добавьте следующее в ваше объявление сервера:</p>
    <div class="language-nginx highlighter-rouge">
        <pre class="highlight"><code><span class="k">server</span> <span class="p">{</span>
    <span class="kn">location</span> <span class="n">/</span> <span class="p">{</span>
        <span class="kn">try_files</span> <span class="nv">$uri</span> <span class="nv">$uri</span><span class="n">/</span> <span class="n">/index.php</span><span class="p">;</span>
    <span class="p">}</span>
<span class="p">}</span>
</code></pre>
    </div>
    <h3 id="create-your-indexphp-file">3. Создайте файл <code class="highlighter-rouge">index.php</code>.</h3>
    <p>Сначала включите платформу.</p>
    <div class="language-php highlighter-rouge">
        <pre class="highlight"><code><span class="k">require</span> <span class="s1">'.set/set.php'</span><span class="p">;</span>
</code></pre>
    </div>
    <p>Если вы используете Composer, запустите автозагрузчик вместо этого.</p>
    <div class="language-php highlighter-rouge">
        <pre class="highlight"><code><span class="k">require</span> <span class="s1">'vendor/autoload.php'</span><span class="p">;</span>
</code></pre>
    </div>
    <p>Затем определите маршрут и присвойте функцию для обработки запроса.</p>
    <div class="language-php highlighter-rouge">
        <pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
    <span class="k">echo</span> <span class="s1">'Здравствуй, мир!'</span><span class="p">;</span>
<span class="p">});</span>
</code></pre>
    </div>
    <p>Наконец, запустите платформу.</p>
    <div class="language-php highlighter-rouge">
        <pre class="highlight"><code><span class="nx">Set</span><span class="o">::</span><span class="na">start</span><span class="p">()</span>;</span>
</code></pre>
    </div>
</div>

<?php echo $footer_content; ?>