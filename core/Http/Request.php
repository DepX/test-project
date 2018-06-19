<?php

namespace Simple\Http;

use Simple\Http\Request\File;

/**
 * Class Request
 * @package Simple\Http
 */
class Request
{
    /**
     * Gets a variable from the $_REQUEST
     *
     * @param $name
     * @return bool
     */
    public function get($name)
    {
        return (!empty($_REQUEST[$name])) ? $_REQUEST[$name] : false;
    }

    /**
     * Gets a variable from the $_POST
     *
     * @param $name
     * @return bool
     */
    public function getPost($name)
    {
        return (!empty($_POST[$name])) ? $_POST[$name] : false;
    }

    /**
     * Check index the $_REQUEST
     *
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return !empty($_REQUEST[$name]);
    }

    /**
     * Check index the $_POST
     *
     * @param $name
     * @return bool
     */
    public function hasPost($name)
    {
        return !empty($_POST[$name]);
    }

    /**
     * Check request method (POST)
     *
     * @return bool
     */
    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check request method (FILES)
     *
     * @return bool
     */
    public function hasFiles()
    {
        return (!empty($_FILES));
    }

    /**
     * Get array uploaded files
     *
     * @return array|bool
     */
    public function getUploadedFiles()
    {
        if (!$this->hasFiles()) {
            return false;
        }

        $arrayFiles = [];
        foreach ($_FILES as $key => $file) {
            $fileClass = new File();
            $fileClass->setKey($key);
            $fileClass->setName($file['name']);
            $fileClass->setType($file['type']);
            $fileClass->setTmpName($file['tmp_name']);
            $fileClass->setError($file['error']);
            $fileClass->setSize($file['size']);
            $arrayFiles[] = $fileClass;
        }
        return $arrayFiles;
    }
}