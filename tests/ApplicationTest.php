<?php
namespace Ritz\Test\App;

use DI\Container;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Uri;
use Laminas\Dom\Document;
use Laminas\Dom\Document\Query;
use Ritz\App\Bootstrap\Application;
use Ritz\App\Bootstrap\ContainerFactory;
use Ritz\App\Component\IdentityInterface;
use Ritz\App\Component\IdentityStab;
use Ritz\Bootstrap\Server;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use function PHPUnit\Framework\assertEquals;

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

    protected function setUp(): void
    {
        $this->identity = new IdentityStab();
        $this->identity->set(['username' => 'oreore']);

        $this->container = (new ContainerFactory)->create();
        $this->container->set(IdentityInterface::class, $this->identity);
        $this->container->set('debug', true);
    }

    function createRequest($uri)
    {
        $request = ServerRequestFactory::fromGlobals();
        $request = $request->withUri(new Uri($uri));
        assert($request instanceof ServerRequestInterface);
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

        assertEquals(302, $response->getStatusCode());
        assertEquals('/login', $response->getHeaderLine('Location'));
    }

    /**
     * @test
     */
    function top_()
    {
        $request = $this->createRequest('http://localhost/');
        $response = $this->handle($request);

        assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    function attr_()
    {
        $request = $this->createRequest('http://localhost/attr');
        $response = $this->handle($request);

        assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    function relative_()
    {
        $request = $this->createRequest('http://localhost/relative');
        $response = $this->handle($request);

        assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    function notfound_debug()
    {
        $this->container->set('debug', true);
        $request = $this->createRequest('http://localhost/notfound');
        $response = $this->handle($request);
        $response->getBody()->rewind();

        assertEquals(404, $response->getStatusCode());
        self::assertStringNotContainsString('resource/view/Error/404.phtml', $response->getBody()->getContents());
    }

    /**
     * @test
     */
    function notfound_no_debug()
    {
        $this->container->set('debug', false);
        $request = $this->createRequest('http://localhost/notfound');
        $response = $this->handle($request);
        $response->getBody()->rewind();

        assertEquals(404, $response->getStatusCode());
        self::assertStringContainsString('resource/view/Error/404.phtml', $response->getBody()->getContents());
    }

    /**
     * @test
     */
    function method_not_allowed_debug()
    {
        $this->container->set('debug', true);
        $request = $this->createRequest('http://localhost/post');
        $response = $this->handle($request);
        $response->getBody()->rewind();

        assertEquals(405, $response->getStatusCode());
        self::assertStringNotContainsString('resource/view/Error/405.phtml', $response->getBody()->getContents());
    }

    /**
     * @test
     */
    function method_not_allowed_no_debug()
    {
        $this->container->set('debug', false);
        $request = $this->createRequest('http://localhost/post');
        $response = $this->handle($request);
        $response->getBody()->rewind();

        assertEquals(405, $response->getStatusCode());
        self::assertStringContainsString('resource/view/Error/405.phtml', $response->getBody()->getContents());
    }

    /**
     * @test
     */
    function error_debug()
    {
        $this->container->set('debug', true);
        $request = $this->createRequest('http://localhost/error');

        $this->expectException(RuntimeException::class);
        $this->handle($request);
    }

    /**
     * @test
     */
    function error_no_debug()
    {
        $this->container->set('debug', false);
        $request = $this->createRequest('http://localhost/error');
        $response = $this->handle($request);

        assertEquals(500, $response->getStatusCode());
    }

    /**
     * @test
     */
    function callable_()
    {
        $request = $this->createRequest('http://localhost/callable');
        $response = $this->handle($request);

        assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    function callback_()
    {
        $request = $this->createRequest('http://localhost/callback');
        $response = $this->handle($request);

        assertEquals(200, $response->getStatusCode());
    }
}
