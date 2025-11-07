<?php

namespace PHPEMS;

class app
{
    public function plugin()
    {
        $module = M('ev')->url(1)?:'app';
        $plugin = M('ev')->url(2)?:'demo';
        $controller = M('ev')->url(3)?:'index';
        $class = "\\PHPEMS\\plugins\\{$plugin}\\{$controller}";
        $file = PEPATH."/plugins/{$plugin}/{$controller}.{$module}.php";
        if(!file_exists($file))R(array(
            'statusCode' => 300,
            "message" => "需要引用的文件不存在",
            "callbackType" => 'forward',
            "forwardUrl" => "index.php"
        ));
        include_once $file;
        $action = new $class();
        $action->display();
    }
}