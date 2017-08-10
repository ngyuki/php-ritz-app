<?php
namespace Ritz\App;

use function DI\get;

use Psr\Container\ContainerInterface;
use Ritz\Router\Router;
use Ritz\View\PhpRenderer;
use Ritz\View\RendererInterface;
use Ritz\View\TemplateResolver;
use Ritz\App\Component\Identity;
use Ritz\App\Component\IdentityInterface;

return [
    Router::class => function (ContainerInterface $container) {
        return new Router($container->get('app.routes'), $container->get('app.cache_dir'));
    },

    RendererInterface::class => function (ContainerInterface $container) {
        return new PhpRenderer($container->get('app.view.directory'), $container->get('app.view.suffix'));
    },

    TemplateResolver::class => function (ContainerInterface $container) {
        return new TemplateResolver($container->get('app.view.autoload'));
    },

    IdentityInterface::class => get(Identity::class),
];
