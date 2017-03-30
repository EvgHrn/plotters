<?php

require __DIR__ . '/../vendor/autoload.php';

use Carbon\Carbon;

class DatesWorker
{
    public static function parcel()
    {
        //
        $periods = [];

        $from = '2010-10-21 10:15';
        $to = '2011-01-12 23:15';

        // var_dump($from);
        // var_dump($to);
        // var_dump("-----------------------------------------------------------");

        $periods[] = [$from, Carbon::createFromFormat('Y-m-d H:i', $from)
                                    ->endOfDay()
                                    ->toDateTimeString()];
        var_dump($from);
        var_dump(Carbon::createFromFormat('Y-m-d H:i', $from)
                                    ->endOfDay()
                                    ->toDateTimeString());

        return $periods;
    
    }
}