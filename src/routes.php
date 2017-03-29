<?php

require_once 'parser.php';

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

    $from = $params['dateTimeFrom'];
    $to = $params['dateTimeTo'];
    
    $data = Parser::getData($from, $to);

    return $this->view->render($response, 'index.twig', $data);
});

$app->post('/postdata', function ($request, $response, $args) {

    var_dump($request->getQueryParams());
    die();
    // // Sample log message
    // $this->logger->info("Slim-Skeleton '/' route");

    // $params = $request->getQueryParams();

    // $from = $params['dateTimeFrom'];
    // $to = $params['dateTimeTo'];
    
    // $data = Parser::getData($from, $to);

    // return $this->view->render($response, 'index.twig', $data);
});