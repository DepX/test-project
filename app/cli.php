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
        'Acme\Model'        => DIR_APP . '/models'
    ]);
    $loaderClass->registerDirectories([
        DIR_APP . '/tasks'
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

    // init cli
    $console = new \Simple\Cli\Console();
    $console->setDi($diClass);
    $console->setArguments($argv);
    $console->load();
} catch (\Exception $e) {
    echo 'Error code: ' . $e->getCode() . ', Message: ' . $e->getMessage();
}
