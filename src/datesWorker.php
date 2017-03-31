<?php

require __DIR__ . '/../vendor/autoload.php';

use Carbon\Carbon;

class DatesWorker
{

    public static function parcel($from, $to, $period)
    {
        $result;
        switch ($period) {
            case 'day':
                $result = static::parcelDays($from, $to);
                break;
            
            case 'week':
                $result = static::parcelWeeks($from, $to);
                break;

            case 'month':
                $result = static::parcelMonths($from, $to);
                break;

            case 'year':
                $result = static::parcelYears($from, $to);
                break;

            default:
                break;
        }
        return $result;
    }


    // return [ [start, end], [start, end], [start, end], ]
    // format: "2010-10-22 00:00:00"
    // receive format: 'Y-m-d H:i'
    private static function parcelDays(string $from, string $to)
    {
        $periods = [];

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

    // return [ [start, end], [start, end], [start, end], ]
    // format: "2010-10-22 00:00:00"
    private static function parcelWeeks($from, $to)
    {
        $periods = [];

        $from = Carbon::createFromFormat('Y-m-d H:i', $from);
        $to = Carbon::createFromFormat('Y-m-d H:i', $to);

        // If choose just one week
        if ($from->endOfWeek()->toDateString() == $to->endOfWeek()->toDateString())
        {
            return [[$from->toDateTimeString(), $to->toDateTimeString()]];
        }

        // Add first week period
        $periods[] = [$from->toDateTimeString(), $from->endOfWeek()->toDateTimeString()];

        $startOfNextWeek = function ($someWeek) {
            return $someWeek->addWeek()->startOfWeek();
        };

        $iter = function ($acc, $startOfSomeWeek) use (&$iter, &$to, &$startOfNextWeek) {
            // If we are in last day
            if ( $startOfSomeWeek->endOfWeek()->toDateString() == $to->endOfWeek()->toDateString())
            {
                $acc[] = [$startOfSomeWeek->toDateTimeString(), $to->toDateTimeString()];
                return $acc;
            }

            $acc[] = [$startOfSomeWeek->toDateTimeString(), $startOfSomeWeek->endOfWeek()->toDateTimeString()];

            return $iter($acc, $startOfNextWeek($startOfSomeWeek));
        };

        // return [ [start, end], [start, end], [start, end], ]
        // format: "2010-10-22 00:00:00"
        return $iter($periods, $startOfNextWeek($from));
    }
}