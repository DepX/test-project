<?php

namespace Simple;

class Loader
{
    private $namespaces = [];

    private $directories = [];

    private $di;

    private function loader($className)
    {
        $isFound = false; // namespace is found

        // register custom function
        foreach ($this->namespaces as $namespace => $directory) {
            $checkClass = strpos($className, $namespace);
            if ($checkClass !== false) {
                $isFound = true;

                $fileClass = $this->changeSeparator(str_replace($namespace, $directory, $className) . '.php');

                if (!file_exists($fileClass)) {
                    throw new \Exception('File not found: ' . $className);
                }

                include_once $fileClass;
            }
        }

        if (!$isFound) {
            // register custom standard namespace (/core)
            $fileClass = $this->changeSeparator(str_replace("Simple", __DIR__, $className) . '.php');
            if (!file_exists($fileClass)) {
                throw new \Exception('File not found: ' . $fileClass);
            }
            include_once $fileClass;
        }

        // include files
        foreach ($this->directories as $directory) {
            foreach (glob($directory . '/*.php') as $filename)
            {
                include_once $filename;
            }
        }
    }

    private function changeSeparator($path){
        return str_replace('\\', '/', $path);
    }

    public function setDi($di)
    {
        $this->di = $di;
    }

    /**
     * @param array $directories
     */
    public function registerDirectories($directories)
    {
        $this->directories = $directories;
    }

    /**
     * @param array $namespaces
     */
    public function registerNamespaces($namespaces)
    {
        $this->namespaces = $namespaces;
    }

    /**
     *
     */
    public function register()
    {
        spl_autoload_register([$this, 'loader']);
    }
}