<?php
namespace Ritz\App\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ritz\Router\RouteResult;
use Ritz\View\ViewModel;
use Zend\Diactoros\Response;

class RoutingMissMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $route = $request->getAttribute(RouteResult::class);

        if ($route  instanceof RouteResult) {
            if ($route->getInstance()) {
                return $delegate->process($request);
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
