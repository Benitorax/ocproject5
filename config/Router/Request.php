<?php
namespace Config\Router;

use Config\Router\Session;
use Config\Router\Parameter;

class Request
{
    private $get;
    private $post;
    private $cookies;
    private $server;
    private $session;
    private $attributes;


    public function __construct()
    {
        $this->get = new Parameter($_GET);
        $this->post = new Parameter($_POST);
        $this->cookies = new Parameter($_COOKIE);
        $this->server = new Parameter($_SERVER);
        $this->session = new Session($_SESSION);
        $this->attributes = new Parameter([]);
    }

    public function getGet(): Parameter
    {
        return $this->get;
    }

    public function getPost(): Parameter
    {
        return $this->post;
    }

    public function getCookies(): Parameter
    {
        return $this->cookies;
    }

    public function getServer(): Parameter
    {
        return $this->server;
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function getAttributes(): Parameter
    {
        return $this->attributes;
    }

    public function getMethod()
    {
        return $this->server->get('REQUEST_METHOD', 'GET');
    }

    public function getRequestUri()
    {
        $requestUri = '';

        if ('1' == $this->server->get('IIS_WasUrlRewritten') && '' != $this->server->get('UNENCODED_URL')) {
            // IIS7 with URL Rewrite: make sure we get the unencoded URL (double slash problem)
            $requestUri = $this->server->get('UNENCODED_URL');
            $this->server->remove('UNENCODED_URL');
            $this->server->remove('IIS_WasUrlRewritten');
        } elseif ($this->server->has('REQUEST_URI')) {
            $requestUri = $this->server->get('REQUEST_URI');

            if ('' !== $requestUri && '/' === $requestUri[0]) {
                // To only use path and query remove the fragment.
                if (false !== $pos = strpos($requestUri, '#')) {
                    $requestUri = substr($requestUri, 0, $pos);
                }
            } else {
                // HTTP proxy reqs setup request URI with scheme and host [and port] + the URL path,
                // only use URL path.
                $uriComponents = parse_url($requestUri);

                if (isset($uriComponents['path'])) {
                    $requestUri = $uriComponents['path'];
                }

                if (isset($uriComponents['query'])) {
                    $requestUri .= '?'.$uriComponents['query'];
                }
            }
        } elseif ($this->server->has('ORIG_PATH_INFO')) {
            // IIS 5.0, PHP as CGI
            $requestUri = $this->server->get('ORIG_PATH_INFO');
            if ('' != $this->server->get('QUERY_STRING')) {
                $requestUri .= '?'.$this->server->get('QUERY_STRING');
            }
            $this->server->remove('ORIG_PATH_INFO');
        }

        // normalize the request URI to ease creating sub-requests from this request
        $this->server->set('REQUEST_URI', $requestUri);

        return $requestUri;
    }
}