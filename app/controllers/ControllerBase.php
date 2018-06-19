<?php

namespace Acme\Controller;

use Simple\Mvc\Controller;

/**
 * Main controller
 *
 * Class ControllerBase
 * @package Acme\Controller
 */
class ControllerBase extends Controller
{
    /**
     * Global parameters controllers
     *
     * @return array
     */
    public function defaultParams()
    {
        return [
            'title' => 'Default title'
        ];
    }

    /**
     * @param $template
     * @param array $params
     * @return mixed
     */
    public function render($template, $params = array())
    {
        return $this->view->render($template,
            array_merge(
                $this->defaultParams(),
                $params
            )
        );
    }

    /**
     * Error page 404
     */
    public function page404()
    {
        $this->response->setStatusCode(404);
        $this->view->render('index/page404', [
            'title' => '404 Not Found'
        ]);
    }
}