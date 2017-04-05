<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';


use Carbon\Carbon;

class DatesWorker
{
    /**
    *@param string $from with fromat 'Y-m-d H:i:s'
    *@param string $to with fromat 'Y-m-d H:i:s'
    *@param string $period - 'day', 'week', 'month' or 'year'
    *@return array with format [ [start, end], [start, end], [start, end], ]. Each 'start' and 'end' has format: "2010-10-22 00:00:00"
    */
    public static function parcel(string $from, string $to, string $period)
    {
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

    /**
    *@param string $start with fromat 'Y-m-d H:i:s'
    *@param string $end with fromat 'Y-m-d H:i:s'
    *@return array with format [ [start, end], [start, end], [start, end], ]. Each 'start' and 'end' has format: "2010-10-22 00:00:00"
    */
    private static function parcelDays(string $start, string $end)
    {
        $periods = [];

 
        $from = Carbon::createFromFormat('Y-m-d H:i:s', $start);
        $to = Carbon::createFromFormat('Y-m-d H:i:s', $end);

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

    /**
    *@param string $from with fromat 'Y-m-d H:i:s'
    *@param string $to with fromat 'Y-m-d H:i:s'
    *@return array with format [ [start, end], [start, end], [start, end], ]. Each 'start' and 'end' has format: "2010-10-22 00:00:00"
    */
    private static function parcelWeeks(string $from, string $to)
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

        $from = Carbon::createFromFormat('Y-m-d H:i:s', $from);
        $to = Carbon::createFromFormat('Y-m-d H:i:s', $to);

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

    /**
    *@param string $from with fromat 'Y-m-d H:i:s'
    *@param string $to with fromat 'Y-m-d H:i:s'
    *@return array with format [ [start, end], [start, end], [start, end], ]. Each 'start' and 'end' has format: "2010-10-22 00:00:00"
    */
    private static function parcelMonths(string $from, string $to)
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

        $from = Carbon::createFromFormat('Y-m-d H:i:s', $from);
        $to = Carbon::createFromFormat('Y-m-d H:i:s', $to);

        // If choose just one month
        if ($startOfMonth($from)->toDateString() == $startOfMonth($to)->toDateString())
        {
            return [[$from->toDateTimeString(), $to->toDateTimeString()]];
        }

        // Add first month period
        $periods[] = [$from->toDateTimeString(), $endOfMonth($from)->toDateTimeString()];

        $iter = function ($acc, $startOfSomeMonth) use (&$iter, &$to, &$startOfNextMonth, &$endOfMonth) {
            // If we are in last month of period
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

    /**
    *@param string $from with fromat 'Y-m-d H:i:s'
    *@param string $to with fromat 'Y-m-d H:i:s'
    *@return array with format [ [start, end], [start, end], [start, end], ]. Each 'start' and 'end' has format: "2010-10-22 00:00:00"
    */
    private static function parcelYears(string $from, string $to)
    {
        $periods = [];

        $startOfYear = function(Carbon $carbonDate){
            $date = new Carbon($carbonDate);
            return $date->startOfYear();
        };

        $endOfYear = function(Carbon $carbonDate){
            $date = new Carbon($carbonDate);
            return $date->endOfYear();
        };

        $startOfNextYear = function (Carbon $carbonDate) {
            $date = new Carbon($carbonDate);
            return $date->addYear()->startOfYear();
        };

        $from = Carbon::createFromFormat('Y-m-d H:i:s', $from);
        $to = Carbon::createFromFormat('Y-m-d H:i:s', $to);

        // If choose just one Year
        if ($startOfYear($from)->toDateString() == $startOfYear($to)->toDateString())
        {
            return [[$from->toDateTimeString(), $to->toDateTimeString()]];
        }

        // Add first Year period
        $periods[] = [$from->toDateTimeString(), $endOfYear($from)->toDateTimeString()];

        $iter = function ($acc, $startOfSomeYear) use (&$iter, &$to, &$startOfNextYear, &$endOfYear) {
            // If we are in last Year of period
            if ( $endOfYear($startOfSomeYear)->toDateString() == $endOfYear($to)->toDateString())
            {
                $acc[] = [$startOfSomeYear->toDateTimeString(), $to->toDateTimeString()];
                return $acc;
            }

            $acc[] = [$startOfSomeYear->toDateTimeString(), $endOfYear($startOfSomeYear)->toDateTimeString()];

            return $iter($acc, $startOfNextYear($startOfSomeYear));
        };

        // return [ [start, end], [start, end], [start, end], ]
        // format: "2010-10-22 00:00:00"
        return $iter($periods, $startOfNextYear($from));
    }
}
