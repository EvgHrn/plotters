<?php

require __DIR__ . '/../vendor/autoload.php';

use Carbon\Carbon;

class DatesWorker
{

    public static function parcel()
    {



    }

    public static function parcelDays()
    {
        //
        $periods = [];

        $from = '2010-10-21 10:15';
        $to = '2011-01-12 23:15';

        $from = Carbon::createFromFormat('Y-m-d H:i', $from);
        $to = Carbon::createFromFormat('Y-m-d H:i', $to);

        var_dump($from);
        var_dump($to);


        // If choose just one day
        if ($from->toDateString() == $to->toDateString())
        {
            return [[$from->toDateTimeString(), $to->toDateTimeString()]];
        }


        // Add first day period
        $periods[] = [$from->toDateTimeString(), $from->endOfDay()->toDateTimeString()];

        $iter = function ($acc, $startOfSomeDay) use (&$iter, &$finishOfPeriod)
        {
            // If we are in last day
            if ( $startOfSomeDay->toDateString() == $finishOfPeriod->toDateString())
            {
                $acc[] = [$startOfSomeDay->toDateTimeString(), $finishOfPeriod->toDateTimeString()];
                return $acc;
            }

            $acc[] = [$startOfSomeDay->toDateTimeString(), $startOfSomeDay->endOfDay()->toDateTimeString()];

            return $iter($acc, $startOfSomeDay->addDay());
        };

        var_dump($iter($periods, $from->addDay()->startOfDay()));

        return $periods;
    
    }
}