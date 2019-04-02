<?php


/// SETUP - NEED TO BE CHANGED

$domainname = 'http://moodle35.test';           // Moodle root URL

$token = '9437ea259fc079a1ec66bc46c43c2df2';    // Generated in Moodle: Admin > Plugins > Web Services > Manage Tokens

$params = ['items' => [
    ['type' => 'system', 'id' => 0],            // To get the platform instance. The ID is always 0
    ['type' => 'course', 'id' => 2],            // To get a course. The ID is visible in the course URL (e.g. http://moodle35.test/course/view.php?id=2)
    ['type' => 'lti', 'id' => 1]                // To get an LTI activity. The ID is not visible in the URL (not the same ID!). Check your DB "lti" table :(
]];


/// REST CALL

header('Content-Type: text/plain');
$functionname = 'logstore_trax_get_activities';
$serverurl = $domainname . '/webservice/rest/server.php'. '?moodlewsrestformat=json&wsfunction='.$functionname . '&wstoken=' . $token;
require_once('./curl.php');
$curl = new curl;
$items = $curl->post($serverurl, $params);
print_r($items);

