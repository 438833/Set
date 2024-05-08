<?php

class ChannelController
{
	protected $app;

	public function __construct($app)
	{
		$this->app = $app;

		$pdoWrapper = $app->db_preferences();

		$sqlChannel = "SELECT * FROM `channel`";

		// Выполнение запроса и получение результата
		$resultChannel = $pdoWrapper->fetchAll($sqlChannel);

		// SQL запрос для выборки данных из таблицы channelSourceInfo
		$sqlChannelSourceInfo = "SELECT * FROM `channelSourceInfo`";

		// Выполнение запроса и получение результата
		$resultChannelSourceInfo = $pdoWrapper->fetchAll($sqlChannelSourceInfo);

		// Объединение результатов запросов
		$data = array();

		foreach ($resultChannel as $channel) {
			$channelId = $channel['channelId'];
			$channelData = array(
				'channelId' => $channelId,
				'name' => $channel['name'],
				'companyId' => $channel['companyId'],
				'dateCreated' => $channel['dateCreated'],
				'channelType' => $channel['channelType'],
				'countNotReadMessages' => $channel['countNotReadMessages'],
				'countMessages' => $channel['countMessages'],
				'isClosed' => $channel['isClosed'],
				'channelSourceInfo' => array()
			);

			foreach ($resultChannelSourceInfo as $sourceInfo) {
				if ($sourceInfo['channelId'] == $channelId) {
					$channelData['channelSourceInfo'] = array(
						'sourceId' => $sourceInfo['sourceId'],
						'sourceName' => $sourceInfo['sourceName'],
						'sourceType' => $sourceInfo['sourceType']
					);
					break;
				}
			}

			$data[] = $channelData;
		}

		// Преобразование данных в JSON формат
		$jsonData = json_encode(array('entities' => $data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

		// Преобразование JSON в массив
		$data = json_decode($jsonData, true);

		// Создание HTML таблицы
		$html = '<table border="1">';
		$html .= '<thead><tr><th>channelId</th><th>name</th><th>companyId</th><th>dateCreated</th><th>channelType</th><th>countNotReadMessages</th><th>countMessages</th><th>isClosed</th><th>channelSourceInfo</th></tr></thead>';
		$html .= '<tbody>';

		foreach ($data['entities'] as $entity) {
			$html .= '<tr>';
			$html .= '<td>' . $entity['channelId'] . '</td>';
			$html .= '<td>' . $entity['name'] . '</td>';
			$html .= '<td>' . $entity['companyId'] . '</td>';
			$html .= '<td>' . $entity['dateCreated'] . '</td>';
			$html .= '<td>' . $entity['channelType'] . '</td>';
			$html .= '<td>' . $entity['countNotReadMessages'] . '</td>';
			$html .= '<td>' . $entity['countMessages'] . '</td>';
			$html .= '<td>' . $entity['isClosed'] . '</td>';
			
			// Создание подтаблицы для channelSourceInfo
			$html .= '<td>';
			$html .= '<table border="1">';
			$html .= '<thead><tr><th>sourceId</th><th>sourceName</th><th>sourceType</th></tr></thead>';
			$html .= '<tbody>';
			$html .= '<tr>';
			$html .= '<td><a href="channel/'.$entity['channelSourceInfo']['sourceId'].'">' . $entity['channelSourceInfo']['sourceId'] . '</a></td>';
			$html .= '<td>' . $entity['channelSourceInfo']['sourceName'] . '</td>';
			$html .= '<td>' . $entity['channelSourceInfo']['sourceType'] . '</td>';
			$html .= '</tr>';
			$html .= '</tbody>';
			$html .= '</table>';
			$html .= '</td>';
			
			$html .= '</tr>';
		}

		$html .= '</tbody>';
		$html .= '</table>';

		// Вывод HTML таблицы
		echo $html;

	}

}

?>