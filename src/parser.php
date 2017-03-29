<?php

require __DIR__ . '/../vendor/autoload.php';


class Parser
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

    static public function getData($from, $to)
    {
        $pdo = static::getPDO();

        $selectStatement = $pdo->select()
                           ->from('printsessions')
                           ->where('id', '=', 1234);

        $stmt = $selectStatement->execute();
        $data = $stmt->fetch();

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