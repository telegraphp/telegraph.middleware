<?php
namespace Telegraph\Middleware;

use Telegraph\Middleware\FakePhp;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

function header($string, $flag = null)
{
    FakePhp::header($string, $flag);
}

class ResponseSenderTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        FakePhp::$headers = [];

        ob_start();
        $sender = new ResponseSender();
        $response = $sender(
            ServerRequestFactory::fromGlobals(),
            function ($request) {
                $response = new Response();
                $response = $response->withHeader('content-type', 'foo/bar');
                $response->getBody()->write('DOOM');
                return $response;
            }
        );
        $body = ob_get_clean();

        $expect = [
            'HTTP/1.1 200 OK',
            'Content-Type: foo/bar'
        ];
        $this->assertSame($expect, FakePhp::$headers);
        $this->assertSame('DOOM', $body);
    }
}
