<?php


/// SETUP - NEED TO BE CHANGED
$domainname = 'http://moodle35.test';
$functionname = 'logstore_trax_get_actors';
$token = '9437ea259fc079a1ec66bc46c43c2df2';

$params = ['items' => [
    ['type' => 'user', 'id' => 2]
]];

/// REST CALL
header('Content-Type: text/plain');
$serverurl = $domainname . '/webservice/rest/server.php'. '?moodlewsrestformat=json&wsfunction='.$functionname . '&wstoken=' . $token;
require_once('./curl.php');
$curl = new curl;
$items = $curl->post($serverurl, $params);
print_r($items);
die;
