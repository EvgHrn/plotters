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
}