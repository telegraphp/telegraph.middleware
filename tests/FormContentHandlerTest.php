<?php
namespace Telegraph\Middleware;

use Telegraph\Middleware\FormContentHandler;
use Zend\Diactoros\Response;

class FormContentHandlerTest extends AbstractContentHandlerTest
{
    public function requestData()
    {
        return [
            ['POST'],
            ['PUT'],
            ['DELETE'],
            ['other'],
        ];
    }

    /**
     * @dataProvider requestData
     */
    public function testInvokeWithApplicableMimeType($method)
    {
        $request = $this->getRequest(
            $method,
            $mime = 'application/x-www-form-urlencoded',
            http_build_query($body = ['test' => 'form'], '', '&')
        );

        $next = function ($request) use ($mime, $body) {
            $this->assertSame($mime, $request->getHeaderLine('Content-Type'));
            $this->assertSame($body, $request->getParsedBody());
            return new Response();
        };

        $handler = new FormContentHandler();
        $resolved = $handler($request, $next);
    }

    public function testInvokeWithInvalidMethod()
    {
        $request = $this->getRequest(
            $method = 'GET',
            $mime = 'application/x-www-form-urlencoded'
        );

        $next = function ($request) use ($mime) {
            $this->assertSame($mime, $request->getHeaderLine('Content-Type'));
            $this->assertEmpty($request->getParsedBody());
            return new Response();
        };

        $handler = new FormContentHandler();
        $resolved = $handler($request, $next);
    }

    public function testInvokeWithNonApplicableMimeType()
    {
        $request = $this->getRequest(
            $method = 'POST',
            $mime = 'application/json',
            $body = json_encode((object) ['test' => 'json'])
        );

        $next = function ($request) use ($mime) {
            $this->assertSame($mime, $request->getHeaderLine('Content-Type'));
            $this->assertNull($request->getParsedBody());
            return new Response();
        };

        $handler = new FormContentHandler();
        $resolved = $handler($request, $next);
    }
}
