<?php 
// include your composer dependencies
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setApplicationName("DATO-SEO");
$client->setDeveloperKey("AIzaSyBf_MGPLbvI0KDALhNOhzT8Fkv-JKTnl90");

$service = new Google_Service_Books($client);
$optParams = array('filter' => 'free-ebooks');
$results = $service->volumes->listVolumes('Henry David Thoreau', $optParams);
var_dump($results);
?>