<?php

namespace Simple\Mvc;

use Simple\DI\InjectionInterface;
use Simple\Exception;

class View implements InjectionInterface
{
    private $di;
    private $basePath;
    private $variables;
    private $currentTemplate;

    /**
     * @param $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @return mixed
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Set variable
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->variables[$name] = $value;
    }

    /**
     * Get variable
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->variables[$name];
    }

    /**
     * @param \Simple\DI\DefaultFactory $di
     */
    public function setDi($di)
    {
        $this->di = $di;
    }

    /**
     * @return \Simple\DI\DefaultFactory
     */
    public function getDi()
    {
        return $this->di;
    }

    /**
     * Render template
     *
     * @param $template
     * @param array $variables
     */
    public function render($template, $variables = [])
    {
        $this->currentTemplate = $template;
        $this->variables = $variables;

        foreach ($this->di->getServices() as $key => $service) {
            $this->variables[$key] = $this->di->getService($key);
        }

        $fileIndex = $this->basePath . '/index.phtml';
        if (!file_exists($fileIndex)) {
            throw new Exception('Template not found "index"');
        }
        include $fileIndex;
    }

    /**
     * Load partial
     *
     * @param $template
     * @return string
     */
    public function partial($template, $variables = [])
    {
        $file = $this->basePath . '/' . $template . '.phtml';
        if (!file_exists($file)) {
            throw new Exception('Partial not found "' . $template . '"');
        }
        foreach ($variables as $key => $variable) {
            $this->variables[$key] = $variable;
        }

        ob_start();
        include $file;
        return ob_get_clean();
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        $file = $this->basePath . '/' . $this->currentTemplate . '.phtml';
        if (!file_exists($file)) {
            throw new Exception('Template not found "' . $this->currentTemplate . '"');
        }

        ob_start();
        include $file;
        return ob_get_clean();
    }
}