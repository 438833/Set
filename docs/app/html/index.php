<?php echo $header_content; ?>			<div id="content">
    <h1 id="what-is-set">Что такое Set?</h1>
    <p>Set - это быстрая, простая, расширяемая среда для PHP (форк), за основу которого взят Flight (flightphp). Set позволяет вам быстро и легко создавать веб-приложения, следующие принципам REST.</p>
    <div class="language-php highlighter-rouge">
        <pre class="highlight"><code><span class="k">require</span> <span class="s1">'.set/set.php'</span><span class="p">;</span>

<span class="nx">Set</span><span class="o">::</span><span class="na">route</span><span class="p">(</span><span class="s1">'/'</span><span class="p">,</span> <span class="k">function</span><span class="p">(){</span>
  <span class="k">echo</span> <span class="s1">'Здравствуй, мир!'</span><span class="p">;</span>
<span class="p">});</span>

<span class="nx">Set</span><span class="o">::</span><span class="na">start</span><span class="p">();</span>
</code></pre>
    </div>
    <p><a href="learn">Узнать больше</a></p>
    <h1 id="requirements">Требования</h1>
    <p>Для работы Set требуется PHP версии 5.3 или выше.</p>
    <h1 id="license">Лицензия</h1>
    <p>Set распространяется под <a href="LICENSE">лицензией MIT</a>.</p>
    <h1 id="contributing">Вклад</h1>
    <p>Этот веб-сайт размещен на <a href="https://github.com/438833/Set">Github</a>. Обновления и переводы на другие языки приветствуются.</p>
</div>
<?php echo $footer_content; ?>