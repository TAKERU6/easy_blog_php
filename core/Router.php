<?php

class Router
{
    protected $routes;

    public function __construct($definitions)
    {
        $this->routes = $this->compileRoutes($definitions);
    }

    public function compileRoutes($definitions)
    {
        $routes = array();

        foreach ($definitions as $url => $params) {
            $tokens = explode('/', ltrim($url, '/'));
            foreach ($tokens as $i => $token) {
                if (0 === strpos($token, ':')) {
                    $name = substr($token, 1);
                    $token = '(?P<' . $name . '>[^/]+)';
                }
                $tokens[$i] = $token;
            }

            $pattern = '/' . implode('/', $tokens);
            $routes[$pattern] = $params;
        }

        return $routes;
    }

    public function resolve($path_info)
    {
        if ('/' !== substr($path_info, 0, 1)) {
            $path_info = "/" . $path_info;
        }

        foreach ($this->routes as $pattern => $params) {
            if (preg_match('#^' . $pattern . '$#', $path_info, $matches)) {
                $params = array_merge($params, $matches);

                return $params;
            }
        }

        return false;
    }
}

//trim( 文字列 [, 削除リスト] ) 文字列の両端の空白を削除
// rtrim( 文字列 [, 削除リスト] ) 文字列の右端の空白を削除
// ltrim( 文字列 [, 削除リスト] ) 文字列の左端の空白を削除
//explode() 文字列を文字列により分割
// 例
// $str = 'one|two|three|four';
//print_r(explode('|', $str, 2));
// ↓
// Array
// (
//     [0] => one
//     [1] => two|three|four
// ) 
//strpos() 文字列内の部分文字列が最初に現れる場所を見つける
//substr() 第２引数で指定された位置から length バイト分の文字列を返します。
//protected 宣言されたメンバーには、 そのクラス自身、そのクラスを継承したクラス、および親クラスからのみアクセスできます