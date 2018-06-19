<?php

define('DIR_ROOT', realpath(__DIR__ . '/../'));
define('DIR_APP', DIR_ROOT . '/app');
define('DIR_PUBLIC', DIR_ROOT . '/public');

try {
    require DIR_ROOT . "/vendor/autoload.php";
    require DIR_ROOT . '/core/Loader.php';

    // register namespaces
    $loaderClass = new \Simple\Loader();
    $loaderClass->registerNamespaces([
        'Acme\Service'      => DIR_APP . '/services',
        'Acme\Controller'   => DIR_APP . '/controllers',
        'Acme\Model'        => DIR_APP . '/models'
    ]);
    $loaderClass->register();

    // init dependency injection
    $diClass = new \Simple\DI\DefaultFactory();

    // init config
    $diClass->setService('config', function () {
        $config = require DIR_APP . '/config/config.php';
        return $config;
    });

    // init database
    $diClass->setService('db', new \Simple\Mvc\Database());

    // init routers
    $diClass->setService('router', function () {
        $router = require DIR_APP . '/config/router.php';
        return $router;
    });

    // init url
    $diClass->setService('url', new \Simple\Mvc\Url());

    // init view
    $diClass->setService('view', function () {
        $view = new \Simple\Mvc\View();
        $view->setBasePath(DIR_APP . '/views');
        return $view;
    });

    // init crypt
    $diClass->setService('crypt', function () {
        $crypt = new \Simple\Crypt();
        $crypt->setKey('&wW5kzMe$Kb#7&t?9axKA@Q$?JY-MB%=');
        return $crypt;
    });

    // init security
    $diClass->setService('security', new \Acme\Service\Security());

    // init paginator
    $diClass->setService('paginator', new \Acme\Service\Paginator());

    // init app
    $applicationClass = new \Simple\Mvc\Application($diClass);
    $applicationClass->load();
} catch (\Exception $e) {
    echo 'Error code: ' . $e->getCode() . ', Message: ' . $e->getMessage();
}


