<?php

function chartAdapt($array)
{
    //array(5) { [1]=> int(1) [2]=> int(1) [3]=> int(1) [4]=> int(0) [5]=> int(3) }

    // {
    //     "cols": [
    //             {"label":"Date", "type":"string"},
    //             {"label":"Plotter1", "type":"number"},
    //             {"label":"Plotter2", "type":"number"},
    //             {"label":"Plotter3", "type":"number"},
    //             {"label":"Plotter4", "type":"number"},
    //             {"label":"Plotter5", "type":"number"}
    //         ],
    //     "rows": [
    //             {"c":[{"v":"Laravel"}, {"v":1}, {"v":1}, {"v":1}, {"v":1}, {"v":1}]},
    //             {"c":[{"v":"Laravel"}, {"v":10}, {"v":0}, {"v":3}, {"v":4}, {"v":4}]}
    //         ]
    // }

    $resutl = [
        "cols" => [
            ["label" => "Date", "type" => "string"],
            ["label" => "Plotter1", "type" => "number"],
            ["label" => "Plotter2", "type" => "number"],
            ["label" => "Plotter3", "type" => "number"],
            ["label" => "Plotter4", "type" => "number"],
            ["label" => "Plotter5", "type" => "number"]
        ],
        "rows" => []
    ];



}