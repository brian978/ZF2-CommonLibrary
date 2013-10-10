<?php
/**
* ZF2-AuthModule
*
* @link      https://github.com/brian978/ZF2-ExtendedFramework
* @copyright Copyright (c) 2013
* @license   Creative Commons Attribution-ShareAlike 3.0
*/
return array(
    'auth_module' => array(
        // When setting this option to true, the user will be redirected to the
        // authentication page when not logged in
        'redirect_to_login' => false,
        'loginRoute' => 'auth',
        'loginFailedRoute' => 'auth',
        'loginSuccessRoute' => 'index',
        'loggedOutRoute' => 'auth',
        'alreadyLoggedInRoute' => 'index',
        'rememberFlag' => 'remember_me',
        'rememberExpire' => 2592000,
    ),
);
