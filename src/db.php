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

        // var_dump($from, $to);
        // die();

        $pdo = static::getPDO();

        $selectStatement = $pdo->select()
                           ->from('printsessions')
                           ->where('start_datetime', '>=', $from)
                           ->where('start_datetime', '<=', $to);

        $stmt = $selectStatement->execute();
        $data = $stmt->fetch();

        var_dump($stmt);
        die();

        // $result = [
        //     [date(DATE_ATOM, mktime(11, 11, 18, 15, 10, 2010)), 3, 2, 3, 4, 5],
        //     [date(DATE_ATOM, mktime(11, 11, 18, 15, 10, 2011)), 3, 0, 3, 4, 5],
        //     [date(DATE_ATOM, mktime(11, 11, 18, 15, 10, 2012)), 2, 2, 3, 4, 10],
        //     [date(DATE_ATOM, mktime(11, 11, 18, 15, 10, 2013)), 3, 8, 3, 4, 5],
        //     [date(DATE_ATOM, mktime(11, 11, 18, 15, 10, 2014)), 3, 2, 3, 4, 0]
        // ];
        return $result;
    }
}