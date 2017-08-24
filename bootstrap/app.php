<?php
namespace Ritz\App;

use function DI\env;
use function DI\get;

return [
    'debug' => env('APP_DEBUG', true),

    'whoops' => get('debug'),

    // キャッシュディレクトリ
    'app.cache_dir' => getenv('APP_CACHE_DIR') ?: __DIR__ . '/../cache/',

    // キャッシュの有効無効
    'app.use_cache' => getenv('APP_USE_CACHE'),

    // テンプレートファイルを格納するディレクトリ
    'app.view.directory' => dirname(__DIR__) . '/resource/view/',

    // テンプレートファイルの拡張子
    'app.view.suffix' => '.phtml',

    // コントローラーのクラス名からテンプレート名へのマッピング
    'app.view.autoload' => [
        'Ritz\\App\\Controller\\' => 'App/',
    ],
];
