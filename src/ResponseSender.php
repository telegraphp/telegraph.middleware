<?php
/**
 *
 * This file is part of Telegraph for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @copyright 2016, Telegraph for PHP
 *
 */
namespace Telegraph\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 *
 * Sends the PSR-7 response.
 *
 * @package telegraph/middleware
 *
 */
class ResponseSender
{
    /**
     *
     * Sends the PSR-7 Response.
     *
     * @param RequestInterface $request The HTTP request.
     *
     * @param callable $next The next middleware in the queue.
     *
     * @return ResponseInterface
     *
     */
    public function __invoke(RequestInterface $request, callable $next)
    {
        $response = $next($request);
        $this->sendStatus($response);
        $this->sendHeaders($response);
        $this->sendBody($response);
        return $response;
    }

    /**
     *
     * Sends the Response status line.
     *
     * @return null
     *
     */
    protected function sendStatus(ResponseInterface $response)
    {
        $version = $response->getProtocolVersion();
        $status = $response->getStatusCode();
        $phrase = $response->getReasonPhrase();
        header("HTTP/{$version} {$status} {$phrase}");
    }

    /**
     *
     * Sends all Response headers.
     *
     * @return null
     *
     */
    protected function sendHeaders(ResponseInterface $response)
    {
        foreach ($response->getHeaders() as $name => $values) {
            $this->sendHeader($name, $values);
        }
    }

    /**
     *
     * Sends one Response header.
     *
     * @param string $name The header name.
     *
     * @param array $values The values for that header.
     *
     * @return null
     *
     */
    protected function sendHeader($name, $values)
    {
        $name = str_replace('-', ' ', $name);
        $name = ucwords($name);
        $name = str_replace(' ', '-', $name);
        foreach ($values as $value) {
            header("{$name}: {$value}", false);
        }
    }

    /**
     *
     * Streams the Response body 8192 bytes at a time via `echo`.
     *
     * @return null
     *
     */
    protected function sendBody(ResponseInterface $response)
    {
        $stream = $response->getBody();
        $stream->rewind();
        while (! $stream->eof()) {
            echo $stream->read(8192);
        }
    }
}
