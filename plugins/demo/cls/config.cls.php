<?php

namespace PHPEMS\plugins\demo;
use function \PHPEMS\M;
use function \PHPEMS\P;
class config
{
    public $title = '插件演示';
    public $describe = '此插件可以让用户在注册时获得10个积分，内容模块访问量在原来数据基础上加300';
    public $manageUrl = 'index.php?plugins-master-demo';
}