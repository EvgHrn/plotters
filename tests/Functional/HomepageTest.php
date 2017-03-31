<?php

//namespace Tests\Functional;
require __DIR__ . '/../../vendor/autoload.php';
require_once  __DIR__ . '/../../src/DatesWorker.php';

class DatesWorkerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
     */
    public function testParcelDays()
    {
        $result = DatesWorker::parcel('2017-01-01 15:15', '2017-01-03 22:22', 'day');
        $this->assertEquals($result, [
            ['2017-01-01 15:15:00', '2017-01-01 23:59:59'],
            ['2017-01-02 00:00:00', '2017-01-02 23:59:59'],
            ['2017-01-03 00:00:00', '2017-01-03 22:22:00']
        ]);
        // $this->assertContains('SlimFramework', (string)$response->getBody());
        // $this->assertNotContains('Hello', (string)$response->getBody());
    }

    /**
     * Test that the index route with optional name argument returns a rendered greeting
     */
    // public function testGetHomepageWithGreeting()
    // {
    //     $response = $this->runApp('GET', '/name');

    //     $this->assertEquals(200, $response->getStatusCode());
    //     $this->assertContains('Hello name!', (string)$response->getBody());
    // }

    // /**
    //  * Test that the index route won't accept a post request
    //  */
    // public function testPostHomepageNotAllowed()
    // {
    //     $response = $this->runApp('POST', '/', ['test']);

    //     $this->assertEquals(405, $response->getStatusCode());
    //     $this->assertContains('Method not allowed', (string)$response->getBody());
    // }
}

// class HomepageTest extends BaseTestCase
// {
//     /**
//      * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
//      */
//     public function testGetHomepageWithoutName()
//     {
//         $response = $this->runApp('GET', '/');

//         $this->assertEquals(200, $response->getStatusCode());
//         $this->assertContains('SlimFramework', (string)$response->getBody());
//         $this->assertNotContains('Hello', (string)$response->getBody());
//     }

//     /**
//      * Test that the index route with optional name argument returns a rendered greeting
//      */
//     public function testGetHomepageWithGreeting()
//     {
//         $response = $this->runApp('GET', '/name');

//         $this->assertEquals(200, $response->getStatusCode());
//         $this->assertContains('Hello name!', (string)$response->getBody());
//     }

//     /**
//      * Test that the index route won't accept a post request
//      */
//     public function testPostHomepageNotAllowed()
//     {
//         $response = $this->runApp('POST', '/', ['test']);

//         $this->assertEquals(405, $response->getStatusCode());
//         $this->assertContains('Method not allowed', (string)$response->getBody());
//     }
// }