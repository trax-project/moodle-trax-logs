<?php


/// SETUP - NEED TO BE CHANGED

$domainname = 'http://moodle35.test';           // Moodle root URL

$token = '9437ea259fc079a1ec66bc46c43c2df2';    // Generated in Moodle: Admin > Plugins > Web Services > Manage Tokens

$params = ['items' => [
    ['type' => 'user', 'id' => 2]               // To get a user. The ID is visible in the user URL (http://moodle35.test/user/view.php?id=2)
]];


/// REST CALL

header('Content-Type: text/plain');
$functionname = 'logstore_trax_get_actors';
$serverurl = $domainname . '/webservice/rest/server.php'. '?moodlewsrestformat=json&wsfunction='.$functionname . '&wstoken=' . $token;
require_once('./curl.php');
$curl = new curl;
$items = $curl->post($serverurl, $params);
print_r($items);
