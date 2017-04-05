<?php

require_once 'Db.php';
require_once 'DatesWorker.php';
require_once 'DataChartAdapter.php';

use Carbon\Carbon;

$app->get('/', function ($request, $response, $args) {

    // $this->logger->info("Slim-Skeleton '/' route");
    $params = [];

    ob_start();
    var_dump($_COOKIE);
    $content = ob_get_contents();
    ob_end_clean();
    $this->logger->info("Slim '/' route cookies: " . $content);

    $cookies = $_COOKIE;
    if (array_key_exists('start', $cookies)) {
        $params['start'] = $cookies['start'];
    } else {
        $params['start'] = Carbon::now()->startOfWeek()->format('Y-m-d\TH:i:s');
    }

    if (array_key_exists('stop', $cookies)) {
        $params['stop'] = $cookies['stop'];
    } else {
        $params['stop'] = Carbon::now()->endOfWeek()->format('Y-m-d\TH:i:s');
    }

    if (array_key_exists('period', $cookies)) {
        $params['period'] = $cookies['period'];
    } else {
        $params['period'] = '0';
    }

    // Render index view
    return $this->view->render($response, 'index.twig', ['params' => $params]);
});

$app->get('/getdata', function ($request, $response, $args) {

    $params = $request->getQueryParams();

    setcookie("start", $params['start_datetime'], 0);
    setcookie("stop", $params['stop_datetime'], 0);

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

    setcookie("period", $periodId, 0);

    return $response->withStatus(302)
                    ->withHeader('Location', '/');
});

$app->get('/postdata', function ($request, $response, $args) {

    ob_start();
    var_dump($_SERVER['QUERY_STRING']);
    $content = ob_get_contents();
    ob_end_clean();
    $this->logger->info("Slim '/postdata' route QUERY_STRING: " . $content);

    $data = $request->getQueryParams();

    Db::saveData($data);

});