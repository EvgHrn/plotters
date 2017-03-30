<?php

require __DIR__ . '/../vendor/autoload.php';


class Db
{
    private function getPDO()
    {
        $settings = require __DIR__ . '/../src/settings.php';
        $dbSettings = $settings['settings']['db'];
        $dsn = "mysql:host={$dbSettings['host']};dbname={$dbSettings['name']};charset=utf8";
        $usr = $dbSettings['user'];
        $pwd = $dbSettings['pw'];

        return new \Slim\PDO\Database($dsn, $usr, $pwd);
    }

    static public function saveData($data)
    {
        $pdo = static::getPDO();
        $insertStatement = $pdo->insert(array('session_id', 'plotter', 'start_datetime', 
                                                'stop_datetime', 'passes', 'meters'))
                            ->into('printsessions')
                            ->values(array($data['session_id'], $data['plotter'], $data['start_datetime'], 
                                                $data['stop_datetime'], $data['passes'], $data['meters']));

        $insertId = $insertStatement->execute(false);
    }

    static public function getData($from, $to)
    {

        $from = str_replace("T", " ", $from).":00";
        $to = str_replace("T", " ", $to).":00";

        $pdo = static::getPDO();

        $stmt = $pdo->prepare('SELECT plotter, meters FROM printsessions WHERE start_datetime 
                                BETWEEN STR_TO_DATE(:ff, "%Y-%m-%d %H:%i:%s") 
                                AND STR_TO_DATE(:tt, "%Y-%m-%d %H:%i:%s");');

        $stmt->execute(['ff' => $from, 'tt' => $to]);
        $rawData = $stmt->fetchAll(PDO::FETCH_GROUP );

        $data = array_reduce(array_keys($rawData), function ($acc, $plotter) use ($rawData) {

            $plotterData = $rawData[$plotter];
            $sum = array_reduce($plotterData, function ($acc, $item){
                return $acc += $item['meters'];
            }, 0);
            $acc[$plotter] = $sum;
            return $acc;

        }, [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]);

        return $data;
    }
}