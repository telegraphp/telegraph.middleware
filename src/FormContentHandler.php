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

/**
 *
 * Handles URL-encoded content.
 *
 * @package telegraph/middleware
 *
 */
class FormContentHandler extends AbstractContentHandler
{
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
        return 'application/x-www-form-urlencoded' === $mime;
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
        parse_str($body, $parsed);
        return $parsed;
    }
}
