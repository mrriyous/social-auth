<?php

$route['login/social/finish'] = 'LoginController/login_social_finish';
$route['login/social/confirm'] = 'LoginController/login_social_retrieve';
$route['login/social/(:any)'] = 'LoginController/login_using_social/$1';