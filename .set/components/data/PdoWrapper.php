<?php

namespace set\components\data;

use set\components\util\Collection;
use PDO;
use PDOStatement;

class PdoWrapper extends PDO
{

	public function runQuery($sql, $params = array())
	{
        $processed_sql_data = $this->processInStatementSql($sql, $params);
        $sql = $processed_sql_data['sql'];
        $params = $processed_sql_data['params'];
        $statement = $this->prepare($sql);
        $statement->execute($params);
        return $statement;
    }

    public function fetchField($sql, $params = array())
    {
        $result = $this->fetchRow($sql, $params);
        $data = $result->getData();
        return reset($data);
    }

	public function fetchRow($sql, $params = array())
    {
        $sql .= stripos($sql, 'LIMIT') === false ? ' LIMIT 1' : '';
        $result = $this->fetchAll($sql, $params);
        return count($result) > 0 ? $result[0] : new Collection();
    }

    public function fetchAll($sql, $params = array())
    {
        $processed_sql_data = $this->processInStatementSql($sql, $params);
        $sql = $processed_sql_data['sql'];
        $params = $processed_sql_data['params'];
        $statement = $this->prepare($sql);
        $statement->execute($params);
        $results = $statement->fetchAll();
        if(is_array($results) === true && count($results) > 0)
		{
			for($i = 0, $l = count($results); $i < $l; $i++)
			{
				$result = &$results[$i];
				$result = new Collection($result);
			}
        }
		else
		{
            $results = array();
        }
        return $results;
    }

    protected function processInStatementSql($sql, array $params = array())
    {
        $sql = preg_replace('/IN\s*\(\s*\?\s*\)/i', 'IN(?)', $sql);

        $current_index = 0;
        while(($current_index = strpos($sql, 'IN(?)', $current_index)) !== false)
		{
            $preceeding_count = substr_count($sql, '?', 0, $current_index - 1);
            $param = $params[$preceeding_count];
            $question_marks = '?';
            if(is_string($param) || is_array($param))
			{
                $params_to_use = $param;
                if(is_string($param))
				{
                    $params_to_use = explode(',', $param);
                }
				for($i = 0, $l = count($params_to_use); $i < $l; $i++)
				{
					if(is_string($params_to_use[$i]))
					{
						$params_to_use[$i] = trim($params_to_use[$i]);
					}
				}
                $question_marks = join(',', array_fill(0, count($params_to_use), '?'));
                $sql = substr_replace($sql, $question_marks, $current_index + 3, 1);
                array_splice($params, $preceeding_count, 1, $params_to_use);
            }
            $current_index += strlen($question_marks) + 4;
        }
        return array('sql' => $sql, 'params' => $params);
    }
}

?>