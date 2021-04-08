<?php
namespace Ritz\App\Middleware;

use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ritz\View\ViewModel;
use Ritz\App\Component\IdentityInterface;

class LoginMiddleware implements MiddlewareInterface
{
    /**
     * @var IdentityInterface
     */
    private $identity;

    public function __construct(IdentityInterface $identity)
    {
        $this->identity = $identity;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $noLogin = $request->getAttribute('noLogin');

        if ($noLogin) {
            return $handler->handle($request);
        }

        if ($this->identity->is() === false) {
            return new RedirectResponse('/login');
        }

        $response = $handler->handle($request);

        if ($response instanceof ViewModel) {
            $response = $response->withVariable('identify', $this->identity->get());
        }

        return $response;
    }
}
