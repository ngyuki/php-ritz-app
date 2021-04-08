<?php
namespace Ritz\App\Middleware;

use Laminas\Diactoros\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ritz\View\ViewModel;

class ErrorMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Exception $ex) {
            $message = (new Response())->withStatus(500)->getReasonPhrase();
            return (new ViewModel())
                ->withVariable('message', $message)
                ->withTemplate('Error/error')
                ->withStatus(500);
        }
    }
}
