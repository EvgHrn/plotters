<?php

require __DIR__ . '/../vendor/autoload.php';

use Carbon\Carbon;

class DatesWorker
{

    public static function parcel($from, $to, $period)
    {
        


    }


    // return [ [start, end], [start, end], [start, end], ]
    // format: "2010-10-22 00:00:00"
    public static function parcelDays($from, $to)
    {
        $periods = [];

        $from = '2010-10-21 10:15';
        $to = '2011-02-30 23:15';

        $from = Carbon::createFromFormat('Y-m-d H:i', $from);
        $to = Carbon::createFromFormat('Y-m-d H:i', $to);

        // If choose just one day
        if ($from->toDateString() == $to->toDateString())
        {
            return [[$from->toDateTimeString(), $to->toDateTimeString()]];
        }

        // Add first day period
        $periods[] = [$from->toDateTimeString(), $from->endOfDay()->toDateTimeString()];

        $startOfNextDay = function ($someDay) {
            return $someDay->addDay()->startOfDay();
        };

        $iter = function ($acc, $startOfSomeDay) use (&$iter, &$to, &$startOfNextDay) {
            // If we are in last day
            if ( $startOfSomeDay->toDateString() == $to->toDateString())
            {
                $acc[] = [$startOfSomeDay->toDateTimeString(), $to->toDateTimeString()];
                return $acc;
            }

            $acc[] = [$startOfSomeDay->toDateTimeString(), $startOfSomeDay->endOfDay()->toDateTimeString()];

            return $iter($acc, $startOfNextDay($startOfSomeDay));
        };

        return $iter($periods, $startOfNextDay($from));

    }
}