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
 * Handles JSON content.
 *
 * @package telegraph/middleware
 *
 */
class JsonContentHandler extends AbstractContentHandler
{
    /**
     *
     * When true, returned objects will be converted into associative arrays.
     *
     * @var bool
     *
     */
    protected $assoc;

    /**
     *
     * User specified recursion depth.
     *
     * @var int
     *
     */
    protected $maxDepth;

    /**
     *
     * Bitmask of JSON decode options. Currently only JSON_BIGINT_AS_STRING is
     * supported (default is to cast large integers as floats).
     *
     * @var int
     *
     */
    protected $options;

    /**
     *
     * Constructor.
     *
     * @param bool $assoc Return objects as associative arrays?
     *
     * @param int $maxDepth Max recursion depth.
     *
     * @param int $options Bitmask of JSON decode options.
     *
     */
    public function __construct($assoc = false, $maxDepth = 512, $options = 0)
    {
        $this->assoc = $assoc;
        $this->maxDepth = $maxDepth;
        $this->options = $options;
    }

    /**
     *
     * Checks if the content type is appropriate for handling.
     *
     * @param string $mime The mime type.
     *
     * @return boolean
     *
     */
    protected function isApplicableMimeType($mime)
    {
        return preg_match('~^application/([a-z.]+\+)?json($|;)~', $mime);
    }

    /**
     *
     * Parses the request body.
     *
     * @param string $body The request body.
     *
     * @return mixed
     *
     * @uses throwException()
     *
     */
    protected function getParsedBody($body)
    {
        $body = json_decode($body, $this->assoc, $this->maxDepth, $this->options);

        if (! json_last_error()) {
            return $body;
        }

        return $this->throwException('Error parsing JSON: ' . json_last_error_msg());
    }
}
