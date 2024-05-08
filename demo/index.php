<?php

$t = time();
$url = 'http'.(!empty($_SERVER['HTTPS'])?'s':'').'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$localPath = parse_url($url, PHP_URL_PATH);
$parentPath = dirname($localPath);
$parentUrl = parse_url($url, PHP_URL_SCHEME).'://'.parse_url($url, PHP_URL_HOST). $parentPath.'/';

?><!DOCTYPE html>
<html lang="ru">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta http-equiv="cleartype" content="on" />
		<meta name="viewport" content="width=970" />
		<meta name="MobileOptimized" content="970" />
		<meta http-equiv="x-pjax-version" content="reader-layout" />
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, user-scalable=no, maximum-scale=1.0" />
		<title>Demo</title>
		<style>
			:after, :before
			{
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				box-sizing: border-box;
			}

			*:focus
			{
				outline: 0 !important;
			}

			*, *::before, *::after
			{
				margin: 0;
				padding: 0;
				border: 0;
				box-sizing: border-box;
			}

			html, body
			{
				width: 100%;
				height: 100%;
			}

			body
			{
				background-color: #fff;
				color: #555;
				margin: 0px;
				font-family: 'Open Sans', sans-serif;
			}

			.clearfix
			{
				* + height: 1%;
			}

			.clearfix:after
			{
				content: '.';
				display: block;
				height: 0;
				clear: both;
				visibility: hidden;
			}

			.b:after, .b:before
			{
				display: table;
				content: ' ';
			}

			.l
			{
				float: left;
			}

			.r
			{
				float: right;
			}

			.m
			{
				margin-right: 10px;
				margin-bottom: 10px;
			}

			button, input, optgroup, select, textarea
			{
				color: inherit;
				font: inherit;
				margin: 0;
			}

			button
			{
				overflow: visible;
			}

			button, select
			{
				text-transform: none;
			}

			button, html input[type=button], input[type=reset], input[type=submit]
			{
				-webkit-appearance: button;
				cursor: pointer;
			}

			button, input, select, textarea
			{
				font-family: inherit;
				font-size: inherit;
				line-height: inherit;
			}

			.btn.focus, .btn:focus, .btn:hover
			{
				text-decoration: none;
			}

			.btn
			{
				display: inline-block;
				font-weight: 400;
				text-align: center;
				white-space: nowrap;
				vertical-align: middle;
				-ms-touch-action: manipulation;
				touch-action: manipulation;
				cursor: pointer;
				background-image: none;
				border: 1px solid transparent;
				padding: 6px 12px;
				font-size: 14px;
				line-height: 1.42857143;
				border-radius: 4px;
				-webkit-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none;
				user-select: none;
			}

			.btn-primary
			{
				color: #fff;
				background-color: #5f90b9;
				border-color: #7396b5;
			}

			.btn-warning
			{
				color: #fff;
				background-color: #b95f5f;
				border-color: #b57373;
			}

			.btn.active, .btn:active
			{
				background-image: none;
				outline: 0;
				-webkit-box-shadow: inset 0 3px 5px rgba(0,0,0,.125);
				box-shadow: inset 0 3px 5px rgba(0,0,0,.125);
			}

			.btn-primary:hover
			{
				color: #fff;
				background-color: rgb(76, 109, 138);
				border-color: rgb(68, 107, 140);
			}

			.btn-warning:disabled,
			.btn-warning[disabled]
			{
				color: #fff;
				background-color: rgb(218, 157, 157);
				border-color: rgb(214, 161, 161);
			}

			.btn-warning:hover:not([disabled])
			{
				color: #fff;
				background-color: rgb(138, 76, 76);
				border-color: rgb(140, 68, 68);
			}

			textarea
			{
				width: 100%;
				height: 150px;
				padding: 6px 12px;
				font-size: 14px;
				line-height: 1.42857143;
				color: #555;
				background-color: #fff;
				background-image: none;
				border: 1px solid #ccc;
				border-radius: 4px;
				-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
				box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
				-webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
				-o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
				transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
			}

			a
			{
				color: #268bd2;
				transition: color 200ms;
			}

			.container
			{
				padding-right: 15px;
				padding-left: 15px;
				margin-right: auto;
				margin-left: auto;
				min-width: 332px;
			}

			@media (min-width: 578px)
			{
				.m
				{
					margin-bottom: 0px;
				}
			}

			@media (min-width: 768px)
			{
				.container
				{
					width: 750px;
				}
			}

			@media (min-width: 992px)
			{
				.container
				{
					width: 970px;
				}
			}

			.form-group
			{
				margin-bottom: 15px;
			}

			.form-label
			{
				display: inline-block;
				max-width: 100%;
				margin-bottom: 5px;
				letter-spacing: .05em;
				padding: 8px 0;
			}

			.form-control
			{
				display: block;
				width: 100%;
				height: 34px;
				padding: 6px 12px;
				font-size: 14px;
				line-height: 1.42857143;
				color: #555;
				background-color: #fff;
				background-image: none;
				border: 1px solid rgb(210, 210, 210);
				border-radius: 4px;
				-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
				box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
				-webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
				-o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
				-webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
				transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
				transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
				transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
			}

			.form-control:focus
			{
				border-color: rgb(205, 192, 214);
				outline: 0;
				-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(168, 147, 183, 0.6);
				box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(168, 147, 183, 0.6);
			}

			.panel
			{
				margin-bottom: 20px;
				background-color: #fff;
				border: 1px solid transparent;
				border-radius: 4px;
				-webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
				box-shadow: 0 1px 1px rgba(0,0,0,.05);
			}

			.panel-default
			{
				border-color: rgb(243, 243, 243);
			}

			.panel-heading
			{
				padding: 10px 15px;
				border-bottom: 1px solid transparent;
				border-top-left-radius: 3px;
				border-top-right-radius: 3px;
			}

			.panel-default > .panel-heading
			{
				color: rgb(109, 128, 144);
				background-color: rgb(245, 245, 245);
				border-color: rgb(221, 221, 221);
			}

			.panel-title
			{
				margin-top: 0;
				margin-bottom: 0;
				font-size: 16px;
				font-weight: 600;
				color: inherit;
			}

			.panel-title
			{
				margin-top: 8px;
			}

			.panel-body
			{
				padding: 15px;
			}

			.panel-result
			{
				background-color: #ffffff;
				color: #000000;
			}

			.panel-result
			{
				position: relative;
				overflow: hidden;
				font: 12px/normal 'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', 'source-code-pro', monospace;
				direction: ltr;
				text-align: left;
				-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
			}

			.panel-result
			{
				width: 100%;
				font-size: 14px;
			}

			.panel-result
			{
				min-width: 200px;
				flex-basis: 33%;
				border-radius: 4px;
				padding: 5px;
				background-color: rgb(247, 247, 247);
			}

			.panel-table
			{
				margin-top: 10px;
				width: 100%;
				font-size: 14px;
			}

			.json
			{
				font-size: 14px;
			}

		</style>
		<link rel="stylesheet" href="<?php echo $url; ?>/assets/jsonview/css/jsonview.css?v=<?php echo $t; ?>" />
		<link rel="stylesheet" href="<?php echo $url; ?>/assets/json2table/css/json2table.css?v=<?php echo $t; ?>" />
		<script type="text/javascript" charset="utf-8" src="<?php echo $url; ?>/assets/jsonview/js/jsonview.js?v=<?php echo $t; ?>"></script>
		<script type="text/javascript" charset="utf-8" src="<?php echo $url; ?>/assets/json2table/js/json2table.js?v=<?php echo $t; ?>"></script>
	</head>
	<body>
		<div class="container">
			<div class="form-group">
				<label for="name" class="form-label">API запрос</label>
				<input type="text" class="form-control" id="interface" placeholder="Адрес сервера для запроса" value="<?php echo $parentUrl; ?>test">
			</div>
			<div class="form-group">
				<label for="name" class="form-label">JSON запрос</label>
				<textarea class="form-area" id="jsonRequest" placeholder="Введите JSON запрос"></textarea>
			</div>
			<div class="panel panel-default b">
				<div class="panel-heading b clearfix">
					<h3 class="panel-title b l">Ответ сервера</h3>
					<button type="button" class="btn btn-primary r" id="btn-request">Запросить ответ</button>
				</div>
				<div class="panel-heading b clearfix">
					<button type="button" class="btn btn-warning l m" id="btn-auth">Запросить авторизацию</button>
					<button type="button" class="btn btn-warning l" id="btn-auth-request" disabled="disabled">Запросить авторизованный ответ</button>
				</div>
				<div class="panel-heading b clearfix">
					<button type="button" class="btn btn-primary l m" id="btn-collapse">Свернуть до первого уровня (1)</button>
					<button type="button" class="btn btn-primary l" id="btn-maxlvl">Показать JSON до первого уровня (1)</button>
				</div>
				<div class="panel-body">
					<div class="panel-result">
						<div id="json" class="json"></div>
					</div>
					<div class="panel-table">
						<div id="table" class="table"></div>
					</div>
				</div>
			</div>
		</div>
	</body>
	<script type="text/javascript">
		function httpGetAsync(uri, callback)
		{
			var xmlHttp = new XMLHttpRequest();
			xmlHttp.onreadystatechange = function()
			{ 
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
				{
					callback(xmlHttp.responseText);
				}
			}
			xmlHttp.open('GET', uri, true);
			xmlHttp.send(null);
		}
		function httpGet(uri)
		{
			var xmlHttp = new XMLHttpRequest();
			xmlHttp.open('GET', uri, false);
			xmlHttp.send(null);
			return xmlHttp.responseText;
		}
		function httpPostAsync(uri, data, callback)
		{
			var xmlHttp = new XMLHttpRequest();
			xmlHttp.onreadystatechange = function()
			{ 
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
				{
					callback(xmlHttp.responseText);
				}
			}
			xmlHttp.open('POST', uri, true);
			xmlHttp.setRequestHeader('Content-Type', 'application/json');
			xmlHttp.send(JSON.stringify(data));
		}
		function isValidUrl(string)
		{
			try
			{
				new URL(string);
			}
			catch (_)
			{
				return false;  
			}
			return true;
		}
		function isValidJson(string)
		{
			try
			{
				JSON.parse(string);
			}
			catch (_)
			{
				return false;  
			}
			return true;
		}
		var jsonObj = {},
			jsonView = new JSONView();
		document.querySelector("#json").appendChild(jsonView.getContainer());
		var setJSON = function()
		{
			try
			{
				var value = JSON.stringify('Сервер недоступен'),
					uri = isValidUrl(document.querySelector("#interface").value) ? document.querySelector("#interface").value : false;
					value = httpGet(uri);
					if(isValidJson(value))
					{
						var table = new Json2Table(value, 'table');
						table.attributes = { class: 'json2table', cellspacing: '0', cellpadding: '0' };
						table.convert();
					}
					else
					{
						value = JSON.stringify('Ошибка формата JSON, проверьте серверную передачу данных');
					}
				jsonObj = JSON.parse(value);
			}
			catch(err)
			{
				alert(err);
			}
		};
		setJSON();
		jsonView.showJSON(jsonObj);
		var requestBtn = document.querySelector("#btn-request"),
			collapseBtn = document.querySelector("#btn-collapse"),
			maxlvlBtn = document.querySelector("#btn-maxlvl"),
			authBtn = document.querySelector("#btn-auth"),
			authRequestBtn = document.querySelector("#btn-auth-request");
	
		requestBtn.addEventListener("click", function(){
			
			
			var url = new URL(document.querySelector("#interface").value);
			url.searchParams.delete('login');
			url.searchParams.delete('password');
			url.searchParams.delete('token');
			document.querySelector("#interface").value = url;
			
			var jsonRequest = document.querySelector("#jsonRequest").value.trim();
			if(jsonRequest !== '') {
				httpPostAsync(url.href, JSON.parse(jsonRequest), function(response) {
					value = response;
					if(isValidJson(value))
					{
						var table = new Json2Table(value, 'table');
						table.attributes = { class: 'json2table', cellspacing: '0', cellpadding: '0' };
						table.convert();
						jsonObj = JSON.parse(value);
						jsonView.showJSON(jsonObj);
					}
					else
					{
						value = JSON.stringify('Ошибка формата JSON, проверьте серверную передачу данных');
					}
				});
			} else {
				setJSON();
				jsonView.showJSON(jsonObj);
			}
			authBtn.removeAttribute("disabled");
			authRequestBtn.setAttribute("disabled", "disabled");
		});

		collapseBtn.addEventListener("click", function() {
			jsonView.showJSON(jsonObj, null, 1);
		});
		maxlvlBtn.addEventListener("click", function() {
			jsonView.showJSON(jsonObj, 1);
		});
		authBtn.addEventListener("click", function() {
			var url = new URL(document.querySelector("#interface").value);
			url.searchParams.delete('token');
			url.searchParams.set('login', 'test@gmail.com');
			url.searchParams.set('password', 'NUIPH07ws');
			document.querySelector("#interface").value = url;
			setJSON();
			jsonView.showJSON(jsonObj);
			authBtn.setAttribute("disabled", "disabled");
			authRequestBtn.removeAttribute("disabled");
		});
		authRequestBtn.addEventListener("click", function() {
			var url = new URL(document.querySelector("#interface").value);
			url.searchParams.delete('login');
			url.searchParams.delete('password');
			url.searchParams.set('token', '098f6bcd4621d373cade4e832627b4f6');
			document.querySelector("#interface").value = url;
			setJSON();
			jsonView.showJSON(jsonObj);
			jsonView.showJSON(jsonObj, 1);
		});
	</script>
</html>