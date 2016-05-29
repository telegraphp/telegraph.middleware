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

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 *
 * Catches exceptions thrown by middleware later in the queue.
 *
 * @package telegraph/middleware
 *
 */
class ExceptionHandler
{
    /**
     *
     * The Response to use when showing the excpetion; this *replaces* the
     * existing Response.
     *
     * @var ResponseInterface
     *
     */
    protected $exceptionResponse;

    /**
     *
     * Constructor.
     *
     * @param ResponseInterface $exceptionResponse The Response to use when
     * showing the exception.
     *
     */
    public function __construct(ResponseInterface $exceptionResponse)
    {
        $this->exceptionResponse = $exceptionResponse;
    }

    /**
     *
     * Catches any exception thrown in the queue this middleware, and puts its
     * message into the Response.
     *
     * @param RequestInterface $request The request.
     *
     * @param callable $next The next middleware in the queue.
     *
     * @return ResponseInterface
     *
     */
    public function __invoke(RequestInterface $request, callable $next)
    {
        try {
            $response = $next($request);
        } catch (Exception $e) {
            $response = $this->exceptionResponse->withStatus(500);
            $response->getBody()->write(get_class($e) . ': ' . $e->getMessage());
        }
        return $response;
    }
}
