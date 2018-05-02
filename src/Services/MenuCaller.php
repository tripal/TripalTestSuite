<?php

namespace StatonLab\TripalTestSuite\Services;

use StatonLab\TripalTestSuite\Mocks\TestResponseMock;

class MenuCaller
{
    /**
     * Path to call.
     *
     * @var
     */
    protected $path;

    /**
     * POST or GET parameters.
     *
     * @var array
     */
    protected $params;

    /**
     * POST, GET, PUT, PATCH, DELETE.
     *
     * @var string
     */
    protected $method = 'GET';

    /**
     * Set method.
     *
     * @param $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Set public path.
     *
     * @param $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Add a single param to the request.
     *
     * @param $key
     * @param $value
     */
    public function addParam($key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * Add parameters to the request.
     *
     * @param $params
     * @return $this
     */
    public function addParams($params)
    {
        if (isset($params['form_params'])) {
            $params = $params['form_params'];
        } elseif (isset($params['query'])) {
            $params = $params['query'];
        }

        foreach ($params as $key => $value) {
            $this->params[$key] = $value;
        }

        return $this;
    }

    /**
     * Creates a mock HTTP response.
     *
     * @return \StatonLab\TripalTestSuite\Mocks\TestResponseMock
     * @throws \Exception
     */
    public function send()
    {
        $allowed_methods = ['POST', 'GET', 'PUT', 'PATCH', 'DELETE'];
        if (! in_array($this->method, $allowed_methods)) {
            throw new \Exception("Unknown method $this->method.");
        }

        $this->injectParams();

        $value = menu_execute_active_handler($this->path, false);

        $status = 200;
        if ($value === MENU_ACCESS_DENIED) {
            $status = 403;
            $value = "Access Denied Forbidden 403";
        } elseif ($value === MENU_NOT_FOUND) {
            $status = 404;
            $value = "Page not found";
        }

        return new TestResponseMock([
            'status' => $status,
            'body' => drupal_render_page($value),
            'headers' => [
                'Cache-Control' => 'no-cache, must-revalidate',
                'Connection' => 'Keep-Alive',
                'Content-Language' => 'en',
                'Content-Type' => 'text/html; charset=utf-8',
                'Date' => gmdate('D, d M Y H:is e'),
                'Expires' => 'Sun, 19 Nov 1978 05:00:00 GMT',
                'Keep-Alive' => 'timeout=5, max=100',
                'Server' => 'Apache/2.4.29 (Unix) PHP/7.1.14',
                'Transfer-Encoding' => 'chunked',
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'SAMEORIGIN',
                'X-Generator' => 'Drupal 7 (http://drupal.org)',
            ],
        ]);
    }

    /**
     * Injects parameters into the request.
     */
    protected function injectParams()
    {
        if (in_array($this->method, ['GET', 'DELETE'])) {
            foreach ($this->params as $key => $param) {
                $_GET[$key] = $param;
            }

            return;
        }

        foreach ($this->params as $key => $param) {
            $_POST[$key] = $param;
        }
    }
}
