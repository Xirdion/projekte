<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 19.04.2017
 * Time: 13:18
 */

require __DIR__.'/vendor/autoload.php';

$client = new \GuzzleHttp\Client([
    'base_uri' => 'http://apiweb.voggenreitert.idowapro.vn.idowa.de/',
    'defaults' => [
        'exceptions' => false
    ]
]);

$nickname = 'ObjectOrienter'.rand(0, 999);
$data = array(
    'nickname' => $nickname,
    'avatarNumber' => 5,
    'tagLine' => 'a test div!'
);

// 1) Create a programmer resource
$response = $client->post('/api/programmers', [
    'body' => json_encode($data)
]);
showResponse($response, 'create Programmer:');

// 2) GET a programmer resource
$response = $client->get($response->getHeader('Location')[0]);
showResponse($response, 'get Programmer:');

// 3) GET all programmers
$response = $client->get('/api/programmers');
showResponse($response, 'get all Programmers:');
/**
 * @param \Psr\Http\Message\ResponseInterface $response
 * @param string $headline
 */
function showResponse($response, string $headline = "") {
    echo("\n----------------------------------------");
    echo("\n\n");
    if ($headline) {
        echo($headline."\n\n");
    }
    echo("Status: ".$response->getStatusCode()."\n");
    foreach ($response->getHeaders() as $header => $values) {
        echo($header.": ");
        foreach ($values as $value) {
            echo("'".$value."'\t");
        }
        echo("\n");
    }
    echo("\n\n");
    echo($response->getBody());
    echo("\n\n");
    echo("----------------------------------------\n");
}