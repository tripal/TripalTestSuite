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
            $params['form_token'] = drupal_get_token();
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
        $_SERVER['REQUEST_METHOD'] = $this->method;
        $this->injectParams();

        list($status, $value, $headers) = $this->execute();

        drupal_add_http_header('Content-Type', null);

        return new TestResponseMock([
            'status' => $status,
            'body' => $value,
            'headers' => [
                    'Cache-Control' => 'no-cache, must-revalidate',
                    'Connection' => 'Keep-Alive',
                    'Content-Language' => 'en',
                    'Content-Type' => 'text/html; charset=utf-8',
                    'Date' => gmdate('D, d M Y H:is e'),
                    'Expires' => 'Sun, 19 Nov 1978 05:00:00 GMT',
                    'Keep-Alive' => 'timeout=5, max=100',
                    'Server' => 'Apache/2.4.29 (Unix) PHP/'.phpversion(),
                    'Transfer-Encoding' => 'chunked',
                    'X-Content-Type-Options' => 'nosniff',
                    'X-Frame-Options' => 'SAMEORIGIN',
                    'X-Generator' => 'Drupal 7 (http://drupal.org)',
                ] + $headers,
        ]);
    }

    protected function execute()
    {
        $buffer = '';
        ob_start(function ($str) use (&$buffer) {
            $buffer .= $str;
        });
        menu_execute_active_handler($this->path);
        ob_end_clean();

        $status = 200;
        $header = drupal_get_http_header('Status');
        if ($header && strstr($header, '404') !== false) {
            $status = 404;
        } elseif ($header && strstr($header, '403') !== false) {
            $status = 403;
        }

        $value = $buffer;

        return [
            $status,
            $value,
            drupal_get_http_header(),
        ];
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

        $menu_item = menu_get_item($this->path);
        $_POST['form_id'] = isset($menu_item['page_arguments']) ? $menu_item['page_arguments'][0] : '';
        foreach ($this->params as $key => $param) {
            $_POST[$key] = $param;
        }
    }
}
