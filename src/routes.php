<?php

require_once 'Db.php';
require_once 'DatesWorker.php';

$app->get('/', function ($request, $response, $args) {

    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->view->render($response, 'index.twig', $args);
});

$app->get('/getdata', function ($request, $response, $args) {

    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    $params = $request->getQueryParams();

    $from = $params['start_datetime'];
    $to = $params['stop_datetime'];
    $period = $params['period'];

    $data = Db::getData($from, $to, $period);

    var_dump($data);
    die();

    return $this->view->render($response, 'index.twig');
});

$app->get('/postdata', function ($request, $response, $args) {

    // var_dump($request->getQueryParams());
    // die();
    
    $data = $request->getQueryParams();

    Db::saveData($data);

});