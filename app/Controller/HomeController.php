<?php
namespace Ritz\App\Controller;

use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ServerRequestInterface;
use Ritz\View\ViewModel;

class HomeController
{
    public function indexAction()
    {
        return [];
    }

    public function userAction($name)
    {
        return (new ViewModel())->withTemplate('App/Home/show')->withVariables([
            'msg' => "ルートパラメータ ... name => $name",
        ]);
    }

    public function attrAction(ServerRequestInterface $request)
    {
        $attr = $request->getAttribute('attr');
        return (new ViewModel())->withTemplate('App/Home/show')->withVariables([
            'msg' => "ルーターで属性を指定 ... attr => $attr",
        ]);
    }

    public function relativeAction()
    {
        return (new ViewModel())->withTemplate('./show')
            ->withVariable('msg', "テンプレート名を相対で指定");
    }

    public function responseAction()
    {
        return new TextResponse("アクションから Response オブジェクトを返す");
    }

    public function errorAction()
    {
        throw new \RuntimeException("アクションで例外");
    }

    public function __invoke()
    {
        return (new ViewModel())->withTemplate('App/Home/show')->withVariables([
            'msg' => "アクションを指定せずに callable なクラスを指定",
        ]);
    }
}
