<?php

$clientId = 'clientId';
$clientSecret = 'clientSecret';
$redirectUri = 'http://localhost';
$domain = 'http://localhost';

require_once('../../Baidu.php');
$baidu = new Baidu($clientId, $clientSecret, $redirectUri, new BaiduCookieStore($clientId));
// Get User ID and User Name
$user = $baidu->getLoggedInUser();
