<?php

require_once 'Db.php';
require_once 'DatesWorker.php';
require_once 'DataChartAdapter.php';

use Carbon\Carbon;

$app->get('/', function ($request, $response, $args) {

    // $this->logger->info("Slim-Skeleton '/' route");
    $params = [];

    if ($response->hasHeader('start')) {
        $params['start'] = $response->getHeader('start');
    } else {
        $params['start'] = Carbon::now()->startOfWeek()->format('Y-m-d\TH:i:s');
    }

    if ($response->hasHeader('stop')) {
        $params['stop'] = $response->getHeader('stop');
    } else {
        $params['stop'] = Carbon::now()->endOfWeek()->format('Y-m-d\TH:i:s');
    }

    if ($response->hasHeader('period')) {
        $params['period'] = $response->getHeader('period');
    } else {
        $params['period'] = '0';
    }

    // Render index view
    return $this->view->render($response, 'index.twig', ['params' => $params]);
});

$app->get('/getdata', function ($request, $response, $args) {

    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    $params = $request->getQueryParams();

    $from = new Carbon($params['start_datetime']);
    $from = $from->format('Y-m-d H:i:s');

    $to = new Carbon($params['stop_datetime']);
    $to = $to->format('Y-m-d H:i:s');

    $periodsWithId = [
        'day' => '0', 
        'week' => '1',
        'month' => '2',
        'year' => '3'
    ];

    $periodId = $periodsWithId[$params['period']];

    $data = Db::getData($from, $to, $params['period']);

    DataChartAdapter::adaptForChart($data);

    return $response->withStatus(302)
                    ->withAddedHeader('start', $from)
                    ->withAddedHeader('stop', $to)
                    ->withAddedHeader('period', $periodId)
                    ->withHeader('Location', '/');

    //return $this->view->render($response, 'index.twig');
});

$app->get('/postdata', function ($request, $response, $args) {

    $data = $request->getQueryParams();

    Db::saveData($data);

});