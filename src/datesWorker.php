<?php

declare(strict_types=1);

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

        $endOfDay = function (Carbon $someDay) {
            $date = new Carbon($someDay);
            return $date->endOfDay();
        };

        $startOfNextDay = function (Carbon $someDay) {
            $date = new Carbon($someDay);
            return $date->addDay()->startOfDay();
        };

        $iter = function ($acc, $startOfSomeDay) use (&$iter, &$to, &$startOfNextDay, &$endOfDay) {
            // If we are in last day
            if ( $startOfSomeDay->toDateString() == $to->toDateString())
            {
                $acc[] = [$startOfSomeDay->toDateTimeString(), $to->toDateTimeString()];
                return $acc;
            }

            $acc[] = [$startOfSomeDay->toDateTimeString(), $endOfDay($startOfSomeDay)->toDateTimeString()];

            return $iter($acc, $startOfNextDay($startOfSomeDay));
        };

        return $iter($periods, $startOfNextDay($from));
    }

    // return [ [start, end], [start, end], [start, end], ]
    // format: "2010-10-22 00:00:00"
    private static function parcelWeeks($from, $to)
    {
        $periods = [];

        $startOfWeek = function(Carbon $carbonDate){
            $date = new Carbon($carbonDate);
            return $date->startOfWeek();
        };

        $endOfWeek = function(Carbon $carbonDate){
            $date = new Carbon($carbonDate);
            return $date->endOfWeek();
        };

        $startOfNextWeek = function (Carbon $carbonDate) {
            $date = new Carbon($carbonDate);
            return $date->addWeek()->startOfWeek();
        };

        $from = Carbon::createFromFormat('Y-m-d H:i', $from);
        $to = Carbon::createFromFormat('Y-m-d H:i', $to);

        // If choose just one week
        if ($startOfWeek($from)->toDateString() == $startOfWeek($to)->toDateString())
        {
            return [[$from->toDateTimeString(), $to->toDateTimeString()]];
        }

        // Add first week period
        $periods[] = [$from->toDateTimeString(), $endOfWeek($from)->toDateTimeString()];

        $iter = function ($acc, $startOfSomeWeek) use (&$iter, &$to, &$startOfNextWeek, &$endOfWeek) {
            // If we are in last day
            if ( $endOfWeek($startOfSomeWeek)->toDateString() == $endOfWeek($to)->toDateString())
            {
                $acc[] = [$startOfSomeWeek->toDateTimeString(), $to->toDateTimeString()];
                return $acc;
            }

            $acc[] = [$startOfSomeWeek->toDateTimeString(), $endOfWeek($startOfSomeWeek)->toDateTimeString()];

            return $iter($acc, $startOfNextWeek($startOfSomeWeek));
        };

        // return [ [start, end], [start, end], [start, end], ]
        // format: "2010-10-22 00:00:00"
        return $iter($periods, $startOfNextWeek($from));
    }

    private static function parcelMonths($from, $to)
    {
        $periods = [];

        $startOfMonth = function(Carbon $carbonDate){
            $date = new Carbon($carbonDate);
            return $date->startOfMonth();
        };

        $endOfMonth = function(Carbon $carbonDate){
            $date = new Carbon($carbonDate);
            return $date->endOfMonth();
        };

        $startOfNextMonth = function (Carbon $carbonDate) {
            $date = new Carbon($carbonDate);
            return $date->addMonth()->startOfMonth();
        };

        $from = Carbon::createFromFormat('Y-m-d H:i', $from);
        $to = Carbon::createFromFormat('Y-m-d H:i', $to);

        // If choose just one week
        if ($startOfMonth($from)->toDateString() == $startOfMonth($to)->toDateString())
        {
            return [[$from->toDateTimeString(), $to->toDateTimeString()]];
        }

        // Add first week period
        $periods[] = [$from->toDateTimeString(), $endOfMonth($from)->toDateTimeString()];

        $iter = function ($acc, $startOfSomeMonth) use (&$iter, &$to, &$startOfNextMonth, &$endOfMonth) {
            // If we are in last day
            if ( $endOfMonth($startOfSomeMonth)->toDateString() == $endOfMonth($to)->toDateString())
            {
                $acc[] = [$startOfSomeMonth->toDateTimeString(), $to->toDateTimeString()];
                return $acc;
            }

            $acc[] = [$startOfSomeMonth->toDateTimeString(), $endOfMonth($startOfSomeMonth)->toDateTimeString()];

            return $iter($acc, $startOfNextMonth($startOfSomeMonth));
        };

        // return [ [start, end], [start, end], [start, end], ]
        // format: "2010-10-22 00:00:00"
        return $iter($periods, $startOfNextMonth($from));
    }
}