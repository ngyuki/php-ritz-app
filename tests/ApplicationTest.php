<?php
namespace Ritz\Test\App;

use DI\Container;
use Ritz\App\Bootstrap\Application;
use Ritz\App\Bootstrap\ContainerFactory;
use Ritz\App\Component\IdentityInterface;
use Ritz\App\Component\IdentityStab;
use Ritz\Bootstrap\Server;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Uri;
use Zend\Dom\Document;
use Zend\Dom\Document\Query;

class ApplicationTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var IdentityStab
     */
    private $identity;

    protected function setUp()
    {
        $this->identity = new IdentityStab();
        $this->identity->set(['username' => 'oreore']);

        $this->container = (new ContainerFactory)->create();
        $this->container->set(IdentityInterface::class, $this->identity);
    }

    function createRequest($uri)
    {
        $request = ServerRequestFactory::fromGlobals();
        $request = $request->withUri(new Uri($uri));
        return $request;
    }

    function query(Document $document, $expr)
    {
        return $this->queryAll($document, $expr)[0];
    }

    function queryAll(Document $document, $expr)
    {
        return Query::execute($expr, $document, Query::TYPE_CSS);
    }

    function handle(ServerRequestInterface $request)
    {
        $response = (new Server())->handle($this->container->get(Application::class), $request);
        return $response;
    }

    /**
     * @test
     */
    function redirect_login()
    {
        $this->identity->clear();

        $request = $this->createRequest('http://localhost/');
        $response = $this->handle($request);

        self::assertEquals(302, $response->getStatusCode());
        self::assertEquals('/login', $response->getHeaderLine('Location'));
    }

    /**
     * @test
     */
    function top_()
    {
        $request = $this->createRequest('http://localhost/');
        $response = $this->handle($request);

        self::assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    function attr_()
    {
        $request = $this->createRequest('http://localhost/attr');
        $response = $this->handle($request);

        self::assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    function relative_()
    {
        $request = $this->createRequest('http://localhost/relative');
        $response = $this->handle($request);

        self::assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    function notfound_()
    {
        $request = $this->createRequest('http://localhost/notfound');
        $response = $this->handle($request);

        self::assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    function error_()
    {
        $request = $this->createRequest('http://localhost/error');
        $response = $this->handle($request);

        self::assertEquals(500, $response->getStatusCode());
    }

    /**
     * @test
     */
    function callable_()
    {
        $request = $this->createRequest('http://localhost/callable');
        $response = $this->handle($request);

        self::assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    function callback_()
    {
        $request = $this->createRequest('http://localhost/callback');
        $response = $this->handle($request);

        self::assertEquals(200, $response->getStatusCode());
    }
}
