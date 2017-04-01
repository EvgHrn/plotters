<?php

require __DIR__ . '/../vendor/autoload.php';

class DataChartAdapter
{
    /**
    *@param array $data - with format [ 
    *                                   ['start_of_period' => date, 1 => meters, 2 => meters, 3 => meters, 4 => meters, 5 => meters], 
     *                                  ['start_of_period' => date, 1 => meters, 2 => meters, 3 => meters, 4 => meters, 5 => meters], 
     *                                   ...
     *                                ] 
    */
    static public function adaptForChart($data)
    {
        $adaptedData = static::adapt($data);
        $file = fopen("data.json", "w");
        fwrite($file, $adaptedData);
        fclose($file);
    }

    static private function adapt($rowData)
    {
        $result = [];
        $result["cols"] = [
                ["label" => "Date", "type" => "string"],
                ["label" => "Plotter1", "type" => "number"],
                ["label" => "Plotter2", "type" => "number"],
                ["label" => "Plotter3", "type" => "number"],
                ["label" => "Plotter4", "type" => "number"],
                ["label" => "Plotter5", "type" => "number"]
            ];
        $rows = array_map(function($periodData){
            // our $periodData: ['start_of_period' => date, 1 => meters, 2 => meters, 3 => meters, 4 => meters, 5 => meters]
            //we need [ "c":[ ["v":"Date"], ["v":1], ["v":1], ["v":1], ["v":1], ["v":1] ] ]
            $result = [];
            $startOfPeriodJustDay = explode(" ", $periodData["start_of_period"])[0];
            $result["c"] = [ 
                            ["v" => $startOfPeriodJustDay],
                            ["v" => $periodData[1]],
                            ["v" => $periodData[2]],
                            ["v" => $periodData[3]],
                            ["v" => $periodData[4]],
                            ["v" => $periodData[5]]
                        ];
            return $result;
        }, $rowData);
        $result["rows"] = $rows;
        // var_dump($result);
        // die();
        return json_encode($result);
    }
}