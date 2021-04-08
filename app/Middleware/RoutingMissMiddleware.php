<?php
namespace Ritz\App\Middleware;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ritz\Router\RouteResult;
use Ritz\View\ViewModel;

class RoutingMissMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute(RouteResult::class);

        if ($route  instanceof RouteResult) {
            if ($route->getInstance()) {
                return $handler->handle($request);
            }
            $status = $route->getStatus();
        } else {
            $status = 404;
        }

        static $map = [
            404 => '404',
            405 => '405',
        ];

        if (isset($map[$status])) {
            $template = $map[$status];
        } else {
            $template = 'error';
        }

        $message = (new Response())->withStatus($status)->getReasonPhrase();
        return (new ViewModel())
            ->withVariable('message', $message)
            ->withTemplate("Error/$template")
            ->withStatus($status);
    }
}
