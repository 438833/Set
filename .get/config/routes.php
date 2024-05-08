<?php

class Greeting
{
    public function __construct() {
        $this->name = 'Джон Доу';
    }

    public function hello() {
        echo "Привет, {$this->name}!";
    }
}

$app->group(
	''
	, function($router) use ($app)
	{

		$IndexController = new IndexController($app);
		$router->add(
			'/(index(/*|.php))'
			, function()
			{
				echo "<br /><b><font color=\"MediumVioletRed\">Первая страница</font></b><br /><hr color=\"MediumVioletRed\"><br />";
			}
			, false
			, 'index'
		);
		$router->add(
			'/crypt'
			, function() use($app)
			{
				$crypto = $app->crypto();
				echo "<br /><b><font color=\"MediumVioletRed\">Тесты:</font></b><br /><hr color=\"MediumVioletRed\"><br />";
				$originalValue = '<br />Мясо и салат, пиво, скрученный косяк<br />
Моя жизнь — мармелад, потому что рядом брат, не один<br />
Ещё один, ещё целая орда<br />
Сквозь года и города пацан семейный, навсегда<br />
Воздух любит позитив, дело любит работяг<br />
Чувства — тоже хорошо, но это люди не едят<br />
В этом блюде нет деньжат, в этом клубе нет любви<br />
Я где-то посередине, моя жизнь — мармелад<br />
Да-а, Пало подарил мне кислород<br />
Жанне с твоего района — пока только лишь аборт<br />
В этом месте один сорт<br />
Я собрал весь этот сброд и сорвал с ними джекпот<br />
Мы как бренд, мы как тренд, будто Пума и Том Форд<br />
Поебались в мире мод; улыбаюсь, меня прёт<br />
В месте, где видно, как звёзды заполняют небосвод<br />
«Тысяча девятьсот девяносто» — вписано на обороте (Scroll)<br />
Я везде как у себя на родине<br />';
				$password = '123456';
				$encrypted = $crypto->encrypt($originalValue, $password);
				$chunks = str_split($encrypted, 200);
				$result = implode("<br />", $chunks);
				$decrypted = $crypto->decrypt($encrypted, $password);
				echo "<br />Зашифрованная строка: <br /><br /><b><font color=\"green\">{$result}</font></b><br />";
				echo "<br />Расшифрованная строка: <br /><b><font color=\"blue\">{$decrypted}</font></b><br />";
			}
			, false
			, 'index'
		);
	}
	, array(
		new HeaderSecurityMiddleware, new CorsMiddleware
	)
);

?>