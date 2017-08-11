<?php
require __DIR__  . '/../vendor/autoload.php';

use Ritz\Bootstrap\Server;
use Ritz\App\Bootstrap\Application;
use Ritz\App\Bootstrap\ContainerFactory;

$container = (new ContainerFactory())->create();
$server = new Server();
$server->run($container->get(Application::class));
