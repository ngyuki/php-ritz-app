<?php
namespace Ritz\App\Bootstrap;

use Franzl\Middleware\Whoops\WhoopsMiddleware;
use Laminas\Stratigility\MiddlewarePipe;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ritz\App\Middleware\RoutingMissMiddleware;
use Ritz\Middleware\DispatchMiddleware;
use Ritz\Middleware\RenderMiddleware;
use Ritz\Middleware\RouteMiddleware;
use Ritz\App\Middleware\ErrorMiddleware;
use Ritz\App\Middleware\LoginMiddleware;

/**
 * アプリケーションクラス
 */
class Application implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $pipeline = new MiddlewarePipe();

        if ($this->container->get('debug') && $this->container->get('whoops')) {
            // デバッグ時は Whoops を有効にする
            // このミドルウェアはすべての例外をキャッチしてデバッグ用ページをレスポンスに書き込む
            $pipeline->pipe(new WhoopsMiddleware());
        }

        // ルーティング結果をリクエストの属性に設定する
        $pipeline->pipe($this->container->get(RouteMiddleware::class));

        // コントローラーやミドルウェアが返した ViewModel を元にテンプレートをレンダリングする
        $pipeline->pipe($this->container->get(RenderMiddleware::class));

        if (!$this->container->get('debug')) {
            // ルーティング失敗にテンプレートを割り当てる
            $pipeline->pipe($this->container->get(RoutingMissMiddleware::class));

            // 例外発生時にエラーページのテンプレートを選択するミドルウェア
            // すべての例外をキャッチしてエラーページのための ViewModel を返す
            $pipeline->pipe($this->container->get(ErrorMiddleware::class));
        }

        // ログインをチェックしてログイン画面へのリダイレクトを行うミドルウェア
        $pipeline->pipe($this->container->get(LoginMiddleware::class));

        // ルーティング結果を元にコントローラーのアクションメソッドをディスパッチする
        $pipeline->pipe($this->container->get(DispatchMiddleware::class));

        return $pipeline->process($request, $handler);
    }
}
