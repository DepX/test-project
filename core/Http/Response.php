<?php

namespace Simple\Http;

use Simple\DI\InjectionInterface;
use Simple\Exception;

/**
 * Class Response
 * @package Simple\Http
 */
class Response implements InjectionInterface
{
    private $di;

    private $defaultCodes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version Not Supported',
    );

    /**
     * @param \Simple\DI\DefaultFactory $di
     * @return $this
     */
    public function setDi($di)
    {
        $this->di = $di;

        return $this;
    }

    /**
     * @return \Simple\DI\DefaultFactory
     */
    public function getDi()
    {
        return $this->di;
    }

    /**
     * Set status code
     *
     * @param $code
     * @param string $message
     */
    public function setStatusCode($code, $message = '')
    {
        if (!empty($this->defaultCodes[$code]) && empty($message)) {
            header('HTTP/1.1 ' . $code . ' ' . $this->defaultCodes[$code]);
        } else {
            header('HTTP/1.1 ' . $code . ' ' . $message);
        }
    }

    /**
     * Returns the status code
     *
     * @return string
     */
    public function getStatusCode()
    {
        return http_response_code();
    }

    /**
     * Returns the status message
     *
     * @return string
     */
    public function getStatusMessage()
    {
        if (!empty($this->defaultCodes[http_response_code()])) {
            return $this->defaultCodes[http_response_code()];
        }

        return false;
    }

    /**
     * Redirect
     *
     * @param $path
     * @param int $statusCode
     */
    public function redirect($path, $statusCode = 200)
    {
        if (is_array($path)) {
            if ($this->getDi()->getService('url')->get($path) !== false) {
                $scheme = (!empty($_SERVER['REQUEST_SCHEME'])) ? $_SERVER['REQUEST_SCHEME'] : 'http';
                $domain = $scheme . '://' . $_SERVER['HTTP_HOST'];
                $this->setStatusCode($statusCode);
                header('Location: ' . $domain . $this->getDi()->getService('url')->get($path));
            } else {
                throw new Exception('Error in path generate');
            }

            exit();
        } else {
            header('Location: ' . $path, true, $statusCode);
            exit();
        }
    }
}