<?php

require __DIR__ . '/../vendor/autoload.php';

class Db
{
    /**
    *@return \Slim\PDO\Database
    */
    static private function getPDO()
    {
        $settings = require __DIR__ . '/../src/settings.php';
        $dbSettings = $settings['settings']['db'];
        $dsn = "mysql:host={$dbSettings['host']};dbname={$dbSettings['name']};charset=utf8";
        $usr = $dbSettings['user'];
        $pwd = $dbSettings['pw'];
        
        return new \Slim\PDO\Database($dsn, $usr, $pwd);
    }
    
    /**
    *Save data to database.
    *Fields: 'session_id', 'plotter', 'start_datetime', 'stop_datetime', 'passes', 'meters'
    *
    *@param array $data
    *@return \Slim\PDO\Database
    */
    static public function saveData($data)
    {
        $pdo = static::getPDO();
        $insertStatement = $pdo->insert(array('session_id', 'plotter', 'start_datetime',
        'stop_datetime', 'passes', 'meters', 'speed'))
        ->into('printsessions')
        ->values(array($data['session_id'], $data['plotter'], $data['start_datetime'],
        $data['stop_datetime'], $data['passes'], $data['meters'], $data['meters']));
        
        $insertId = $insertStatement->execute(false);
    }
    
    /**
    *Get data from database.
    *
    *@param string $from - with format 'Y-m-d H:i'
    *@param string $to - with format 'Y-m-d H:i'
    *@param string $period - 'day', 'week', 'month' or 'year'
    *@return array - with format [ ['start_of_period' => date, 1 => meters, 2 => meters, 3 => meters, 4 => meters, 5 => meters], 
     *                              ['start_of_period' => date, 1 => meters, 2 => meters, 3 => meters, 4 => meters, 5 => meters], 
     *                               ...
     *                            ]
    */
    static public function getData($from, $to, $period)
    {       
        $periods = DatesWorker::parcel($from, $to, $period);
        
        $pdo = static::getPDO();
        
        $stmt = $pdo->prepare('SELECT plotter, meters FROM printsessions WHERE start_datetime
                                BETWEEN STR_TO_DATE(:ff, "%Y-%m-%d %H:%i:%s")
                                AND STR_TO_DATE(:tt, "%Y-%m-%d %H:%i:%s");');
        
        $periodsData = array_map(function($item) use (&$stmt){
            $start = $item[0];
            $end = $item[1];
            $stmt->execute(['ff' => $start, 'tt' => $end]);
            $rawData = $stmt->fetchAll(PDO::FETCH_GROUP );

            return array_reduce(array_keys($rawData), function ($acc, $plotter) use ($rawData) {
                $plotterData = $rawData[$plotter];
                $sum = array_reduce($plotterData, function ($acc, $item){
                    return $acc += $item['meters'];
                }, 0);
                $acc[$plotter] = $sum;
                return $acc;
            }, ['start_of_period' => $start, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]);

        }, $periods);
        
        // return [ ['start_of_period' => date, 1 => meters, 2 => meters, 3 => meters, 4 => meters, 5 => meters], 
        //          ['start_of_period' => date, 1 => meters, 2 => meters, 3 => meters, 4 => meters, 5 => meters], 
        //          ...
        //         ]
        return $periodsData;
    }
}
