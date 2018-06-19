<?php

$routerClass = new \Simple\Mvc\Router(true);
$routerClass->setDefaultNamespace('Acme\Controller');

/*** index ***/
$routerClass->add('/', [
    'controller' => 'index',
    'action' => 'index',
])->setName('home');
/*** /index ***/


/*** task ***/
$routerClass->add('/task/{id:[0-9]+}', [
    'controller' => 'task',
    'action' => 'index',
])->setName('task');
$routerClass->add('/task/add', [
    'controller' => 'task',
    'action' => 'add',
])->setName('task-add');
$routerClass->add('/task/edit/{id:[0-9]+}', [
    'controller' => 'task',
    'action' => 'edit',
])->setName('task-edit');
$routerClass->add('/task/delete/{id:[0-9]+}', [
    'controller' => 'task',
    'action' => 'delete',
])->setName('task-delete');
/*** /task ***/


/*** security ***/
$routerClass->add('/login', [
    'controller' => 'security',
    'action' => 'login',
])->setName('security-login');
$routerClass->add('/logout', [
    'controller' => 'security',
    'action' => 'logout',
])->setName('security-logout');
/*** /security ***/


/*** profile ***/
$routerClass->add('/profile', [
    'controller' => 'profile',
    'action' => 'index',
])->setName('profile-index');
/*** /profile ***/

$routerClass->notFound([
    'controller' => 'index',
    'action' => 'page404',
]);

return $routerClass;
