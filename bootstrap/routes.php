<?php
namespace Ritz\App;

use function DI\value;
use FastRoute\RouteCollector;
use Ritz\App\Controller\HomeController;
use Ritz\App\Controller\LoginController;
use Ritz\View\ViewModel;

return [
    // ルートの定義
    'app.routes' => value(function(RouteCollector $r) {

        $r->get('/',          [HomeController::class, 'indexAction']);
        $r->get('/view',      [HomeController::class, 'viewAction']);
        $r->get('/response',  [HomeController::class, 'responseAction']);
        $r->get('/error',     [HomeController::class, 'errorAction']);
        $r->get('/relative',  [HomeController::class, 'relativeAction']);
        $r->get('/attr',      [HomeController::class, 'attrAction', 'attr' => 'val']);
        $r->get('/callable',  [HomeController::class]);

        $r->get('/callback', [function () {
            return (new ViewModel())
                ->withTemplate('App/Home/show')
                ->withVariables([
                    'msg' => 'ハンドラにクロージャーを指定'
                ]);
        }]);

        $r->get('/login',    [LoginController::class, 'indexAction']);
        $r->post('/login',   [LoginController::class, 'loginAction']);
        $r->get('/logout',   [LoginController::class, 'logoutAction']);

        $r->addGroup('/user', function (RouteCollector $r) {
            $r->get('/{name}', [HomeController::class, 'userAction']);
        });
    }),
];
