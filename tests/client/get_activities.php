<?php


/// SETUP - NEED TO BE CHANGED
$domainname = 'http://moodle35.test';
$functionname = 'logstore_trax_get_activities';
$token = '9437ea259fc079a1ec66bc46c43c2df2';

$params = ['items' => [
    ['type' => 'system', 'id' => 0],
    ['type' => 'course', 'id' => 2],
    ['type' => 'lti', 'id' => 1]
]];

/// REST CALL
header('Content-Type: text/plain');
$serverurl = $domainname . '/webservice/rest/server.php'. '?moodlewsrestformat=json&wsfunction='.$functionname . '&wstoken=' . $token;
require_once('./curl.php');
$curl = new curl;
$items = $curl->post($serverurl, $params);
print_r($items);
die;

