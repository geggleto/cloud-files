<?php
/**
 * Created by PhpStorm.
 * User: Glenn
 * Date: 2016-08-25
 * Time: 8:17 AM
 */
include __DIR__."/vendor/autoload.php";

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();
$dotenv->required(['account', 'api', 'api_username', 'container']);

$account = getenv('account');
$api = getenv('api');
$username = getenv('api_username');
$container = getenv('container');

$apiToken = new \Upload\ApiToken($api, $username);

//token - result['token']
$result = $apiToken->getToken();

$fileUpload = new \Upload\ApiFileUpload($result['token'], $account, 5, parse_url($result['endPoint'])['host'], $result['endPoint']);
$fileUpload->setContainer($container);


//Uploads a file immediately
$response = $fileUpload->uploadFileNow("lol.jpg", "lol.jpg");
print $response->getStatusCode(); //Should be 201