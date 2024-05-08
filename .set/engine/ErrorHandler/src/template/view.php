<?php

/**
 * Библиотека для обработки исключений и ошибок
 *
 * @since     1.0.0
 */

?>
<div class="handler-alert default <?=strtolower($stack['type'])?>">

    <span class="handler-head">
        <?=$stack['code']?>
        <?=strtoupper($stack['type'])?>
        <a href="https://stackoverflow.com/search?q=[php] <?=$stack['message']?>" class="so-link">&#9906;</a>
    </span>

    <span class="handler-head handler-right">
        <?=$stack['mode']?>
    </span>

    <span class="handler-message"><br><br>
        <?=$stack['message']?>
    </span><br><br>

    <div class="handler-preview">
		<div class="handler-box">
			<span class="handler-file">
				<?=$stack['file']?>
			</span><br>
			<code>
			<?=$stack['preview']?>
			</code>
		</div>
    </div>

    <div class="handler-trace">
        <?=nl2br($stack['trace'])?>
    </div><br>

</div>